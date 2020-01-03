<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSafetyUserFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('safety_person')->nullable();
            $table->string('safety_phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // need to separate dropColumn calls into multiple table modifications
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('safety_person');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('safety_phone');
        });
    }
}
