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
        '\Minime\Annotations\Types\Integer'  => 'integer',
        '\Minime\Annotations\Types\String'   => 'string',
        '\Minime\Annotations\Types\Float'    => 'float',
        '\Minime\Annotations\Types\Json'     => 'json',
        '\Minime\Annotations\Types\Concrete' => '->'
    ];

    /**
    * The regex equivalent of $types
    *
    * @var string
    */
    protected $typesPattern;

    /**
     * The regex to extract data from a single line
     *
     * @var string
     */
    protected $dataPattern;

    /**
     * Parser constructor
     *
     */
    public function __construct()
    {
        $this->buildTypesPattern();
        $this->dataPattern = '/(?<=\\'. self::TOKEN_ANNOTATION_IDENTIFIER .')('
            . self::TOKEN_ANNOTATION_NAME
            .')(((?!\s\\'. self::TOKEN_ANNOTATION_IDENTIFIER .').)*)/s';
    }

    public function registerType($class, $token)
    {
        $this->types[$class] = $token;
        $this->buildTypesPattern();
    }

    public function unregisterType($class)
    {
        unset($this->types[$class]);
        $this->buildTypesPattern();
    }

    /**
     * Parse a given docblock
     *
     * @param  string $docblock
     * @return array
     */
    public function parse($docblock)
    {
        $docblock = $this->getDocblockTagsSection($docblock);
        $annotations = $this->parseAnnotations($docblock);
        foreach ($annotations as &$value) {
            if (1 == count($value)) {
                $value = $value[0];
            }
        }

        return $annotations;
    }

    /**
     * Filters docblock tags section, removing unwanted long and short descriptions
     *
     * @param  string $docblock A docblok string without delimiters
     * @return string Tag section from given docblock
     */
    protected function getDocblockTagsSection($docblock)
    {
        $docblock = $this->sanitizeDocblock($docblock);
        preg_match('/^\s*\\'.self::TOKEN_ANNOTATION_IDENTIFIER.'/m', $docblock, $matches, PREG_OFFSET_CAPTURE);

        // return found docblock tag section or empty string
        return isset($matches[0]) ? substr($docblock, $matches[0][1]) : '';
    }

    /**
     * Filters docblock delimiters
     *
     * @param  string $docblock A raw docblok string
     * @return string A docblok string without delimiters
     */
    protected function sanitizeDocblock($docblock)
    {
        return preg_replace('/^\s*\*\s{0,1}|\/\*{1,2}|\s*\*\//m', '', $docblock);
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
        preg_match_all($this->dataPattern, $str, $found);
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
        $type = '\Minime\\Annotations\\Types\\Dynamic';
        if (preg_match($this->typesPattern, $value, $found)) { // strong typed
            $type = $found[1];
            $value = trim(substr($value, strlen($type)));
            if (in_array($type, $this->types)) {
                $type = array_search($type, $this->types);
            }
        }

        return (new $type)->parse($value, $key);
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

    protected function buildTypesPattern()
    {
        $this->typesPattern = '/^('.implode('|', $this->types).')(\s+)/';
    }
}
