<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterFilenameToSurveillances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('surveillance_detail', function (Blueprint $table) {
            $table->string('filename')->nullable();
            $table->string('file_type', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('surveillance_detail', function (Blueprint $table) {
            $table->string('filename')->nullable();
            $table->string('file_type', 50)->nullable();
        });
    }
}
