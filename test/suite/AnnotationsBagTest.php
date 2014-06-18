<?php

namespace Minime\Annotations;

use Minime\Annotations\Fixtures\AnnotationConstructInjection;

/**
 * @group bag
 */
class AnnotationsBagTest extends \PHPUnit_Framework_TestCase
{

    private $Bag;

    private $Rules;

    public function setUp()
    {
        $this->Rules = new ParserRules;

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
            ],
            $this->Rules
        );
    }

    /**
     * @test
     * @expectedException PHPUnit_Framework_Error
     */
    public function constructMustTakeAParserRule()
    {
        new AnnotationsBag(['post' => 20]);
    }

    /**
     * @test
     * @expectedException PHPUnit_Framework_Error
     */
    public function constructAcceptsOnlyArrays()
    {
        new AnnotationsBag('', $this->Rules);
    }

    /**
     * @test
     */
    public function constructRemoveIncorrectIndex()
    {
        $this->Bag = new AnnotationsBag([0 => true, 'post' => 20], $this->Rules);
        $this->assertSame($this->Bag->export(), ['post' => 20]);
    }

    /**
     * @test
     */
    public function nullForUnsetAnnotation()
    {
        $this->assertSame(false, $this->Bag->get('post'));
        $this->assertSame(null, $this->Bag->get('bar'));
    }

    public function testArrayAccessBag()
    {
        $this->Bag = new AnnotationsBag([], $this->Rules);
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
     * @expectedException InvalidArgumentException
     */
    public function testArrayAccessInvalidSetterBag()
    {
        $this->Bag = new AnnotationsBag([], $this->Rules);
        $this->Bag[0] = 'orange';
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testArrayAccessInvalidGetterBag()
    {
        $this->Bag = new AnnotationsBag([], $this->Rules);
        $res = $this->Bag[0];
    }

    /**
     * @test
     */
    public function grep()
    {
        $this->assertCount(3, $this->Bag->grep('val'));
        $this->assertCount(2, $this->Bag->grep('config'));

        // grep that always matches nothing
        $this->assertCount(0, $this->Bag->grep('^$')->export());

        // chained grep
        $this->assertSame(['val.max' => 16], $this->Bag->grep('max$')->export());
        $this->assertSame(['config.export' => ['json', 'csv']], $this->Bag->grep('export$')->export());
    }

    /**
     * @test
     */
    public function useNamespace()
    {
        $this->Bag = new AnnotationsBag(
            [
                'path.to.the.treasure' => 'cheers!',
                'path.to.the.cake' => 'the cake is a lie',
                'another.path.to.cake' => 'foo',
                'path.to.the.cake.another.path.to.the.cake' => 'the real cake',
            ],
            $this->Rules
        );

        $this->assertSame(
            ['treasure' => 'cheers!', 'cake' => 'the cake is a lie', 'cake.another.path.to.the.cake' => 'the real cake'],
            $this->Bag->useNamespace('path.to.the')->export()
        );

        // chained namespace grep
        $this->assertSame(
            ['the.treasure' => 'cheers!', 'the.cake' => 'the cake is a lie', 'the.cake.another.path.to.the.cake' => 'the real cake'],
            $this->Bag->useNamespace('path')->useNamespace('to')->export()
        );
        $this->assertSame(
            ['treasure' => 'cheers!', 'cake' => 'the cake is a lie', 'cake.another.path.to.the.cake' => 'the real cake'],
            $this->Bag->useNamespace('path')->useNamespace('to')->useNamespace('the')->export()
        );
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @dataProvider invalidNamespaceDataProvider
     */
    public function useNamespaceWithInvalidArgument($namespace)
    {
        $this->Bag->useNamespace($namespace);
    }

    public function invalidNamespaceDataProvider()
    {
        return [
            [0],
            ['0'],
            ['val.'],
            ['.val'],
            ['val.val.'],
            ['.val.val']
        ];
    }

    /**
     * @test
     */
    public function union()
    {
        $this->Bag = new AnnotationsBag(
            [
                'alpha' => 'a',
            ],
            $this->Rules
        );

        $Bag = new AnnotationsBag(
            [
                'alpha'   => 'x',
                'delta'   => 'd',
                'epsilon' => 'e',
            ],
            $this->Rules
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

    /**
     * @test
     */
    public function isTraversable()
    {
        foreach ($this->Bag as $annotation => $value) {
            $this->assertEquals($value, $this->Bag->get($annotation));
        }
    }

    /**
     * @test
     */
    public function isCountable()
    {
        $this->assertCount(10, $this->Bag->export());
        $this->assertCount(10, $this->Bag);
    }

    /**
     * @test
     */
    public function isJsonSerializable()
    {
        $this->assertSame(json_encode($this->Bag->export()), json_encode($this->Bag));
    }

    /**
     * @test
     */
    public function getAsArray()
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

    /**
     * @test
     */
    public function concreteAnnotationSupport()
    {
        $this->assertInstanceOf(
            '\Minime\Annotations\Fixtures\AnnotationConstructInjection',
            $this->Bag->get('Minime\Annotations\Fixtures\AnnotationConstructInjection')
        );

        $this->assertCount(1, $this->Bag->grep('Minime\\\Annotations')->export());

        $this->assertInstanceOf(
            '\Minime\Annotations\Fixtures\AnnotationConstructInjection',
            $this->Bag->useNamespace('Minime\Annotations\Fixtures')->get('AnnotationConstructInjection')
        );
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function pregAcceptsOnlyStringKeys()
    {
        $this->Bag->grep(0);
    }
}
