<?php

namespace App\Console\Commands;

use App\Console\Commands\Traits\CanMakeModuleFiles;
use Illuminate\Console\Command;

class MakeAction extends Command
{
    use CanMakeModuleFiles;

    protected $signature = 'make:module-action {name} {--module=}';
    protected $description = 'Create a new Action (Use Case) in the specified module';

    public function handle()
    {
        $name = $this->argument('name');
        $module = $this->option('module');

        $this->createModuleFile($name, $module, 'Actions');
    }

    protected function getStub(string $name, string $namespace): string
    {
        return <<<PHP
        <?php

        namespace {$namespace};

        use App\Core\Abstracts\Action;

        class {$name} extends Action
        {
            public function execute(array \$data): void
            {
                //
            }
        }

        PHP;
    }
}
