<?php

namespace Minime\Annotations\Interfaces;

/**
 *
 * Interface for ParserRules
 *
 * @package Annotations
 *
 */
interface ParserRulesInterface
{
    /**
     * Tells if a string validate the annotationName regular expression
     *
     * @param string $key
     *
     * @return boolean
     *
     */
    public function isValidKey($key);

    /**
     * Get the annotationName regular expression
     *
     * @return string
     *
     */
    public function getRegexAnnotationName();

    /**
     * Get the annotationIdentifier string
     *
     * @return string
     *
     */
    public function getAnnotationIdentifier();
}
