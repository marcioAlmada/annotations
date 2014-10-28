<?php

namespace Minime\Annotations;

use Mockery;

use Minime\Annotations\Interfaces\CacheInterface;
use Minime\Annotations\Cache\FileCache;
use Minime\Annotations\Cache\ArrayCache;
use Minime\Annotations\Cache\ApcCache;
use Minime\Annotations\Fixtures\AnnotationsFixture;

class CacheTest extends \PHPUnit_Framework_TestCase
{

    protected $fixtureClass = 'Minime\Annotations\Fixtures\AnnotationsFixture';

    public function tearDown()
    {
        Mockery::close();
    }

    public function getReader()
    {
        return new Reader(new Parser);
    }

    public function testReaderCacheInteraction()
    {
        $key = md5('/** @value foo */');
        $ast = ['value' => 'foo'];

        $cache = Mockery::mock('Minime\Annotations\Interfaces\CacheInterface', function ($mock) use ($key, $ast) {
            $mock->shouldReceive('getKey')->twice()->andReturn($key);
            $mock->shouldReceive('get')->twice()->andReturn(false, $ast, $ast);
            $mock->shouldReceive('set')->once()->with($key, $ast);
        });

        $reader = $this->getReader();
        $reader->setCache($cache);

        $this->assertSame(
            $reader->getPropertyAnnotations($this->fixtureClass, 'inline_docblock_fixture')->get('value'),
            $reader->getPropertyAnnotations($this->fixtureClass, 'inline_docblock_fixture')->get('value') // from cache
        );
    }

    public function testArrayCache(){
        $this->assertCacheHandlerWorks(new ArrayCache());
    }

    public function testFileCache(){
        $this->assertCacheHandlerWorks(new FileCache(__DIR__ . '/../../build/'));
    }

    public function testFileCacheWithDefaultStoragePath(){
        new FileCache();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp #^Cache path is not a writable/readable directory: .+\.#
     */
    public function testFileCacheWithBadStoragePath(){
        new FileCache(__DIR__ . '/invalid/path/');
    }

    /**
     * @requires extension apc
     */
    public function testApcCache(){
        $this->assertCacheHandlerWorks(new ApcCache());
    }

    public function assertCacheHandlerWorks(CacheInterface $cache)
    {
        $reader = $this->getReader();
        $reader->setCache($cache);

        $reader->getCache()->clear();

        $this->assertSame(
            $reader->getPropertyAnnotations($this->fixtureClass, 'integer_fixture')->toArray(),
            $reader->getPropertyAnnotations($this->fixtureClass, 'integer_fixture')->toArray()
        );

        $this->assertSame(
            $reader->getPropertyAnnotations($this->fixtureClass, 'float_fixture')->toArray(),
            $reader->getPropertyAnnotations($this->fixtureClass, 'float_fixture')->toArray()
        );

        $this->assertSame(
            $reader->getPropertyAnnotations($this->fixtureClass, 'namespaced_fixture')->toArray(),
            $reader->getPropertyAnnotations($this->fixtureClass, 'namespaced_fixture')->toArray()
        );

        $this->assertSame(
            $reader->getPropertyAnnotations($this->fixtureClass, 'serialize_fixture')->toArray(),
            $reader->getPropertyAnnotations($this->fixtureClass, 'serialize_fixture')->toArray()
        );

        $this->assertEquals(
            $reader->getPropertyAnnotations($this->fixtureClass, 'json_fixture')->toArray(),
            $reader->getPropertyAnnotations($this->fixtureClass, 'json_fixture')->toArray()
        );

        $this->assertEquals(
            $reader->getPropertyAnnotations($this->fixtureClass, 'strong_typed_fixture')->toArray(),
            $reader->getPropertyAnnotations($this->fixtureClass, 'strong_typed_fixture')->toArray()
        );

        $this->assertEquals(
            $reader->getPropertyAnnotations($this->fixtureClass, 'multiline_value_fixture')->toArray(),
            $reader->getPropertyAnnotations($this->fixtureClass, 'multiline_value_fixture')->toArray()
        );

        $this->assertEquals(
            $reader->getPropertyAnnotations($this->fixtureClass, 'concrete_fixture')->toArray(),
            $reader->getPropertyAnnotations($this->fixtureClass, 'concrete_fixture')->toArray()
        );

        $reader->getCache()->clear();
    }

}
