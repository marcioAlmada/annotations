<?php

namespace Minime\Annotations\Interfaces;

/**
 * Interface for annotation reader
 *
 * @package Annotations
 * @api
 */
interface ReaderInterface
{
    /**
     * @param \Minime\Annotations\Interfaces\CacheInterface $cache Cache handler
     */
    public function setCache(CacheInterface $cache);

    /**
     * @return \Minime\Annotations\Interfaces\CacheInterface Cache handler
     */
    public function getCache();

    /**
     * @param \Minime\Annotations\Interfaces\ParserInterface $parser
     */
    public function setParser(ParserInterface $parser);

    /**
     * @return \Minime\Annotations\Interfaces\ParserInterface
     */
    public function getParser();

    /**
     * Retrieve all annotations from a given class
     *
     * @param  mixed                                                  $class Full qualified class name or object
     * @return \Minime\Annotations\Interfaces\AnnotationsBagInterface Annotations collection
     * @throws \ReflectionException                                   If class is not found
     */
    public function getClassAnnotations($class);

    /**
     * Retrieve all annotations from a given property of a class
     *
     * @param  mixed                                                  $class    Full qualified class name or object
     * @param  string                                                 $property Property name
     * @return \Minime\Annotations\Interfaces\AnnotationsBagInterface Annotations collection
     * @throws \ReflectionException                                   If property is undefined
     */
    public function getPropertyAnnotations($class, $property);

    /**
     * Retrieve all annotations from a given method of a class
     *
     * @param  mixed                                                  $class  Full qualified class name or object
     * @param  string                                                 $method Method name
     * @return \Minime\Annotations\Interfaces\AnnotationsBagInterface Annotations collection
     * @throws \ReflectionException                                   If method is undefined
     */
    public function getMethodAnnotations($class, $method);

    /**
     * Retrieve all annotations from a given function or closure
     *
     * @param  mixed $fn                                              Full qualified function name or closure
     * @return \Minime\Annotations\Interfaces\AnnotationsBagInterface Annotations collection
     * @throws \ReflectionException                                   If function is not found
     */
    public function getFunctionAnnotations($fn);

    /**
     * Retrieve all annotations from a given constant of a class
     *
     * @param string|object                                     $class fully qualified name or instance of the class
     * @param string                                            $const name of the constant
     * @return \Minime\Annotations\Interfaces\AnnotationsBagInterface Annotations collection
     */
    public function getConstantAnnotations($class, $const);

    /**
     * Retrieve annotations from docblock of a given reflector
     *
     * @param  \Reflector                                             $Reflection Reflector object
     * @return \Minime\Annotations\Interfaces\AnnotationsBagInterface Annotations collection
     */
    public function getAnnotations(\Reflector $Reflection);
}
