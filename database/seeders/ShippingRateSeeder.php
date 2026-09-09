<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\ShippingRate;
use App\Models\State;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ShippingRateSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Nigeria
        |--------------------------------------------------------------------------
        */

        $country = Country::whereRaw('LOWER(name) = ?', ['nigeria'])
            ->first();

        if (!$country) {
            throw new RuntimeException(
                'Nigeria was not found in the countries table.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Shipping Zones
        |--------------------------------------------------------------------------
        */

        $shippingZones = [

            /*
            |--------------------------------------------------------------------------
            | ABUJA / FCT
            |--------------------------------------------------------------------------
            */

            [
                'state' => [
                    'Federal Capital Territory',
                    'FCT',
                    'Abuja',
                ],

                'zones' => [

                    [
                        'name' => 'Central Abuja',
                        'cost' => 2800,
                        'areas' => [
                            'Central Business District',
                            'CBD',
                            'Garki Area 1',
                            'Garki Area 2',
                            'Garki Area 3',
                            'Garki Area 7',
                            'Garki Area 8',
                            'Garki Area 10',
                            'Garki Area 11',
                            'Central Area',
                            'Secretariat',
                            'Three Arms Zone',
                            'Aso Rock',
                        ],
                    ],

                    [
                        'name' => 'Wuse',
                        'cost' => 2800,
                        'areas' => [
                            'Wuse 1',
                            'Wuse 2',
                            'Wuse Zone 1',
                            'Wuse Zone 2',
                            'Wuse Zone 3',
                            'Wuse Zone 4',
                            'Wuse Zone 5',
                            'Wuse Zone 6',
                            'Wuse Zone 7',
                            'Berger',
                        ],
                    ],

                    [
                        'name' => 'Maitama / Asokoro',
                        'cost' => 3000,
                        'areas' => [
                            'Maitama',
                            'Maitama Extension',
                            'Ministers Hill',
                            'Asokoro',
                            'Mpape',
                        ],
                    ],

                    [
                        'name' => 'Jabi / Utako',
                        'cost' => 3200,
                        'areas' => [
                            'Jabi',
                            'Utako',
                            'Mabushi',
                            'Wuye',
                        ],
                    ],

                    [
                        'name' => 'Gwarinpa / Kado',
                        'cost' => 3500,
                        'areas' => [
                            'Gwarinpa',
                            'Kado',
                            'Life Camp',
                            'Dawaki',
                        ],
                    ],

                    [
                        'name' => 'Lugbe / Lokogoma',
                        'cost' => 4000,
                        'areas' => [
                            'Lugbe',
                            'Lokogoma',
                            'Apo',
                            'Galadimawa',
                            'Games Village',
                        ],
                    ],

                    [
                        'name' => 'Kubwa / Dutse',
                        'cost' => 4500,
                        'areas' => [
                            'Kubwa',
                            'Dutse',
                            'Bwari',
                            'Dei-Dei',
                        ],
                    ],

                    [
                        'name' => 'Nyanya / Karu',
                        'cost' => 4500,
                        'areas' => [
                            'Nyanya',
                            'Karu',
                            'Jikwoyi',
                            'Kurudu',
                        ],
                    ],

                    [
                        'name' => 'Kuje',
                        'cost' => 5500,
                        'areas' => [
                            'Kuje',
                        ],
                    ],

                    [
                        'name' => 'Gwagwalada',
                        'cost' => 6000,
                        'areas' => [
                            'Gwagwalada',
                        ],
                    ],

                ],
            ],


            /*
            |--------------------------------------------------------------------------
            | LAGOS
            |--------------------------------------------------------------------------
            */

            [
                'state' => [
                    'Lagos',
                    'Lagos State',
                ],

                'zones' => [

                    [
                        'name' => 'Ikeja',
                        'cost' => 2500,
                        'areas' => [
                            'Ikeja',
                            'Oregun',
                            'Allen',
                            'Alausa',
                            'Maryland',
                        ],
                    ],

                    [
                        'name' => 'Yaba / Surulere',
                        'cost' => 2800,
                        'areas' => [
                            'Yaba',
                            'Sabo',
                            'Surulere',
                            'Lawanson',
                            'Costain',
                        ],
                    ],

                    [
                        'name' => 'Lagos Island',
                        'cost' => 3200,
                        'areas' => [
                            'Lagos Island',
                            'Marina',
                            'CMS',
                            'Victoria Island',
                            'Ikoyi',
                        ],
                    ],

                    [
                        'name' => 'Lekki Core',
                        'cost' => 3500,
                        'areas' => [
                            'Lekki Phase 1',
                            'Ikate',
                            'Osapa',
                            'Oniru',
                        ],
                    ],

                    [
                        'name' => 'Mainland East',
                        'cost' => 3200,
                        'areas' => [
                            'Ojota',
                            'Ketu',
                            'Mile 12',
                            'Magodo',
                            'Shangisha',
                        ],
                    ],

                    [
                        'name' => 'Mainland West',
                        'cost' => 3500,
                        'areas' => [
                            'Egbeda',
                            'Idimu',
                            'Ipaja',
                            'Akowonjo',
                            'Agege',
                        ],
                    ],

                    [
                        'name' => 'Ajah',
                        'cost' => 4000,
                        'areas' => [
                            'Ajah',
                            'Abraham Adesanya',
                            'Badore',
                            'Addo',
                        ],
                    ],

                    [
                        'name' => 'Sangotedo',
                        'cost' => 4500,
                        'areas' => [
                            'Sangotedo',
                            'Abijo',
                            'Awoyaya',
                        ],
                    ],

                    [
                        'name' => 'Ikorodu',
                        'cost' => 5000,
                        'areas' => [
                            'Ikorodu',
                        ],
                    ],

                    [
                        'name' => 'Badagry Corridor',
                        'cost' => 5500,
                        'areas' => [
                            'Festac',
                            'Ojo',
                            'Iyana-Iba',
                            'Badagry',
                        ],
                    ],

                    [
                        'name' => 'Epe Corridor',
                        'cost' => 6000,
                        'areas' => [
                            'Lakowe',
                            'Bogije',
                            'Epe',
                        ],
                    ],

                ],
            ],


            /*
            |--------------------------------------------------------------------------
            | IMO
            |--------------------------------------------------------------------------
            */

            [
                'state' => [
                    'Imo',
                    'Imo State',
                ],

                'zones' => [

                    [
                        'name' => 'Owerri Central',
                        'cost' => 2000,
                        'areas' => [
                            'Douglas',
                            'Wetheral',
                            'Ikenegbu',
                            'Aladinma',
                            'Works Layout',
                            'Owerri Municipal',
                        ],
                    ],

                    [
                        'name' => 'New Owerri',
                        'cost' => 2500,
                        'areas' => [
                            'New Owerri',
                            'Concorde',
                            'Area A',
                            'Area B',
                            'Area C',
                        ],
                    ],

                    [
                        'name' => 'World Bank / Umuguma',
                        'cost' => 2500,
                        'areas' => [
                            'World Bank',
                            'Umuguma',
                            'Port Harcourt Road',
                        ],
                    ],

                    [
                        'name' => 'Egbu / Naze',
                        'cost' => 3000,
                        'areas' => [
                            'Egbu',
                            'Naze',
                            'Emekuku',
                            'Airport Road',
                        ],
                    ],

                    [
                        'name' => 'Nekede / Ihiagwa',
                        'cost' => 3000,
                        'areas' => [
                            'Nekede',
                            'Ihiagwa',
                            'FUTO',
                        ],
                    ],

                    [
                        'name' => 'Obinze',
                        'cost' => 3500,
                        'areas' => [
                            'Obinze',
                        ],
                    ],

                    [
                        'name' => 'Mbaise',
                        'cost' => 4000,
                        'areas' => [
                            'Aboh Mbaise',
                            'Ahiazu Mbaise',
                            'Ezinihitte Mbaise',
                        ],
                    ],

                    [
                        'name' => 'Orlu',
                        'cost' => 4500,
                        'areas' => [
                            'Orlu',
                        ],
                    ],

                    [
                        'name' => 'Okigwe',
                        'cost' => 5000,
                        'areas' => [
                            'Okigwe',
                        ],
                    ],

                    [
                        'name' => 'Ohaji / Egbema',
                        'cost' => 5500,
                        'areas' => [
                            'Ohaji',
                            'Egbema',
                        ],
                    ],

                ],
            ],


            /*
            |--------------------------------------------------------------------------
            | RIVERS / PORT HARCOURT
            |--------------------------------------------------------------------------
            */

            [
                'state' => [
                    'Rivers',
                    'Rivers State',
                ],

                'zones' => [

                    [
                        'name' => 'Port Harcourt Central',
                        'cost' => 2500,
                        'areas' => [
                            'Old GRA',
                            'New GRA',
                            'D-Line',
                            'Mile 1',
                            'Mile 3',
                            'Diobu',
                        ],
                    ],

                    [
                        'name' => 'Trans Amadi',
                        'cost' => 2800,
                        'areas' => [
                            'Trans Amadi',
                            'Ogbunabali',
                            'Amadi-Ama',
                            'Abuloma',
                        ],
                    ],

                    [
                        'name' => 'Woji / Elelenwo',
                        'cost' => 3000,
                        'areas' => [
                            'Woji',
                            'Elelenwo',
                            'Peter Odili Road',
                        ],
                    ],

                    [
                        'name' => 'Rumuola',
                        'cost' => 3000,
                        'areas' => [
                            'Rumuola',
                            'Rumuomasi',
                            'Rumuigbo',
                            'Rumuokwuta',
                        ],
                    ],

                    [
                        'name' => 'Eliozu / Rumuokoro',
                        'cost' => 3500,
                        'areas' => [
                            'Eliozu',
                            'Rumuokoro',
                            'Eneka',
                        ],
                    ],

                    [
                        'name' => 'Choba',
                        'cost' => 3500,
                        'areas' => [
                            'Choba',
                            'Alakahia',
                            'Uniport',
                        ],
                    ],

                    [
                        'name' => 'Aluu',
                        'cost' => 4000,
                        'areas' => [
                            'Aluu',
                        ],
                    ],

                    [
                        'name' => 'Eleme / Onne',
                        'cost' => 4500,
                        'areas' => [
                            'Eleme',
                            'Onne',
                        ],
                    ],

                ],
            ],


            /*
            |--------------------------------------------------------------------------
            | DELTA
            |--------------------------------------------------------------------------
            */

            [
                'state' => [
                    'Delta',
                    'Delta State',
                ],

                'zones' => [

                    [
                        'name' => 'Asaba Central',
                        'cost' => 2500,
                        'areas' => [
                            'Asaba GRA',
                            'Okpanam Road',
                            'DBS Road',
                            'Summit Road',
                            'Nnebisi Road',
                        ],
                    ],

                    [
                        'name' => 'Asaba Outer',
                        'cost' => 3000,
                        'areas' => [
                            'Okpanam',
                            'Ibusa',
                            'Oko',
                            'Asaba Airport',
                        ],
                    ],

                    [
                        'name' => 'Ogwashi-Uku',
                        'cost' => 3500,
                        'areas' => [
                            'Ogwashi-Uku',
                        ],
                    ],

                    [
                        'name' => 'Agbor',
                        'cost' => 4000,
                        'areas' => [
                            'Agbor',
                        ],
                    ],

                    [
                        'name' => 'Warri / Effurun',
                        'cost' => 4000,
                        'areas' => [
                            'Warri',
                            'Effurun',
                            'Enerhen',
                            'Ekpan',
                        ],
                    ],

                    [
                        'name' => 'Ughelli',
                        'cost' => 4500,
                        'areas' => [
                            'Ughelli',
                        ],
                    ],

                    [
                        'name' => 'Sapele',
                        'cost' => 4500,
                        'areas' => [
                            'Sapele',
                        ],
                    ],

                    [
                        'name' => 'Kwale',
                        'cost' => 5000,
                        'areas' => [
                            'Kwale',
                        ],
                    ],

                ],
            ],


            /*
            |--------------------------------------------------------------------------
            | ANAMBRA
            |--------------------------------------------------------------------------
            */

            [
                'state' => [
                    'Anambra',
                    'Anambra State',
                ],

                'zones' => [

                    [
                        'name' => 'Onitsha',
                        'cost' => 2500,
                        'areas' => [
                            'Onitsha Main Town',
                            'Onitsha GRA',
                            'Fegge',
                            'Odoakpu',
                        ],
                    ],

                    [
                        'name' => 'Nkpor / Obosi',
                        'cost' => 3000,
                        'areas' => [
                            'Nkpor',
                            'Obosi',
                            'Ogidi',
                            'Awada',
                        ],
                    ],

                    [
                        'name' => 'Awka',
                        'cost' => 2800,
                        'areas' => [
                            'Awka',
                            'Okpuno',
                            'Amawbia',
                            'Ifite',
                        ],
                    ],

                    [
                        'name' => 'Nnewi',
                        'cost' => 3500,
                        'areas' => [
                            'Nnewi',
                            'Otolo',
                            'Uruagu',
                            'Umudim',
                        ],
                    ],

                    [
                        'name' => 'Ekwulobia',
                        'cost' => 4000,
                        'areas' => [
                            'Ekwulobia',
                            'Aguata',
                        ],
                    ],

                    [
                        'name' => 'Ihiala',
                        'cost' => 4500,
                        'areas' => [
                            'Ihiala',
                            'Uli',
                            'Okija',
                        ],
                    ],

                    [
                        'name' => 'Otuocha',
                        'cost' => 4500,
                        'areas' => [
                            'Otuocha',
                            'Aguleri',
                            'Umuleri',
                        ],
                    ],

                ],
            ],

        ];


        /*
        |--------------------------------------------------------------------------
        | Seed Data
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $country,
            $shippingZones
        ) {

            foreach ($shippingZones as $stateData) {

                $state = $this->findState(
                    $country->id,
                    $stateData['state']
                );


                if (!$state) {

                    $this->command?->warn(
                        'State not found: ' .
                            implode(
                                ' / ',
                                $stateData['state']
                            )
                    );

                    continue;
                }


                foreach ($stateData['zones'] as $zone) {

                    /*
                    |--------------------------------------------------------------------------
                    | Create / Update Shipping Zone
                    |--------------------------------------------------------------------------
                    */

                    $shippingRate = ShippingRate::updateOrCreate(
                        [
                            'country_id' => $country->id,
                            'state_id'   => $state->id,
                            'zone_name'  => $zone['name'],
                        ],
                        [
                            'shipping_cost' => $zone['cost'],
                            'is_active'     => true,
                        ]
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Replace Covered Areas
                    |--------------------------------------------------------------------------
                    */

                    $shippingRate->areas()->delete();


                    foreach (
                        array_unique($zone['areas'])
                        as $area
                    ) {

                        $shippingRate->areas()->create([
                            'name' => trim($area),
                        ]);
                    }


                    $this->command?->info(
                        "{$state->name} → {$zone['name']} seeded."
                    );
                }
            }
        });


        $this->command?->newLine();

        $this->command?->info(
            'Shipping zones seeded successfully.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Find State Using Possible Database Names
    |--------------------------------------------------------------------------
    */

    private function findState(
        int $countryId,
        array $possibleNames
    ): ?State {

        foreach ($possibleNames as $name) {

            $state = State::where(
                'country_id',
                $countryId
            )
                ->whereRaw(
                    'LOWER(name) = ?',
                    [strtolower($name)]
                )
                ->first();


            if ($state) {
                return $state;
            }
        }


        return null;
    }
}
