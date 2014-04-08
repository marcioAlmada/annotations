<?php

namespace Minime\Annotations;

use ReflectionClass;
use ReflectionProperty;
use ReflectionMethod;
use Reflector;

/**
 * A facade to facilitate annotations retrieval
 *
 * @package Annotations
 * @author  Márcio Almada and the Minime Community
 * @license MIT
 *
 */
class Facade
{
    /**
     * Retrieve all annotations from a given class
     *
     * @param  string                             $class Full qualified class name
     * @return \Minime\Annotations\AnnotationsBag Annotations collection
     * @throws \\ReflectionException              If class is not found
     */
    public static function getClassAnnotations($class)
    {
        return static::getAnnotations(new ReflectionClass($class));
    }

    /**
     * Retrieve all annotations from a given property of a class
     *
     * @param  string                             $class    Full qualified class name
     * @param  string                             $property Property name
     * @return \Minime\Annotations\AnnotationsBag Annotations collection
     * @throws \\ReflectionException              If property is undefined
     */
    public static function getPropertyAnnotations($class, $property)
    {
        return static::getAnnotations(new ReflectionProperty($class, $property));
    }

    /**
     * Retrieve all annotations from a given method of a class
     *
     * @param  string                             $class  Full qualified class name
     * @param  string                             $method Method name
     * @return \Minime\Annotations\AnnotationsBag Annotations collection
     * @throws \\ReflectionException              If method is undefined
     */
    public static function getMethodAnnotations($class, $method)
    {
        return static::getAnnotations(new ReflectionMethod($class, $method));
    }

    protected static function getAnnotations(Reflector $Reflection)
    {
        $Rules = new ParserRules;
        $annotations = (new Parser($Reflection->getDocComment(), $Rules))->parse();

        return new AnnotationsBag($annotations, $Rules);
    }
}
