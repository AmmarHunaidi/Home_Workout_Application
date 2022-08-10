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
        Schema::create('workouts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->deafult('');
            $table->integer('length')->default(0);
            $table->integer('excersise_count')->default(0);
            $table->integer('predicted_burnt_calories')->default(0);
            $table->double('review_count')->default(0);
            $table->string('equipment');
            $table->integer('difficulty');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('categorie_id')->references('id')->on('workout_categories')->cascadeOnDelete();
            $table->text('workout_image_url')->default('Default/default.jpg');
            $table->boolean('approval')->default(1);
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
        Schema::dropIfExists('workouts');
        Schema::enableForeignKeyConstraints();
    }
};
