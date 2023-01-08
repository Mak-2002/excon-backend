<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('experts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->text('photo_path')->nullable();
            $table->text('address_en')->nullable();
            $table->text('address_ar')->nullable();
            $table->double('rating_sum')->default(0);
            $table->double('rating_count')->default(0);
            $table->integer('fav_count')->default(0);
            $table->text('bio_en')->nullable();
            $table->text('bio_ar')->nullable();
            $table->double('service_cost');
            $table->integer('start_time_1')->default(0);
            $table->integer('start_time_2')->default(24);
            $table->integer('end_time_1')->default(0);
            $table->integer('end_time_2')->default(0);
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
        Schema::dropIfExists('experts');
    }
};