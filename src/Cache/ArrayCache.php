<?php

namespace Minime\Annotations\Cache;

use Minime\Annotations\Interfaces\CacheInterface;

/**
 * A memory storage cache implementation
 *
 * @package Minime\Annotations
 */
class ArrayCache implements CacheInterface
{
    /**
     * Cached annotations
     *
     * @var array
     */
    protected $annotations = [];

    public function getKey($docblock)
    {
        return md5($docblock);
    }

    public function set($key, array $annotations)
    {
        if (! array_key_exists($key, $this->annotations)) {
            $this->annotations[$key] = $annotations;
        }
    }

    public function get($key)
    {
        if (array_key_exists($key, $this->annotations)) {
            return $this->annotations[$key];
        }

        return [];
    }

    public function clear()
    {
        $this->annotations = [];
    }

}
