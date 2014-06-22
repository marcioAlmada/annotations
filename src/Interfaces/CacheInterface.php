<?php

namespace Minime\Annotations\Interfaces;

interface CacheInterface
{
    public function getKey($docblock);
    public function set($key, array $annotations);
    public function get($key);
    public function clear();
}
