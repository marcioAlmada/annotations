<?php

namespace Minime\Annotations\Traits;

use Minime\Annotations\Facade;

trait Reader
{
	public function getClassAnnotations()
	{
		return Facade::getClassAnnotations($this);
	}

	public function getPropertyAnnotations($property)
	{
		return Facade::getPropertyAnnotations($this, $property);
	}

	public function getMethodAnnotations($method)
	{
		return Facade::getMethodAnnotations($this, $method);
	}
}