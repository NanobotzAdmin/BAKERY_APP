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
        Schema::create('pm_product_recipe', function (Blueprint $table) {
            $table->id();
            $table->string('recipe_name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('pm_product_item_id')->nullable();
            $table->integer('yield')->nullable();
            $table->integer('pm_variation_value_type_id')->nullable();
            $table->integer('status')->default(0);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('pm_product_recipe');
    }
};
