<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pm_product_item', function (Blueprint $table) {
            if (!Schema::hasColumn('pm_product_item', 'pm_product_id')) {
                $table->unsignedBigInteger('pm_product_id')->nullable()->after('id');
            }
        });

        if (Schema::hasColumn('pm_product_item', 'product_name') && !Schema::hasColumn('pm_product_item', 'product_item_name')) {
            DB::statement('ALTER TABLE pm_product_item CHANGE product_name product_item_name VARCHAR(150) NOT NULL');
        }

        if (Schema::hasColumn('pm_product_item', 'product_code') && !Schema::hasColumn('pm_product_item', 'bin_code')) {
            DB::statement('ALTER TABLE pm_product_item CHANGE product_code bin_code VARCHAR(50) NOT NULL');
        }

        if (Schema::hasColumn('pm_product_item', 'product_description')) {
            DB::statement('ALTER TABLE pm_product_item DROP COLUMN product_description');
        }

        Schema::table('pm_product_item', function (Blueprint $table) {
            if (Schema::hasColumn('pm_product_item', 'pm_product_id')) {
                $table->foreign('pm_product_id')
                    ->references('id')
                    ->on('pm_product')
                    ->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('pm_product_item', function (Blueprint $table) {
            if (Schema::hasColumn('pm_product_item', 'pm_product_id')) {
                $table->dropForeign(['pm_product_id']);
                $table->dropColumn('pm_product_id');
            }
        });

        if (Schema::hasColumn('pm_product_item', 'product_item_name') && !Schema::hasColumn('pm_product_item', 'product_name')) {
            DB::statement('ALTER TABLE pm_product_item CHANGE product_item_name product_name VARCHAR(150) NOT NULL');
        }

        if (Schema::hasColumn('pm_product_item', 'bin_code') && !Schema::hasColumn('pm_product_item', 'product_code')) {
            DB::statement('ALTER TABLE pm_product_item CHANGE bin_code product_code VARCHAR(50) NOT NULL');
        }

        if (!Schema::hasColumn('pm_product_item', 'product_description')) {
            Schema::table('pm_product_item', function (Blueprint $table) {
                $table->string('product_description', 255)->nullable()->after('product_name');
            });
        }
    }
};


