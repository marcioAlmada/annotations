<?php

namespace Minime\Annotations;

use InvalidArgumentException;
use StrScan\StringScanner;

/**
 * Represents a specialized StrScan\StringScanner. This class contains top level
 * scan methods to facilitate annotations parsing.
 *
 * @package Annotations
 * @author  Márcio Almada and the Minime Community
 * @license MIT
 *
 */
class Scanner extends StringScanner
{

    /**
     * Variable Identifier
     *
     * @var string
     */
    protected $identifier;

    /**
     * Variable Identifier length
     *
     * @var integer
     */
    protected $identifier_length;

    /**
     * Variable Regular expression pattern
     *
     * @var string
     */
    protected $pattern;

    /**
     * The constructor
     *
     * @param string $identifier variable identifier
     * @param string $pattern    variable regular expression pattern
     * @param string $source     single line string to be parsed
     */
    public function __construct($identifier = '', $pattern = '', $source = '')
    {
        $this->setIdentifier($identifier);
        $this->setPattern($pattern);
        parent::__construct($source);
    }

    /**
     * Source Setter
     *
     * @param string $source the string to be parse
     *
     * @return self
     *
     * @throws InvalidArgumentException If $source is not a string
     */
    public function setSource($source)
    {
        if (! is_string($source)) {
            throw new InvalidArgumentException('string expected, got `' . gettype($source) . '` instead');
        }
        mb_internal_encoding('UTF-8');
        $this->source = $source;
        $this->length = mb_strlen($source);
        $this->head = 0;
        $this->last = 0;
        $this->captures = [];
        $this->match = null;

        return $this->reset();
    }

    /**
     * Scanner Variable Identifier setter
     *
     * @param string $identifier
     *
     * @return self
     *
     * @throws InvalidArgumentException If $identifier is not a string
     */
    public function setIdentifier($identifier)
    {
        if (! is_string($identifier)) {
            throw new InvalidArgumentException('Submitted parameters must be strings');
        }

        $this->identifier = $identifier;
        $this->identifier_length = strlen($identifier);

        return $this;
    }

    /**
     * Identifier property getter
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Scanner Variable Pattern setter
     *
     * @param string $pattern
     *
     * @return self
     *
     * @throws InvalidArgumentException If $identifier is not a string
     */
    public function setPattern($pattern)
    {
        if (! is_string($pattern)) {
            throw new InvalidArgumentException('Submitted parameters must be strings');
        }

        $this->pattern = $pattern;

        return $this;
    }

    /**
     * Pattern property getter
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * fetch the next available variable name
     *
     * @return string
     */
    public function fetchVariableName()
    {
        $key = $this->scan($this->pattern);
        if ($key) {
            return substr($key, $this->identifier_length);
        }

        return null;
    }

    /**
     * Is the current value to extract is an implicit boolean
     *
     * @param string $identifier
     *
     * @return boolean
     */
    public function isImplicitBoolean()
    {
        return '' == $this->peek() || $this->check('/\\'.$this->identifier.'/');
    }
}
