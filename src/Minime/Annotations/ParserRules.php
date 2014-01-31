<?php

namespace Minime\Annotations;

use Minime\Annotations\Interfaces\ParserRulesInterface;

/**
 * An annotation parser rules
 *
 * @package Annotations
 * @author  Márcio Almada and the Minime Community
 * @license MIT
 *
 */
class ParserRules implements ParserRulesInterface
{

    /**
     * Annotation Identifier
     * @var string
     */
    private $annotationIdentifier = '@';

    /**
     * Annotation Name Regular Expression
     * @var string
     */
    private $annotationNameRegex = '[a-zA-Z\_\-][a-zA-Z0-9\_\-\.\\\]*';

    /**
     * Namespace identifier
     * @var string
     */
    private $namespaceIdentifier = '.';

    /**
     * Namespace regex
     * @var string
     */
    private $namespaceRegex = '[a-zA-Z\_\-]+(\.([a-zA-Z0-9\_\-\\\]+))*';

    /**
     * Valid a key according to internal rules
     * @param string $key
     *
     * @return boolean
     */
    public function isKeyValid($key)
    {
        return preg_match('/^'.$this->getAnnotationNameRegex().'$/', $key);
    }

    /**
     * Valid a key according to internal rules
     * @param string $key
     *
     * @return boolean
     */
    public function isNamespaceValid($key)
    {
        return preg_match('/^'.$this->getNamespaceRegex().'$/', $key);
    }

    /**
     * Return the AnnotationName regular expression
     * @return string
     */
    public function getAnnotationNameRegex()
    {
        return $this->annotationNameRegex;
    }

    /**
     * Return the AnnotationIdentifier
     * @return string
     */
    public function getAnnotationIdentifier()
    {
        return $this->annotationIdentifier;
    }

    /**
     * Return the NamespaceIdentifier
     * @return string
     */
    public function getNamespaceIdentifier()
    {
        return $this->namespaceIdentifier;
    }

    /**
     * Return the Namespace regular expression
     * @return string
     */
    public function getNamespaceRegex()
    {
        return $this->namespaceRegex;
    }
}
