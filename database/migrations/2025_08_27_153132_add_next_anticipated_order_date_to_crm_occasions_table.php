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
            $table->date('next_anticipated_order_date')->nullable()->after('last_order_date_latest');
            $table->index('next_anticipated_order_date');
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
            $table->dropIndex(['next_anticipated_order_date']);
            $table->dropColumn('next_anticipated_order_date');
        });
    }
};
