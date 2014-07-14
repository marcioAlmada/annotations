<?php

namespace Minime\Annotations\Interfaces;

/**
 * Interface for Type
 *
 * @package Annotations
 */
interface TypeInterface
{
    /**
     * Parses a type
     * @param  string $value      value to be processed
     * @param  string $annotation annotation name
     * @return mixed
     */
    public function parse($value, $annotation = null);
}
