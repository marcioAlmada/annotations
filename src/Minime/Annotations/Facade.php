<?php

namespace Minime\Annotations;

use \ReflectionClass;
use \ReflectionProperty;
use \ReflectionMethod;

class Load {

	public static function fromClass($class)
	{
		$reflection = new ReflectionClass($class);
		return new Reader($reflection->getDocComment());
	}

	public static function fromProperty($class, $property)
	{
		$reflection = new ReflectionProperty($class, $property);
		return new Reader($reflection->getDocComment());
	}

	public static function fromMethod($class, $methd)
	{
		$reflection = new ReflectionMethod($class, $methd);
		return new Reader($reflection->getDocComment());
	} 

}