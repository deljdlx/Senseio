<?php
namespace Phi\Traits;


use Phi\Interfaces\InstanceAutoConfigurator;

Trait AutoConfigurable
{



    public static $autoConfiguratorSuffix='Autoconfigure';



    public static function getNewInstance() {
        $arguments=func_get_args();

        $possibleConfiguratorParameter=end($arguments);

        if($possibleConfiguratorParameter instanceof InstanceAutoConfigurator) {
            array_pop($arguments);
        }



        $reflector=new \ReflectionClass(get_called_class());
        $instance=$reflector->newInstanceArgs((array) $arguments);



        if($possibleConfiguratorParameter instanceof InstanceAutoConfigurator) {
            $possibleConfiguratorParameter->autoconfigure($instance);
        }
        else {
            $calledClassName=get_called_class();
            $autoConfigureClassName=$calledClassName.static::$autoConfiguratorSuffix;

            if(class_exists($autoConfigureClassName) && method_exists($autoConfigureClassName, 'autoConfigure')) {
                $configurator=new $autoConfigureClassName();
                $configurator->autoconfigure($instance);
            }
        }




        return $instance;
    }

}
