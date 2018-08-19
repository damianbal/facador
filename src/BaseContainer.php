<?php

namespace damianbal\Facador;

use Psr\Container\ContainerInterface;

class BaseContainer implements ContainerInterface
{
    public static $instance = null;
    protected $dependencies = [];
    protected $bindings = [];

    /**
     * Get container
     *
     * @return damianbal\Facador\BaseContainer
     */
    public static function getInstance()
    {
        if (static::$instance == null) {
            static::$instance = new BaseContainer;
        }

        return static::$instance;
    }

    /**
     * Bind
     *
     * @param string $class_interface
     * @param string $class_implementation
     * @return void
     */
    public function bind($class_interface, $class_implementation)
    {
        $this->bindings[$class_interface] = $class_implementation;
    }

    /**
     * Set dependency
     *
     * @param string $id
     * @param mixed $object
     * @return void
     */
    public function set($id, $object)
    {
        $this->dependencies[$id] = $object;
    }

    /**
     * Returns dependency object
     *
     * @param string $id
     * @return mixed
     */
    public function get($id)
    {
        return $this->dependencies[$id];
    }

    public function remove($id)
    {
        unset($this->dependencies[$id]);
    }

    public function reset()
    {
        $this->dependencies = [];
        $this->bindings = [];
    }

    /**
     * Create dependency of class and name, make sure
     * that you bind class before creating dependency
     *
     * @param [type] $name
     * @param [type] $class
     * @return void
     */
    public function createDependency($name, $class)
    {
        $ref = new \ReflectionClass($class);
       
        // if there is no binding just create new class instance
        if(!isset($this->bindings[$class]) && !$ref->isInterface()) {
            $dependency = new $class;
            $this->dependencies[$name] = $dependency;
            return $this->dependencies[$name];
        }

        if(!isset($this->bindings[$class]) && $ref->isInterface()) {
            throw new \Exception("Bind class first, as it is interface so it can not be created!");
        }

        //$dependency = $this->bindings[$class]($this);
        $dependency = call_user_func($this->bindings[$class]);
        $this->dependencies[$name] = $dependency;

        return $this->dependencies[$name];
    }

    /**
     * Check if container has dependency
     *
     * @param string $id
     * @return boolean
     */
    public function has($id): bool
    {
        return isset($this->dependencies[$id]);
    }
}
