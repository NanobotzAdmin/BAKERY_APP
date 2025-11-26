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
        Schema::create('pm_product_item', function (Blueprint $table) {
            $table->id();
            $table->string('product_name', 150);
            $table->string('product_description', 255)->nullable();
            $table->string('product_code',50)->unique();
            $table->integer('pm_product_item_type_id');
            $table->integer('pm_product_main_category_id')->nullable();
            $table->integer('pm_product_sub_category_id')->nullable();
            $table->unsignedBigInteger('pm_product_item_variation_id');
            $table->unsignedBigInteger('pm_product_item_variation_value_id');
            $table->double('selling_price',22,2)->nullable();
            $table->double('cost_price',22,2)->nullable();
            $table->integer('status');
            $table->integer('created_by');
            $table->integer('updated_by');

            $table->foreign('pm_product_main_category_id')->references('id')->on('pm_product_main_category');
            $table->foreign('pm_product_sub_category_id')->references('id')->on('pm_product_sub_category');
            $table->foreign('pm_product_item_variation_id')->references('id')->on('pm_variation');
            $table->foreign('pm_product_item_variation_value_id')->references('id')->on('pm_variation_value');
            $table->foreign('created_by')->references('id')->on('um_user');
            $table->foreign('updated_by')->references('id')->on('um_user');
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
        Schema::dropIfExists('pm_product_item');
    }
};
