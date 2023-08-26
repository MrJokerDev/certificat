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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('seria')->nullable();
            $table->string('seria_number')->unique()->length(7);
            $table->string('ism');
            $table->string('familiya');
            $table->string('sharif')->nullable();
            $table->date('berilgan_sana');
            $table->string('umumiy')->nullable();
            $table->string('umumiy_ball')->nullable();
            $table->string('modul_1')->nullable();
            $table->string('modul_ball_1')->nullable();
            $table->string('modul_2')->nullable();
            $table->string('modul_ball_2')->nullable();
            $table->string('modul_3')->nullable();
            $table->string('modul_ball_3')->nullable();
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
        Schema::dropIfExists('teachers');
    }
};
