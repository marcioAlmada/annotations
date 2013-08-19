<?php

namespace Minime\Annotations\Traits;

use Minime\Annotations\Load;

trait Reader
{
	public function getClassAnnotations()
	{
		return Load::fromClass($this);
	}

	public function getPropertyAnnotations($property)
	{
		return Load::fromProperty($this, $property);
	}

	public function getMethodAnnotations($method)
	{
		return Load::fromMethod($this, $method);
	}
}