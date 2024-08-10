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
        if (! apcu_exists($key)) {
            apcu_store($key, $annotations);
        }
    }

    public function get($key)
    {
        if (apcu_exists($key)) {
            return apcu_fetch($key);
        }

        return [];
    }

    public function clear()
    {
        $cache = apcu_cache_info();
        if ($cache) {
            foreach ($cache['cache_list'] as $entry) {
                if(isset($entry['info'])
                   && strpos($entry['info'], 'minime-annotations:') === 0) {
                    apcu_delete($entry['info']);
                }
            }
        }
    }
}
