<?php
namespace Developervkindia\ModuleGenerator\Controllers;

use Illuminate\Http\Request;
use Developervkindia\ModuleGenerator\Models\ModuleGenerator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
class ModuleGeneratorController
{
    public function __invoke(ModuleGenerator $moduleGenerator) {
        $test = $moduleGenerator->test();

        return view('module-generator::index', compact('test'));
    }
    public function generate(Request $request)
    {
        $moduleName = Str::studly($request->input('module_name')); // Converts to 'Project'
        $tableName = Str::plural(Str::snake($moduleName)); // Converts to 'projects'
        $fields = $request->input('field_name');
        $types = $request->input('field_type');
        $nullables = $request->input('nullable');

        // Generate all parts
        $this->createMigration($tableName, $fields, $types, $nullables);
        $this->createModel($moduleName, $fields);
        $this->createController($moduleName, $fields);
        $this->createRoutes($moduleName);
        $this->createViews($moduleName, $fields);

        return response()->json(['message' => 'Module generated successfully!']);
    }

    protected function createMigration($tableName, $fields, $types, $nullables)
{
    $timestamp = date('Y_m_d_His');
    $fileName = "{$timestamp}_create_{$tableName}_table.php";
    $filePath = database_path("migrations/{$fileName}");

    $migrationContent = $this->generateMigrationContent($tableName, $fields, $types, $nullables);

    // Debug the generated content before writing it to a file
    // dd($migrationContent);

    // Write the migration file
    File::put($filePath, $migrationContent);
}
protected function generateMigrationContent($tableName, $fields, $types, $nullables)
    {
        $fieldLines = '';

        foreach ($fields as $index => $field) {
            $type = $types[$index];
            $nullable = $nullables[$index] == '1' ? '->nullable()' : '';
            $fieldLines .= "\$table->{$type}('{$field}'){$nullable};\n            ";
        }

        // Properly format and return the migration file content
        return <<<PHP
                    <?php

                    use Illuminate\Database\Migrations\Migration;
                    use Illuminate\Database\Schema\Blueprint;
                    use Illuminate\Support\Facades\Schema;

                    return new class extends Migration
                    {
                        /**
                         * Run the migrations.
                         *
                         * @return void
                         */
                        public function up()
                        {
                            Schema::create('$tableName', function (Blueprint \$table) {
                                \$table->id();
                                $fieldLines
                                \$table->timestamps();
                            });
                        }

                        /**
                         * Reverse the migrations.
                         *
                         * @return void
                         */
                        public function down()
                        {
                            Schema::dropIfExists('$tableName');
                        }
                    };
                    PHP;
    }
    protected function createModel($moduleName, $fields)
{
    $modelPath = app_path("Models/{$moduleName}.php");

    $fillableFields = implode("', '", $fields);

    $modelContent = <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class $moduleName extends Model
{
    use HasFactory;

    protected \$fillable = ['$fillableFields'];
}
PHP;

    // Write the model to app/Models
    File::put($modelPath, $modelContent);
}
protected function createController($moduleName, $fields)
{
    $controllerPath = app_path("Http/Controllers/{$moduleName}Controller.php");

    $controllerContent = <<<PHP
<?php

namespace App\Http\Controllers;

use App\Models\\$moduleName;
use Illuminate\Http\Request;

class {$moduleName}Controller extends Controller
{
    // Show list of records
    public function index()
    {
        \$records = $moduleName::all();
        return view('{$moduleName}s.index', compact('records'));
    }

    // Show create form
    public function create()
    {
        return view('{$moduleName}s.create');
    }

    // Store a new record
    public function store(Request \$request)
    {
        \$request->validate([
            'name' => 'required',
            'description' => 'nullable',
        ]);

        $moduleName::create(\$request->all());
        return redirect()->route('{$moduleName}s.index');
    }

    // Show edit form
    public function edit(\$id)
    {
        \$record = $moduleName::find(\$id);
        return view('{$moduleName}s.edit', compact('record'));
    }

    // Update a record
    public function update(Request \$request, \$id)
    {
        \$request->validate([
            'name' => 'required',
            'description' => 'nullable',
        ]);

        \$record = $moduleName::find(\$id);
        \$record->update(\$request->all());

        return redirect()->route('{$moduleName}s.index');
    }

    // Delete a record
    public function destroy(\$id)
    {
        \$record = $moduleName::find(\$id);
        \$record->delete();

        return redirect()->route('{$moduleName}s.index');
    }
}
PHP;

    // Write the controller to app/Http/Controllers
    File::put($controllerPath, $controllerContent);
}
protected function createRoutes($moduleName)
{
    $routeFile = base_path('routes/web.php');
    $routeNamespace = 'App\Http\Controllers\\' . $moduleName . 'Controller';

    // Read the existing content of the routes file
    $routeContent = file_get_contents($routeFile);

    // Check if the 'use' statement for the controller already exists
    if (!str_contains($routeContent, "use $routeNamespace;")) {
        // If not, add it at the top of the file
        $useStatement = "use $routeNamespace;\n";
        $routeContent = preg_replace('/<\?php/', "<?php\n$useStatement", $routeContent);
    }

    // Add the route for the resource controller
    $routeContent .= "\nRoute::resource('" . strtolower($moduleName) . "s', " . $moduleName . "Controller::class);";

    // Write the updated content back to the routes file
    file_put_contents($routeFile, $routeContent);
}

protected function createViews($moduleName, $fields)
{
    $viewFolder = resource_path('views/' . strtolower($moduleName) . 's');

    // Create the views folder if it doesn't exist
    if (!File::exists($viewFolder)) {
        File::makeDirectory($viewFolder, 0777, true);
    }

    // Create the Index View
    $indexViewContent = <<<HTML
@extends('layouts.app')

@section('content')
    <h1>All {$moduleName}s</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach (\$records as \$record)
                <tr>
                    <td>{{ \$record->name }}</td>
                    <td>{{ \$record->description }}</td>
                    <td>
                        <a href="{{ route(' . strtolower($moduleName) . 's.edit', \$record->id) }}" class="btn btn-primary btn-sm">Edit</a>
                        <form action="{{ route(' . strtolower($moduleName) . 's.destroy', \$record->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
HTML;

    File::put($viewFolder . '/index.blade.php', $indexViewContent);

    // Add similar content for create.blade.php, edit.blade.php, etc.
}

}
