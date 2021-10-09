<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFutherShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->smallInteger('d1_threshold')->default(3);
            $table->boolean('is_d1_not_needed')->default(false);
            $table->boolean('is_d2_not_needed')->default(false);
            $table->boolean('is_d3_not_needed')->default(false);
            $table->boolean('is_d4_not_needed')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn([
                'd1_threshold',
                'is_d1_not_needed',
                'is_d2_not_needed',
                'is_d3_not_needed',
                'is_d4_not_needed',
            ]);
        });
    }
}
