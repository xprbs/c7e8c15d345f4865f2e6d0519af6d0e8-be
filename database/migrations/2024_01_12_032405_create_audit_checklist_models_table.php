<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditChecklistModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_checklist', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('status',2)->nullable();
            $table->softDeletes();
            $table->string('dataAreaId')->nullable();
            $table->uuid('audit_uid')->nullable(); // Project Audit ID
            $table->string('audit_number')->nullable(); // Project Number / Kode
            $table->string('audit_name')->nullable(); // Project Name
            $table->string('audit_category')->nullable(); // Project Type (ISO / SHE)
            $table->string('audit_ref')->nullable(); // Project Type (14001, 45001, Proper, SMK3)
            $table->timestamp('audit_date')->nullable(); 
            $table->string('audit_location')->nullable(); // Department
            $table->uuid('question_uid')->nullable(); // Pertanyaan
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_checklist');
    }
}
