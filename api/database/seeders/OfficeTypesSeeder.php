<?php

namespace Database\Seeders;

use App\Models\OfficeTypes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OfficeTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'Banks',
            'Barangays',
            'Business Groups',
            'Churches',
            'City Government Office',
            'Cooperatives',
            'Foreign Dignitaries',
            'GOCCs/Natl Govt/Regional Govt/Field Office',
            'Homeowners Associations',
            'Hospitals/Clinics',
            'Hotel Restaurant and Bar',
            'Law Offices',
            'Local Government Unit',
            'Media Outfit (Pink/Radio/Broadcast)',
            'Non-Government Organization',
            'Non-Office Individual/Concerned Citi,zens',
            'OSCA (Senior Citizens Associations)',
            'Party List',
            'Sangguniang Panlungsod Members',
            'Sangguniang Panlungsod Offices',
            'SOFAD/SCOFAD',
            'Special Committees of the City',
            'Sports Groups/Clubs',
            'Transport Groups',
            'Vendors and Peddlers Associations',
        ];

        foreach ($types as $type) {
            OfficeTypes::create([
                'hashid' => Str::random(8),
                'name' => $type,
            ]);
        }
    }
}
