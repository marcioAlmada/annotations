<?php

namespace Minime\Annotations\Fixtures;

class AnnotationConstructInjection
{

    public $foo;

    public function __construct($foo, $bar = null)
    {
        $this->foo = $foo;
    }
}