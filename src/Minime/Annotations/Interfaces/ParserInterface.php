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
     * uses the parserRules object to parse the given string
     *
     * @return array
     *
     */
    public function parse($str);
}
