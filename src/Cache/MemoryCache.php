<?php

namespace Minime\Annotations\Cache;

use Minime\Annotations\Interfaces\CacheInterface;

class MemoryCache implements CacheInterface
{
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

        return false;
    }

    public function clear()
    {
        $this->annotations = [];
    }

}
