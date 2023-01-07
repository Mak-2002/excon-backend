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
        Schema::create('calendar_day_hours', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('calendar_day_id');
            $table->time('hour');
            $table->integer('state');
            /*
            -1 -> not in schedule
             0 -> not occupied 
             1 -> occupied
            */
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calendar_day_hours');
    }
};
