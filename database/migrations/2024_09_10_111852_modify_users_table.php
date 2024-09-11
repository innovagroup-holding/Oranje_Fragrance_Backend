<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            Schema::table('users', function (Blueprint $table) {

                $table->dropColumn('name');


                $table->string('first_name')->after('id');
                $table->string('last_name')->after('first_name')->nullable();
                $table->string('number')->after('email')->nullable();

                $table->string('country')->after('number')->nullable();
                $table->string('location')->after('country')->nullable();
                $table->string('street')->after('location')->nullable();
                $table->string('house_number')->after('street')->nullable();
                $table->string('post_code')->after('house_number')->nullable();

                $table->boolean('agreed_privacy_policy')->after('email_verified_at')->default(false);
                $table->boolean('agreed_terms_of_use')->after('email_verified_at')->default(false);
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
