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
                'name' => 'Accreditation',
            ],
            [
                'code' => 'AIP',
                'name' => 'Annual Investment Plan',
            ],
            [
                'code' => 'AOEOMC',
                'name' => 'Administrative/Executive Order/Memorandum',
            ],
            [
                'code' => 'APO',
                'name' => 'Approved City Ordinance',
            ],
            [
                'code' => 'APR',
                'name' => 'Approved City Resolution',
            ],
            [
                'code' => 'AROM',
                'name' => 'Audit Report/Observation Memorandum',
            ],
            [
                'code' => 'BAB',
                'name' => 'Barangay Annual/Supplemental Budgets',
            ],
            [
                'code' => 'BAC',
                'name' => 'Barangay Admin Cases/Complaint/Affidavit Complaint',
            ],
            [
                'code' => 'BAO',
                'name' => 'Barangay Ordinance',
            ],
            [
                'code' => 'BAR',
                'name' => 'Barangay Resolution/Liga ng mga Barangay',
            ],
            [
                'code' => 'BO-SC',
                'name' => 'Barangay Ordinance of Senior Citizen Officials',
            ],
            [
                'code' => 'BR',
                'name' => 'Board Resolution',
            ],
            [
                'code' => 'BR-SC',
                'name' => 'Barangay Resolution of Senior Citizen Officials',
            ],
            [
                'code' => 'CAB',
                'name' => 'City Annual/Supplemental Budgets/Budget For',
            ],
            [
                'code' => 'CDRRMP',
                'name' => 'City Disaster Risk Reduction and Management',
            ],
            [
                'code' => 'CFO',
                'name' => 'Citizen\'s Forum',
            ],
            [
                'code' => 'CON',
                'name' => 'Contract (MOA/MOU/Deed of Donations/TOR)',
            ],
            [
                'code' => 'COP',
                'name' => 'Court Pleadings',
            ],
            [
                'code' => 'COR',
                'name' => 'Committee Reports',
            ],
            [
                'code' => 'CORD',
                'name' => 'City Ordinance',
            ],
            [
                'code' => 'CRES',
                'name' => 'City Resolution',
            ],
            [
                'code' => 'DBR',
                'name' => 'Departmental Budget Review',
            ],
            [
                'code' => 'DEC',
                'name' => 'Decision/Order',
            ],
            [
                'code' => 'EML',
                'name' => 'E-Mailed Letter',
            ],
            [
                'code' => 'FAX',
                'name' => 'Faxed Letter',
            ],
            [
                'code' => 'IP',
                'name' => 'IPS/Indigenous Cultural Communities',
            ],
            [
                'code' => 'LET',
                'name' => 'Letters/Invitations/Seminars/Acknowledgments/Indorsements',
            ],
            [
                'code' => 'LGOR',
                'name' => 'LGU Resolution/Ordinance',
            ],
            [
                'code' => 'LVS',
                'name' => 'Leave Applications',
            ],
            [
                'code' => 'MEMO',
                'name' => 'Memorandum',
            ],
            [
                'code' => 'NAR',
                'name' => 'NGO Annual Report',
            ],
            [
                'code' => 'ORAMO',
                'name' => 'Oral/Related Motion',
            ],
            [
                'code' => 'PER',
                'name' => 'Performance Evaluation Report',
            ],
            [
                'code' => 'PO',
                'name' => 'Proposed/Amendatory Ordinance',
            ],
            [
                'code' => 'PO-SCO',
                'name' => 'Proposed Ordinance - Senior Citizen Official',
            ],
            [
                'code' => 'PO-SO',
                'name' => 'Proposed Ordinance - Scout Officials for a',
            ],
            [
                'code' => 'PR',
                'name' => 'Proposed Resolution',
            ],
            [
                'code' => 'PR-SCO',
                'name' => 'Proposed Resolution - Senior Citizen Official',
            ],
            [
                'code' => 'PR-SO',
                'name' => 'Proposed Resolution - Scout Officials for a',
            ],
            [
                'code' => 'PS',
                'name' => 'Privilege Speech',
            ],
            [
                'code' => 'PTO',
                'name' => 'Proposed Tax Ordinance',
            ],
            [
                'code' => 'ROS',
                'name' => 'Resolution of Association',
            ],
            [
                'code' => 'SCOFAD',
                'name' => 'Administrative/Executive Order/Memorandum',
            ],
            [
                'code' => 'URO',
                'name' => 'Unsigned Resolution/Ordinance',
            ],
            [
                'code' => 'VETO',
                'name' => 'Vetoed Resolution/Ordinance',
            ],
            [
                'code' => 'WPA',
                'name' => 'Water Permit Application',
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
