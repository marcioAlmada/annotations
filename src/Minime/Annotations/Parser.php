<?php

namespace Minime\Annotations;

use ReflectionClass;
use Minime\Annotations\Interfaces\ParserInterface;
use Minime\Annotations\Interfaces\ParserRulesInterface;

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
     * declared in a ['token' => 'symbol'] associative array
     *
     * @var array
     */
    protected $types = [
        'integer'  => 'integer',
        'string'   => 'string',
        'float'    => 'float',
        'json'     => 'json',
        'eval'     => 'eval',
        'concrete' => '->'
    ];

    /**
    * The regex equivalent of $types
    *
    * @var string
    */
    protected $types_pattern;

    /**
     * The regex to extract data from a single line
     *
     * @var string
     */
    protected $data_pattern;

    /**
     * Parser constructor
     *
     * @param string               $raw_doc_block the doc block to parse
     * @param ParserRulesInterface $rules
     */
    public function __construct($raw_doc_block, ParserRulesInterface $rules)
    {
        $this->raw_doc_block = preg_replace('/^\s*\*\s{0,1}|\/\*{1,2}|\s*\*\//m', '', $raw_doc_block);
        $this->types_pattern = '/^('.implode('|', $this->types).')(\s)*(\S)+/';
        $this->rules = $rules;
        $identifier = $rules->getAnnotationIdentifier();
        $this->data_pattern = '/(?<=\\'.$identifier.')('
            .$rules->getAnnotationNameRegex()
            .')((?:(?!\s\\'.$identifier.').)*)/s';
    }

    /**
     * Parse a given docblock
     *
     * @return array
     */
    public function parse()
    {
        $annotations = $this->parseAnnotations($this->raw_doc_block);
        foreach ($annotations as &$value) {
            if (1 == count($value)) {
                $value = $value[0];
            }
        }
        unset($value);

        return $annotations;
    }

    /**
     * Creates raw [annotation => value, [...]] tree
     *
     * @param  string $str
     * @return array
     */
    protected function parseAnnotations($str)
    {
        $annotations = [];
        preg_match_all($this->data_pattern, $str, $found);
        foreach ($found[2] as $key => $value) {
            $annotations[$found[1][$key]][] = $this->parseValue($value, $found[1][$key]);
        }

        return $annotations;
    }

    /**
     * Parse a single annotation value
     *
     * @param  string          $value
     * @throws ParserException If the type is not recognized
     * @return mixed
     */
    public function parseValue($value, $key = null)
    {
        $value = trim($value);
        if ('' === $value) { // implicit boolean

            return true;
        }
        $type = 'dynamic';
        if (preg_match($this->types_pattern, $value, $found)) { // strong typed
            $type = $found[1];
            $value = trim(substr($value, strlen($type)));
        }
        if (in_array($type, $this->types)) {
            $type = array_search($type, $this->types);
        }
        $method = 'parse'.ucfirst(strtolower($type));

        return self::$method($value, $key);
    }

    /**
     * Parse a given undefined type value
     *
     * @param  string $value
     * @return mixed
     */
    protected static function parseDynamic($value)
    {
        $json = static::jsonDecode($value);
        if (JSON_ERROR_NONE === json_last_error()) {
            return $json;
        } elseif (false !== ($float = filter_var($value, FILTER_VALIDATE_FLOAT))) {
            return $float;
        }

        return $value;
    }

    /**
     * Parse a given valueas string
     *
     * @param  string $value
     * @return mixed
     */
    protected static function parseString($value)
    {
        return $value;
    }

    /**
     * Filter a value to be an Integer
     *
     * @param  string          $value
     * @throws ParserException If $value is not an integer
     * @return integer
     */
    protected static function parseInteger($value)
    {
        if (false === ($value = filter_var($value, FILTER_VALIDATE_INT))) {
            throw new ParserException("Raw value must be integer. Invalid value '{$value}' given.");
        }

        return $value;
    }

    /**
     * Filter a value to be a Float
     *
     * @param  string          $value
     * @throws ParserException If $value is not a float
     * @return float
     */
    protected static function parseFloat($value)
    {
        if (false === ($value = filter_var($value, FILTER_VALIDATE_FLOAT))) {
            throw new ParserException("Raw value must be float. Invalid value '{$value}' given.");
        }

        return $value;
    }

    /**
     * Filter a value to be a Json
     *
     * @param  string          $value
     * @throws ParserException If $value is not a Json
     * @return mixed
     */
    protected static function parseJson($value)
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

    /**
     * Filter a value to be a PHP eval
     *
     * @param  string          $value
     * @throws ParserException If $value is not a valid PHP code
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

    /**
     * Process a value to be a concrete annotation
     *
     * @param  string $value json string
     * @param  string $class name of concrete annotation type (class)
     * @return object
     */
    public function parseConcrete($value, $class)
    {
        if (!class_exists($class)) {
            throw new ParserException("Concrete annotation expects {$class} to be a valid class.");
        }
        $parsed_value = static::parseJson($value);
        if (is_scalar($parsed_value)) {
            throw new ParserException("Json value for annotation({$class}) must be of type array or object.");
        } elseif ( is_array($parsed_value) ) {
            $reflect  = new ReflectionClass($class);
            $instance = $reflect->newInstanceArgs($parsed_value);
        } elseif ( is_object($parsed_value) ) {
            $instance = new $class();
            array_walk($parsed_value, function ($value, $property) use ($instance) {
                $instance->{'set'. ucfirst($property)}($value);
            });
        }

        return $instance;
    }

}
