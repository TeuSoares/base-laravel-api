<?php

namespace App\Console\Commands;

use App\Console\Commands\Traits\CanMakeModuleFiles;
use Illuminate\Console\Command;

class MakeRequest extends Command
{
    use CanMakeModuleFiles;

    protected $signature = 'make:new-request {name} {--module=}';
    protected $description = 'Create a new Form Request in the specified module';

    public function handle()
    {
        $name = $this->argument('name');
        $module = $this->option('module');

        $this->createModuleFile($name, $module, 'Requests');
    }

    protected function getStub(string $name, string $namespace): string
    {
        return <<<PHP
        <?php

        namespace {$namespace};

        use App\Core\Abstracts\Request;

        class {$name} extends Request
        {
            public function rules(): array
            {
                return [
                    //
                ];
            }
        }

        PHP;
    }
}
