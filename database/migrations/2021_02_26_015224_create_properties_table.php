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
            $table->unsignedBigInteger('user_id'); // Foreign key Owner
            $table->unsignedBigInteger('category_id');
            $table->string('title');
            $table->string('reference')->unique();
            $table->float('plot_meters');
            $table->float('built_meters');
            $table->string('address');
            $table->json('location');
            $table->longText('description')->nullable();
            $table->enum('energetic_certification', ['obtained', 'in process', 'pending']);
            $table->boolean('sold')->default(false);
            $table->boolean('active')->default(true);

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('category_id')->references('id')->on('categories');
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
