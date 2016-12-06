<?php

namespace TaylorNetwork\MenuGenerator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class MenuGeneratorServiceProvider extends ServiceProvider
{
    /**
     * @var Request
     */
    private $request;

    /**
     * Boot
     * 
     * @param Request $request
     */
    public function boot (Request $request)
    {
        $this->publishes([
            __DIR__.'/config/menu_generator.php' => config_path('menu_generator.php'),
        ]);

        $this->request = $request;
    }

    /**
     * Register
     */
    public function register ()
    {
        $this->mergeConfigFrom(__DIR__.'/config/menu_generator.php', 'menu_generator');
        
        if (config('menu_generator.registerCommands', true))
        {
            $this->registerCommands();
        }
        
        $this->registerMenus();
    }

    /**
     * Register Commands
     */
    public function registerCommands()
    {
        $this->commands([
            Commands\MenuDefinitionCommand::class,
        ]);
    }

    /**
     * Register Menus
     */
    public function registerMenus()
    {
        $namespace = config('menu_generator.namespace', 'Menus');
        $menus = glob(app_path(str_replace('\\', '/', $namespace)).'/*.php');

        if (count($menus))
        {
            foreach ($menus as $menu)
            {
                $this->share('App\\' . $namespace . '\\' . substr(last(explode('/', $menu)), 0, -4));
            }
        }
    }

    /**
     * View Share 
     * 
     * @param $class
     */
    public function share($class)
    {
        $key = $class::menuKey;
        
        if ($key !== null)
        {
            $var = str_replace('{key}', $key, config('menu_generator.shareKey', '{key}Menu'));
            
            $instance = new $class ($this->request);
            $instance->define();
            
            View::share($var, $instance);
        }
    }
}