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
        Schema::create('crm_occasions', function (Blueprint $table) {
            $table->id();
            $table->string('customer_id'); // FK to crm_customers
            $table->enum('occasion_type', ['birthday', 'anniversary', 'other']);
            $table->string('honoree_name')->nullable();
            $table->date('anchor_week_start_date'); // Always Monday of the anchor week
            $table->integer('anchor_window_days')->default(7); // Flexibility within the week
            $table->enum('anchor_confidence', ['high', 'low']);
            $table->date('last_order_date_latest')->nullable();
            $table->integer('history_count')->default(1);
            $table->string('history_years')->nullable();
            $table->string('source_occasion_ids')->nullable();
            $table->date('next_anchor_week_start')->nullable(); // Next Monday for this occasion
            $table->date('reminder_date')->nullable(); // Sunday 8 days before anchor_week_start_date
            $table->boolean('reminder_sent')->default(false);
            $table->timestamps();
            
            $table->foreign('customer_id')->references('customer_id')->on('crm_customers');
            $table->index(['customer_id', 'occasion_type']);
            $table->index(['anchor_week_start_date']);
            $table->index(['reminder_date', 'reminder_sent']);
            $table->index('next_anchor_week_start');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crm_occasions');
    }
};
