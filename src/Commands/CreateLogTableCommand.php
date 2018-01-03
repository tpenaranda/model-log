<?php

namespace TPenaranda\ModelLog\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use TPenaranda\ModelLog\ModelLog;
use Error, ReflectionClass;

class CreateLogTableCommand extends Command
{
    protected function configure()
    {
        $this->setName('model-log:create-log-table')
        ->setDescription('Create a DB log table for the model that we want to track attributes changes.')
        ->setAliases(['create-log-table']);
    }

    public function handle()
    {
        $answer = $this->anticipate("Please specify the model name (including namespace) that you want to track changes, example: 'App\User'", ['App\\']);
        $model_name_with_namespace = trim(trim($answer, "'"), '\\');
        $log_table_name = ModelLog::getLogTableName($model = new $model_name_with_namespace());
        $model_table_name = $model->getTable();

        if (Schema::hasTable($log_table_name)) {
            $this->error("Error: '{$log_table_name}' DB table already exists, rebuild will destroy current data!");
            $log_table_name .= '_' . strtolower(str_random(5));
        } else {
            $this->warn("'{$log_table_name}' DB table is going to be created.");
        }

        if (!$this->confirm("Do you wish to continue?")) {
            return;
        }

        $model_name = (new ReflectionClass($model))->getShortName();
        $namespace = trim(str_before($model_name_with_namespace, $model_name), '\\');

        $stub_file = file_get_contents(__DIR__ . '/../Stubs/LogTableMigration.php.stub');
        $migration_name = "create_{$log_table_name}_table";

        $replacements = [
            'namespace' => $namespace,
            'model_name' => $model_name,
            'migration_class_name' => ucfirst(camel_case($migration_name)),
        ];

        $map_function = function ($item) { return "/\{\{ $item \}\}/"; };

        $migration_file_data = preg_replace(array_map($map_function, array_keys($replacements)), array_values($replacements), $stub_file);

        $file_name = date('Y_m_d_His', time()) . "_{$migration_name}.php";

        file_put_contents(database_path('migrations/'. $file_name), $migration_file_data);


        $this->info("Migration file {$file_name} created.");

        if ($this->confirm("Run migrations now?")) {
            $this->call('migrate');
        } else {
            $this->warn('** Remember to run migrations before using the logger trait in your model. **');
        }

        $this->info("\nDone!.\n");
    }
}
