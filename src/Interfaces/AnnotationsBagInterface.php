<?php

namespace Minime\Annotations\Interfaces;

use Minime\Annotations\AnnotationsBag;

/**
 * An annotation collection interface
 *
 * @package Annotations
 * @author  Márcio Almada and the Minime Community
 * @license MIT
 *
 */
interface AnnotationsBagInterface extends \ArrayAccess, \Countable, \IteratorAggregate, \JsonSerializable
{
    /**
     * Isolates a given namespace of annotations.
     * @param string $pattern namespace
     *
     * @return \Minime\Annotations\AnnotationsBag
     */
    public function useNamespace($pattern);

    /**
     * Performs union operations against a given AnnotationsBag
     *
     * @param  AnnotationsBag                     $bag The annotation bag to be united
     * @return \Minime\Annotations\AnnotationsBag Annotations collection with union results
     */
    public function union(AnnotationsBagInterface $bag);

    /**
     * Filters annotations based on a regexp
     * @param  string                             $pattern Valid regexp
     * @throws \InvalidArgumentException          If non valid regexp is passed
     * @return \Minime\Annotations\AnnotationsBag Annotations collection with filtered results
     */
    public function grep($pattern);

    /**
     * Retrieve annotation values as an array even if there's only one single value
     *
     * @param string $key A valid annotation tag, should match parser rules
     *
     * @return array
     */
    public function getAsArray($key);

    /**
     * replace a set of annotations values
     * @param array $attributes
     *
     * @return self
     */
    public function replace(array $attributes);

    /**
     * Checks if a given annotation is declared
     * @param string $key A valid annotation tag, should match parser rules
     *
     * @throws \InvalidArgumentException If $key is not validated by the parserRules
     *
     * @return boolean
     */
    public function has($key);

    /**
     * Set a single annotation value
     * @param string $key   a valid annotation tag, should match parser rules
     * @param mixed  $value the param value
     *
     * @throws \InvalidArgumentException If $key is not validated by the parserRules
     *
     * @return self
     */
    public function set($key, $value);

    /**
     * Retrieves a single annotation value
     *
     * @param string $key A valid annotation tag, should match parser rules
     *
     * @return mixed|null
     */
    public function get($key);

    /**
     * Unbox all annotations in the form of an associative array
     *
     * @return array associative array of annotations
     */
    public function toArray();
}
