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
    Schema::create('queues', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('merchant_id');
        $table->date('date');
        $table->time('time');
        $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
        $table->integer('queue_number')->nullable(); // posisi dalam antrean
        $table->integer('total_price')->nullable();  // total harga, opsional
        $table->timestamps();

        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('queue_service');
    }
};
