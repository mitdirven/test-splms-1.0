<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $document_types = [
            [
                'code' => 'ACR',
                'name' => 'ACCREDITATION',
            ],
            [
                'code' => 'AIP',
                'name' => 'ANNUAL INVESTMENT PLAN'
            ],
            [
                'code' => 'AOEOMC',
                'name' => 'ADMINISTRATIVE/EXECUTIVE ORDER/MEMORANDUM'
            ],
            [
                'code' => 'APO',
                'name' => 'APPROVED CITY ORDINANCE'
            ],
            [
                'code' => 'APR',
                'name' => 'APPROVED CITY RESOLUTION'
            ],
            [
                'code' => 'AROM',
                'name' => 'AUDIT REPORT/OBSERVATION MEMORANDUM'
            ],
            [
                'code' => 'BAB',
                'name' => 'BRGY ANNUAL/SUPPLEMENTAL BUDGETS'
            ],
            [
                'code' => 'BAC',
                'name' => 'BRGY ADMIN CASES/COMPLAINT/AFFIDAVIT COMPLAINT'
            ],
            [
                'code' => 'BAO',
                'name' => 'BRGY ORDINANCE'
            ],
            [
                'code' => 'BAR',
                'name' => 'BRGY RESOLUTION/LIGA NG MGA BRGY'
            ],
            [
                'code' => 'BO-SC',
                'name' => 'BARANGAY ORDINANCE OF SENIOR CITIZEN OFFICIALS'
            ],
            [
                'code' => 'BR',
                'name' => 'BOARD RESOLUTION'
            ],
            [
                'code' => 'BR-SC',
                'name' => 'BARANGAY RESOLUTION OF SENIOR CITIZEN OFFICIALS'
            ],
            [
                'code' => 'CAB',
                'name' => 'CITY ANNUAL/SUPPLEMENTAL BUDGETS/BUDGET FOR'
            ],
            [
                'code' => 'CDRRMP',
                'name' => 'CITY DISASTER RISK REDUCTION AND MANAGEMENT'
            ],
            [
                'code' => 'CFO',
                'name' => 'CITIZEN\'S FORUM'
            ],
            [
                'code' => 'CON',
                'name' => 'CONTRACT (MOA/MOU/DEED OF DONATIONS/TOR)'
            ],
            [
                'code' => 'COP',
                'name' => 'COURT PLEADINGS'
            ],
            [
                'code' => 'COR',
                'name' => 'COMMITTEE REPORTS'
            ],
            [
                'code' => 'CORD',
                'name' => 'CITY ORDINANCE'
            ],
            [
                'code' => 'CRES',
                'name' => 'CITY RESOLUTION'
            ],
            [
                'code' => 'DBR',
                'name' => 'DEPARTMENTAL BUDGET REVIEW'
            ],
            [
                'code' => 'DEC',
                'name' => 'DECISION/ORDER'
            ],
            [
                'code' => 'EML',
                'name' => 'E-MAILED LETTER'
            ],
            [
                'code' => 'FAX',
                'name' => 'FAXED LETTER'
            ],
            [
                'code' => 'IP',
                'name' => 'IPS/INDIGENOUS CULTURAL COMMUNITIES'
            ],
            [
                'code' => 'LET',
                'name' => 'LETS/INVITS/SEMS/ACKNOWS/INDOS'
            ],
            [
                'code' => 'LGOR',
                'name' => 'LGU RESOLUTION/ORDINANCE'
            ],
            [
                'code' => 'LVS',
                'name' => 'LEAVES APPLICATION'
            ],
            [
                'code' => 'MEMO',
                'name' => 'MEMORANDUM'
            ],
            [
                'code' => 'NAR',
                'name' => 'NGO-ANNUAL REPORT'
            ],
            [
                'code' => 'ORAMO',
                'name' => 'ORAL/RELATED MOTION'
            ],
            [
                'code' => 'PER',
                'name' => 'PERFORMANCE EVALUATION REPORT'
            ],
            [
                'code' => 'PO',
                'name' => 'PROPOSED/AMENDATORY ORDINANCE'
            ],
            [
                'code' => 'PO-SCO',
                'name' => 'PROPOSED ORDINANCE-SENIOR CITIZEN OFFICIAL'
            ],
            [
                'code' => 'PO-SO',
                'name' => 'PROPOSED ORDINANCE-SCOUT OFFICIALS FOR A'
            ],
            [
                'code' => 'PR',
                'name' => 'PROPOSED RESOLUTION'
            ],
            [
                'code' => 'PR-SCO',
                'name' => 'PROPOSED RESOLUTION-SENIOR CITIZEN OFFICIAL'
            ],
            [
                'code' => 'PR-SO',
                'name' => 'PROPOSED RESOLUTION-SCOUT OFFICIALS FOR A'
            ],
            [
                'code' => 'PS',
                'name' => 'PRIVILEGE SPEECH'
            ],
            [
                'code' => 'PTO',
                'name' => 'PROPOSED TAX ORDINANCE'
            ],
            [
                'code' => 'ROS',
                'name' => 'RESOLUTION OF ASSOCIATION'
            ],
            [
                'code' => 'SCOFAD',
                'name' => 'ADMINISTRATIVE/EXECUTIVE ORDER/MEMORANDUM'
            ],
            [
                'code' => 'URO',
                'name' => 'UNSIGNED RESOLUTION/ORDINANCE'
            ],
            [
                'code' => 'VETO',
                'name' => 'VETOED RES/ORD'
            ],
            [
                'code' => 'WPA',
                'name' => 'WATER PERMIT APPLICATION'
            ],
        ];

        foreach ($document_types as $type) {
            DocumentType::create([
                'hashid' => Str::random(8),
                'code' => $type['code'],
                'name' => $type['name'],
            ]);
        }
    }
}
