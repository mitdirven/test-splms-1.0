<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Position::create([
            'hashid' => Str::random(8),
            'code' => 'SPSEC',
            'name' => 'Sangguniang Panlungsod Secretary',
            'description' => 'Serves as the head of the legislative secretariat, ensuring proper documentation and legislative processes.',
        ]);

        Position::create([
            'hashid' => Str::random(8),
            'code' => 'COUNC',
            'name' => 'City Councilor',
            'description' => 'Elected official responsible for crafting ordinances, resolutions, and policies for the city government.',
        ]);

        Position::create([
            'hashid' => Str::random(8),
            'code' => 'VICE-MAY',
            'name' => 'Vice Mayor',
            'description' => 'Presides over the Sangguniang Panlungsod sessions and oversees legislative functions.',
        ]);

        Position::create([
            'hashid' => Str::random(8),
            'code' => 'LB-MEM',
            'name' => 'Liga ng mga Barangay Representative',
            'description' => 'Represents the barangays in the Sangguniang Panlungsod, voicing concerns and legislative needs of local communities.',
        ]);

        Position::create([
            'hashid' => Str::random(8),
            'code' => 'SKREP',
            'name' => 'Sangguniang Kabataan Federation President',
            'description' => 'Represents the youth sector in the Sangguniang Panlungsod and advocates for youth-related policies and programs.',
        ]);

        Position::create([
            'hashid' => Str::random(8),
            'code' => 'AO IV',
            'name' => 'Administrative Officer IV',
            'description' => 'Manages administrative tasks, personnel matters, and legislative documentation within the office.',
        ]);

        Position::create([
            'hashid' => Str::random(8),
            'code' => 'LSO III',
            'name' => 'Legislative Staff Officer III',
            'description' => 'Assists in legislative research, drafting ordinances, and preparing reports for councilors.',
        ]);

        Position::create([
            'hashid' => Str::random(8),
            'code' => 'STENOG',
            'name' => 'Stenographer',
            'description' => 'Records and transcribes minutes of legislative sessions and committee meetings.',
        ]);

        Position::create([
            'hashid' => Str::random(8),
            'code' => 'RECORDS',
            'name' => 'Records Officer',
            'description' => 'Maintains and organizes legislative documents, resolutions, and ordinances for easy retrieval and reference.',
        ]);

        Position::create([
            'hashid' => Str::random(8),
            'code' => 'IT-SP',
            'name' => 'IT Support Staff',
            'description' => 'Provides technical support for the Sangguniang Panlungsod, including system maintenance and troubleshooting.',
        ]);
    }
}
