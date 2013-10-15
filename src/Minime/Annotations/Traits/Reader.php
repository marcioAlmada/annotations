<?php

namespace Minime\Annotations\Traits;

use Minime\Annotations\Facade;

/**
 *
 * A trait for adding Annotations parsing
 *
 * @package Annotations
 *
 */
trait Reader
{
    /**
     * Retrieve all annotations from current class
     * 
     * @return Minime\Annotations\AnnotationsBag Annotations collection
     */
    public function getClassAnnotations()
    {
        return Facade::getClassAnnotations($this);
    }

    /**
     * Retrieve all annotations from a given property of current class
     * 
     * @param  string $property Property name
     * @return Minime\Annotations\AnnotationsBag Annotations collection
     */
    public function getPropertyAnnotations($property)
    {
        return Facade::getPropertyAnnotations($this, $property);
    }

    /**
     * Retrieve all annotations from a given method of current class
     * 
     * @param  string $property Method name
     * @return Minime\Annotations\AnnotationsBag Annotations collection
     */
    public function getMethodAnnotations($method)
    {
        return Facade::getMethodAnnotations($this, $method);
    }
}
