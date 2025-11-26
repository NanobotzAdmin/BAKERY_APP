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
        Schema::create('pm_product_has_ingredients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pm_product_item_id');
            $table->unsignedBigInteger('pm_raw_material_id');
            $table->unsignedBigInteger('pm_variation_value_type_id');
            $table->double('pm_variation_value', 22, 2);
            $table->integer('status');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();

            $table->foreign('pm_product_item_id')->references('id')->on('pm_product_item');
            $table->foreign('pm_raw_material_id')->references('id')->on('pm_product');
            $table->foreign('created_by')->references('id')->on('um_user');
            $table->foreign('updated_by')->references('id')->on('um_user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pm_product_has_ingredients');
    }
};
