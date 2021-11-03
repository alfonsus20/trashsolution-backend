<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenjualanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->float('lat_pengguna');
            $table->float('long_pengguna');
            $table->float('lat_trashpicker');
            $table->float('long_trashpicker');
            $table->boolean('status')->default(false);
            $table->integer('total_harga');
            $table->unsignedBigInteger('id_pengguna');
            $table->unsignedBigInteger('id_trashpicker');
            $table->foreign('id_pengguna')->references('id')->on('pengguna');
            $table->foreign('id_trashpicker')->references('id')->on('trashpicker');
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
        Schema::dropIfExists('penjualan');
    }
}
