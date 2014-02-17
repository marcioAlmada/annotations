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
            throw new ParserException("Concrete annotation expects {$class} to exist.");
        }
        $prototype = (new Json)->parse($value);
        if ('object' !== gettype($prototype)) {
            throw new ParserException("Json value for annotation({$class}) must be of type object.");
        }

        return $this->makeInstance($class, $prototype);
    }

    public function makeInstance($class, \stdClass $prototype)
    {
        $reflection = (new ReflectionClass($class));
        if (isset($prototype->__construct)) {
            if(is_array($prototype->__construct)) {
                $instance = $reflection->newInstanceArgs( $prototype->__construct );
            } else {
                $instance = $reflection->newInstance( $prototype->__construct );
            }
            unset($prototype->__construct);
        } else {
            $instance = $reflection->newInstance();
        }

        return $this->doMethodConfiguration($instance, $prototype);
    }

    public function doMethodConfiguration($instance, \stdClass $prototype)
    {
        foreach ($prototype as $method => $value) {
            if(is_array($value)) {
                call_user_func_array([$instance, $method], $value);
            } else {
                $instance->{ $method }($value);
            }
        }

        return $instance;
    }

}
