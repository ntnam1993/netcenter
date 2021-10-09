<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFairDetailShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->string('fair_detail_flow_last_commented_date', 6)->nullable();
            $table->boolean('is_b1_not_needed')->default(false);
            $table->boolean('is_b2_not_needed')->default(false);
            $table->boolean('is_b3_not_needed')->default(false);
            $table->boolean('is_b4_not_needed')->default(false);
            $table->boolean('is_b5_not_needed')->default(false);
            $table->boolean('is_b6_not_needed')->default(false);
            $table->boolean('is_b7_not_needed')->default(false);
            $table->boolean('is_b8_not_needed')->default(false);
            $table->boolean('is_b9_not_needed')->default(false);
            $table->boolean('is_b10_not_needed')->default(false);
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
                'fair_detail_flow_last_commented_date',
                'is_b1_not_needed',
                'is_b2_not_needed',
                'is_b3_not_needed',
                'is_b4_not_needed',
                'is_b5_not_needed',
                'is_b6_not_needed',
                'is_b7_not_needed',
                'is_b8_not_needed',
                'is_b9_not_needed',
                'is_b10_not_needed'
            ]);
        });
    }
}
