<?php

namespace YaangVu\LumenGenerator\Console;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Str;
use InvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Console\Input\InputOption;
use YaangVu\LumenGenerator\NamespaceGenerator;

class ControllerMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new controller class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        $stub = null;

        if ($this->option('parent')) {
            $stub = '/stubs/controller.nested.stub';
        } elseif ($this->option('model')) {
            $stub = '/stubs/controller.model.stub';
        } elseif ($this->option('invokable')) {
            $stub = '/stubs/controller.invokable.stub';
        } elseif ($this->option('resource')) {
            $stub = '/stubs/controller.stub';
        }

        if ($this->option('api') && is_null($stub)) {
            $stub = '/stubs/controller.api.stub';
        } elseif ($this->option('api') && !is_null($stub) && !$this->option('invokable')) {
            $stub = str_replace('.stub', '.api.stub', $stub);
        }

        if ($this->option('base')) {
            $stub = '/stubs/controller.base.stub';
        }

        $stub = $stub ?? '/stubs/controller.plain.stub';

        return __DIR__ . $stub;
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in base namespace.
     *
     * @param string $name
     *
     * @return string
     * @throws FileNotFoundException
     */
    protected function buildClass($name): string
    {
        $controllerNamespace = $this->getNamespace($name);

        $replace = [];

        if ($this->option('parent')) {
            $replace = $this->buildParentReplacements();
        }

        if ($this->option('model')) {
            $replace = $this->buildModelReplacements($replace);
        }

        $replace["use $controllerNamespace\Controller;\n"] = '';

        if ($this->option('service')) {
            $replace = $this->buildServiceReplacements();
        }

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    /**
     * Build the replacements for a parent controller.
     *
     * @return array
     */
    #[ArrayShape(['ParentDummyFullModelClass' => "mixed|string", 'ParentDummyModelClass' => "string", 'ParentDummyModelVariable' => "string"])]
    protected function buildParentReplacements(): array
    {
        $parentModelClass = $this->parseModel($this->option('parent'));

        if (!class_exists($parentModelClass)) {
            if ($this->confirm("A $parentModelClass model does not exist. Do you want to generate it?", true)) {
                $this->call('make:model', ['name' => $parentModelClass]);
            }
        }

        return [
            'ParentDummyFullModelClass' => $parentModelClass,
            'ParentDummyModelClass'     => class_basename($parentModelClass),
            'ParentDummyModelVariable'  => lcfirst(class_basename($parentModelClass)),
        ];
    }


    /**
     * Build the service replacement values.
     *
     *
     * @return array
     */
    #[ArrayShape(['DummyFullModelClass' => "mixed|string", 'DummyModelClass' => "string", 'DummyModelVariable' => "string"])]
    protected function buildServiceReplacements(): array
    {
        $name    = $this->getNameInput();
        $service = $this->option('service');
        $service = $service == $name ? $service : "$name/$service";

        $fullServiceClass = NamespaceGenerator::generateFullNamespace("$service", 'Service');
        $serviceClass     = NamespaceGenerator::generateClass($service, 'Service');
        $serviceVariable  = lcfirst(class_basename($serviceClass));

        if (!class_exists($fullServiceClass)) {
            if ($this->confirm("A $fullServiceClass service does not exist. Do you want to generate it?", true)) {
                $this->call('make:service', [
                    'name'    => $service,
                    '--model' => $this->option('model') ? $this->option('model') : null
                ]);
            }
        }

        return [
            'DummyFullModelClass' => $fullServiceClass,
            'DummyModelClass'     => $serviceClass,
            'DummyModelVariable'  => $serviceVariable,
        ];
    }


    /**
     * Get the fully-qualified model class name.
     *
     * @param string $model
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected function parseModel(string $model): string
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        $model = trim(str_replace('/', '\\', $model), '\\');

        if (!Str::startsWith($model, $rootNamespace = $this->laravel->getNamespace())) {
            $model = $rootNamespace . $model;
        }

        return $model;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['api', null, InputOption::VALUE_NONE, 'Exclude the create and edit methods from the controller.'],
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the controller already exists'],
            ['invokable', 'i', InputOption::VALUE_NONE, 'Generate a single method, invokable controller class.'],
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Generate a resource controller for the given model.'],
            ['service', 'sv', InputOption::VALUE_OPTIONAL, 'Create a new service business file for the model'],
            ['parent', 'p', InputOption::VALUE_OPTIONAL, 'Generate a nested resource controller class.'],
            ['resource', 'r', InputOption::VALUE_NONE, 'Generate a resource controller class.'],
            ['base', 'b', InputOption::VALUE_NONE, 'Generate a base controller class.'],
        ];
    }

    /**
     * @Description
     *
     * @Author yaangvu
     * @Date   Jan 19, 2022
     *
     * @param string $stub
     * @param string $name
     *
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name): static
    {
        $searches = [
            ['ServiceNamespace', 'ServiceClass'],
            ['{{ serviceNamespace }}', '{{ serviceClass }}'],
            ['{{serviceNamespace}}', '{{ serviceClass }}']
        ];

        foreach ($searches as $search) {
            $stub = str_replace(
                $search,
                [NamespaceGenerator::generateFullNamespace($name, 'Service'),
                 NamespaceGenerator::generateClass($name, 'Service')],
                $stub
            );
        }

        return parent::replaceNamespace($stub, $name);
    }
}
