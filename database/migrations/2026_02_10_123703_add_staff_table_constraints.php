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
        Schema::table('staff', function (Blueprint $table) {
            // Fix column types to match parent tables
            // users.id is int(9) unsigned
            $table->integer('user_id')->unsigned()->change();

            $table->dropColumn('role_id');

            // Add a foreign key to the users table
            // When a user is deleted, cascade deletes their staff record
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // Add unique constraint to enforce 1:1 relationship
            // One user can only have one staff record
            $table->unique('user_id', 'staff_user_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['user_id']);

            // Drop unique constraint
            $table->dropUnique('staff_user_id_unique');
        });
    }
};
