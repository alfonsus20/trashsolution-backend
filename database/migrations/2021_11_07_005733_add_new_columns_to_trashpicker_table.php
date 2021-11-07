<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsToTrashpickerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trashpicker', function (Blueprint $table) {
            $table->float('lat')->default(0.0)->after('phone');
            $table->float('long')->default(0.0)->after('lat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trashpicker', function (Blueprint $table) {
            //
        });
    }
}
