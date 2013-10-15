<?php

namespace Minime\Annotations;

/**
 *
 * A facade for annotations parsing
 *
 * @package Annotations
 *
 */
class Facade
{
    /**
     * Retrieve all annotations from a given class
     *
     * @param  string                            $class Full qualified class name
     * @return Minime\Annotations\AnnotationsBag Annotations collection
     * @throws \\ReflectionException              If class is not found
     */
    public static function getClassAnnotations($class)
    {
        $reflection = new \ReflectionClass($class);
        $rules = new ParserRules;
        $docblock = (new Parser($reflection->getDocComment(), $rules))->parse();

        return new AnnotationsBag($docblock, $rules);
    }

    /**
     * Retrieve all annotations from a given property of a class
     *
     * @param  string                            $class    Full qualified class name
     * @param  string                            $property Property name
     * @return Minime\Annotations\AnnotationsBag Annotations collection
     * @throws \\ReflectionException              If property is undefined
     */
    public static function getPropertyAnnotations($class, $property)
    {
        $reflection = new \ReflectionProperty($class, $property);
        $rules = new ParserRules();
        $docblock = (new Parser($reflection->getDocComment(), $rules))->parse();

        return new AnnotationsBag($docblock, $rules);
    }

    /**
     * Retrieve all annotations from a given method of a class
     *
     * @param  string                            $class    Full qualified class name
     * @param  string                            $property Method name
     * @return Minime\Annotations\AnnotationsBag Annotations collection
     * @throws \\ReflectionException              If method is undefined
     */
    public static function getMethodAnnotations($class, $method)
    {
        $reflection = new \ReflectionMethod($class, $method);
        $rules = new ParserRules();
        $docblock = (new Parser($reflection->getDocComment(), $rules))->parse();

        return new AnnotationsBag($docblock, $rules);
    }
}
