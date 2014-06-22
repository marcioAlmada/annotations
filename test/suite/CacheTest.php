<?php

namespace Minime\Annotations;

use Mockery;

use Minime\Annotations\Interfaces\CacheInterface;
use Minime\Annotations\Cache\FileCache;
use Minime\Annotations\Cache\MemoryCache;
use Minime\Annotations\Fixtures\AnnotationsFixture;


class CacheTest extends \PHPUnit_Framework_TestCase
{

    protected $fixtureClass = 'Minime\Annotations\Fixtures\AnnotationsFixture';

    public function tearDown() {
        Mockery::close();
    }

    public function getReader(CacheInterface $cache) {
        return new Reader(new Parser(new ParserRules()), $cache);
    }

    public function testReaderCacheInteraction()
    {
        $key = md5('/** @value foo */');
        $ast = ['value' => 'foo'];

        $cache = Mockery::mock('Minime\Annotations\Interfaces\CacheInterface', function($mock) use ($key, $ast){
            $mock->shouldReceive('getKey')->twice()->andReturn($key);
            $mock->shouldReceive('get')->twice()->andReturn(false, $ast, $ast);
            $mock->shouldReceive('set')->once()->with($key, $ast);
        });

        $reader = $this->getReader($cache);

        $this->assertSame(
            $reader->getPropertyAnnotations($this->fixtureClass, 'inline_docblock_fixture')->get('value'),
            $reader->getPropertyAnnotations($this->fixtureClass, 'inline_docblock_fixture')->get('value') // from cache
        );
    }

}
