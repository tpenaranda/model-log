<?php

namespace TPenaranda\ModelLog\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use TPenaranda\ModelLog\ModelLogEntry;
use File;

class CreateLogTableCommand extends Command
{
    const VALID_OPERATIONS = ['create', 'drop'];

    protected $create_migration_count;
    protected $drop_migration_count;
    protected $table_name;

    protected function configure()
    {
        $this->setName('model-log:create-log-table')->setDescription('Generate two migrations to drop and create again the DB log table.')->setAliases(['create-log-table']);
    }

    protected function calculateMigrationNumbers()
    {
        foreach (File::files(database_path('migrations/')) as $file) {
            if (preg_match('/('. implode('|', self::VALID_OPERATIONS) . ")_{$this->table_name}_table_?(.*).php/", $file, $matches)) {
                $second_match = (int) $matches[2];
                switch ($matches[1]) {
                    case 'create':
                        if ($this->create_migration_count <= $second_match) {
                            $this->create_migration_count = $second_match + 1;
                        }
                        break;

                    case 'drop':
                        if ($this->drop_migration_count <= $second_match) {
                            $this->drop_migration_count = $second_match + 1;
                        }
                        break;
                }
            }
        }
    }

    protected function buildMigrationFor($operation = '')
    {
        if (!in_array($operation, self::VALID_OPERATIONS)) {
            return false;
        }

        $stub_data = file_get_contents(__DIR__ . "/../Stubs/{$operation}_" . $this->table_name . '_table' . '.php.stub');
        $migration_count_variable = "{$operation}_migration_count";
        $migration_count = $this->$migration_count_variable;

        $replacements = [
            'log_table_name' => $this->table_name,
            'migration_count' => $migration_count,
        ];

        $map_function = function ($item) {
            return "/\{\{ $item \}\}/";
        };
        $output_data = preg_replace(array_map($map_function, array_keys($replacements)), array_values($replacements), $stub_data);

        $migration_count_with_underscore = empty($migration_count) ? false : "_{$migration_count}";

        $now = Carbon::now('UTC')->addSeconds('create' == $operation ? 1 : 0)->format('Y_m_d_His');

        $output_name =  "{$now}_{$operation}_{$this->table_name}_table{$migration_count_with_underscore}.php";

        file_put_contents(database_path('migrations/'. $output_name), $output_data);
    }

    public function handle()
    {
        $this->table_name = ModelLogEntry::getModel()->getTable();

        $this->calculateMigrationNumbers();

        if (Schema::hasTable($this->table_name)) {
            $this->info("ModelLog DB table ({$this->table_name}) already exists in your database.");

            if (!$this->confirm("Do you want to create a migration to drop current table and a new one to re-build the table?")) {
                return;
            }

            $this->error('All ModelLog data will be lost!.');
            $this->info('Note: If your intention is only drop all log data, it can be done by running \ModelLogEntry::flushAll()');

            if (!$this->confirm("Continue?")) {
                return;
            }

            $this->buildMigrationFor('drop');
        }

        $this->info("Building migration to create ModelLog DB table ({$this->table_name})...\n");

        $this->buildMigrationFor('create');

        $this->call('migrate');

        $this->info("\nDone!.");
    }
}
