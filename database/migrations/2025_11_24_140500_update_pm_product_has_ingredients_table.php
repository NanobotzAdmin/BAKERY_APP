<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePmProductHasIngredientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pm_product_has_ingredients', function (Blueprint $table) {
            if (Schema::hasColumn('pm_product_has_ingredients', 'pm_variation_id')) {
                $table->dropColumn('pm_variation_id');
            }
            if (Schema::hasColumn('pm_product_has_ingredients', 'pm_variation_value_id')) {
                $table->dropColumn('pm_variation_value_id');
            }
            if (Schema::hasColumn('pm_product_has_ingredients', 'quantity')) {
                $table->dropColumn('quantity');
            }

            $table->unsignedBigInteger('pm_variation_value_type_id')->after('pm_raw_material_id');
            $table->double('pm_variation_value', 22, 2)->after('pm_variation_value_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pm_product_has_ingredients', function (Blueprint $table) {
            if (Schema::hasColumn('pm_product_has_ingredients', 'pm_variation_value_type_id')) {
                $table->dropColumn('pm_variation_value_type_id');
            }
            if (Schema::hasColumn('pm_product_has_ingredients', 'pm_variation_value')) {
                $table->dropColumn('pm_variation_value');
            }

            $table->unsignedBigInteger('pm_variation_id')->after('pm_raw_material_id');
            $table->unsignedBigInteger('pm_variation_value_id')->after('pm_variation_id');
            $table->double('quantity', 22, 2)->after('pm_variation_value_id');
        });
    }
}

