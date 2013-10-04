<?php

namespace Minime\Annotations;

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
                'config.export' => ['json', 'csv']
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
            ]
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
                'another.path.to.cake' => 'foo'
            ]
        );

        $this->assertSame(
            ['treasure' => 'cheers!', 'cake' => 'the cake is a lie'],
            $this->Bag->useNamespace('path.to.the')->export()
        );

        // chained namespace grep
        $this->assertSame(
            ['the.treasure' => 'cheers!', 'the.cake' => 'the cake is a lie'],
            $this->Bag->useNamespace('path')->useNamespace('to')->export()
        );
        $this->assertSame(
            ['treasure' => 'cheers!', 'cake' => 'the cake is a lie'],
            $this->Bag->useNamespace('path')->useNamespace('to')->useNamespace('the')->export()
        );
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
            ]
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
    public function getAcceptsOnlyStringKeys()
    {
        $this->Bag->get(0);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function pregAcceptsOnlyStringKeys()
    {
        $this->Bag->grep(0)->export();
    }
}
