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
     * @param ParserRulesInterface $parserRules
     */
    public function __construct(ParserRulesInterface $parserRules);

    /**
     * Parses a docblock string
     *
     * @param string $docBlock
     * @return array
     */
    public function parse($docBlock);

    /**
     * @return ParserRulesInterface
     */
    public function getRules();
}
