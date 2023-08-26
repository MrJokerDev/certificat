<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Date;
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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->integer('seria_number')->nullable();
            $table->string('full_name');
            $table->string('nik_name')->unique();
            $table->string('course');
            $table->string('season');
            $table->string('sertificat_1')->nullable();
            $table->string('sertificat_2')->nullable();
            $table->string('progress_0')->nullable();
            $table->string('progress_1')->nullable();
            $table->string('progress_2')->nullable();
            $table->string('progress_3')->nullable();
            $table->string('progress_4')->nullable();
            $table->string('progress_5')->nullable();
            $table->string('date_of_issue')->default(Carbon::today()->toDateString());
            $table->timestamps();
            $table->string('seria')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
};
