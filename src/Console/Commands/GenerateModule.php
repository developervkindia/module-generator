namespace Developervkindia\ModuleGenerator\Console\Commands;

use Illuminate\Console\Command;

class GenerateModule extends Command
{
    protected $signature = 'generate:module {name}';
    protected $description = 'Generate a new module with boilerplate code';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $name = $this->argument('name');
        $stubPath = __DIR__ . '/../../stubs/module.stub';
        $targetPath = base_path("modules/{$name}/{$name}.php");

        $content = str_replace('{{moduleName}}', $name, file_get_contents($stubPath));
        file_put_contents($targetPath, $content);

        $this->info("Module {$name} generated successfully.");
    }
}
