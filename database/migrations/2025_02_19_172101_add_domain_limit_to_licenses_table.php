<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('licenses', function (Blueprint $table) {
            // domain_limit: 0 means unlimited; any positive integer indicates the number of allowed production domains.
            $table->integer('domain_limit')->default(0)->comment('0 = unlimited')->after('status');
        });
    }

    public function down()
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->dropColumn('domain_limit');
        });
    }
};
