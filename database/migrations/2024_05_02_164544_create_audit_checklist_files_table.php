<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditChecklistFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_checklist_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->softDeletes();
            $table->string('dataAreaId')->nullable();
            $table->uuid('audit_uid')->nullable(); // Audit uid
            $table->uuid('question_uid')->nullable(); // Question uid
            $table->uuid('question_detail_uid')->nullable(); // Question detail id
            $table->string('filename')->nullable();
            $table->string('filepath')->nullable();
            $table->string('filetype')->nullable();
            $table->string('filesize')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_checklist_files');
    }
}
