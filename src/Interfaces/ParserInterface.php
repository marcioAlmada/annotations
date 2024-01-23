<?php

namespace Minime\Annotations\Interfaces;

/**
 * Interface for Parser
 *
 * @package Annotations
 * @api
 */
interface ParserInterface
{
    /**
     * Parses a docblock string
     *
     * @param  string $docBlock
     * @return array
     */
    public function parse($docBlock);

    /**
     * Register set of namespaces for ConcreteType to autolookup.
     *
     * @param array $namespaces
     */
    public function registerConcreteNamespaceLookup(array $namespaces);
}
