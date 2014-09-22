<?php

namespace Minime\Annotations\Interfaces;

/**
 * Interface for annotations Cache
 *
 * @package Annotations
 * @api
 */
interface CacheInterface
{
    /**
     * Generates uuid for a given docblock string
     *
     * @param  string $docblock docblock string
     * @return string uuid that maps to the given docblock
     */
    public function getKey($docblock);

    /**
     * Adds an annotation AST to cache
     *
     * @param string $key         cache entry uuid
     * @param array  $annotations annotation AST
     */
    public function set($key, array $annotations);

    /**
     * Retrieves cached annotations from docblock uuid
     *
     * @param  string $key cache entry uuid
     * @return array  cached annotation AST
     */
    public function get($key);

    /**
     * Resets cache
     */
    public function clear();
}
