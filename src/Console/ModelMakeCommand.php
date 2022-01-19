<?php

namespace YaangVu\LumenGenerator\Console;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;
use YaangVu\LumenGenerator\NamespaceGenerator;

class ModelMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Eloquent model class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';

    /**
     * Execute the console command.
     *
     * @return bool|null
     * @throws FileNotFoundException
     */
    public function handle(): ?bool
    {
        if (parent::handle() === false && !$this->option('force')) {
            return false;
        }

        if ($this->option('all') || $this->option('base')) {
            $this->input->setOption('factory', true);
            $this->input->setOption('seed', true);
            $this->input->setOption('migration', true);
            $this->input->setOption('controller', true);
            $this->input->setOption('resource', true);
            $this->input->setOption('service', true);
        }

        if ($this->option('factory')) {
            $this->createFactory();
        }

        if ($this->option('migration')) {
            $this->createMigration();
        }

        if ($this->option('seed')) {
            $this->createSeeder();
        }

        if ($this->option('controller') || $this->option('resource') || $this->option('api')) {
            $this->createController();
        }

        if ($this->option('service')) {
            $this->createService();
        }

        return true;
    }

    /**
     * Create a model factory for the model.
     *
     * @return void
     */
    protected function createFactory()
    {
        $name       = Str::studly($this->argument('name'));
        $arrName    = NamespaceGenerator::parseNameInput($name);
        $factory    = $arrName['hasSub'] ? ($arrName['first'] . '/' . $arrName['last']) : $arrName['first'];
        $modelClass = NamespaceGenerator::generateFullNamespace($name, 'Model');

        $this->call('make:factory', [
            'name'    => "{$factory}Factory",
            '--model' => $modelClass,
        ]);
    }

    /**
     * Create a migration file for the model.
     *
     * @return void
     */
    protected function createMigration()
    {
        $table = Str::snake(Str::pluralStudly(class_basename($this->argument('name'))));

        if ($this->option('pivot')) {
            $table = Str::singular($table);
        }

        $this->call('make:migration', [
            'name'     => "create_{$table}_table",
            '--create' => $table,
        ]);
    }

    /**
     * Create a seeder file for the model.
     *
     * @return void
     */
    protected function createSeeder()
    {
        $seeder = Str::studly(class_basename($this->argument('name')));

        $this->call('make:seed', [
            'name' => "{$seeder}Seeder",
        ]);
    }

    /**
     * Create a controller for the model.
     *
     * @return void
     */
    protected function createController()
    {
        $name       = Str::studly($this->argument('name'));
        $arrName    = NamespaceGenerator::parseNameInput($name);
        $controller = $arrName['hasSub'] ? ($arrName['first'] . '/' . $arrName['last']) : $arrName['first'];
        $modelClass = NamespaceGenerator::generateFullNamespace($name, 'Model');

        $this->call('make:controller', array_filter([
                                                        'name'    => "{$controller}Controller",
                                                        '--model' => $this->option('resource') || $this->option('api') ? $modelClass : null,
                                                        '--api'   => $this->option('api'),
                                                        '--base'  => $this->option('base'),
                                                    ]));
    }

    /**
     * Create a controller for the model.
     *
     * @return void
     */
    protected function createService()
    {
        $name       = Str::studly($this->argument('name'));
        $arrName    = NamespaceGenerator::parseNameInput($name);
        $service    = $arrName['hasSub'] ? ($arrName['first'] . '/' . $arrName['last']) : $arrName['first'];
        $modelClass = NamespaceGenerator::generateFullNamespace($name, 'Model');

        $this->call('make:service', array_filter([
                                                     'name'    => "{$service}Service",
                                                     '--model' => $modelClass,
                                                 ]));
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        $stub = $this->option('pivot')
            ? '/stubs/model.pivot.stub'
            : '/stubs/model.stub';

        return __DIR__ . $stub;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['all', 'a', InputOption::VALUE_NONE, 'Generate a migration, seeder, factory, and resource controller for the model'],
            ['controller', 'c', InputOption::VALUE_NONE, 'Create a new controller for the model'],
            ['factory', 'f', InputOption::VALUE_NONE, 'Create a new factory for the model'],
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the model already exists'],
            ['migration', 'm', InputOption::VALUE_NONE, 'Create a new migration file for the model'],
            ['seed', 's', InputOption::VALUE_NONE, 'Create a new seeder file for the model'],
            ['service', 'sv', InputOption::VALUE_OPTIONAL, 'Create a new service business file for the model'],
            ['pivot', 'p', InputOption::VALUE_NONE, 'Indicates if the generated model should be a custom intermediate table model'],
            ['resource', 'r', InputOption::VALUE_NONE, 'Indicates if the generated controller should be a resource controller'],
            ['api', null, InputOption::VALUE_NONE, 'Indicates if the generated controller should be an API controller'],
            ['base', 'b', InputOption::VALUE_NONE, 'Indicates if the generated controller should be an Base controller'],
        ];
    }
}
