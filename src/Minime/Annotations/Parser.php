<?php

namespace Minime\Annotations;

use StrScan\StringScanner;

class Parser
{
    /**
     * The Doc block to parse
     * @var string
     */
    private $raw_doc_block;

    /**
     * Parser constructor
     * @param string $raw_doc_block  the doc block to parse
     */
    public function __construct($raw_doc_block)
    {
        $this->raw_doc_block = $raw_doc_block;
    }

    /**
     * Parse a given docblock
     * @return AnnotationsBag an AnnotationBag Object
     */
    public function parse()
    {
        $parameters = [];
        $lines = array_map("rtrim", explode("\n", $this->raw_doc_block));
        foreach ($lines as $line) {
            $tokenizer = new StringScanner($line);
            $tokenizer->skip('/\s+\*\s+/');
            while (! $tokenizer->hasTerminated()) {
                $key = $tokenizer->scan('/\@[A-z0-9\_\-]+/');
                if (! $key) { // next line when no annotation is found
                    $tokenizer->terminate();
                    continue;
                }

                $key = str_replace('@', '', $key);
                $tokenizer->skip('/\s+/');
                if ('' == $tokenizer->peek() || $tokenizer->check('/\@/')) { // if implicit boolean
                    $parameters[$key] = true;
                    continue;
                }

                $type = 'string';
                if ($tokenizer->check('/(string|integer|float|json)/')) { //if strong typed
                    $type = $tokenizer->scan('/\w+/');
                    $tokenizer->skip('/\s+/');
                }
                $value = $tokenizer->getRemainder();
                $parameters[$key][] = Parser::parseValue($value, $type);
            }
        }

        $parameters = array_map(
            function ($value) {
                if (is_array($value) && 1 == count($value)) {
                    $value = $value[0];
                }
                return $value;
            },
            $parameters
        );

        return new AnnotationsBag($parameters);
    }

    /**
     * Parse a given value against a specific type
     * @param  string $value
     * @param  string $type  the type to parse the value against
     *
     * @throws ParserException If the type is not recognized
     * 
     * @return scalar|object
     */
    private static function parseValue($value, $type = 'string')
    {
        $method = 'parse'.ucfirst(strtolower($type));
        if (! method_exists('Parser', $method)) {
            throw new ParserException("Invalid Type '{$type}' unknown OR no yet implemented.");
        }
        return Parser::{$method}($value);
    }

    /**
     * Parse a given value
     * @param  string $value
     * 
     * @return scalar|object
     */
    private static function parseString($value)
    {
        if (! isset($value) || 'null' == $value || 'NULL' == $value) {
            return null;
        }
        $json = json_decode($value);
        if (JSON_ERROR_NONE == json_last_error()) {
            return $json;
        }
        return $value;
    }

    /**
     * Filter a value to be an Integer
     * @param  string $value
     *
     * @throws ParserException If $value is not an integer
     * 
     * @return integer
     */
    private static function parseInteger($value)
    {
        $value = filter_var($value, FILTER_VALIDATE_INT);
        if (false === $value) {
            throw new ParserException("Raw value must be integer. Invalid value '{$value}' given.");
        }
        return $value;
    }

    /**
     * Filter a value to be a Float
     * @param  string $value
     *
     * @throws ParserException If $value is not a float
     * 
     * @return float
     */
    private static function parseFloat($value)
    {
        $value = filter_var($value, FILTER_VALIDATE_FLOAT);
        if (false === $value) {
            throw new ParserException("Raw value must be float. Invalid value '{$value}' given.");
        }
        return $value;
    }

    /**
     * Filter a value to be a Json
     * @param  string $value
     *
     * @throws ParserException If $value is not a Json
     * 
     * @return scalar|object
     */
    private static function parseJson($value)
    {
        $json = json_decode($value);
        $error = json_last_error();
        if (JSON_ERROR_NONE != $error) {
            throw new ParserException("Invalid JSON string supplied.");
        }
        return $json;
    }
}
