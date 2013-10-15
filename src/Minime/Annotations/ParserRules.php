<?php

namespace Minime\Annotations;

use Minime\Annotations\Interfaces\ParserRulesInterface;

/**
 *
 * An annotation parser rules
 *
 * @package Annotations
 *
 */
class ParserRules implements ParserRulesInterface
{

    /**
     * Annotation Name Regular Expression
     * @var string
     */
    private $regexAnnotationName = '[a-zA-Z\_][a-zA-Z0-9\_\-\.]*';

    /**
     * Annotation Identifier
     * @var string
     */
    private $annotationIdentifier = '@';

    /**
     * Valid a key according to internal rules
     * @param string $key
     *
     * @return boolean
     */
    public function isValidKey($key)
    {
        return preg_match('/'.$this->regexAnnotationName.'/', $key);
    }

    /**
     * Return the AnnotationName regular expression
     * @return string
     */
    public function getRegexAnnotationName()
    {
        return $this->regexAnnotationName;
    }

    /**
     * Return the AnnotationIdentifier
     * @return string
     */
    public function getAnnotationIdentifier()
    {
        return $this->annotationIdentifier;
    }
}
