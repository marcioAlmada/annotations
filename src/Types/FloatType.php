<?php

namespace Minime\Annotations\Types;

use Minime\Annotations\Interfaces\TypeInterface;
use Minime\Annotations\ParserException;

class FloatType implements TypeInterface
{
    /**
     * @var TypeInterface
     */
    private static $instance;

    public static function getType()
    {
        if (!isset(self::$instance)) {
            self::$instance = new FloatType();
        }

        return self::$instance;
    }

    /**
     * Filter a value to be a Float
     *
     * @param  string                              $value
     * @param  null                                $annotation Unused
     * @throws \Minime\Annotations\ParserException
     * @return float
     */
    public function parse($value, $annotation = null)
    {
        if (false === ($value = filter_var($value, FILTER_VALIDATE_FLOAT))) {
            throw new ParserException("Raw value must be float. Invalid value '{$value}' given.");
        }

        return $value;
    }

}
