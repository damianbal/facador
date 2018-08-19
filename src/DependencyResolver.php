<?php

namespace damianbal\Facador;

class DependencyResolver 
{
    /**
     * Create class and inject dependencies and arguments to constrcutor
     *
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public static function createClass($class_name, $args)
    {   
        $class = new \ReflectionClass($class_name);

        $class_constructor = $class->getConstructor();

        $params = $args;

        $deps = [];

        foreach ($class_constructor->getParameters() as $param) {

            // is type hinted?
            if($param->hasType()) {
                $type = $param->getType();
                $name = $param->getName();

                // check if we have it in container
                if(BaseContainer::getInstance()->has($name)) {
                    // we got it so just add it to deps
                    $deps[$name] = BaseContainer::getInstance()->get($name);
                }
                // it does not exist in container so try to create it and add it to deps
                else {
                    $dependency = BaseContainer::getInstance()->createDependency($name, (string) $type);
                    $deps[$name] = $dependency;
                }
            }   

        }

        return $class->newInstanceArgs(array_merge($deps, $params));
    }
    
    /**
     * Invoke method and inject dependencies, and arguments
     *
     * @param mixed $obj
     * @param string $method
     * @return mixed
     */
    public static function invoke($obj, $method, $args = [])
    {
        if($obj == null) return;

        $class = new \ReflectionClass($obj);

        $m = $class->getMethod($method);

        $params = $args;

        $deps = [];

        foreach ($m->getParameters() as $param) {

            // is type hinted?
            if ($param->hasType()) {
                $type = $param->getType();
                $name = $param->getName();

                // check if we have it in container
                if (BaseContainer::getInstance()->has($name)) {
                    // we got it so just add it to deps
                    $deps[$name] = BaseContainer::getInstance()->get($name);
                }
                // it does not exist in container so try to create it and add it to deps
                else {
                    $dependency = BaseContainer::getInstance()->createDependency($name, $type);
                    $deps[$name] = $dependency;
                }
            }

        }

        // invoke method with dependencies and arguments
        return $m->invokeArgs($obj, array_merge($deps, $params));
    }
}