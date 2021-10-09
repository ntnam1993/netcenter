<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFairFlowShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->string('fair_list_flow_last_commented_date', 6)->nullable();
            $table->smallInteger('a1_threshold')->default(3);
            $table->smallInteger('a5_threshold')->default(3);
            $table->smallInteger('a7_threshold')->default(40);
            $table->boolean('is_a1_not_needed')->default(false);
            $table->boolean('is_a2_not_needed')->default(false);
            $table->boolean('is_a3_not_needed')->default(false);
            $table->boolean('is_a4_not_needed')->default(false);
            $table->boolean('is_a5_not_needed')->default(false);
            $table->boolean('is_a6_not_needed')->default(false);
            $table->boolean('is_a7_not_needed')->default(false);
            $table->boolean('is_a8_not_needed')->default(false);
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
                'fair_list_flow_last_commented_date',
                'a1_threshold',
                'a5_threshold',
                'a7_threshold',
                'is_a1_not_needed',
                'is_a2_not_needed',
                'is_a3_not_needed',
                'is_a4_not_needed',
                'is_a5_not_needed',
                'is_a6_not_needed',
                'is_a7_not_needed',
                'is_a8_not_needed'
            ]);
        });
    }
}
