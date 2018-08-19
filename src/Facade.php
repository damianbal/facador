<?php

namespace damianbal\Facador;

use damianbal\Facador\BaseContainer;

class Facade 
{
    /**
     * Returns name of dependency in container
     *
     * @return string
     */
    protected static function getDependencyName() {}   

    /**
     * Invoke method on dependency object from container 
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        $dependency = BaseContainer::getInstance()->get(static::getDependencyName());

        if($dependency == null) {
            throw new \Exception("Dependency does not exist in container!");
        }

        $reflection = new \ReflectionMethod(get_class($dependency) . "::" . $name);
        return $reflection->invokeArgs($dependency, $arguments);
    }
}