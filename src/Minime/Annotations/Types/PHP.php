<?php

namespace Minime\Annotations\Types;

use Minime\Annotations\Interfaces\TypeInterface;
use Minime\Annotations\ParserException;

class PHP implements TypeInterface
{

    /**
     * Filter a value to be a PHP eval
     *
     * @param  string          $value
     * @throws ParserException If $value is not a valid PHP code
     * @return mixed
     */
    public function parse($value, $annotation = null)
    {
        $output = @eval("return {$value};");
        if (false === $output) {
            throw new ParserException("Raw value should be valid PHP. Invalid code '{$value}' given.");
        }

        return $output;
    }

}
