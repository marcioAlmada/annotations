<?php

namespace Minime\Annotations\Fixtures;

class AnnotationConstructSugarInjection
{

    public $foo;
    public $bar;

    public function __construct($foo, $bar = 'bar')
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }
}
