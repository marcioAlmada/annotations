<?php

namespace Minime\Annotations;

class AnnotationsBag
{
	private $parameters = [];

	public function __construct($parameters)
	{
		if(!is_array($parameters))
		{
			throw new \InvalidArgumentException("AnnotationsBag expects array of annotations");
		}
		$this->parameters = $parameters;
	}

	public function export()
	{
		return $this->parameters;
	}

	public function has($key)
	{
		if(is_string($key))
		{
			if(isset($this->parameters[$key]))
			{
				return true;
			}
			return false;
		}
		throw new \InvalidArgumentException('Annotation key must be a string');
	}

	public function get($key)
	{
		if($this->has($key))
		{
			return $this->parameters[$key];
		}
		return null;
	}
}
