<?php

namespace App\Console\Commands\Traits;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Trait to provide file creation logic for modular architecture commands.
 */
trait CanMakeModuleFiles
{
    /**
     * Create the directory structure and the file within a specific module.
     */
    public function createModuleFile(string $name, string $module, string $type): void
    {
        // Validation: Ensure the module name is provided
        if (!$module) {
            $this->error('The --module option is required.');
            return;
        }

        // Normalize the module name to StudlyCase (e.g., user_auth -> UserAuth)
        $module = Str::studly($module);

        // Standardize path separators for the current OS
        // Example: "Auth/User/Create" -> "Auth\User\Create" (on Windows)
        $name = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $name);

        // Break the string into an array of parts to separate sub-folders from the class name
        // Example: ["Auth", "User", "Create"]
        $nameParts = explode(DIRECTORY_SEPARATOR, $name);

        // Extract the last element as the Class Name
        // Example: $className = "Create", $nameParts = ["Auth", "User"]
        $className = array_pop($nameParts);

        // If there are parts left, join them back to create the sub-directory path
        // Example: $subPath = "Auth\User\"
        $subPath = count($nameParts) > 0
            ? implode(DIRECTORY_SEPARATOR, $nameParts) . DIRECTORY_SEPARATOR
            : '';

        // Define absolute paths for the module and the specific file type (e.g., Actions, Controllers)
        $modulePath = base_path("app/Modules/{$module}");
        $directoryPath = "{$modulePath}/{$type}/{$subPath}";
        $filePath = "{$directoryPath}/{$className}.php";

        // Recursively create directories if they do not exist
        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true);
        }

        // Prevent overwriting existing files to avoid data loss
        if (File::exists($filePath)) {
            $this->error("{$type} {$className} already exists in {$module} module.");
            return;
        }

        // Generate the dynamic PSR-4 namespace based on the module and sub-path
        // Example: "App\Modules\User\Actions\Auth\User"
        $relativeNamespace = count($nameParts) > 0
            ? '\\' . str_replace(DIRECTORY_SEPARATOR, '\\', implode(DIRECTORY_SEPARATOR, $nameParts))
            : '';

        $namespace = "App\\Modules\\{$module}\\{$type}{$relativeNamespace}";

        // Create the file using the stub template provided by the implementing command
        File::put($filePath, $this->getStub($className, $namespace));

        $this->info("{$type} created successfully: {$filePath}");
    }

    /**
     * Define the stub template for the file.
     * This must be implemented by the command using this trait.
     */
    abstract function getStub(string $name, string $namespace): string;
}
