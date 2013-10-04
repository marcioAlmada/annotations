<?php

namespace Minime\Annotations;

class AnnotationsBag implements \IteratorAggregate, \Countable
{
    /**
     * Associative arrays of annotations
     * @var array
     */
    private $attributes = [];

    /**
     * Grep delimeter
     * @var string
     */
    private $delim = '/';

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Retrieves a single annotation value
     * @param string $str the delimeter use by the Object
     *
     * @throws InvalidArgumentExcpetion If $str is not a single character string
     *
     * @return self
     */
    public function setDelimeter($str)
    {
        $str = trim($str);
        if (! is_string($str) || ! $str || 1 != strlen($str)) {
            throw new \InvalidArgumentException(
                "The delimeter must be a single caracter string"
            );
        }
        $this->delim = $str;

        return $this;
    }

    /**
     * return the current Object delimeter
     * @return string
     */
    public function getDelimeter()
    {
        return $this->delim;
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
     * @param string $key A valid annotation tag, should match /[A-z0-9\-\_]/
     *
     * @throws \InvalidArgumentException If non string key is passed
     *
     * @return boolean TRUE if annotation is declared, FALSE if not
     */
    public function has($key)
    {
        if (! is_string($key)) {
            throw new \InvalidArgumentException('Annotation key must be a string');
        }

        return array_key_exists($key, $this->attributes);
    }

    /**
     * Retrieves a single annotation value
     * @param  string $key A valid annotation tag, should match /[A-z0-9\-\_]/
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
        $res = $this->get($key);
        if (is_null($res)) {
            return array(null);
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
            throw new \InvalidArgumentException('Grep pattern must be a regexp string');
        }
        $regex = $this->delim.preg_quote($pattern, $this->delim).$this->delim;
        $results = [];
        foreach ($this->attributes as $key => $value) {
            if (preg_match($regex, $key)) {
                $results[$key] = $value;
            }
        }
        $res = new self($results);

        return $res->setDelimeter($this->delim);
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
        $res = new self($results);

        return $res->setDelimeter($this->delim);
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
