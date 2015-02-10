<?php

namespace Core\Bootstrap;

use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Volt as PhVolt;

class RegisterViewHelpersListener
{
    protected function afterMergeConfig($event, $application)
    {
        $di = $application->getDI();
        $config = $di->get('config');

        if (isset($config['volt']) && $config['volt']) {
            $di->set(
                'volt',
                function ($view, $di) use ($config) {

                    $volt = new PhVolt($view, $di);
                    $volt->setOptions([
                        'compiledPath'      =>  $config['volt']['compiledPath'],
                        'compiledExtension' =>  $config['volt']['compiledExtension'],
                        'compiledSeparator' =>  $config['volt']['compiledSeparator'],
                        'stat'              =>  $config['volt']['stat'],
                    ]);

                    //custom view filters
                    if (isset($config['view_helpers_filters']) && $config['view_helpers_filters']) {
                        foreach ($config['view_helpers_filters'] as $key => $function) {
                            $volt->getCompiler()->addFilter($key, $function);
                        }
                    }

                    //custom view functions
                    if (isset($config['view_helpers']) && $config['view_helpers']) {
                        foreach ($config['view_helpers'] as $key => $function) {
                            $volt->getCompiler()->addFunction($key, $function);
                        }
                    }

                    return $volt;
                }
            );
        }
    }
}
