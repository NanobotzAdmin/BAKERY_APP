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
        Schema::create('vm_vendor', function (Blueprint $table) {
            $table->id();
            $table->string('vendor_name');
            $table->string('vendor_email');
            $table->string('vendor_phone');
            $table->string('vendor_address_line_1');
            $table->string('vendor_address_line_2');
            $table->string('vendor_city');
            $table->integer('created_by');
            $table->integer('updated_by');
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
        Schema::dropIfExists('vm_vendor');
    }
};
