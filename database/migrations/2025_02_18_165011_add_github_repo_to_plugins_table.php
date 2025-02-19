<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGithubRepoToPluginsTable extends Migration
{
    public function up()
    {
        Schema::table('plugins', function (Blueprint $table) {
            $table->string('github_repo')->after('slug');
        });
    }

    public function down()
    {
        Schema::table('plugins', function (Blueprint $table) {
            $table->dropColumn('github_repo');
        });
    }
}
