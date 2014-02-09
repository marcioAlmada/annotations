<?php

namespace Minime\Annotations\Types;

use ReflectionClass;
use Minime\Annotations\Interfaces\TypeInterface;
use Minime\Annotations\Types\Json;
use Minime\Annotations\ParserException;

class Concrete implements TypeInterface
{

    /**
     * Process a value to be a concrete annotation
     *
     * @param  string $value json string
     * @param  string $class name of concrete annotation type (class)
     * @return object
     */
    public function parse($value, $class = null)
    {
        if (!class_exists($class)) {
            throw new ParserException("Concrete annotation expects {$class} to be a valid class.");
        }
        $parsed = (new Json)->parse($value);
        switch ( gettype($parsed) ) {
            default:
                throw new ParserException("Json value for annotation({$class}) must be of type array or object.");
            case 'array':
                return $this->doConstructStrategy($class, $parsed);
            case 'object':
                return $this->doSetterInjectionStrategy($class, $parsed);
        }
    }

    public function doConstructStrategy($class, $args)
    {
            $reflect  = new ReflectionClass($class);

            return $reflect->newInstanceArgs($args);
    }

    public function doSetterInjectionStrategy($class, \stdClass $prototype)
    {
        $instance = new $class();
        foreach ($prototype as $property => $value) {
            $instance->{ 'set' . ucfirst($property) }($value);
        }

        return $instance;
    }

}
