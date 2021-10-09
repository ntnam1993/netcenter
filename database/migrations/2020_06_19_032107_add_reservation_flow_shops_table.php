<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReservationFlowShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->string('reservation_flow_last_commented_date', 6)->nullable();
            $table->smallInteger('c14_threshold')->default(60);
            $table->boolean('is_c1_not_needed')->default(false);
            $table->boolean('is_c2_not_needed')->default(false);
            $table->boolean('is_c3_not_needed')->default(false);
            $table->boolean('is_c4_not_needed')->default(false);
            $table->boolean('is_c5_not_needed')->default(false);
            $table->boolean('is_c6_not_needed')->default(false);
            $table->boolean('is_c7_not_needed')->default(false);
            $table->boolean('is_c8_not_needed')->default(false);
            $table->boolean('is_c9_not_needed')->default(false);
            $table->boolean('is_c10_not_needed')->default(false);
            $table->boolean('is_c12_not_needed')->default(false);
            $table->boolean('is_c13_not_needed')->default(false);
            $table->boolean('is_c14_not_needed')->default(false);
            $table->boolean('is_c15_not_needed')->default(false);
            $table->boolean('is_c16_not_needed')->default(false);
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
                'reservation_flow_last_commented_date',
                'c14_threshold',
                'is_c1_not_needed',
                'is_c2_not_needed',
                'is_c3_not_needed',
                'is_c4_not_needed',
                'is_c5_not_needed',
                'is_c6_not_needed',
                'is_c7_not_needed',
                'is_c8_not_needed',
                'is_c9_not_needed',
                'is_c10_not_needed',
                'is_c12_not_needed',
                'is_c13_not_needed',
                'is_c14_not_needed',
                'is_c15_not_needed',
                'is_c16_not_needed'
            ]);
        });
    }
}
