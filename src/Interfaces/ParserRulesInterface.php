<?php

namespace Minime\Annotations\Interfaces;

/**
 * Interface for ParserRules
 *
 * @package Annotations
 *
 */
interface ParserRulesInterface
{
    /**
     * Tells if a string validate the annotation name regular expression
     *
     * @param string $key
     *
     * @return boolean
     *
     */
    public function isKeyValid($key);

    /**
     * Tells if a string validate the namespace regular expression
     *
     * @param string $key
     *
     * @return boolean
     *
     */
    public function isNamespaceValid($key);

    /**
     * Get the annotation name regular expression
     *
     * @return string
     *
     */
    public function getAnnotationNameRegex();

    /**
     * Get the annotation identifier string
     *
     * @return string
     *
     */
    public function getAnnotationIdentifier();

    /**
     * Get the namespace identifier string
     * @return string
     */
    public function getNamespaceIdentifier();

    /**
     * Return the namespace regular expression
     * @return string
     */
    public function getNamespaceRegex();

    /**
     * Deals with special cases involving annotation keys
     * @param  string $key
     * @return string
     */
    public function sanitizeKey($key);
}
