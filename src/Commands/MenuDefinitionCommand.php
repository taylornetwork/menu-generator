<?php

namespace TaylorNetwork\MenuGenerator\Commands;

use Illuminate\Console\GeneratorCommand;

class MenuDefinitionCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'menu:definition';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a menu definition';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Menu Definition';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/definition.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\' . config('menu_generator.namespace', 'Menus');
    }
}