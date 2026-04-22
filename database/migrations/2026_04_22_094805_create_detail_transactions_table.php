<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetailTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('detail_transactions', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('id_transaksi')->unsigned()->index();
            $table->integer('id_menu')->unsigned()->index();

            $table->string('deskripsi');
            $table->string('foto')->nullable();

            $table->integer('harga');
            $table->integer('jumlah');
            $table->integer('total');

            $table->timestamps();

            // relasi ke transactions
            $table->foreign('id_transaksi')
                ->references('id')->on('transactions')
                ->onDelete('cascade');

            // relasi ke menu (opsional, sesuaikan nama tabel menu kamu)
            $table->foreign('id_menu')
                ->references('id')->on('menus')
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
       Schema::dropIfExists('detail_transactions');
    }
}
