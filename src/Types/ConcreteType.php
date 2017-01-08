<?php

namespace Minime\Annotations\Types;

use stdClass;
use ReflectionClass;
use Minime\Annotations\Interfaces\TypeInterface;
use Minime\Annotations\ParserException;

class ConcreteType implements TypeInterface
{

    /**
     * Process a value to be a concrete annotation
     *
     * @param  string                              $value json string
     * @param  string                              $class name of concrete annotation type (class)
     * @throws \Minime\Annotations\ParserException
     * @return object
     */
    public function parse($value, $class = null)
    {
        if (! class_exists($class)) {
            throw new ParserException("Concrete annotation expects {$class} to exist.");
        }

        $prototype = (new JsonType)->parse($value);

        if ($prototype instanceof stdClass) {
            if (! $this->isPrototypeSchemaValid($prototype)) {
                throw new ParserException("Only arrays should be used to configure concrete annotation method calls.");
            }

            return $this->makeInstance($class, $prototype);
        }

        if (is_array($prototype)) {
            return $this->makeConstructSugarInjectionInstance($class, $prototype);
        }

        throw new ParserException("Json value for annotation({$class}) must be of type object or array.");
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
        foreach ($prototype as $method => $args) {
            if (! is_array($args)) {
                return false;
            }
        }

        return true;
    }

}
