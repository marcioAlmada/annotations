<?php

namespace Minime\Annotations\Fixtures;

class AnnotationSetterInjection
{
    public $foo;

    public function setFoo($foo)
    {
        $this->foo = $foo;
    }
}