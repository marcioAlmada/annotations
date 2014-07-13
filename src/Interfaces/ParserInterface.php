<?php

namespace Minime\Annotations\Interfaces;

/**
 *
 * Interface for Parser
 *
 * @package Annotations
 *
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
}
