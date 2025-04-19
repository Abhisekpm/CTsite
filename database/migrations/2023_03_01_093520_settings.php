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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('website_name')->default('Chocholate Therapy');
            $table->string('logo')->default('logo-black.png');
            $table->string('favicon')->default('favicon.png');
            $table->string('meta_title')->default('Chocholate Therapy');
            $table->string('meta_keywords')->default('chocholate, cake, chocholate therapy');
            $table->text('meta_description')->default('Chocholate Therapy');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
