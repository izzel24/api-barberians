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
        Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('queue_id');
    $table->enum('method', ['midtrans', 'cash', 'transfer']);
    $table->enum('status', ['unpaid', 'paid', 'failed'])->default('unpaid');
    $table->string('transaction_id')->nullable(); // untuk simpan ID dari Midtrans
    $table->string('snap_token')->nullable();     // kalau pakai Midtrans Snap
    $table->timestamps();

    $table->foreign('queue_id')->references('id')->on('queues')->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
