<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add user_id to pickup_points
        Schema::table('pickup_points', function (Blueprint $table) {
            if (!Schema::hasColumn('pickup_points', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            }
        });

        // Migrate data from staff table to pickup_points.user_id
        if (Schema::hasTable('staff')) {
            DB::statement('UPDATE pickup_points
                           JOIN staff ON pickup_points.staff_id = staff.id
                           SET pickup_points.user_id = staff.user_id');
        }

        // Remove staff_id from pickup_points
        if (Schema::hasColumn('pickup_points', 'staff_id')) {
            Schema::table('pickup_points', function (Blueprint $table) {
                $table->dropColumn('staff_id');
            });
        }

        // Finally drop the redundant staff table
        Schema::dropIfExists('staff');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Create staff table back
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->timestamps();
        });

        // Add staff_id back to pickup_points
        Schema::table('pickup_points', function (Blueprint $table) {
            $table->integer('staff_id')->after('id')->nullable();
        });

        // Populate staff table and restore pickup_points.staff_id
        $users = DB::table('users')->where('user_type', 'staff')->get();
        foreach ($users as $user) {
            $staffId = DB::table('staff')->insertGetId([
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('pickup_points')->where('user_id', $user->id)->update([
                'staff_id' => $staffId,
            ]);
        }

        // Drop user_id from pickup_points
        Schema::table('pickup_points', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};
