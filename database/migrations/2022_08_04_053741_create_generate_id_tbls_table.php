<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateGenerateIdTblsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Only create trigger for MySQL/MariaDB, skip for SQLite testing
        $connection = DB::connection();
        if ($connection->getDriverName() !== 'sqlite') {
            DB::unprepared('
                CREATE TRIGGER id_store BEFORE INSERT ON users FOR EACH ROW
                BEGIN
                    INSERT INTO sequence_tbl VALUES (NULL);
                    SET NEW.user_id = CONCAT("0", LPAD(LAST_INSERT_ID(), 4, "0"));
                END
            ');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Only drop trigger for MySQL/MariaDB, skip for SQLite testing
        $connection = DB::connection();
        if ($connection->getDriverName() !== 'sqlite') {
            DB::unprepared('DROP TRIGGER "id_store"');
        }
    }
}
