<?php

namespace Core\Bootstrap;

use ReflectionClass;
use Phalcon\Config;

class LoadModulesListener
{
    public function init($event, $application)
    {
        $modules = $application->getModules();
        $di = $application->getDi();
        $config = $di->get('config');
        foreach ($modules as &$moduleOptions) {
            $class = $moduleOptions['className'];

            $reflection = new ReflectionClass($class);
            $moduleOptions['path'] = $reflection->getFileName();

            $module = new $class();
            $moduleOptions['object'] = $module;

            //register autoloaders
            if (method_exists($module, 'registerAutoloaders')) {
                $module->registerAutoloaders();
            }
            //register services
            if (method_exists($module, 'registerServices')) {
                $module->registerServices($application->getDi());
            }

            //add view helpers config
            if (method_exists($module, 'registerViewHelpers')) {
                $item = $module->registerViewHelpers($application->getDi());
                $item = new Config($item);
                $config->merge($item);
            }
        }
        $application->registerModules($modules, true);
    }
}
