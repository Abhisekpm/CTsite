<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crm_occasions', function (Blueprint $table) {
            // Update occasion_type enum to include all types we see in the data
            DB::statement("ALTER TABLE crm_occasions MODIFY COLUMN occasion_type ENUM('birthday', 'anniversary', 'other', 'gender_reveal', 'baby_shower', 'bridal_shower', 'graduation', 'religious_ceremony')");
            
            // Update anchor_confidence enum to include medium
            DB::statement("ALTER TABLE crm_occasions MODIFY COLUMN anchor_confidence ENUM('high', 'low', 'medium')");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crm_occasions', function (Blueprint $table) {
            // Revert to original enum values
            DB::statement("ALTER TABLE crm_occasions MODIFY COLUMN occasion_type ENUM('birthday', 'anniversary', 'other')");
            DB::statement("ALTER TABLE crm_occasions MODIFY COLUMN anchor_confidence ENUM('high', 'low')");
        });
    }
};
