<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $docs = [
            // Core (should already exist — keep idempotent)
            ['key' => 'passport',                'name' => 'Passport',                          'required' => true,  'active' => true],
            ['key' => 'good_conduct',           'name' => 'Certificate of Good Conduct',       'required' => true,  'active' => true],
            ['key' => 'cv',                     'name' => 'Curriculum Vitae (CV)',             'required' => true,  'active' => true],
            ['key' => 'photo',                  'name' => 'Passport Photo',                    'required' => true,  'active' => true],

            // Common additional items (inactive by default — admin can enable as needed)
            ['key' => 'id_card',                'name' => 'National ID Card',                  'required' => false, 'active' => false],
            ['key' => 'birth_certificate',      'name' => 'Birth Certificate',                 'required' => false, 'active' => false],
            ['key' => 'academic_certificates',  'name' => 'Academic Certificates',             'required' => false, 'active' => false],
            ['key' => 'kcse_certificate',       'name' => 'KCSE Certificate',                  'required' => false, 'active' => false],
            ['key' => 'diploma_certificate',    'name' => 'Diploma Certificate',               'required' => false, 'active' => false],
            ['key' => 'degree_certificate',     'name' => 'Degree Certificate',                'required' => false, 'active' => false],
            ['key' => 'professional_cert',      'name' => 'Professional Certificate',          'required' => false, 'active' => false],
            ['key' => 'work_experience_letters','name' => 'Work Experience Letters',           'required' => false, 'active' => false],
            ['key' => 'recommendation_letter',  'name' => 'Recommendation/Reference Letter',   'required' => false, 'active' => false],
            ['key' => 'driving_license',        'name' => 'Driving License',                   'required' => false, 'active' => false],
            ['key' => 'medical_certificate',    'name' => 'Medical Fitness Certificate',       'required' => false, 'active' => false],
            ['key' => 'yellow_fever_card',      'name' => 'Yellow Fever Card',                 'required' => false, 'active' => false],
            ['key' => 'covid_vaccination_card', 'name' => 'COVID-19 Vaccination Card',         'required' => false, 'active' => false],
            ['key' => 'full_photo',             'name' => 'Full Body Photo',                   'required' => false, 'active' => false],
            ['key' => 'bank_statements',        'name' => 'Bank Statements',                   'required' => false, 'active' => false],
            ['key' => 'payslips',               'name' => 'Payslips',                          'required' => false, 'active' => false],
            ['key' => 'kra_pin',                'name' => 'KRA PIN Certificate',               'required' => false, 'active' => false],
            ['key' => 'nhif_card',              'name' => 'NHIF Card',                          'required' => false, 'active' => false],
            ['key' => 'nssf_card',              'name' => 'NSSF Card',                          'required' => false, 'active' => false],
            ['key' => 'consent_letter',         'name' => 'Consent/No Objection Letter',       'required' => false, 'active' => false],
            ['key' => 'visa_page',              'name' => 'Visa Page/Approval',                'required' => false, 'active' => false],
            ['key' => 'contract_signed',        'name' => 'Signed Contract',                   'required' => false, 'active' => false],
        ];

        foreach ($docs as $d) {
            try {
                DB::table('document_types')->updateOrInsert(
                    ['key' => $d['key']],
                    [
                        'name' => $d['name'],
                        'required' => (bool)$d['required'],
                        'active' => (bool)$d['active'],
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            } catch (\Throwable $e) {
                // ignore individual failures
            }
        }
    }

    public function down(): void
    {
        // Do not delete; keep admin customizations. Optionally could remove extras.
    }
};

