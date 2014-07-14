<?php

namespace Minime\Annotations;

use Minime\Annotations\Interfaces\ParserInterface;

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
    const TOKEN_ANNOTATION_IDENTIFIER = '@';

    const TOKEN_ANNOTATION_NAME = '[a-zA-Z\_\-\\\][a-zA-Z0-9\_\-\.\\\]*';

    /**
     * The parsable type in a given docblock
     * declared in a ['token' => 'symbol'] associative array
     *
     * @var array
     */
    protected $types = [
        'Integer'  => 'integer',
        'String'   => 'string',
        'Float'    => 'float',
        'Json'     => 'json',
        'Concrete' => '->'
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
     */
    public function __construct()
    {
        $this->types_pattern = '/^('.implode('|', $this->types).')(\s+)/';
        $this->data_pattern = '/(?<=\\'. self::TOKEN_ANNOTATION_IDENTIFIER .')('
            . self::TOKEN_ANNOTATION_NAME
            .')(((?!\s\\'. self::TOKEN_ANNOTATION_IDENTIFIER .').)*)/s';
    }

    /**
     * Parse a given docblock
     *
     * @param  string $docBlock
     * @return array
     */
    public function parse($docBlock)
    {
        $docBlock = preg_replace('/^\s*\*\s{0,1}|\/\*{1,2}|\s*\*\//m', '', $docBlock);

        $annotations = $this->parseAnnotations($docBlock);
        foreach ($annotations as &$value) {
            if (1 == count($value)) {
                $value = $value[0];
            }
        }

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
            $annotations[ $this->sanitizeKey($found[1][$key]) ][] = $this->parseValue($value, $found[1][$key]);
        }

        return $annotations;
    }

    /**
     * Parse a single annotation value
     *
     * @param  string $value
     * @param  string $key
     * @return mixed
     */
    protected function parseValue($value, $key = null)
    {
        $value = trim($value);
        if ('' === $value) { // implicit boolean

            return true;
        }
        $type = 'Dynamic';
        if (preg_match($this->types_pattern, $value, $found)) { // strong typed
            $type = $found[1];
            $value = trim(substr($value, strlen($type)));
            if (in_array($type, $this->types)) {
                $type = array_search($type, $this->types);
            }
        }
        $typeParser = "Minime\\Annotations\\Types\\". $type;

        return (new $typeParser)->parse($value, $key);
    }

    /**
     * Makes `@\My\Namespaced\Class` equivalent of `@My\Namespaced\Class`
     * @param  string $key
     * @return string
     */
    protected function sanitizeKey($key)
    {
        if (0 === strpos($key, '\\')) {
            $key = substr($key, 1);
        }

        return $key;
    }
}
