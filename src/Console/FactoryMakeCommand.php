<?php

namespace YaangVu\LumenGenerator\Console;

use Symfony\Component\Console\Input\InputOption;
use YaangVu\LumenGenerator\NamespaceGenerator;

class FactoryMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'yaang:factory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model factory';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Factory';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/factory.stub';
    }

    /**
     * Build the class with the given name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function buildClass($name)
    {
        $namespaceModel = $this->option('model')
            ? $this->qualifyClass($this->option('model'))
            : trim($this->rootNamespace(), '\\') . '\\Model';

        $model = class_basename($namespaceModel);

        return str_replace(
            [
                'NamespacedDummyModel',
                'DummyModel',
            ],
            [
                $namespaceModel,
                $model,
            ],
            parent::buildClass($name)
        );
    }

    /**
     * Get the destination class path.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getPath($name)
    {
        // $name = str_replace(
        //     ['\\', '/'], '', $this->argument('name')
        // );
        $name = NamespaceGenerator::parseNameInput($this->argument('name'))['last'];

        return $this->laravel->databasePath() . "/factories/{$name}Factory.php";
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'The name of the model'],
        ];
    }
}
