<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode_transaksi')->unique()->index();

            $table->integer('id_user')->unsigned()->index();

            $table->dateTime('tanggal_order');
            $table->dateTime('tanggal_pengiriman')->nullable();

            $table->string('status');

            $table->timestamps();

            // optional (kalau mau relasi ke users)
            $table->foreign('id_user')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
