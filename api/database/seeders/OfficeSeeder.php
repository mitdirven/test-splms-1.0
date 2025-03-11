<?php

namespace Database\Seeders;

use App\Models\Office;
use App\Models\OfficeTypes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $offices = [
            [
                'code' => 'CO-MO',
                'type' => 'City Government Office',
                'name' => 'Office of the City Mayor',
            ],
            [
                'code' => '',
                'type' => 'City Government Office',
                'name' => 'Office of the City Vice Mayor',
            ],
            [
                'code' => '',
                'type' => 'City Government Office',
                'name' => 'Office of the Assistant City Secretary',
            ],
            [
                'code' => 'AIM',
                'type' => 'City Government Office',
                'name' => 'AIM Division',
            ],
            [
                'code' => 'AD',
                'type' => 'City Government Office',
                'name' => 'Administrative Division',
            ],
            [
                'code' => 'RD',
                'type' => 'City Government Office',
                'name' => 'Research Division',
            ],
            [
                'code' => 'SCD',
                'type' => 'City Government Office',
                'name' => 'Stenographic-Clerical Services Division',
            ],
            [
                'code' => 'SORD',
                'type' => 'City Government Office',
                'name' => 'Steno Ord/Res Division',
            ],
        ];

        foreach ($offices as $office) {
            Office::create([
                'hashid' => Str::random(8),
                'code' => $office['code'],
                'type_id' => OfficeTypes::where('name', $office['type'])->first()->id,
                'name' => $office['name'],
                'address' => null,
                'contact_no' => null,
                'email' => null,
            ]);
        }
    }
}
