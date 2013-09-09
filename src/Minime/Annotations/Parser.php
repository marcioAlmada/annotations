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
                $value = $tokenizer->getRemainder();
                if ($tokenizer->check('/(string|integer|float|json)/')) { //if strong typed
                    $type = $tokenizer->scan('/\w+/');
                    $tokenizer->skip('/\s+/');
                    $value = $tokenizer->getRemainder();
                }
                $parameters[$key][] = $this->parseValue($value, $type);
            }
        }

        $parameters = $this->condense($parameters);

        return new AnnotationsBag($parameters);
    }

    /**
     * Filter parameters array to remove single value array
     * @param  array $parameters
     * 
     * @return array
     */
    private function condense(array $parameters)
    {
        foreach ($parameters as &$value) {
            if (! is_bool($value) && 1 == count($value)) {
                $value = $value[0];
            }
        }
        unset($value);
        return $parameters;
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
    private function parseValue($value, $type = 'string')
    {
        $method = 'parse'.ucfirst(strtolower($type));
        if (! method_exists($this, $method)) {
            throw new ParserException("Invalid Strong Type '{$type}' no yet implemented.");
        }
        return $this->{$method}($value);
    }

    /**
     * Parse a given value
     * @param  string $value
     * 
     * @return scalar|object
     */
    private function parseString($value)
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
    private function parseInteger($value)
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
    private function parseFloat($value)
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
    private function parseJson($value)
    {
        $json = json_decode($value);
        $error = json_last_error();
        if (JSON_ERROR_NONE != $error) {
            throw new ParserException("Invalid JSON string supplied.");
        }
        return $json;
    }
}
