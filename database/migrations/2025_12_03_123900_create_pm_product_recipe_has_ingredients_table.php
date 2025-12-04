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
        Schema::create('pm_product_recipe_has_ingredients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pm_product_recipe_id');
            $table->unsignedBigInteger('pm_product_item_id');
            $table->unsignedBigInteger('metirial_product_id');
            $table->integer('pm_variation_value_type_id');
            $table->integer('quantity');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();

            $table->foreign('pm_product_recipe_id')->references('id')->on('pm_product_recipe');
            $table->foreign('pm_product_item_id')->references('id')->on('pm_product_item');
            $table->foreign('metirial_product_id')->references('id')->on('pm_product_item');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pm_product_recipe_has_ingredients');
    }
};
