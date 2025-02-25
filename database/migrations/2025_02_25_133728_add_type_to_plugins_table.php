<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToPluginsTable extends Migration
{
    public function up()
    {
        Schema::table('plugins', function (Blueprint $table) {
            $table->string('type')->default('plugin')->after('description');
        });
    }

    public function down()
    {
        Schema::table('plugins', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
