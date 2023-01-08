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
        Schema::create('calendar_days', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('expert_id');
            $table->date('date');
            $table->integer('first_av_st_1')->default(0);
            $table->integer('end_time_1')->default(0);
            $table->integer('first_av_st_2')->default(0);
            $table->integer('end_time_2')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calendar_days');
    }
};
