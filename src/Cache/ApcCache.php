<?php

namespace Minime\Annotations\Cache;

use Minime\Annotations\Interfaces\CacheInterface;

/**
 * Apc cache storage implementation
 *
 * @package Minime\Annotations
 * @author paolo.fagni@gmail.com
 */
class ApcCache implements CacheInterface
{
    /**
     * Cached annotations
     *
     * @var array
     */
    protected $annotations = [];

    public function getKey($docblock)
    {
        return 'minime-annotations:' . md5($docblock);
    }

    public function set($key, array $annotations)
    {
        if (! apc_exists($key)) {
            apc_store($key, $annotations);
        }
    }

    public function get($key)
    {
        if (apc_exists($key)) {
            return apc_fetch($key);
        }

        return [];
    }

    public function clear()
    {
        $cache = apc_cache_info('user');
        foreach ($cache['cache_list'] as $entry) {
            if(isset($entry['info'])
               && strpos($entry['info'], 'minime-annotations:') === 0) {
                apc_delete($entry['info']);
            }
        }
    }
}
