<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'app_name',
                'value' => 'Equipment Inventory Management System',
                'group' => 'general',
                'type' => 'string',
                'description' => 'Application name displayed in the header'
            ],
            [
                'key' => 'app_short_name',
                'value' => 'EIMS',
                'group' => 'general',
                'type' => 'string',
                'description' => 'Short application name for PWA'
            ],
            [
                'key' => 'company_name',
                'value' => 'Your Company Name',
                'group' => 'general',
                'type' => 'string',
                'description' => 'Company name for reports'
            ],
            [
                'key' => 'company_address',
                'value' => 'Your Company Address',
                'group' => 'general',
                'type' => 'string',
                'description' => 'Company address for reports'
            ],
            
            // Equipment Settings
            [
                'key' => 'equipment_code_prefix',
                'value' => 'EQP',
                'group' => 'equipment',
                'type' => 'string',
                'description' => 'Prefix for equipment codes'
            ],
            [
                'key' => 'transaction_code_prefix',
                'value' => 'TRX',
                'group' => 'equipment',
                'type' => 'string',
                'description' => 'Prefix for transaction codes'
            ],
            [
                'key' => 'borrowing_code_prefix',
                'value' => 'BRW',
                'group' => 'equipment',
                'type' => 'string',
                'description' => 'Prefix for borrowing codes'
            ],
            [
                'key' => 'maintenance_code_prefix',
                'value' => 'MNT',
                'group' => 'equipment',
                'type' => 'string',
                'description' => 'Prefix for maintenance codes'
            ],
            [
                'key' => 'disposal_code_prefix',
                'value' => 'DSP',
                'group' => 'equipment',
                'type' => 'string',
                'description' => 'Prefix for disposal codes'
            ],
            
            // Admin Settings
            [
                'key' => 'max_admins',
                'value' => '2',
                'group' => 'admin',
                'type' => 'integer',
                'description' => 'Maximum number of admin accounts allowed'
            ],
            
            // Notification Settings
            [
                'key' => 'borrowing_overdue_days',
                'value' => '7',
                'group' => 'notification',
                'type' => 'integer',
                'description' => 'Days before marking borrowing as overdue'
            ],
            [
                'key' => 'maintenance_reminder_days',
                'value' => '7',
                'group' => 'notification',
                'type' => 'integer',
                'description' => 'Days before maintenance due to send reminder'
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
