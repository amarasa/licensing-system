<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNoteToLicensesTable extends Migration
{
    public function up()
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->text('note')->nullable()->after('domain_limit');
        });
    }

    public function down()
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->dropColumn('note');
        });
    }
}
