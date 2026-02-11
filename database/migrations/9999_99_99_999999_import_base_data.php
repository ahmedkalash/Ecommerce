<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Output\ConsoleOutput;

use function Illuminate\Filesystem\join_paths;

/**
 * Import/Seed base Data
 *
 * This migration selectively imports seed data based on the environment.
 *
 * Import Strategy:
 * - Testing: Minimal data for fast test execution
 * - Development/Production: Full data import for complete functionality
 *
 * @see database/base_build_data/README.md for files descriptions
 */
class ImportBaseData extends Migration
{
    /**
     * Run the migrations.
     *
     * Imports base data in the correct order based on the environment.
     */
    public function up(): void
    {
        // Import/seed base data (selective based on environment)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->importBaseData();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     *
     * Truncate all tables except the migrations table.
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
                DB::statement("TRUNCATE TABLE `{$tableName}`");
            }
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Import base/seed data files.
     *
     * Selectively imports data files based on environment:
     * - Testing: Minimal import (~18KB) for fast test execution
     * - Development/Production: Selective full import based on needs
     *
     * @return void
     */
    private function importBaseData()
    {
        $filesToImport = $this->getDataFilesToImport();

        foreach ($filesToImport as $filename) {
            $this->importDataFile($filename);
        }
    }

    /**
     * Determine which data files to import based on the environment.
     *
     * @return array List of filenames to import (in order)
     */
    private function getDataFilesToImport(): array
    {
        // Todo: Determine what are the minimal and basic data files required for testing and development/production environments
        // Check if running in the test environment
        $isTestEnvironment = app()->runningUnitTests();
        if ($isTestEnvironment) {
            // TESTING ENVIRONMENT: Import minimal data only
            return [
                join_paths('for_testing_env', '0_init_db_config.sql'),             // DB initialization (required)
                join_paths('for_testing_env', '2_business_settings_table.sql'),    // Critical app settings (required)
                join_paths('for_testing_env', '6.multiple_tables.sql'),            // UI elements (486KB)
                join_paths('for_testing_env', '10_roles_and_permissions.sql'),     // Roles & Permissions
                join_paths('for_testing_env', '100_final_db_config.sql'),          // Finalization/COMMIT (required)
            ];
        }

        // DEVELOPMENT/PRODUCTION ENVIRONMENT: Import full data
        // Total: ~4-8MB depending on selections
        return [
            '0_init_db_config.sql',             // DB initialization
            '1_app_translations_table.sql',     // UI translations (224KB)
            '2_business_settings_table.sql',    // Critical app settings (REQUIRED)
            '3.multiple_tables.sql',            // Product demo data (19KB)
            '4_cities_table.sql',               // Cities database (4.5MB!)
            '5_states_table.sql',               // States database (334KB)
            '6.multiple_tables.sql',            // UI elements (486KB)
            '7_translations_table.sql',         // Entity translations (3.1MB)
            '8_uploads_table.sql',              // Demo uploads
            '9_users_table.sql',                // Demo users
            '10_roles_and_permissions.sql',     // Roles & Permissions
            '100_final_db_config.sql',          // Finalization/COMMIT
        ];
    }

    /**
     * Import a single data file.
     *
     * @param  string  $filename  Filename (relative to base_build_data directory)
     */
    private function importDataFile(string $filename): void
    {
        $filePath = database_path(join_paths('base_build_data', $filename));

        if (! file_exists($filePath)) {
            if ($filename == '0_init_db_config.sql' || $filename == '100_final_db_config.sql') {
                throw new RuntimeException("Critical: Config files (\"0_init_db_config.sql\" and \"100_final_db_config.sql\") must exist: {$filePath}");
            }
            $output = new ConsoleOutput;
            $output->writeln("<comment>Warning: Data file not found: {$filePath}\n</comment>");

            return;
        }

        $sql = file_get_contents($filePath);

        // Execute the data SQL
        DB::unprepared($sql);
    }
}
