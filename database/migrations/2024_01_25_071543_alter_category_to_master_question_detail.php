<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCategoryToMasterQuestionDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_question_detail', function (Blueprint $table) {
            $table->text('question_category1')->nullable();
            $table->text('question_category2')->nullable();
            $table->text('question_category3')->nullable();
            $table->text('question_category4')->nullable();
            $table->text('question_category5')->nullable();
            $table->text('question_category6')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_question_detail', function (Blueprint $table) {
            //
        });
    }
}
