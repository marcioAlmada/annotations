<?php

namespace Minime\Annotations\Types;

use Minime\Annotations\Interfaces\TypeInterface;

class DynamicType implements TypeInterface
{

    /**
     * Parse a given undefined type value
     *
     * @param  string $value
     * @param  null   $annotation Unused
     * @return mixed
     */
    public function parse($value, $annotation = null)
    {
        if ('' === $value) return true; // implicit boolean

        $json = JsonType::jsonDecode($value);

        if (JSON_ERROR_NONE === json_last_error()) {
            return $json;
        }
        elseif (false !== ($int = filter_var($value, FILTER_VALIDATE_INT))) {
            return $int;
        }
        elseif (false !== ($float = filter_var($value, FILTER_VALIDATE_FLOAT))) {
            return $float;
        }

        return $value;
    }

}
