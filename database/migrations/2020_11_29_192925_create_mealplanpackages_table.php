<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMealplanpackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mealplanpackages', function (Blueprint $table) {
            $table->id();
            $table->integer('mealPanDuration_id');
            $table->integer('mealPlan_id');
            $table->integer('user_id');
            $table->string('title');
            $table->string('featuredImage');
            $table->longText('recipe');
            $table->integer('price');
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
        Schema::dropIfExists('mealplanpackages');
    }
}
