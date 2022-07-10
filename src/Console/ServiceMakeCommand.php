<?php

namespace YaangVu\LumenGenerator\Console;

use Symfony\Component\Console\Input\InputOption;

class ServiceMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'yaang:service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Service';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        $stub = '/stubs/service.stub';

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
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Generate a resource controller for the given model.'],
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
        $modelReplacements = $this->buildModelReplacements();

        $stub = str_replace(
            array_keys($modelReplacements),
            array_values($modelReplacements),
            $stub
        );

        return parent::replaceNamespace($stub, $name);
    }
}
