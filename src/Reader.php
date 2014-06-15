<?php
namespace Minime\Annotations;

use Minime\Annotations\Interfaces\ParserInterface;
use Minime\Annotations\Interfaces\ReaderInterface;

/**
 * This class is the primary entry point to read annotations
 *
 * @package Minime\Annotations
 */
class Reader implements ReaderInterface
{
    /**
     * @var Interfaces\ParserInterface
     */
    protected $parser;

    /**
     * @param ParserInterface $parser
     */
    public function __construct(ParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Retrieve all annotations from a given class
     *
     * @param  mixed                                                  $class Full qualified class name or object
     * @return \Minime\Annotations\Interfaces\AnnotationsBagInterface Annotations collection
     * @throws \ReflectionException                                   If class is not found
     */
    public function getClassAnnotations($class)
    {
        return $this->getAnnotations(new \ReflectionClass($class));
    }

    /**
     * Retrieve all annotations from a given property of a class
     *
     * @param  mixed                                                  $class    Full qualified class name or object
     * @param  string                                                 $property Property name
     * @return \Minime\Annotations\Interfaces\AnnotationsBagInterface Annotations collection
     * @throws \ReflectionException                                   If property is undefined
     */
    public function getPropertyAnnotations($class, $property)
    {
        return $this->getAnnotations(new \ReflectionProperty($class, $property));
    }

    /**
     * Retrieve all annotations from a given method of a class
     *
     * @param  mixed                                                  $class  Full qualified class name or object
     * @param  string                                                 $method Method name
     * @return \Minime\Annotations\Interfaces\AnnotationsBagInterface Annotations collection
     * @throws \ReflectionException                                   If method is undefined
     */
    public function getMethodAnnotations($class, $method)
    {
        return $this->getAnnotations(new \ReflectionMethod($class, $method));
    }

    /**
     * Retrieve annotations from docblock of a given reflector
     *
     * @param  \Reflector                                             $Reflection Reflector object
     * @return \Minime\Annotations\Interfaces\AnnotationsBagInterface Annotations collection
     */
    public function getAnnotations(\Reflector $Reflection)
    {
        $array = $this->parser->parse($Reflection->getDocComment());

        return new AnnotationsBag($array, $this->parser->getRules());
    }
}
