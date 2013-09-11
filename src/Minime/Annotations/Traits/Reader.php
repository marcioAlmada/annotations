<?php

namespace Minime\Annotations\Traits;

use Minime\Annotations\Facade;

trait Reader
{
	/**
	 * Retrieve all annotations from current class
	 * 
	 * @return Minime\Annotations\AnnotationsBag Annotations collection
	 */
	public function getClassAnnotations()
	{
		$annotations = Facade::getClassAnnotations($this);
		return $annotations;
	}

	/**
	 * Retrieve all annotations from a given property of current class
	 * 
	 * @param  string $property Property name
	 * @return Minime\Annotations\AnnotationsBag Annotations collection
	 */
	public function getPropertyAnnotations($property)
	{
		$annotations = Facade::getPropertyAnnotations($this, $property);
		return $annotations;
	}

	/**
	 * Retrieve all annotations from a given method of current class
	 * 
	 * @param  string $property Method name
	 * @return Minime\Annotations\AnnotationsBag Annotations collection
	 */
	public function getMethodAnnotations($method)
	{
		$annotations = Facade::getMethodAnnotations($this, $method);
		return $annotations;
	}
}