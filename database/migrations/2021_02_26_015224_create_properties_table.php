<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Foreign key Owner
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('city_id');
            $table->string('title');
            $table->string('reference')->unique();
            $table->string('image')->default(null)->nullable();
            $table->float('plot_meters')->default(0);
            $table->float('built_meters')->default(0);
            $table->integer('rooms')->default(0)->nullable();
            $table->integer('baths')->default(0)->nullable();
            $table->string('address')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->longText('description')->nullable();
            $table->enum('energetic_certification', ['obtingut', 'en proces', 'pendent'])->nullable();
            $table->boolean('sold')->default(false);
            $table->boolean('active')->default(true);
            $table->float('price')->default(0);

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('city_id')->references('id')->on('cities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('properties');
    }
}
