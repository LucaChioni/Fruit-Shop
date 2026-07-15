<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->convertTimestamps('UTC', 'Europe/Rome');
    }

    public function down(): void
    {
        $this->convertTimestamps('Europe/Rome', 'UTC');
    }

    private function convertTimestamps(string $fromTimezone, string $toTimezone): void
    {
        DB::table('orders')->orderBy('id')->each(function (object $order) use ($fromTimezone, $toTimezone) {
            $timestamps = collect(['created_at', 'updated_at'])
                ->filter(fn (string $column) => $order->{$column} !== null)
                ->mapWithKeys(fn (string $column) => [
                    $column => Carbon::parse($order->{$column}, $fromTimezone)
                        ->setTimezone($toTimezone)
                        ->format('Y-m-d H:i:s'),
                ])
                ->all();

            if ($timestamps !== []) {
                DB::table('orders')->where('id', $order->id)->update($timestamps);
            }
        });
    }
};
