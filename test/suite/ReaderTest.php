<?php
namespace Minime\Annotations;

use Minime\Annotations\Fixtures\AnnotationsFixture;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    private $fixture;

    public function setUp()
    {
        $this->fixture = new AnnotationsFixture;
    }

    /**
     * @return Reader
     */
    private function getReader()
    {
        return new Reader(new Parser);
    }

    public function testGetReader()
    {
        $this->assertInstanceOf('Minime\Annotations\Interfaces\ParserInterface', $this->getReader()->getParser());
    }

    public function testGetAnnotations()
    {
        $reflectionClass = new \ReflectionClass($this->fixture);
        $annotations = $this->getReader()->getAnnotations($reflectionClass);
        $this->assertTrue($annotations->get('get'));
    }

    public function testReadFunctionAnnotations()
    {
        if(! function_exists($fn = __NAMESPACE__ . '\\fn')){
            /** @bar */ function fn(){}
        }

        $this->assertTrue($this->getReader()->getFunctionAnnotations($fn)->get('bar'));
    }

    public function testReadClosureAnnotations()
    {
        /** @foo */
        $closure = function(){};
        $this->assertTrue($this->getReader()->getFunctionAnnotations($closure)->get('foo'));
    }

    public function testReadClassAnnotations()
    {
        $annotations = $this->getReader()->getClassAnnotations($this->fixture);
        $this->assertTrue($annotations->get('get'));
    }

    public function testReadPropertyAnnotations()
    {
        $annotations = $this->getReader()->getPropertyAnnotations($this->fixture, 'single_values_fixture');
        $this->assertEquals('foo', $annotations['param_a']);
        $this->assertEquals('bar', $annotations['param_b']);
    }

    public function testReadMethodAnnotations()
    {
        $annotations = $this->getReader()->getMethodAnnotations($this->fixture, 'method_fixture');
        $this->assertTrue($annotations->get('post'));
    }

    public function testReadConstantAnnotations()
    {

        // Single constant with annotation
        $annotations = $this->getReader()->getConstantAnnotations($this->fixture, "CONSTANT_FIXTURE");
        $this->assertCount(2, $annotations);
        $this->assertSame($annotations->get("fix"), 56);
        $this->assertTrue($annotations->has("foo"));


        // Many constant under the same const declaration with annotation
        $annotations = $this->getReader()->getConstantAnnotations($this->fixture, "CONSTANT_MANY1");
        $this->assertCount(1, $annotations);
        $this->assertSame($annotations->get("value"), "foo");

        $annotations = $this->getReader()->getConstantAnnotations($this->fixture, "CONSTANT_MANY2");
        $this->assertCount(2, $annotations);
        $this->assertSame($annotations->get("value"), "bar");
        $this->assertSame($annotations->get("type"), "constant");


        // single const with no anntation
        $annotations = $this->getReader()->getConstantAnnotations($this->fixture, "CONSTANT_EMPTY");
        $this->assertCount(0, $annotations);

        // Many constant under the same const declaration with no anntation
        $annotations = $this->getReader()->getConstantAnnotations($this->fixture, "CONSTANT_EMPTY_MANY1");
        $this->assertCount(0, $annotations);

        $annotations = $this->getReader()->getConstantAnnotations($this->fixture, "CONSTANT_EMPTY_MANY2");
        $this->assertCount(0, $annotations);

    }

    public function testCreateFromDefaults()
    {
        $this->assertInstanceOf('Minime\Annotations\Reader', Reader::createFromDefaults());
    }
}
