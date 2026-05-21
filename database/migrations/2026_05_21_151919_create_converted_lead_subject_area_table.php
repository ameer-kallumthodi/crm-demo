<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('converted_lead_subject_area', function (Blueprint $table) {
            $table->id();
            $table->foreignId('converted_lead_id')->constrained('converted_leads')->cascadeOnDelete();
            $table->foreignId('subject_area_id')->constrained('subject_areas')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['converted_lead_id', 'subject_area_id'], 'cl_sa_unique');
        });

        if (Schema::hasColumn('converted_leads', 'subject_area_id')) {
            DB::table('converted_leads')
                ->whereNotNull('subject_area_id')
                ->orderBy('id')
                ->chunkById(200, function ($leads) {
                    $now = now();
                    $rows = [];
                    foreach ($leads as $lead) {
                        $rows[] = [
                            'converted_lead_id' => $lead->id,
                            'subject_area_id' => $lead->subject_area_id,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                    if ($rows !== []) {
                        DB::table('converted_lead_subject_area')->insertOrIgnore($rows);
                    }
                });

            Schema::table('converted_leads', function (Blueprint $table) {
                $table->dropForeign(['subject_area_id']);
                $table->dropColumn('subject_area_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('converted_leads', 'subject_area_id')) {
            Schema::table('converted_leads', function (Blueprint $table) {
                $table->foreignId('subject_area_id')
                    ->nullable()
                    ->after('subject_id')
                    ->constrained('subject_areas')
                    ->nullOnDelete();
            });

            $pivotRows = DB::table('converted_lead_subject_area')
                ->select('converted_lead_id', DB::raw('MIN(subject_area_id) as subject_area_id'))
                ->groupBy('converted_lead_id')
                ->get();

            foreach ($pivotRows as $row) {
                DB::table('converted_leads')
                    ->where('id', $row->converted_lead_id)
                    ->update(['subject_area_id' => $row->subject_area_id]);
            }
        }

        Schema::dropIfExists('converted_lead_subject_area');
    }
};
