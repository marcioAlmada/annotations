<?php

namespace Minime\Annotations;

use Minime\Annotations\Interfaces\ParserInterface;
use Minime\Annotations\Interfaces\ParserRulesInterface;
use Minime\Annotations\Scanner;

/**
 * An Annotations parser
 *
 * @package Annotations
 * @author  Márcio Almada and the Minime Community
 * @license MIT
 *
 */
class Parser implements ParserInterface
{

    /**
     * The Doc block to parse
     *
     * @var string
     */
    private $raw_doc_block;

    /**
     * The ParserRules object
     *
     * @var ParserRulesInterface
     */
    private $rules;

    /**
     * The parsable type in a given docblock
     *
     * @var array
     */
    protected $types = ['string', 'integer', 'float', 'json', 'eval'];

    /**
     * Parser constructor
     *
     * @param string $raw_doc_block the doc block to parse
     */
    public function __construct($raw_doc_block, ParserRulesInterface $rules)
    {
        $this->raw_doc_block = $raw_doc_block;
        $this->rules = $rules;
    }

    /**
     * Parse a given docblock
     * @return AnnotationsBag an AnnotationBag Object
     */
    public function parse()
    {
        $parameters = [];
        $identifier_pattern = $this->rules->getAnnotationIdentifier();
        $key_pattern = $this->rules->getAnnotationNameRegex();
        $line_pattern = "/(?<={$identifier_pattern}){$key_pattern}(.*?)(?=\n|\s\*\/)/s";
        $types_pattern = '/('.implode('|', $this->types).')/';
        preg_match_all($line_pattern, $this->raw_doc_block, $tree);
        foreach ($tree[0] as $line_string) {
            $line = new Scanner($line_string);
            $key = $line->scanKey($key_pattern);
            if ($line->scanImplicitBoolean($identifier_pattern)) { // if implicit boolean
                $parameters[$key][] = true;
                while (! $line->hasTerminated()) {
                    $line->skip("/\\{$identifier_pattern}/");
                    $key = $line->scanKey($key_pattern);
                    $parameters[$key][] = true;
                }
                continue;
            }
            $type = $line->scanType($types_pattern, 'dynamic');
            $remainder = $line->getRemainder();
            if ($remainder === '') {
                $parameters[$key][] = $type;
                continue;
            }
            $parameters[$key][] = self::parseValue($remainder, $type);
        }

        return self::condense($parameters);
    }

    /**
     * Filter an array to converted to string a single value array
     * @param array $parameters
     *
     * @return array
     */
    protected static function condense(array $parameters)
    {
        return array_map(function ($value) {
            if (is_array($value) && 1 == count($value)) {
                $value = $value[0];
            }

            return $value;
        }, $parameters);
    }

    /**
     * Parse a given value against a specific type
     * @param string $value
     * @param string $type  the type to parse the value against
     *
     * @throws ParserException If the type is not recognized
     *
     * @return scalar|object
     */
    protected static function parseValue($value, $type = 'string')
    {
        $method = 'parse'.ucfirst(strtolower($type));

        return self::$method($value);
    }

    /**
     * Parse a given undefined type value
     * @param string $value
     *
     * @return scalar|object
     */
    protected static function parseDynamic($value)
    {

        list($json_decoded, $error) = static::jsonDecode($value);
        if (JSON_ERROR_NONE === $error) {
            return $json_decoded;
        }

        if (static::useJsoncStrategy()) {
            if ($float = static::filterFloat($value)) {
                return $float;
            }
        }

        return $value;
    }

    /**
     * Parse a given valueas string
     * @param string $value
     *
     * @return scalar|object
     */
    protected static function parseString($value)
    {
        return $value;
    }

    /**
     * Filter a value to be an Integer
     * @param string $value
     *
     * @throws ParserException If $value is not an integer
     *
     * @return integer
     */
    protected static function parseInteger($value)
    {
        if (false === ($value = static::filterInteger($value))) {
            throw new ParserException("Raw value must be integer. Invalid value '{$value}' given.");
        }

        return $value;
    }

    protected static function filterInteger($value)
    {
        return filter_var($value, FILTER_VALIDATE_INT);
    }

    /**
     * Filter a value to be a Float
     * @param string $value
     *
     * @throws ParserException If $value is not a float
     *
     * @return float
     */
    protected static function parseFloat($value)
    {
        if (false === ($value = static::filterFloat($value))) {
            throw new ParserException("Raw value must be float. Invalid value '{$value}' given.");
        }

        return $value;
    }

    protected static function filterFloat($value)
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT);
    }

    /**
     * Filter a value to be a Json
     * @param string $value
     *
     * @throws ParserException If $value is not a Json
     *
     * @return scalar|object
     */
    protected static function parseJson($value)
    {
        list($json, $error) = static::jsonDecode($value);
        if (JSON_ERROR_NONE != $error) {
            throw new ParserException("Raw value must be a valid JSON string. Invalid value '{$value}' given.");
        }

        return $json;
    }

    protected static function jsonDecode($value)
    {
        if (static::useJsoncStrategy()) { // routine for pecl-json-c
            $json_decoded = json_decode($value, false, 512, JSON_PARSER_NOTSTRICT);
        } else { // legacy routine for superseded ext-json
            $json_decoded = json_decode($value);
        }

        return [$json_decoded, json_last_error()];
    }

    protected static function useJsoncStrategy()
    {
        return defined('JSON_PARSER_NOTSTRICT');
    }

    /**
     * Filter a value to be a PHP eval
     * @param string $value
     *
     * @throws ParserException If $value is not a valid PHP code
     *
     * @return mixed
     */
    protected static function parseEval($value)
    {
        $output = @eval("return {$value};");
        if (false === $output) {
            throw new ParserException("Raw value should be valid PHP. Invalid code '{$value}' given.");
        }

        return $output;
    }
}
