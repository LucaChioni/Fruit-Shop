<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Validation\ValidationException;
use Yasumi\Yasumi;

class PickupSchedule
{
    private array $italianHolidayProviders = [];

    public function earliestPickupAt(): CarbonInterface
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

    public function validate(CarbonInterface $pickupAt): void
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
                'pickup_at' => __('ui.checkout.closed_date_error'),
            ]);
        }

        if (! $isOpen) {
            throw ValidationException::withMessages([
                'pickup_at' => __('ui.validation.pickup_time_slot'),
            ]);
        }
    }

    public function closedDates(CarbonInterface $startFrom): array
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

    private function isClosedPickupDate(CarbonInterface $date): bool
    {
        return $date->isSunday() || $this->italianHolidays($date->year)->isHoliday($date);
    }

    private function italianHolidays(int $year)
    {
        return $this->italianHolidayProviders[$year]
            ??= Yasumi::create('Italy', $year, 'it_IT');
    }
}
