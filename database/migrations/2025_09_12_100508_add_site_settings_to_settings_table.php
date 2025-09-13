<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert or update site settings in the settings table
        $settings = [
            [
                'key' => 'site_name',
                'value' => 'Base CRM',
                'type' => 'text',
                'description' => 'Website name displayed in title and header',
                'group' => 'site',
                'is_public' => true,
            ],
            [
                'key' => 'site_description',
                'value' => 'CRM Management System',
                'type' => 'text',
                'description' => 'Website description for SEO and meta tags',
                'group' => 'site',
                'is_public' => true,
            ],
            [
                'key' => 'site_logo',
                'value' => 'storage/logo.png',
                'type' => 'file',
                'description' => 'Website logo file path',
                'group' => 'site',
                'is_public' => true,
            ],
            [
                'key' => 'site_favicon',
                'value' => 'storage/favicon.ico',
                'type' => 'file',
                'description' => 'Website favicon file path',
                'group' => 'site',
                'is_public' => true,
            ],
            [
                'key' => 'sidebar_color',
                'value' => '#1e293b',
                'type' => 'color',
                'description' => 'Sidebar background color',
                'group' => 'theme',
                'is_public' => true,
            ],
            [
                'key' => 'topbar_color',
                'value' => '#ffffff',
                'type' => 'color',
                'description' => 'Topbar background color',
                'group' => 'theme',
                'is_public' => true,
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->whereIn('key', ['site_name', 'site_description', 'site_logo', 'site_favicon'])->delete();
    }
};