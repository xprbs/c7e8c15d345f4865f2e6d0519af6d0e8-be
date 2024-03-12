<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterEntityUidToAuditee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audit_checklist_auditee', function (Blueprint $table) {
            $table->string('entity_uid')->nullable();
        });

        Schema::table('audit_checklist_auditor', function (Blueprint $table) {
            $table->string('entity_uid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
