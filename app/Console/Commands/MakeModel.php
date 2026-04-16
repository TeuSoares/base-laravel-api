<?php

namespace App\Console\Commands;

use App\Console\Commands\Traits\CanMakeModuleFiles;
use Illuminate\Console\Command;

class MakeModel extends Command
{
    use CanMakeModuleFiles;

    protected $signature = 'make:new-model {name} {--module=}';
    protected $description = 'Create a new model in the specified module';

    public function handle()
    {
        $name = $this->argument('name');
        $module = $this->option('module');

        $this->createModuleFile($name, $module, 'Models');
    }

    protected function getStub(string $name, string $namespace): string
    {
        return <<<PHP
        <?php

        namespace {$namespace};

        use App\Core\Abstracts\Model;
        use Illuminate\Database\Eloquent\Factories\HasFactory;

        class {$name} extends Model
        {
            protected \$fillable = [
                //
            ];
        }

        PHP;
    }
}
