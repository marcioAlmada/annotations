<?php

namespace Minime\Annotations\Types;

class StringType extends AbstractType
{
    /**
     * @var TypeInterface
     */
    private static $instance;

    public static function getType()
    {
        if (!isset(self::$instance)) {
            self::$instance = new StringType();
        }

        return self::$instance;
    }

    /**
     * Parse a given value as string
     *
     * @param  string $value
     * @param  null   $annotation Unused
     * @return mixed
     */
    public function parse($value, $annotation = null)
    {
        return $value;
    }

}
