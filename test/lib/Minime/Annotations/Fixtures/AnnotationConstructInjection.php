<?php

namespace Minime\Annotations\Fixtures;

class AnnotationConstructInjection
{

    public $foo;
    public $bar;

    public function __construct($foo, $bar = 'baz')
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }
}
