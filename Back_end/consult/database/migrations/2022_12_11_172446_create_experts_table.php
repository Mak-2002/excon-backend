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
            $table->integer('photo_id')->nullable();
            $table->text('address_en')->nullable();
            $table->text('address_ar')->nullable();
            $table->double('rating')->default(0.0);
            $table->text('bio_en');
            $table->text('bio_ar');
            $table->double('service_cost');
            // $table->foreignId('appointment_id')->nullable();
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