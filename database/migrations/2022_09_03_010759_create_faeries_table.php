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
        Schema::create('faeries', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->float('latitude')->nullable();
            $table->float('longitude')->nullable();
            $table->string('name');
            $table->string('image_url')->nullable();
            $table->text('bio')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('faeries');
    }
};