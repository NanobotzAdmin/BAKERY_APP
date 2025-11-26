<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pm_product', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pm_brands_id')->nullable();
            $table->string('product_name', 150);
            $table->string('product_code', 50)->unique();
            $table->text('product_description')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->unsignedBigInteger('pm_product_item_type_id');
            $table->integer('pm_product_main_category_id');
            $table->integer('pm_product_sub_category_id');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();

            if (Schema::hasTable('pm_brands')) {
                $table->foreign('pm_brands_id')->references('id')->on('pm_brands');
            }
            $table->foreign('pm_product_main_category_id')->references('id')->on('pm_product_main_category');
            $table->foreign('pm_product_sub_category_id')->references('id')->on('pm_product_sub_category');
            $table->foreign('created_by')->references('id')->on('um_user');
            $table->foreign('updated_by')->references('id')->on('um_user');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pm_product');
    }
};


