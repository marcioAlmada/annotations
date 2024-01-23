<?php

namespace Minime\Annotations;

/**
 * An Annotations parser
 *
 * @package Annotations
 * @author  Márcio Almada and the Minime Community
 * @license MIT
 *
 */
class Parser extends DynamicParser
{
    /**
     * The lexer table of parsable types in a given docblock
     * declared in a ['token' => 'symbol'] associative array
     *
     * @var array
     */
    protected $types = [
        'integerType'  => 'integer',
        'stringType'   => 'string',
        'floatType'    => 'float',
        'jsonType'     => 'json',
        'concreteType' => '->'
    ];

    /**
     * A fallback type if no strong type declaration found.
     *
     * @var string
     */
    protected $typeFallback = 'dynamicType';

    /**
    * The regex equivalent of $types
    *
    * @var string
    */
    protected $typesPattern;

    /**
     * @var TypeContainer
     */
    private $typeContainer;

    /**
     * Parser constructor
     *
     */
    public function __construct()
    {
        $this->typeContainer = new TypeContainer();
        $this->typeContainer->add($this->typeFallback);

        foreach ($this->types as $key => $value) {
            $this->typeContainer->add($key);
        }

        $this->buildTypesPattern();
        parent::__construct();
    }

    public function registerType($class, $token)
    {
        $this->types[$class] = $token;
        $this->typeContainer->add($class);
        $this->buildTypesPattern();
    }

    public function unregisterType($class)
    {
        unset($this->types[$class]);
        $this->typeContainer->remove($class);
        $this->buildTypesPattern();
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
        $type = $this->typeFallback;

        if (preg_match($this->typesPattern, $value, $found)) { // strong typed
            $type = $found[1];
            $value = trim(substr($value, strlen($type)));

            if (in_array($type, $this->types)) {
                $type = array_search($type, $this->types);
            }
        }

        return $this->typeContainer->{$type}->parse($value, $key);
    }

    /**
     * Makes `@\My\Namespaced\Class` equivalent of `@My\Namespaced\Class`
     *
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

    public function registerConcreteNamespaceLookup(array $namespaces) {
        $this->typeContainer->concreteType->setNamespaces($namespaces);
    }
}
