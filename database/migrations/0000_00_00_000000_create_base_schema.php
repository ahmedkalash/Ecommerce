<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Create Base Database Schema
 *
 * This migration imports the complete database schema from base_schema.sql
 *
 * @see database/base_build_data/README.md for more descriptions
 */
class createBaseSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * Imports database schema.
     *
     * @return void
     */
    public function up()
    {
        // Import complete database schema (110 tables)
        $this->importSchema();
    }

    /**
     * Reverse the migrations.
     *
     * Drops all tables except the migrations table.
     */
    public function down(): void
    {
        // Disable foreign key checks to avoid constraint errors during drop
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Get all tables in the database
        $tables = DB::select('SHOW TABLES');

        // Drop each table except migrations
        foreach ($tables as $table) {
            $tableName = array_values((array) $table)[0];

            // Preserve migrations table to track migration state
            if ($tableName !== 'migrations') {
                DB::statement("DROP TABLE IF EXISTS `{$tableName}`");
            }
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Import the complete database schema.
     *
     * Loads and executes the base_schema.sql file which contains
     * CREATE TABLE statements for all current 110 tables.
     */
    private function importSchema(): void
    {
        $schemaPath = database_path('schema/base_schema.sql');

        if (! file_exists($schemaPath)) {
            throw new \RuntimeException(
                "Schema file not found: {$schemaPath}\n".
                'Expected location: database/schema/base_schema.sql'
            );
        }

        $schema = file_get_contents($schemaPath);

        // Execute the schema SQL (creates all tables)
        DB::unprepared($schema);
    }
}
