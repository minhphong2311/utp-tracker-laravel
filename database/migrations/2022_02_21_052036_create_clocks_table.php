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
        Schema::create('clocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('clockin');
            $table->string('clockin_photo');
            $table->string('clockin_location');
            $table->string('clockin_address');
            $table->string('clockout')->nullable();
            $table->string('clockout_photo')->nullable();
            $table->string('clockout_location')->nullable();
            $table->string('clockout_address')->nullable();
            $table->string('total_time')->nullable();
            $table->string('earned_amount')->nullable();
            $table->string('bonus_pay')->nullable();
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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('clocks');
    }
};
