<?php

namespace Minime\Annotations;

class AnnotationsBag implements \IteratorAggregate, \Countable
{
    /**
     * Associative arrays of annotations
     * @var array
     */
    private $attributes = [];

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
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
     * @param string $key A valid annotation tag, according to rules in Minime\Annotations\Parser
     * @throws \InvalidArgumentException If non string key is passed
     * @return boolean TRUE if annotation is declared, FALSE if not
     */
    public function has($key)
    {
        $this->validateKeyOrFail($key);
        return array_key_exists($key, $this->attributes);
    }

    /**
     * Retrieves a single annotation value
     * @param  string $key A valid annotation tag, according to rules in Minime\Annotations\Parser
     * @return mixed  null if no annotation is found
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
            throw new \InvalidArgumentException('Grep pattern must be a valid regexp string');
        }

        $results = array_intersect_key(
            $this->attributes,
            array_flip(
                preg_grep('/'.$pattern.'/', array_keys($this->attributes))
            )
        );

        return new self($results);
    }

    /**
     * Just an alias for AnnotationsBag::useNamespace.
     *
     * @todo Remove this method in version 2.*
     * @deprecated
     * @param  string                            $pattern
     * @return Minime\Annotations\AnnotationsBag
     */
    public function grepNamespace($pattern)
    {
        return $this->useNamespace($pattern);
    }

    /**
     * Isolates a given namespace of annotations.
     *
     * @param  string                            $pattern namespace
     * @return Minime\Annotations\AnnotationsBag
     */
    public function useNamespace($pattern)
    {
        $annotations = $this->grep('^'.$pattern);
        $results = [];
        foreach ($annotations->export() as $namespace => $value) {
            $results[str_replace($pattern.'.', '', $namespace)] = $value;
        }

        return new self($results);
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

    private function validateKeyOrFail($key)
    {
        if(!$this->isKeyValid($key))
        {
            throw new \InvalidArgumentException('Annotation key must be a valid annotation name.');
        }
    }

    private function isKeyValid($key)
    {
        if (preg_match('/^'. Parser::REGEX_ANNOTATION_NAME .'$/', $key)) {
            return true;    
        }
        return false;
    }
}
