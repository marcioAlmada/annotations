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

    protected $identifier;

    protected $identifier_length;

    protected $pattern;

    /**
     * Source Setter
     * @param string $source the string to be parse
     *
     * @return self
     *
     * @throws InvalidArgumentException If $source type is not a string
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

    public function setIdentifier($identifier)
    {
        if (! is_string($identifier)) {
            throw new InvalidArgumentException('Submitted parameters must be strings');
        }

        $this->identifier = $identifier;
        $this->identifier_length = strlen($identifier);

        return $this;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function setPattern($pattern)
    {
        if (! is_string($pattern)) {
            throw new InvalidArgumentException('Submitted parameters must be strings');
        }

        $this->pattern = $pattern;

        return $this;
    }

    public function getPattern()
    {
        return $this->pattern;
    }

    public function fetchVariableName()
    {
        $key = $this->scan($this->pattern);
        if ($key) {
            return substr($key, $this->identifier_length);
        }

        return false;
    }

    /**
     * Is the current value to extract is an implicit boolean
     * @param string $identifier
     *
     * @return boolean
     */
    public function isImplicitBoolean()
    {
        return '' == $this->peek() || $this->check('/\\'.$this->identifier.'/');
    }
}
