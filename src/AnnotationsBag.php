<?php

namespace Minime\Annotations;

use Minime\Annotations\Interfaces\AnnotationsBagInterface;
use RegexGuard\Factory as RegexGuard;
use ArrayIterator;
use RegexIterator;

/**
 * An annotation collection class
 *
 * @package Annotations
 * @author  Márcio Almada and the Minime Community
 * @license MIT
 *
 */
class AnnotationsBag implements AnnotationsBagInterface
{

    /**
     * Associative arrays of annotations
     *
     * @var array
     */
    private $attributes = [];

    /**
     * The Constructor
     *
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Unbox all annotations in the form of an associative array
     *
     * @return array associative array of annotations
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * Checks if a given annotation is declared
     *
     * @param  string  $key A valid annotation tag, should match parser rules
     * @return boolean
     */
    public function has($key)
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * Set a single annotation value
     *
     * @param  string $key   a valid annotation tag, should match parser rules
     * @param  mixed  $value the param value
     * @return self
     */
    public function set($key, $value)
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * Retrieves a single annotation value
     *
     * @param  string     $key    A valid annotation tag, should match parser rules
     * @param  mixed      $defaut Default value in case $key is not set
     * @return mixed|null
     */
    public function get($key, $defaut = null)
    {
        if ($this->has($key)) {
            return $this->attributes[$key];
        }

        return $defaut;
    }

    /**
     * Retrieve annotation values as an array even if there's only one single value
     *
     * @param  string $key A valid annotation tag, should match parser rules
     * @return array
     */
    public function getAsArray($key)
    {
        if (! $this->has($key)) {
            return [];
        }
        $res = $this->attributes[$key];
        if (is_null($res)) {
            return [null];
        }

        return (array) $res;
    }

    /**
     * Filters annotations based on a regexp
     *
     * @param  string                             $pattern valid regexp
     * @return \Minime\Annotations\AnnotationsBag Annotations collection with filtered results
     */
    public function grep($pattern)
    {
        $results = array_intersect_key($this->attributes, array_flip(
            RegexGuard::getGuard()->grep($pattern, array_keys($this->attributes))
        ));

        return new static($results);
    }

    /**
     * Isolates a given namespace of annotations.
     *
     * @param  string                             $pattern    namespace
     * @param  array                              $delimiters possible namespace delimiters to mask
     * @return \Minime\Annotations\AnnotationsBag
     */
    public function useNamespace($pattern, array $delimiters = ['.', '\\'])
    {
        $mask =  implode('', $delimiters);
        $consumer =  '(' . implode('|', array_map('preg_quote', $delimiters)) .')';
        $namespace_pattern = '/^' . preg_quote(rtrim($pattern, $mask)) .  $consumer . '/';
        $iterator = new RegexIterator(
            $this->getIterator(), $namespace_pattern, RegexIterator::REPLACE, RegexIterator::USE_KEY);
        $iterator->replacement = '';

        return new static(iterator_to_array($iterator));
    }

    /**
     * Performs union operations against a given AnnotationsBag
     *
     * @param  AnnotationsBag                     $bag The annotation bag to be united
     * @return \Minime\Annotations\AnnotationsBag Annotations collection with union results
     */
    public function union(AnnotationsBagInterface $bag)
    {
        return new static($this->attributes + $bag->toArray());
    }

    /**
     * Countable
     */
    public function count()
    {
        return count($this->attributes);
    }

    /**
     * JsonSerializable
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * IteratorAggregate
     */
    public function getIterator()
    {
        return new ArrayIterator($this->attributes);
    }

    /**
     * ArrayAccess - Whether or not an offset exists.
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * ArrayAccess - Returns the value at specified offset.
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * ArrayAccess - Assigns a value to the specified offset.
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);

        return true;
    }

    /**
     * ArrayAccess - Unsets an offset.
     */
    public function offsetUnset($key)
    {
        unset($this->attributes[$key]);
    }
}
