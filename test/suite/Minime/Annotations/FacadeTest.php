<?php

namespace Minime\Annotations;

use \Minime\Annotations\Fixtures\AnnotationsFixture;

class FacadeTest extends \PHPUnit_Framework_TestCase
{
    
    protected $Fixture;

    public function setUp()
    {
        $this->Fixture = new AnnotationsFixture;
    }

    /**
     * @test
     */
    public function getClassAnnotations()
    {
        $annotations = Facade::getClassAnnotations($this->Fixture);
        $this->assertSame(true, $annotations->get('post'));
    }

    /**
     * @test
     */
    public function getPropertyAnnotations()
    {
        $annotations = Facade::getPropertyAnnotations($this->Fixture, 'same_line_fixture');
        $this->assertSame(true, $annotations->get('post'));
    }

    /**
     * @test
     */
    public function getMethodAnnotations()
    {
        $annotations = Facade::getMethodAnnotations($this->Fixture, 'method_fixture');
        $this->assertSame(true, $annotations->get('post'));
    }

    /**
     * @test
     * @expectedException \ReflectionException
     */
    public function exceptionWhenInspectingUndefinedClass()
    {
        $annotations = Facade::getClassAnnotations('Some\Undefined\Class');
    }

    /**
     * @test
     * @expectedException \ReflectionException
     */
    public function exceptionWhenInspectingUndefinedProperty()
    {
        $annotations = Facade::getPropertyAnnotations($this->Fixture, 'undefined_property');
    }

    /**
     * @test
     * @expectedException \ReflectionException
     */
    public function exceptionWhenInspectingUndefinedMethod()
    {
        $annotations = Facade::getMethodAnnotations($this->Fixture, 'undefined_method');
    }
}