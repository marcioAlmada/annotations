<?php

namespace Minime\Annotations;

use Minime\Annotations\Interfaces\ParserRulesInterface;

/**
 *
 * An annotation collection class.
 *
 * @package Annotations
 *
 */
class AnnotationsBag implements \IteratorAggregate, \Countable
{

    /**
     * Associative arrays of annotations
     * @var array
     */
    private $attributes = [];

    /**
     * The ParserRules object
     * @var ParserRulesInterface
     */
    private $rules;

    /**
     * The Constructor
     * @param array                $attributes
     * @param ParserRulesInterface $rules
     */
    public function __construct(array $attributes, ParserRulesInterface $rules)
    {
        $this->rules = $rules;
        foreach (array_keys($attributes) as $key) {
            if ($this->rules->isKeyValid($key)) {
                $this->attributes[$key] = $attributes[$key];
            }
        }
    }

    /**
     * Unbox all annotations in the form of an associative array
     * @return array associative array of annotations
     */
    public function export()
    {
        return $this->attributes;
    }

    /**
     * Checks if a given annotation is declared
     * @param string $key A valid annotation tag, should match parser rules
     *
     * @throws \InvalidArgumentException If non string key is passed
     *
     * @return boolean
     */
    public function has($key)
    {
        if (! $this->rules->isKeyValid($key)) {
            throw new \InvalidArgumentException('Annotation key must be a valid annotation name string, according to parser rules.');
        }

        return array_key_exists($key, $this->attributes);
    }

    /**
     * Retrieves a single annotation value
     * @param string $key A valid annotation tag, should match parser rules
     *
     * @return mixed|null
     */
    public function get($key)
    {
        if ($this->has($key)) {
            return $this->attributes[$key];
        }

        return null;
    }

    /**
     * Retrieve annotation values as an array even if there's only one single value
     *
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
     * @param  string                            $pattern Valid regexp
     * @throws \InvalidArgumentException         If non valid regexp is passed
     * @return Minime\Annotations\AnnotationsBag Annotations collection with filtered results
     */
    public function grep($pattern)
    {
        if (! is_string($pattern)) {
            throw new \InvalidArgumentException('Grep pattern must be a valid regexp string.');
        }

        $results = array_intersect_key(
            $this->attributes,
            array_flip(
                preg_grep('/'.$pattern.'/', array_keys($this->attributes))
            )
        );

        return new static($results, $this->rules);
    }

    /**
     * Just an alias for AnnotationsBag::useNamespace.
     *
     * @todo Remove this method in version 2.*
     * @deprecated
     * @param string $pattern namespace
     *
     * @return Minime\Annotations\AnnotationsBag
     */
    public function grepNamespace($pattern)
    {
        return $this->useNamespace($pattern);
    }

    /**
     * Isolates a given namespace of annotations.
     * @param string $pattern namespace
     *
     * @return Minime\Annotations\AnnotationsBag
     */
    public function useNamespace($pattern)
    {
        $pattern = trim($pattern);
        if (!$this->rules->isNamespaceValid($pattern))
        {
            throw new \InvalidArgumentException('Namespace pattern must be a valid namespace string, according to parser rules.');
        }
        $namespaceIdentifier = $this->rules->getNamespaceIdentifier();
        $length = strlen($pattern);
        if ($namespaceIdentifier != $pattern[$length-1])
        {
            $pattern .= $namespaceIdentifier;
            $length++;
        }
        $results = [];
        foreach ($this->attributes as $key => $value)
        {
            if (strpos($key, $pattern) === 0)
            {
                $results[substr($key, $length)] = $value;
            }
        }

        return new static($results, $this->rules);
    }

    /**
    * Countable
    */
    public function count()
    {
        return count($this->attributes);
    }

    /**
    * IteratorAggregate
    */
    public function getIterator()
    {
        return new \ArrayIterator($this->attributes);
    }
}
