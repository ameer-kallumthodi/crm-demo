<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

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
                'key' => 'site_name',
                'value' => 'Base CRM',
                'type' => 'text',
                'description' => 'Website name',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'site_description',
                'value' => 'Customer Relationship Management System',
                'type' => 'text',
                'description' => 'Website description',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'site_logo',
                'value' => '',
                'type' => 'file',
                'description' => 'Website logo',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'site_favicon',
                'value' => 'storage/favicon.ico',
                'type' => 'file',
                'description' => 'Website favicon',
                'group' => 'general',
                'is_public' => true,
            ],
            
            // Contact Settings
            [
                'key' => 'contact_phone',
                'value' => '+1-234-567-8900',
                'type' => 'text',
                'description' => 'Contact phone number',
                'group' => 'contact',
                'is_public' => true,
            ],
            [
                'key' => 'contact_email',
                'value' => 'info@basecrm.com',
                'type' => 'text',
                'description' => 'Contact email address',
                'group' => 'contact',
                'is_public' => true,
            ],
            [
                'key' => 'contact_address',
                'value' => '123 Business Street, City, State 12345',
                'type' => 'text',
                'description' => 'Contact address',
                'group' => 'contact',
                'is_public' => true,
            ],
            
            // Email Settings
            [
                'key' => 'email_from_name',
                'value' => 'Base CRM',
                'type' => 'text',
                'description' => 'Email sender name',
                'group' => 'email',
                'is_public' => false,
            ],
            [
                'key' => 'email_from_address',
                'value' => 'noreply@basecrm.com',
                'type' => 'text',
                'description' => 'Email sender address',
                'group' => 'email',
                'is_public' => false,
            ],
            
            // Social Media Settings
            [
                'key' => 'facebook_url',
                'value' => '',
                'type' => 'text',
                'description' => 'Facebook page URL',
                'group' => 'social',
                'is_public' => true,
            ],
            [
                'key' => 'twitter_url',
                'value' => '',
                'type' => 'text',
                'description' => 'Twitter profile URL',
                'group' => 'social',
                'is_public' => true,
            ],
            [
                'key' => 'linkedin_url',
                'value' => '',
                'type' => 'text',
                'description' => 'LinkedIn profile URL',
                'group' => 'social',
                'is_public' => true,
            ],
            
            // System Settings
            [
                'key' => 'timezone',
                'value' => 'UTC',
                'type' => 'text',
                'description' => 'System timezone',
                'group' => 'system',
                'is_public' => false,
            ],
            [
                'key' => 'date_format',
                'value' => 'Y-m-d',
                'type' => 'text',
                'description' => 'Date format',
                'group' => 'system',
                'is_public' => false,
            ],
            [
                'key' => 'time_format',
                'value' => 'H:i:s',
                'type' => 'text',
                'description' => 'Time format',
                'group' => 'system',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}