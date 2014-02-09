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
     * parses a docblock string
     *
     * @return array
     *
     */
    public function parse();
}
