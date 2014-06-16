<?php

namespace Minime\Annotations\Types;

use Minime\Annotations\Interfaces\TypeInterface;

class String implements TypeInterface
{

    /**
     * Parse a given value as string
     *
     * @param  string $value
     * @param null $annotation Unused
     * @return mixed
     */
    public function parse($value, $annotation = null)
    {
        return $value;
    }

}
