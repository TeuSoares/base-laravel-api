<?php

namespace App\Console\Commands;

use App\Console\Commands\Traits\CanMakeModuleFiles;
use Illuminate\Console\Command;

class MakeController extends Command
{
    use CanMakeModuleFiles;

    protected $signature = 'make:new-controller {name} {--module=}';
    protected $description = 'Create a new controller in the specified module';

    public function handle()
    {
        $name = $this->argument('name');
        $module = $this->option('module');

        $this->createModuleFile($name, $module, 'Controllers');
    }

    protected function getStub(string $name, string $namespace): string
    {
        return <<<PHP
        <?php

        namespace {$namespace};

        use App\Core\Http\Controllers\Controller;
        use Illuminate\Http\JsonResponse;
        use Illuminate\Http\Request;

        class {$name} extends Controller
        {
            public function index(): JsonResponse
            {
                return \$this->response()->data([]);
            }

            public function show(int \$id): JsonResponse
            {
                return \$this->response()->data([]);
            }

            public function store(Request \$request): JsonResponse
            {
                return \$this->response()->message('');
            }

            public function update(Request \$request): JsonResponse
            {
                return \$this->response()->message('');
            }

            public function destroy(int \$id): JsonResponse
            {
                return \$this->response()->message('');
            }
        }

        PHP;
    }
}
