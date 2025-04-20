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
        // IMPORTANT: Modifying columns requires the doctrine/dbal package.
        // Run 'composer require doctrine/dbal' if you haven't already.
        Schema::table('testimonials', function (Blueprint $table) {
            $table->string('position')->nullable()->change();
            $table->string('image')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('testimonials', function (Blueprint $table) {
            // Revert changes: make non-nullable again (assuming they were originally required)
            $table->string('position')->nullable(false)->change();
            $table->string('image')->nullable(false)->change();
        });
    }
};
