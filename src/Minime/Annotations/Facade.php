<?php

namespace Minime\Annotations;

use \ReflectionClass;
use \ReflectionProperty;
use \ReflectionMethod;

class Facade
{
    /**
     * Retrieve all annotations from a given class
     * 
     * @param  string $class Full qualified class name
     * @return Minime\Annotations\AnnotationsBag Annotations collection
     * @throws  \ReflectionException If class is not found
     */
    public static function getClassAnnotations($class)
    {
        $reflection = new ReflectionClass($class);
        return (new Parser($reflection->getDocComment()))->parse();
    }

    /**
     * Retrieve all annotations from a given property of a class
     * 
     * @param  string $class Full qualified class name
     * @param  string $property Property name
     * @return Minime\Annotations\AnnotationsBag Annotations collection
     * @throws  \ReflectionException If property is undefined
     */
    public static function getPropertyAnnotations($class, $property)
    {
        $reflection = new ReflectionProperty($class, $property);
        return (new Parser($reflection->getDocComment()))->parse();
    }

    /**
     * Retrieve all annotations from a given method of a class
     * 
     * @param  string $class Full qualified class name
     * @param  string $property Method name
     * @return Minime\Annotations\AnnotationsBag Annotations collection
     * @throws  \ReflectionException If method is undefined
     */
    public static function getMethodAnnotations($class, $method)
    {
        $reflection = new ReflectionMethod($class, $method);
        return (new Parser($reflection->getDocComment()))->parse();
    }
}
