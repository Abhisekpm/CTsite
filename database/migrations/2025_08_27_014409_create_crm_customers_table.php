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
        Schema::create('crm_customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_id')->unique(); // email-based ID
            $table->string('buyer_name');
            $table->string('primary_phone', 20)->nullable();
            $table->string('primary_email')->index();
            $table->date('first_order')->nullable();
            $table->date('last_order')->nullable();
            $table->integer('orders_count')->default(0);
            $table->text('fav_flavors')->nullable();
            $table->enum('eggs_ok', ['Yes', 'No', ''])->default('');
            $table->text('allergens')->nullable();
            $table->boolean('marketing_opt_in')->default(false);
            $table->string('channel_preference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['primary_email', 'customer_id']);
            $table->index('last_order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crm_customers');
    }
};
