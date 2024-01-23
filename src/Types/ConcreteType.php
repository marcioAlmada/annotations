<?php

namespace Minime\Annotations\Types;

use stdClass;
use ReflectionClass;
use Minime\Annotations\Interfaces\TypeInterface;
use Minime\Annotations\ParserException;

class ConcreteType implements TypeInterface
{
    /**
     * @var TypeInterface
     */
    private static $instance;

    public static function getType()
    {
        if (!isset(self::$instance)) {
            self::$instance = new ConcreteType();
        }

        return self::$instance;
    }

    /**
     * @var array
     */
    private $namespaceLookup = [];

    /**
     * Set of user defined namespaces to lookup for class autoloading.
     *
     * @param array $namespaces
     */
    public function setNamespaces(array $namespaces)
    {
        $this->namespaceLookup = $namespaces;
    }

    /**
     * @param string $class
     *
     * @return string
     *
     * @throws ParserException
     */
    protected function checkClassExistence($class)
    {
        $found = class_exists($class);
        $classname = $class;
        $i = 0;

        while (!$found && $i < count($this->namespaceLookup)) {
            $classname = $this->namespaceLookup[$i] . $class;
            $found = class_exists($classname);
            $i++;
        }

        if (!$found) {
            throw new ParserException("Concrete annotation expects '{$class}' to exist.");
        }

        return $classname;
    }

    /**
     * Process a value to be a concrete annotation
     *
     * @param  string $value json string
     * @param  string $class name of concrete annotation type (class)
     *
     * @throws ParserException
     *
     * @return object
     */
    public function parse($value, $class = null)
    {
        $classname = $this->checkClassExistence($class);

        $prototype = (new JsonType)->parse($value);

        if ($prototype instanceof stdClass) {
            if (!$this->isPrototypeSchemaValid($prototype)) {
                throw new ParserException("Only arrays should be used to configure concrete annotation method calls.");
            }

            return $this->makeInstance($classname, $prototype);
        }

        if (is_array($prototype)) {
            return $this->makeConstructSugarInjectionInstance($classname, $prototype);
        }

        throw new ParserException("Json value for annotation({$classname}) must be of type object or array.");
    }

    protected function makeConstructSugarInjectionInstance($class, array $prototype) {
        $reflection = new ReflectionClass($class);
        $instance = $reflection->newInstanceArgs($prototype);

        return $instance;
    }

    /**
     * Creates and hydrates a concrete annotation class
     *
     * @param  string   $class     full qualified class name
     * @param  stdClass $prototype object prototype
     * @return object   hydrated concrete annotation class
     */
    protected function makeInstance($class, stdClass $prototype)
    {
        $reflection = new ReflectionClass($class);
        if (isset($prototype->__construct)) {
            $instance = $reflection->newInstanceArgs($prototype->__construct);
            unset($prototype->__construct);
        } else {
            $instance = $reflection->newInstance();
        }

        return $this->doMethodConfiguration($instance, $prototype);
    }

    /**
     * Do configuration injection through method calls
     *
     * @param  object   $instance  concrete annotation instance
     * @param  stdClass $prototype object prototype
     * @return object   hydrated concrete annotation class
     */
    protected function doMethodConfiguration($instance, stdClass $prototype)
    {
        foreach ($prototype as $method => $args) {
            call_user_func_array([$instance, $method], $args);
            }

        return $instance;
    }

    /**
     * Validates a prototype object
     *
     * @param  stdClass $prototype object prototype
     * @return boolean  true if prototype is valid
     */
    protected function isPrototypeSchemaValid(stdclass $prototype)
    {
        foreach ($prototype as $args) {
            if (! is_array($args)) {
                return false;
            }
        }

        return true;
    }

}
