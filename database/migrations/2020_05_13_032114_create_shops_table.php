<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->string('c_code', 10)->primary();
            $table->string('email1', 100);
            $table->string('email2', 100)->nullable();
            $table->string('email3', 100)->nullable();
            $table->smallInteger('goal_type');
            $table->smallInteger('summary_type');
            $table->integer('grouping_key')->nullable();
            $table->boolean('is_client_report_needed')->default(true);
            $table->boolean('is_competitor_sheet_needed')->default(false);
            $table->string('competitor_c_code1', 10)->nullable();
            $table->string('competitor_c_code2', 10)->nullable();
            $table->string('competitor_c_code3', 10)->nullable();
            $table->string('competitor_c_code4', 10)->nullable();
            $table->string('competitor_c_code5', 10)->nullable();
            $table->string('competitor_c_code6', 10)->nullable();
            $table->string('competitor_c_code7', 10)->nullable();
            $table->string('competitor_c_code8', 10)->nullable();
            $table->string('competitor_c_code9', 10)->nullable();
            $table->string('competitor_c_code10', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shops');
    }
}
