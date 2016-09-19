<?php

namespace Minime\Annotations\Types;

use Minime\Annotations\Interfaces\TypeInterface;
use Minime\Annotations\ParserException;

class JsonType implements TypeInterface
{

    /**
     * Filter a value to be a Json
     *
     * @param  string                              $value
     * @param  null                                $annotation Unused
     * @throws \Minime\Annotations\ParserException
     * @return mixed
     */
    public function parse($value, $annotation = null)
    {
        $json = static::jsonDecode($value);
        if (JSON_ERROR_NONE != json_last_error()) {
            throw new ParserException("Raw value must be a valid JSON string. Invalid value '{$value}' given.");
        }

        return $json;
    }

    /**
     * Wrapper fo json_decode function that keeps parser portable
     * between json-ext and pecl-json-c extensions
     *
     * @param  string $value json string
     * @return mixed
     */
    public static function jsonDecode($value)
    {
        if (defined('JSON_PARSER_NOTSTRICT')) { // pecl-json-c ext
            $decoded = json_decode($value, false, 512, JSON_PARSER_NOTSTRICT);
        } else { // json-ext
            $decoded = json_decode($value);
        }

        return $decoded;
    }

}
