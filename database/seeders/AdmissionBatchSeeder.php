<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AdmissionBatch;
use App\Models\Batch;
use App\Models\User;

class AdmissionBatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first admin user for created_by (role_id 2 = Admin, role_id 1 = Super Admin)
        $adminUser = User::whereIn('role_id', [1, 2])->first();
        
        if (!$adminUser) {
            $this->command->warn('No admin user found. Skipping admission batch seeding.');
            return;
        }

        // Get some batches to create admission batches for
        $batches = Batch::take(3)->get();
        
        if ($batches->isEmpty()) {
            $this->command->warn('No batches found. Please create batches first.');
            return;
        }

        $admissionBatchData = [
            [
                'title' => 'Admission Batch 2024-25 A',
                'description' => 'First admission batch for academic year 2024-25',
                'batch_id' => $batches->first()->id,
                'is_active' => true,
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ],
            [
                'title' => 'Admission Batch 2024-25 B',
                'description' => 'Second admission batch for academic year 2024-25',
                'batch_id' => $batches->count() > 1 ? $batches->skip(1)->first()->id : $batches->first()->id,
                'is_active' => true,
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ],
            [
                'title' => 'Admission Batch 2024-25 C',
                'description' => 'Third admission batch for academic year 2024-25',
                'batch_id' => $batches->count() > 2 ? $batches->skip(2)->first()->id : $batches->first()->id,
                'is_active' => false,
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ],
        ];

        foreach ($admissionBatchData as $data) {
            AdmissionBatch::create($data);
        }

        $this->command->info('Admission batches seeded successfully!');
    }
}
