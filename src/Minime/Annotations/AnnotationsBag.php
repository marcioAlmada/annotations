<?php

namespace Minime\Annotations;

class AnnotationsBag implements \IteratorAggregate
{
	private $attributes = [];

	public function __construct($attributes)
	{
		if(!is_array($attributes))
		{
			throw new \InvalidArgumentException("AnnotationsBag expects array of annotations");
		}
		$this->attributes = $attributes;
	}

	public function export()
	{
		return $this->attributes;
	}

	public function has($key)
	{
		if(is_string($key))
		{
			if(isset($this->attributes[$key]))
			{
				return true;
			}
			return false;
		}
		throw new \InvalidArgumentException('Annotation key must be a string');
	}

	public function grep($pattern)
	{

		if(!is_string($pattern))
		{
			throw new \InvalidArgumentException('Grep pattern must be a string');
		}

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

	public function get($key)
	{
		if($this->has($key))
		{
			return $this->attributes[$key];
		}
		return null;
	}

	public function getIterator()
	{
		return new \ArrayIterator($this->attributes);
	}
}
