<?php

namespace Minime\Annotations;

class AnnotationsBag implements \IteratorAggregate
{
	/**
	 * Associative arrays of annotations
	 * @var array
	 */
	private $attributes = [];

	public function __construct($attributes)
	{
		if(!is_array($attributes))
		{
			throw new \InvalidArgumentException("AnnotationsBag expects array of annotations");
		}
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
	 * @param  string  $key A valid annotation tag, should match /[A-z0-9\-\_]/
	 * @return boolean TRUE if annotation is declared, FALSE if not
	 * @throws \InvalidArgumentException If non string key is passed
	 */
	public function has($key)
	{
		if(is_string($key))
		{
			if(array_key_exists($key, $this->attributes))
			{
				return true;
			}
			return false;
		}
		throw new \InvalidArgumentException('Annotation key must be a string');
	}

	/**
	 * Filters annotations based on a regexp
	 * @param  string $pattern Valid regexp
	 * @return Minime\Annotations\AnnotationsBag Annotations collection with filtered results
	 * @throws \InvalidArgumentException If non valid regexp is passed
	 */
	public function grep($pattern)
	{
		if(is_string($pattern))
		{			
			$regex = "/$pattern/";		
			$results = [];

			foreach ($this->attributes as $key => $value)
			{
				if(preg_match($regex, $key))
				{
					$results[$key] = $value;
				}
			}

			return new self($results);
		}

		throw new \InvalidArgumentException('Grep pattern must be a regexp string');
	}

	/**
	 * Just an alias for AnnotationsBag::useNamespace.
	 * 
	 * @todo Remove this method in version 2.*
	 * @deprecated
	 * @param  string $pattern
	 * @return Minime\Annotations\AnnotationsBag
	 */
	public function grepNamespace($pattern)
	{
		return $this->useNamespace($pattern);
	}

	/**
	 * Isolates a given namespace of annotations.
	 * 
	 * @param  string $pattern namespace
	 * @return Minime\Annotations\AnnotationsBag
	 */
	public function useNamespace($pattern)
	{
		$annotations = $this->grep('^'.$pattern);
		$results = [];
		foreach ($annotations->export() as $namespace => $value)
		{
			$results[str_replace($pattern . '.', '', $namespace)] = $value;
		}
		return new self($results);
	}

	/**
	 * Retrieves a single annotation value
	 * @param  string  $key A valid annotation tag, should match /[A-z0-9\-\_]/
	 * @return mixed   null if no annotation is found
	 */
	public function get($key)
	{
		if($this->has($key))
		{
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
		$values = [];
		if($this->has($key))
		{
			$result = $this->get($key);
			
			if(!is_array($result))
			{
				$values[] = $result;
			}
			else
			{
				$values = $result;
			}
		}
		return $values;
	}

	public function getIterator()
	{
		return new \ArrayIterator($this->attributes);
	}
}
