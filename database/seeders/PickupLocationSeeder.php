<?php

namespace Database\Seeders;

use App\Models\PickupLocation;
use App\Models\State;
use Illuminate\Database\Seeder;

class PickupLocationSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Find Lagos State
        |--------------------------------------------------------------------------
        */

        $lagos = State::query()
            ->whereRaw('LOWER(name) = ?', ['lagos'])
            ->first();

        if (! $lagos) {
            $this->command?->warn(
                'Lagos State was not found. Pickup location was not created.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Main Lagos Pickup Location
        |--------------------------------------------------------------------------
        */

        PickupLocation::updateOrCreate(
            [
                'name' => 'Dela Moure Lagos',
            ],
            [
                'address' => 'Lagos, Nigeria',

                'city' => 'Lagos',

                'state_id' => $lagos->id,

                'phone' => null,

                'email' => null,

                'opening_hours' => 'Monday - Saturday',

                'pickup_time' => 'Usually ready within 24 hours',

                'is_active' => true,

                'is_default' => true,
            ]
        );
    }
}
