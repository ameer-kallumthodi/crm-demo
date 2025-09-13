<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class ThemeColorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Theme Colors Settings
        $themeSettings = [
            [
                'key' => 'sidebar_color',
                'value' => '#1e293b',
                'type' => 'color',
                'description' => 'Sidebar background color',
                'group' => 'theme',
                'is_public' => false,
            ],
            [
                'key' => 'topbar_color',
                'value' => '#ffffff',
                'type' => 'color',
                'description' => 'Topbar background color',
                'group' => 'theme',
                'is_public' => false,
            ],
            [
                'key' => 'login_primary_color',
                'value' => '#667eea',
                'type' => 'color',
                'description' => 'Primary color for login form',
                'group' => 'theme',
                'is_public' => false,
            ],
            [
                'key' => 'login_secondary_color',
                'value' => '#764ba2',
                'type' => 'color',
                'description' => 'Secondary color for login form',
                'group' => 'theme',
                'is_public' => false,
            ],
            [
                'key' => 'login_form_style',
                'value' => 'modern',
                'type' => 'text',
                'description' => 'Login form style',
                'group' => 'theme',
                'is_public' => false,
            ],
        ];

        foreach ($themeSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}