<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [

            /*
            |--------------------------------------------------------------------------
            | AFRICA
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'Algeria',
                'iso2' => 'DZ',
                'iso3' => 'DZA',
                'phone_code' => '+213',
                'currency_code' => 'DZD',
                'is_active' => true,
            ],

            [
                'name' => 'Angola',
                'iso2' => 'AO',
                'iso3' => 'AGO',
                'phone_code' => '+244',
                'currency_code' => 'AOA',
                'is_active' => true,
            ],

            [
                'name' => 'Benin',
                'iso2' => 'BJ',
                'iso3' => 'BEN',
                'phone_code' => '+229',
                'currency_code' => 'XOF',
                'is_active' => true,
            ],

            [
                'name' => 'Botswana',
                'iso2' => 'BW',
                'iso3' => 'BWA',
                'phone_code' => '+267',
                'currency_code' => 'BWP',
                'is_active' => true,
            ],

            [
                'name' => 'Burkina Faso',
                'iso2' => 'BF',
                'iso3' => 'BFA',
                'phone_code' => '+226',
                'currency_code' => 'XOF',
                'is_active' => true,
            ],

            [
                'name' => 'Burundi',
                'iso2' => 'BI',
                'iso3' => 'BDI',
                'phone_code' => '+257',
                'currency_code' => 'BIF',
                'is_active' => true,
            ],

            [
                'name' => 'Cabo Verde',
                'iso2' => 'CV',
                'iso3' => 'CPV',
                'phone_code' => '+238',
                'currency_code' => 'CVE',
                'is_active' => true,
            ],

            [
                'name' => 'Cameroon',
                'iso2' => 'CM',
                'iso3' => 'CMR',
                'phone_code' => '+237',
                'currency_code' => 'XAF',
                'is_active' => true,
            ],

            [
                'name' => 'Central African Republic',
                'iso2' => 'CF',
                'iso3' => 'CAF',
                'phone_code' => '+236',
                'currency_code' => 'XAF',
                'is_active' => true,
            ],

            [
                'name' => 'Chad',
                'iso2' => 'TD',
                'iso3' => 'TCD',
                'phone_code' => '+235',
                'currency_code' => 'XAF',
                'is_active' => true,
            ],

            [
                'name' => 'Comoros',
                'iso2' => 'KM',
                'iso3' => 'COM',
                'phone_code' => '+269',
                'currency_code' => 'KMF',
                'is_active' => true,
            ],

            [
                'name' => 'Republic of the Congo',
                'iso2' => 'CG',
                'iso3' => 'COG',
                'phone_code' => '+242',
                'currency_code' => 'XAF',
                'is_active' => true,
            ],

            [
                'name' => 'Democratic Republic of the Congo',
                'iso2' => 'CD',
                'iso3' => 'COD',
                'phone_code' => '+243',
                'currency_code' => 'CDF',
                'is_active' => true,
            ],

            [
                'name' => 'Côte d\'Ivoire',
                'iso2' => 'CI',
                'iso3' => 'CIV',
                'phone_code' => '+225',
                'currency_code' => 'XOF',
                'is_active' => true,
            ],

            [
                'name' => 'Djibouti',
                'iso2' => 'DJ',
                'iso3' => 'DJI',
                'phone_code' => '+253',
                'currency_code' => 'DJF',
                'is_active' => true,
            ],

            [
                'name' => 'Egypt',
                'iso2' => 'EG',
                'iso3' => 'EGY',
                'phone_code' => '+20',
                'currency_code' => 'EGP',
                'is_active' => true,
            ],

            [
                'name' => 'Equatorial Guinea',
                'iso2' => 'GQ',
                'iso3' => 'GNQ',
                'phone_code' => '+240',
                'currency_code' => 'XAF',
                'is_active' => true,
            ],

            [
                'name' => 'Eritrea',
                'iso2' => 'ER',
                'iso3' => 'ERI',
                'phone_code' => '+291',
                'currency_code' => 'ERN',
                'is_active' => true,
            ],

            [
                'name' => 'Eswatini',
                'iso2' => 'SZ',
                'iso3' => 'SWZ',
                'phone_code' => '+268',
                'currency_code' => 'SZL',
                'is_active' => true,
            ],

            [
                'name' => 'Ethiopia',
                'iso2' => 'ET',
                'iso3' => 'ETH',
                'phone_code' => '+251',
                'currency_code' => 'ETB',
                'is_active' => true,
            ],

            [
                'name' => 'Gabon',
                'iso2' => 'GA',
                'iso3' => 'GAB',
                'phone_code' => '+241',
                'currency_code' => 'XAF',
                'is_active' => true,
            ],

            [
                'name' => 'Gambia',
                'iso2' => 'GM',
                'iso3' => 'GMB',
                'phone_code' => '+220',
                'currency_code' => 'GMD',
                'is_active' => true,
            ],

            [
                'name' => 'Ghana',
                'iso2' => 'GH',
                'iso3' => 'GHA',
                'phone_code' => '+233',
                'currency_code' => 'GHS',
                'is_active' => true,
            ],

            [
                'name' => 'Guinea',
                'iso2' => 'GN',
                'iso3' => 'GIN',
                'phone_code' => '+224',
                'currency_code' => 'GNF',
                'is_active' => true,
            ],

            [
                'name' => 'Guinea-Bissau',
                'iso2' => 'GW',
                'iso3' => 'GNB',
                'phone_code' => '+245',
                'currency_code' => 'XOF',
                'is_active' => true,
            ],

            [
                'name' => 'Kenya',
                'iso2' => 'KE',
                'iso3' => 'KEN',
                'phone_code' => '+254',
                'currency_code' => 'KES',
                'is_active' => true,
            ],

            [
                'name' => 'Lesotho',
                'iso2' => 'LS',
                'iso3' => 'LSO',
                'phone_code' => '+266',
                'currency_code' => 'LSL',
                'is_active' => true,
            ],

            [
                'name' => 'Liberia',
                'iso2' => 'LR',
                'iso3' => 'LBR',
                'phone_code' => '+231',
                'currency_code' => 'LRD',
                'is_active' => true,
            ],

            [
                'name' => 'Libya',
                'iso2' => 'LY',
                'iso3' => 'LBY',
                'phone_code' => '+218',
                'currency_code' => 'LYD',
                'is_active' => true,
            ],

            [
                'name' => 'Madagascar',
                'iso2' => 'MG',
                'iso3' => 'MDG',
                'phone_code' => '+261',
                'currency_code' => 'MGA',
                'is_active' => true,
            ],

            [
                'name' => 'Malawi',
                'iso2' => 'MW',
                'iso3' => 'MWI',
                'phone_code' => '+265',
                'currency_code' => 'MWK',
                'is_active' => true,
            ],

            [
                'name' => 'Mali',
                'iso2' => 'ML',
                'iso3' => 'MLI',
                'phone_code' => '+223',
                'currency_code' => 'XOF',
                'is_active' => true,
            ],

            [
                'name' => 'Mauritania',
                'iso2' => 'MR',
                'iso3' => 'MRT',
                'phone_code' => '+222',
                'currency_code' => 'MRU',
                'is_active' => true,
            ],

            [
                'name' => 'Mauritius',
                'iso2' => 'MU',
                'iso3' => 'MUS',
                'phone_code' => '+230',
                'currency_code' => 'MUR',
                'is_active' => true,
            ],

            [
                'name' => 'Morocco',
                'iso2' => 'MA',
                'iso3' => 'MAR',
                'phone_code' => '+212',
                'currency_code' => 'MAD',
                'is_active' => true,
            ],

            [
                'name' => 'Mozambique',
                'iso2' => 'MZ',
                'iso3' => 'MOZ',
                'phone_code' => '+258',
                'currency_code' => 'MZN',
                'is_active' => true,
            ],

            [
                'name' => 'Namibia',
                'iso2' => 'NA',
                'iso3' => 'NAM',
                'phone_code' => '+264',
                'currency_code' => 'NAD',
                'is_active' => true,
            ],

            [
                'name' => 'Niger',
                'iso2' => 'NE',
                'iso3' => 'NER',
                'phone_code' => '+227',
                'currency_code' => 'XOF',
                'is_active' => true,
            ],

            [
                'name' => 'Nigeria',
                'iso2' => 'NG',
                'iso3' => 'NGA',
                'phone_code' => '+234',
                'currency_code' => 'NGN',
                'is_active' => true,
            ],

            [
                'name' => 'Rwanda',
                'iso2' => 'RW',
                'iso3' => 'RWA',
                'phone_code' => '+250',
                'currency_code' => 'RWF',
                'is_active' => true,
            ],

            [
                'name' => 'São Tomé and Príncipe',
                'iso2' => 'ST',
                'iso3' => 'STP',
                'phone_code' => '+239',
                'currency_code' => 'STN',
                'is_active' => true,
            ],

            [
                'name' => 'Senegal',
                'iso2' => 'SN',
                'iso3' => 'SEN',
                'phone_code' => '+221',
                'currency_code' => 'XOF',
                'is_active' => true,
            ],

            [
                'name' => 'Seychelles',
                'iso2' => 'SC',
                'iso3' => 'SYC',
                'phone_code' => '+248',
                'currency_code' => 'SCR',
                'is_active' => true,
            ],

            [
                'name' => 'Sierra Leone',
                'iso2' => 'SL',
                'iso3' => 'SLE',
                'phone_code' => '+232',
                'currency_code' => 'SLE',
                'is_active' => true,
            ],

            [
                'name' => 'Somalia',
                'iso2' => 'SO',
                'iso3' => 'SOM',
                'phone_code' => '+252',
                'currency_code' => 'SOS',
                'is_active' => true,
            ],

            [
                'name' => 'South Africa',
                'iso2' => 'ZA',
                'iso3' => 'ZAF',
                'phone_code' => '+27',
                'currency_code' => 'ZAR',
                'is_active' => true,
            ],

            [
                'name' => 'South Sudan',
                'iso2' => 'SS',
                'iso3' => 'SSD',
                'phone_code' => '+211',
                'currency_code' => 'SSP',
                'is_active' => true,
            ],

            [
                'name' => 'Sudan',
                'iso2' => 'SD',
                'iso3' => 'SDN',
                'phone_code' => '+249',
                'currency_code' => 'SDG',
                'is_active' => true,
            ],

            [
                'name' => 'Tanzania',
                'iso2' => 'TZ',
                'iso3' => 'TZA',
                'phone_code' => '+255',
                'currency_code' => 'TZS',
                'is_active' => true,
            ],

            [
                'name' => 'Togo',
                'iso2' => 'TG',
                'iso3' => 'TGO',
                'phone_code' => '+228',
                'currency_code' => 'XOF',
                'is_active' => true,
            ],

            [
                'name' => 'Tunisia',
                'iso2' => 'TN',
                'iso3' => 'TUN',
                'phone_code' => '+216',
                'currency_code' => 'TND',
                'is_active' => true,
            ],

            [
                'name' => 'Uganda',
                'iso2' => 'UG',
                'iso3' => 'UGA',
                'phone_code' => '+256',
                'currency_code' => 'UGX',
                'is_active' => true,
            ],

            [
                'name' => 'Zambia',
                'iso2' => 'ZM',
                'iso3' => 'ZMB',
                'phone_code' => '+260',
                'currency_code' => 'ZMW',
                'is_active' => true,
            ],

            [
                'name' => 'Zimbabwe',
                'iso2' => 'ZW',
                'iso3' => 'ZWE',
                'phone_code' => '+263',
                'currency_code' => 'ZWG',
                'is_active' => true,
            ],

            /*
            |--------------------------------------------------------------------------
            | NORTH AMERICA
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'United States',
                'iso2' => 'US',
                'iso3' => 'USA',
                'phone_code' => '+1',
                'currency_code' => 'USD',
                'is_active' => true,
            ],

            [
                'name' => 'Canada',
                'iso2' => 'CA',
                'iso3' => 'CAN',
                'phone_code' => '+1',
                'currency_code' => 'CAD',
                'is_active' => true,
            ],

            /*
            |--------------------------------------------------------------------------
            | EUROPE
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'United Kingdom',
                'iso2' => 'GB',
                'iso3' => 'GBR',
                'phone_code' => '+44',
                'currency_code' => 'GBP',
                'is_active' => true,
            ],

            [
                'name' => 'France',
                'iso2' => 'FR',
                'iso3' => 'FRA',
                'phone_code' => '+33',
                'currency_code' => 'EUR',
                'is_active' => true,
            ],

            [
                'name' => 'Germany',
                'iso2' => 'DE',
                'iso3' => 'DEU',
                'phone_code' => '+49',
                'currency_code' => 'EUR',
                'is_active' => true,
            ],

            [
                'name' => 'Italy',
                'iso2' => 'IT',
                'iso3' => 'ITA',
                'phone_code' => '+39',
                'currency_code' => 'EUR',
                'is_active' => true,
            ],

            [
                'name' => 'Spain',
                'iso2' => 'ES',
                'iso3' => 'ESP',
                'phone_code' => '+34',
                'currency_code' => 'EUR',
                'is_active' => true,
            ],

            [
                'name' => 'Netherlands',
                'iso2' => 'NL',
                'iso3' => 'NLD',
                'phone_code' => '+31',
                'currency_code' => 'EUR',
                'is_active' => true,
            ],

            [
                'name' => 'Belgium',
                'iso2' => 'BE',
                'iso3' => 'BEL',
                'phone_code' => '+32',
                'currency_code' => 'EUR',
                'is_active' => true,
            ],

            [
                'name' => 'Ireland',
                'iso2' => 'IE',
                'iso3' => 'IRL',
                'phone_code' => '+353',
                'currency_code' => 'EUR',
                'is_active' => true,
            ],

            [
                'name' => 'Portugal',
                'iso2' => 'PT',
                'iso3' => 'PRT',
                'phone_code' => '+351',
                'currency_code' => 'EUR',
                'is_active' => true,
            ],

            [
                'name' => 'Switzerland',
                'iso2' => 'CH',
                'iso3' => 'CHE',
                'phone_code' => '+41',
                'currency_code' => 'CHF',
                'is_active' => true,
            ],

            [
                'name' => 'Sweden',
                'iso2' => 'SE',
                'iso3' => 'SWE',
                'phone_code' => '+46',
                'currency_code' => 'SEK',
                'is_active' => true,
            ],

            [
                'name' => 'Norway',
                'iso2' => 'NO',
                'iso3' => 'NOR',
                'phone_code' => '+47',
                'currency_code' => 'NOK',
                'is_active' => true,
            ],

            /*
            |--------------------------------------------------------------------------
            | MIDDLE EAST
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'United Arab Emirates',
                'iso2' => 'AE',
                'iso3' => 'ARE',
                'phone_code' => '+971',
                'currency_code' => 'AED',
                'is_active' => true,
            ],

            [
                'name' => 'Saudi Arabia',
                'iso2' => 'SA',
                'iso3' => 'SAU',
                'phone_code' => '+966',
                'currency_code' => 'SAR',
                'is_active' => true,
            ],

            [
                'name' => 'Qatar',
                'iso2' => 'QA',
                'iso3' => 'QAT',
                'phone_code' => '+974',
                'currency_code' => 'QAR',
                'is_active' => true,
            ],

            [
                'name' => 'Kuwait',
                'iso2' => 'KW',
                'iso3' => 'KWT',
                'phone_code' => '+965',
                'currency_code' => 'KWD',
                'is_active' => true,
            ],

            [
                'name' => 'Bahrain',
                'iso2' => 'BH',
                'iso3' => 'BHR',
                'phone_code' => '+973',
                'currency_code' => 'BHD',
                'is_active' => true,
            ],

            [
                'name' => 'Oman',
                'iso2' => 'OM',
                'iso3' => 'OMN',
                'phone_code' => '+968',
                'currency_code' => 'OMR',
                'is_active' => true,
            ],

            /*
            |--------------------------------------------------------------------------
            | ASIA
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'China',
                'iso2' => 'CN',
                'iso3' => 'CHN',
                'phone_code' => '+86',
                'currency_code' => 'CNY',
                'is_active' => true,
            ],

            [
                'name' => 'India',
                'iso2' => 'IN',
                'iso3' => 'IND',
                'phone_code' => '+91',
                'currency_code' => 'INR',
                'is_active' => true,
            ],

            [
                'name' => 'Singapore',
                'iso2' => 'SG',
                'iso3' => 'SGP',
                'phone_code' => '+65',
                'currency_code' => 'SGD',
                'is_active' => true,
            ],

            /*
            |--------------------------------------------------------------------------
            | OCEANIA
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'Australia',
                'iso2' => 'AU',
                'iso3' => 'AUS',
                'phone_code' => '+61',
                'currency_code' => 'AUD',
                'is_active' => true,
            ],

            [
                'name' => 'New Zealand',
                'iso2' => 'NZ',
                'iso3' => 'NZL',
                'phone_code' => '+64',
                'currency_code' => 'NZD',
                'is_active' => true,
            ],
        ];

        $now = now();

        foreach ($countries as $country) {
            DB::table('countries')->updateOrInsert(
                [
                    'iso2' => $country['iso2'],
                ],
                [
                    'name' => $country['name'],
                    'iso3' => $country['iso3'],
                    'phone_code' => $country['phone_code'],
                    'currency_code' => $country['currency_code'],
                    'is_active' => $country['is_active'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
