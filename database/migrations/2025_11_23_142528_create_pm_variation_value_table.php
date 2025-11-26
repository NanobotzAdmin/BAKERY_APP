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
        Schema::create('pm_variation_value', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pm_variation_id');
            $table->bigInteger('pm_variation_value_type_id');
            $table->string('variation_value_name', 45)->nullable();
            $table->string('variation_value', 45);
            $table->tinyInteger('is_active');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();

            $table->foreign('pm_variation_id')->references('id')->on('pm_variation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pm_variation_value');
    }
};
