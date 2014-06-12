<?php
namespace Minime\Annotations\Interfaces;

/**
 * Interface for annotation reader
 *
 * @package Minime\Annotations\Interfaces
 */
interface ReaderInterface
{
    /**
     * Retrieve all annotations from a given class
     *
     * @param  mixed                              $class Full qualified class name or object
     * @return \Minime\Annotations\AnnotationsBag Annotations collection
     * @throws \\ReflectionException              If class is not found
     */
    public function getClassAnnotations($class);

    /**
     * Retrieve all annotations from a given property of a class
     *
     * @param  mixed                              $class Full qualified class name or object
     * @param  string                             $property Property name
     * @return \Minime\Annotations\AnnotationsBag Annotations collection
     * @throws \\ReflectionException              If property is undefined
     */
    public function getPropertyAnnotations($class, $property);

    /**
     * Retrieve all annotations from a given method of a class
     *
     * @param  mixed                              $class Full qualified class name or object
     * @param  string                             $method Method name
     * @return \Minime\Annotations\AnnotationsBag Annotations collection
     * @throws \\ReflectionException              If method is undefined
     */
    public function getMethodAnnotations($class, $method);

    /**
     * Retrieve annotations from docblock of a given reflector
     *
     * @param  \Reflector                          $Reflection Reflector object
     * @return \Minime\Annotations\Interfaces\AnnotationsBagInterface Annotations collection
     */
    public function getAnnotations(\Reflector $Reflection);
}