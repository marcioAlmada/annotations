<?php

namespace Minime\Annotations;

class AnnotationsBagTest extends \PHPUnit_Framework_TestCase
{

    private $Bag;

    private $rules;

    public function setUp()
    {
        $this->rules = new ParserRules;

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
                'config.export' => ['json', 'csv']
            ],
            $this->rules
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
        new AnnotationsBag('', $this->rules);
    }

    public function constructRemoveUncorrectIndex()
    {
        $res = new AnnotationsBag([0 => true, 'post' => 20], $this->rules);
        $this->assertSame($res->export(), ['post' => 20]);
    }

    /**
     * @test
     */
    public function nullForUnsetAnnotation()
    {
        $this->assertSame(false, $this->Bag->get('post'));
        $this->assertSame(null, $this->Bag->get('bar'));
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
    public function grepNamespace()
    {
        $this->Bag = new AnnotationsBag(
            [
                'path.to.the.treasure' => 'cheers!',
                'path.to.the.cake' => 'the cake is a lie',
                'another.path.to.cake' => 'foo'
            ],
            $this->rules
        );

        $this->assertSame(
            ['treasure' => 'cheers!', 'cake' => 'the cake is a lie'],
            $this->Bag->grepNamespace('path.to.the')->export()
        );

        // chained namespace grep
        $this->assertSame(
            ['the.treasure' => 'cheers!', 'the.cake' => 'the cake is a lie'],
            $this->Bag->grepNamespace('path')->grepNamespace('to')->export()
        );
        $this->assertSame(
            ['treasure' => 'cheers!', 'cake' => 'the cake is a lie'],
            $this->Bag->grepNamespace('path')->grepNamespace('to')->grepNamespace('the')->export()
        );
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
            $this->rules
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
     */
    public function useNamespaceWithInvalidArgument()
    {
        $this->Bag->useNamespace(0);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function useNamespaceWithInvalidArgument2()
    {
        $this->Bag->useNamespace('0');
    }

    /**
     * @test     
     */
    public function merge()
    {
        $this->Bag = new AnnotationsBag(
            [
                'alpha' => 'a',
                'beta'  => 'b',
                'gama'  => 'g'
            ],
            $this->rules
        );

        $DefaultBag = new AnnotationsBag(
            [
                'alpha'   => 'x',
                'beta'    => 'b',
                'gama'    => 'g',
                'delta'   => 'd',
                'epsilon' => 'e',
            ],
            $this->rules
        );

        $this->Bag->merge($DefaultBag);

        $this->assertCount(5,  $this->Bag);
        $this->assertSame('a', $this->Bag->get('alpha'));
        $this->assertSame('d', $this->Bag->get('delta'));
        $this->assertSame('e', $this->Bag->get('epsilon'));
    }

    /**
     * @test
     * @expectedException PHPUnit_Framework_Error
     */
    public function mergeAcceptsOnlyAnnotationsBag()
    {
        $this->Bag->merge(0);
    }

    /**
     * @test
     */
    public function isTraversable()
    {
        foreach ($this->Bag as $annotation => $value) {
            $this->assertEquals($value, $this->Bag->get($annotation));
        }

        $this->Bag = new AnnotationsBag(
            [
                'min' => 1,
                'max' => 2,
                'medium' => 3
            ],
            $this->rules
        );
    }

    /**
     * @test
     */
    public function isCountable()
    {
        $this->assertCount(9, $this->Bag->export());
        $this->assertCount(9, $this->Bag);
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
     * @expectedException \InvalidArgumentException
     */
    public function pregAcceptsOnlyStringKeys()
    {
        $this->Bag->grep(0);
    }
}
