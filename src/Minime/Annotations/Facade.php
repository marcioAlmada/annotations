<?php

namespace Minime\Annotations;

use \ReflectionClass;
use \ReflectionProperty;
use \ReflectionMethod;

class Facade
{

	public static function getClassAnnotations($class)
	{
		$reflection = new ReflectionClass($class);
		return (new Parser($reflection->getDocComment()))->parse();
	}

	public static function getPropertyAnnotations($class, $property)
	{
		$reflection = new ReflectionProperty($class, $property);
		return (new Parser($reflection->getDocComment()))->parse();
	}

	public static function getMethodAnnotations($class, $method)
	{
		$reflection = new ReflectionMethod($class, $method);
		return (new Parser($reflection->getDocComment()))->parse();
	} 

}