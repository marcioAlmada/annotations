<?php

/**
 * Cache benchmark
 *
 * Usage:
 *
 * php tests/benchmark/CacheBenchmark.php -r <iterations>
 *
 * Options:
 *
 * -r <integer> how many iterations each benchmark should have, default is 5000
 */

namespace Minime\Annotations;

if(extension_loaded('xdebug'))
    echo "\033[31m### XDebug extension is enabled!\033[0m You might get very inaccurate performance numbers.\n\n";

include __DIR__ . '/../../vendor/autoload.php';

// get cli options
$options = getopt('r:');
$iterations = (isset($options['r'])) ? (int) $options['r'] : 1000;

echo "### Running \033[32m",  $iterations, "\033[0m iterations for each cache handler...\n\n";

// get global start time
$start = microtime(true);

// run benchmarks
benchmark($iterations); // no cache
benchmark($iterations, new Cache\FileCache(__DIR__ . '/../../build/'));
benchmark($iterations, new Cache\MemoryCache());

// get global end time
$end = microtime(true);

echo "\n### Finished benchmark in\033[32m ",  $end - $start, "\033[0m seconds.\n";

/**
 * Runs a benchmark for a given cache handler
 *
 * @param  integer                                     $iterations how many times to iterate benchmark
 * @param  Minime\Annotations\interface\CacheInterface $cache
 */
function benchmark($iterations = 1000, Interfaces\CacheInterface $cache = null)
{
    static $id = 1;
    $startTime = microtime(true);
    $reader = new Reader(new Parser(new ParserRules()), $cache);

    if($cache) $cache->clear();

    $class = 'Minime\Annotations\Fixtures\AnnotationsFixture';
    $reflection = new \ReflectionClass($class);
    $properties = $reflection->getProperties(\ReflectionProperty::IS_PRIVATE);
    $i = 0;

    while ($i++ < $iterations) {
        array_walk($properties, function ($property) use ($reader, $class) {
            if ( false === strpos($property->getName(), 'bad_')) {
                $reader->getPropertyAnnotations($class, $property->getName());
            }
        });
        echo "\rRunning iteration number", "\033[32m ", $i, " \033[0m";
    }
    echo "\033[1A";
    $endTime = microtime(true);
    $msg = "{$id}) Read took \033[32m" . ($endTime - $startTime) . " seconds\033[0m";
    if($cache) $msg .= " with \033[33m\\" .get_class($cache) . "\033[0m";
    else $msg .= " without cache!";
    echo "\n", $msg, "\n";

    $id++;
}
