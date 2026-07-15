<?php

namespace App\Http\Controllers;

use App\Mail\OrderPlaced;
use App\Models\Order;
use App\Models\User;
use App\Services\CartService;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Yasumi\Yasumi;

class CheckoutController extends Controller
{
    private array $italianHolidayProviders = [];

    public function create(Request $request, CartService $cartService)
    {
        $cart = $cartService->getCurrentCart($request);

        if ($cart->items()->doesntExist()) {
            return redirect()
                ->route('cart.index')
                ->with('error', __('ui.flash.cart_empty'));
        }

        return Inertia::render('Checkout/Create', [
            'pickupAtDefault' => $this->earliestPickupAt()->format('Y-m-d\TH:i'),
            'pickupAtMin' => now()->addHours(2)->format('Y-m-d\TH:i'),
            'pickupDateMax' => now()->addDays(369)->format('Y-m-d'),
            'closedPickupDates' => $this->closedPickupDates(now()->startOfDay()),
        ]);
    }

    public function store(Request $request, CartService $cartService): RedirectResponse
    {
        $cart = $cartService->getCurrentCart($request);

        $cart->load('items.product');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', __('ui.flash.cart_empty'));
        }

        $validated = $request->validate([
            'pickup_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ], [
            'pickup_at.required' => __('ui.validation.pickup_required'),
            'pickup_at.date' => __('ui.validation.pickup_date'),
            'notes.max' => __('ui.validation.notes_max'),
        ]);

        $pickupAt = Carbon::parse($validated['pickup_at'])->seconds(0);
        $this->validatePickupAt($pickupAt);

        $order = DB::transaction(function () use ($request, $validated, $cart, $pickupAt) {
            $totalAmount = 0;

            foreach ($cart->items as $item) {
                $totalAmount += $item->quantity * $item->product->price;
            }

            $order = Order::create([
                'user_id' => $request->user()->id,
                'status' => 'pending',
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
                'pickup_at' => $pickupAt,
            ]);

            foreach ($cart->items as $item) {
                $product = $item->product;
                $lineTotal = $item->quantity * $product->price;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_name_en' => $product->name_en,
                    'unit_type' => $product->unit_type,
                    'unit_price' => $product->price,
                    'quantity' => $item->quantity,
                    'line_total' => $lineTotal,
                ]);
            }

            $cart->items()->delete();

            return $order;
        });

        $adminRecipient = config('mail.order_notifications.address');
        $customerRecipient = $request->user()->email;

        if ($adminRecipient) {
            $adminLocale = User::query()
                ->where('email', $adminRecipient)
                ->where('is_admin', true)
                ->value('locale') ?? 'it';

            Mail::to($adminRecipient)->locale($adminLocale)->send(new OrderPlaced($order));
        }

        if ($customerRecipient !== $adminRecipient) {
            Mail::to($customerRecipient)->locale(app()->getLocale())->send(new OrderPlaced($order));
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('success', __('ui.flash.order_created'));
    }

    private function earliestPickupAt(): CarbonInterface
    {
        $pickupAt = now()->addHours(2)->seconds(0);

        while (true) {
            if ($this->isClosedPickupDate($pickupAt)) {
                $pickupAt = $pickupAt->addDay()->setTime(11, 0);

                continue;
            }

            $minutes = ($pickupAt->hour * 60) + $pickupAt->minute;

            if ($minutes < 11 * 60) {
                return $pickupAt->setTime(11, 0);
            }

            if ($minutes > 13 * 60 && $minutes < 16 * 60) {
                return $pickupAt->setTime(16, 0);
            }

            if ($minutes > (19 * 60) + 30) {
                $pickupAt = $pickupAt->addDay()->setTime(11, 0);

                continue;
            }

            return $pickupAt;
        }
    }

    private function validatePickupAt(CarbonInterface $pickupAt): void
    {
        $minimum = now()->addHours(2);
        $minutes = ($pickupAt->hour * 60) + $pickupAt->minute;
        $isOpen = ($minutes >= 11 * 60 && $minutes <= 13 * 60)
            || ($minutes >= 16 * 60 && $minutes <= (19 * 60) + 30);

        if ($pickupAt->lt($minimum)) {
            throw ValidationException::withMessages([
                'pickup_at' => __('ui.validation.pickup_minimum'),
            ]);
        }

        if ($this->isClosedPickupDate($pickupAt)) {
            throw ValidationException::withMessages([
                'pickup_at' => 'Il ritiro non è disponibile la domenica o nei giorni festivi.',
            ]);
        }

        if (! $isOpen) {
            throw ValidationException::withMessages([
                'pickup_at' => __('ui.validation.pickup_time_slot'),
            ]);
        }
    }

    private function isClosedPickupDate(CarbonInterface $date): bool
    {
        return $date->isSunday() || $this->italianHolidays($date->year)->isHoliday($date);
    }

    private function closedPickupDates(CarbonInterface $startFrom): array
    {
        $dates = [];
        $date = Carbon::parse($startFrom)->startOfDay();

        for ($day = 0; $day < 370; $day++) {
            if ($this->isClosedPickupDate($date)) {
                $dates[] = $date->toDateString();
            }

            $date->addDay();
        }

        return $dates;
    }

    private function italianHolidays(int $year)
    {
        return $this->italianHolidayProviders[$year]
            ??= Yasumi::create('Italy', $year, 'it_IT');
    }
}
