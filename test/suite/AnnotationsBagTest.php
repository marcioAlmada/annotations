<?php

namespace Minime\Annotations;

use Minime\Annotations\Fixtures\AnnotationConstructInjection;

/**
 * @group bag
 */
class AnnotationsBagTest extends \PHPUnit_Framework_TestCase
{

    private $Bag;

    public function setUp()
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

    /**
     * @test
     * @expectedException PHPUnit_Framework_Error
     */
    public function constructAcceptsOnlyArrays()
    {
        new AnnotationsBag('');
    }

    public function testGet()
    {
        $this->assertSame(false, $this->Bag->get('post'));
        $this->assertSame(null, $this->Bag->get('bar'));
        $this->assertInstanceOf(
            '\Minime\Annotations\Fixtures\AnnotationConstructInjection',
            $this->Bag->get('Minime\Annotations\Fixtures\AnnotationConstructInjection')
        );
    }

    public function testGetAsArray()
    {
        // single value
        $this->assertSame([false], $this->Bag->getAsArray('put'));
        $this->assertCount(1, $this->Bag->getAsArray('put'));

        // array value
        $this->assertSame(['json', 'csv'], $this->Bag->getAsArray('config.export'));
        $this->assertCount(2, $this->Bag->getAsArray('config.export'));

        // null value
        $this->assertSame([null], $this->Bag->getAsArray('default'));
        $this->assertCount(1, $this->Bag->getAsArray('default'));

        // this value is not set
        $this->assertSame([], $this->Bag->getAsArray('foo'));
        $this->assertCount(0, $this->Bag->getAsArray('foo'));
    }

    public function testArrayAccessBag()
    {
        $this->Bag = new AnnotationsBag([]);
        $this->assertEquals(0, count($this->Bag));
        $this->Bag['fruit'] = 'orange';
        $this->assertEquals(1, count($this->Bag));
        $this->assertSame('orange', $this->Bag['fruit']);
        $this->assertTrue(isset($this->Bag['fruit']));
        $this->assertFalse(isset($this->Bag['cheese']));
        unset($this->Bag['fruit']);
        $this->assertEquals(0, count($this->Bag));
        $this->assertNull($this->Bag['fruit']);
    }

    /**
     * @test
     */
    public function grep()
    {
        $this->assertCount(3, $this->Bag->grep('#val#'));
        $this->assertCount(2, $this->Bag->grep('#config#'));

        // grep that always matches nothing
        $this->assertCount(0, $this->Bag->grep('#^$#')->toArray());

        // chained grep
        $this->assertSame(['val.max' => 16], $this->Bag->grep('#max$#')->toArray());
        $this->assertSame(['config.export' => ['json', 'csv']], $this->Bag->grep('#export$#')->toArray());

        $this->assertCount(1, $this->Bag->grep('#Minime\\\Annotations#')->toArray());
    }

    /**
     * @test
     */
    public function useNamespace()
    {

        $this->assertInstanceOf(
            '\Minime\Annotations\Fixtures\AnnotationConstructInjection',
            $this->Bag->useNamespace('Minime\Annotations\Fixtures\\')->get('AnnotationConstructInjection')
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

        // chained namespace grep
        $this->assertSame(
            ['the.treasure' => 'cheers!', 'the.cake' => 'the cake is a lie', 'the.cake.another.path.to.the.cake' => 'the real cake'],
            $this->Bag->useNamespace('path.')->useNamespace('to.')->toArray()
        );

        $this->assertSame(
            ['treasure' => 'cheers!', 'cake' => 'the cake is a lie', 'cake.another.path.to.the.cake' => 'the real cake'],
            $this->Bag->useNamespace('path.')->useNamespace('to.')->useNamespace('the.')->toArray()
        );
    }

    /**
     * @test
     */
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

    /**
     * @test
     * @expectedException PHPUnit_Framework_Error
     */
    public function unionAcceptsOnlyAnnotationsBag()
    {
        $this->Bag->union(0);
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
