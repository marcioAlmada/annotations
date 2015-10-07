<?php

namespace Minime\Annotations;

use Minime\Annotations\Cache\ArrayCache;
use Minime\Annotations\Interfaces\CacheInterface;
use Minime\Annotations\Interfaces\ParserInterface;
use Minime\Annotations\Interfaces\ReaderInterface;
use Minime\Annotations\Reflector\ReflectionConst;

/**
 * This class is the primary entry point to read annotations
 *
 * @package Minime\Annotations
 */
class Reader implements ReaderInterface
{
    /**
     * @var \Minime\Annotations\Interfaces\ParserInterface
     */
    protected $parser;

    /**
     * @var \Minime\Annotations\Interfaces\CacheInterface
     */
    protected $cache;

    /**
     * @param \Minime\Annotations\Interfaces\ParserInterface $parser
     */
    public function __construct(ParserInterface $parser, CacheInterface $cache = null)
    {
        $this->setParser($parser);
        $this->setCache($cache);
    }

    /**
     * @param \Minime\Annotations\Interfaces\CacheInterface $cache Cache handler
     */
    public function setCache(CacheInterface $cache = null)
    {
        $this->cache = $cache;
    }

    /**
     * @return \Minime\Annotations\Interfaces\CacheInterface Cache handler
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param \Minime\Annotations\Interfaces\ParserInterface $parser
     */
    public function setParser(ParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @return \Minime\Annotations\Interfaces\ParserInterface
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * Retrieve all annotations from a given function or closure
     *
     * @param  mixed                                                  $fn Full qualified function name or closure
     * @return \Minime\Annotations\Interfaces\AnnotationsBagInterface Annotations collection
     * @throws \ReflectionException                                   If function is not found
     */
    public function getFunctionAnnotations($fn)
    {
        return $this->getAnnotations(new \ReflectionFunction($fn));
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
     * Retrieve all annotations from a given constant of a class
     *
     * @param  string|object                                          $class fully qualified name or instance of the class
     * @param  string                                                 $const name of the constant
     * @return \Minime\Annotations\Interfaces\AnnotationsBagInterface Annotations collection
     */
    public function getConstantAnnotations($class, $const)
    {
        return $this->getAnnotations(new ReflectionConst($class, $const));
    }

    /**
     * Retrieve annotations from docblock of a given reflector
     *
     * @param  \Reflector                                             $Reflection Reflector object
     * @return \Minime\Annotations\Interfaces\AnnotationsBagInterface Annotations collection
     */
    public function getAnnotations(\Reflector $Reflection)
    {
        $doc = $Reflection->getDocComment();
        if ($this->cache) {
            $key = $this->cache->getKey($doc);
            $ast = $this->cache->get($key);
            if (! $ast) {
                $ast = $this->parser->parse($doc);
                $this->cache->set($key, $ast);
            }
        } else {
            $ast = $this->parser->parse($doc);
        }

        return new AnnotationsBag($ast);
    }

    /**
     * Shortcut to create an instance of the default annotations Reader
     * bundled with the default Parser implementation
     *
     * @return \Minime\Annotations\Interfaces\ReaderInterface
     */
    public static function createFromDefaults()
    {
        return new self(new Parser, new ArrayCache);
    }
}
