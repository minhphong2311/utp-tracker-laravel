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
        Schema::create('change_clocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clock_id')->constrained()->onDelete('cascade');
            $table->string('clockin');
            $table->string('clockout')->nullable();
            $table->string('total_time')->nullable();
            $table->string('change_clockin');
            $table->string('change_clockout')->nullable();
            $table->string('change_total_time')->nullable();
            $table->string('comment')->nullable();
            $table->enum('status', ['Approved','Rejected','Cancelled','Requested'])->nullable()->default('Requested');
            $table->unsignedInteger('approver')->nullable();
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
        Schema::dropIfExists('change_clocks');
    }
};
