<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterQuestionModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_question', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->softDeletes();
            $table->string('dataAreaId')->nullable();
            $table->uuid('question_uid')->nullable(); // Question ID
            $table->string('question_number')->nullable(); // Question number / kode
            $table->string('question_name')->nullable(); // Question name
            $table->text('question_description')->nullable(); // Description
            $table->string('question_type')->nullable(); // ISO / SHE
            $table->string('question_ref')->nullable();  // 14001, 45001, Proper, SMK5
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_question');
    }
}
