<?php

namespace Minime\Annotations;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Minime\Annotations\Fixtures\AnnotationConstructInjection;

require_once __DIR__ . '/BaseTestCase.php';

/**
 */
class AnnotationsBagTest extends TestCase
{

    private $Bag;

    public function setUp() : void
    {
        $this->Bag = new AnnotationsBag(
            [
                'get' => true,
                'post' => false,
                'put' => false,
                'default' => null,
                'val.max' => 16,
                'val.min' => 6,
                'val.regex' => "/[A-z0-9\_\-]+/",
                'config.container' => 'Some\Collection',
                'config.export' => ['json', 'csv'],
                'Minime\Annotations\Fixtures\AnnotationConstructInjection' => new AnnotationConstructInjection('foo')
            ]
        );
    }

    public function testGet()
    {
        self::assertSame(false, $this->Bag->get('post'));
        self::assertInstanceOf(
            '\Minime\Annotations\Fixtures\AnnotationConstructInjection',
            $this->Bag->get('Minime\Annotations\Fixtures\AnnotationConstructInjection')
        );

        self::assertSame(false, $this->Bag->get('post', true));
        self::assertSame(null, $this->Bag->get('undefined'));
        self::assertSame(false, $this->Bag->get('undefined', false));
        self::assertSame([], $this->Bag->get('undefined', []));
    }

    public function testGetAsArray()
    {
        // single value
        self::assertSame([false], $this->Bag->getAsArray('put'));
        self::assertCount(1, $this->Bag->getAsArray('put'));

        // array value
        self::assertSame(['json', 'csv'], $this->Bag->getAsArray('config.export'));
        self::assertCount(2, $this->Bag->getAsArray('config.export'));

        // null value
        self::assertSame([null], $this->Bag->getAsArray('default'));
        self::assertCount(1, $this->Bag->getAsArray('default'));

        // this value is not set
        self::assertSame([], $this->Bag->getAsArray('foo'));
        self::assertCount(0, $this->Bag->getAsArray('foo'));
    }

    public function testArrayAccessBag()
    {
        $this->Bag = new AnnotationsBag([]);
        self::assertEquals(0, count($this->Bag));
        $this->Bag['fruit'] = 'orange';
        self::assertEquals(1, count($this->Bag));
        self::assertSame('orange', $this->Bag['fruit']);
        self::assertTrue(isset($this->Bag['fruit']));
        self::assertFalse(isset($this->Bag['cheese']));
        unset($this->Bag['fruit']);
        self::assertEquals(0, count($this->Bag));
        self::assertNull($this->Bag['fruit']);
    }

    #[Test]
    public function grep()
    {
        self::assertCount(3, $this->Bag->grep('#val#'));
        self::assertCount(2, $this->Bag->grep('#config#'));

        // grep that always matches nothing
        self::assertCount(0, $this->Bag->grep('#^$#')->toArray());

        // chained grep
        $this->assertSame(['val.max' => 16], $this->Bag->grep('#max$#')->toArray());
        $this->assertSame(['config.export' => ['json', 'csv']], $this->Bag->grep('#export$#')->toArray());

        $this->assertCount(1, $this->Bag->grep('#Minime\\\Annotations#')->toArray());
    }

    #[Test]
    public function useNamespace()
    {

        $this->assertInstanceOf(
            '\Minime\Annotations\Fixtures\AnnotationConstructInjection',
            $this->Bag->useNamespace('Minime\Annotations\Fixtures\\')->get('AnnotationConstructInjection')
        );

        $this->assertSame(
            $this->Bag->useNamespace('Minime\Annotations\Fixtures\\')->get('AnnotationConstructInjection'),
            $this->Bag->useNamespace('Minime\Annotations\Fixtures')->get('AnnotationConstructInjection')
        );

        $this->Bag = new AnnotationsBag(
            [
                'path.to.the.treasure' => 'cheers!',
                'path.to.the.cake' => 'the cake is a lie',
                'another.path.to.cake' => 'foo',
                'path.to.the.cake.another.path.to.the.cake' => 'the real cake',
            ]
        );

        $this->assertSame(
            ['treasure' => 'cheers!', 'cake' => 'the cake is a lie', 'cake.another.path.to.the.cake' => 'the real cake'],
            $this->Bag->useNamespace('path.to.the.')->toArray()
        );

        $this->assertSame(
            $this->Bag->useNamespace('path.to.the')->toArray(),
            $this->Bag->useNamespace('path.to.the.')->toArray()
        );

        // chained namespace grep
        $this->assertSame(
            ['the.treasure' => 'cheers!', 'the.cake' => 'the cake is a lie', 'the.cake.another.path.to.the.cake' => 'the real cake'],
            $this->Bag->useNamespace('path.')->useNamespace('to.')->toArray()
        );

        $this->assertSame(
            ['treasure' => 'cheers!', 'cake' => 'the cake is a lie', 'cake.another.path.to.the.cake' => 'the real cake'],
            $this->Bag->useNamespace('path.')->useNamespace('to.')->useNamespace('the.')->toArray()
        );

        $this->assertSame(
            $this->Bag->useNamespace('path.')->useNamespace('to.')->useNamespace('the.')->toArray(),
            $this->Bag->useNamespace('path')->useNamespace('to')->useNamespace('the')->toArray()
        );
    }

    #[Test]
    public function union()
    {
        $this->Bag = new AnnotationsBag(
            [
                'alpha' => 'a',
            ]
        );

        $Bag = new AnnotationsBag(
            [
                'alpha'   => 'x',
                'delta'   => 'd',
                'epsilon' => 'e',
            ]
        );

        $UnionBag = $this->Bag->union($Bag);

        $this->assertCount(3,  $UnionBag);
        $this->assertSame('a', $UnionBag->get('alpha'));
        $this->assertSame('d', $UnionBag->get('delta'));
        $this->assertSame('e', $UnionBag->get('epsilon'));

        $this->assertNotSame($this->Bag, $this->Bag->union($Bag));
    }

    public function testTraversable()
    {
        foreach ($this->Bag as $annotation => $value) {
            $this->assertEquals($value, $this->Bag->get($annotation));
        }
    }

    public function testCountable()
    {
        $this->assertCount(10, $this->Bag->toArray());
        $this->assertCount(10, $this->Bag);
    }

    public function testJsonSerializable()
    {
        $this->assertSame(json_encode($this->Bag->toArray()), json_encode($this->Bag));
    }

}
