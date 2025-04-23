<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('custom_orders', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('email');
            $table->string('phone', 20); // Set reasonable max length
            $table->date('pickup_date');
            $table->time('pickup_time');
            $table->string('cake_size', 50); // Set reasonable max length
            $table->string('cake_flavor');
            $table->string('eggs_ok'); // Store 'Yes' or 'No' 
            $table->text('message_on_cake')->nullable();
            $table->text('custom_decoration')->nullable();
            $table->string('decoration_image_path')->nullable(); // To store the path of the uploaded image
            $table->text('allergies')->nullable();
            $table->string('status')->default('pending'); // Default status for new orders
            $table->timestamps(); // Adds created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_orders');
    }
};
