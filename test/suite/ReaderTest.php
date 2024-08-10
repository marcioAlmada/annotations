<?php
namespace Minime\Annotations;

use PHPUnit\Framework\TestCase;
use Minime\Annotations\Fixtures\AnnotationsFixture;

class ReaderTest extends TestCase
{
    private $fixture;

    public function setUp(): void
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
        self::assertInstanceOf('Minime\Annotations\Interfaces\ParserInterface', $this->getReader()->getParser());
    }

    public function testGetAnnotations()
    {
        $reflectionClass = new \ReflectionClass($this->fixture);
        $annotations = $this->getReader()->getAnnotations($reflectionClass);
        self::assertTrue($annotations->get('get'));
    }

    public function testReadFunctionAnnotations()
    {
        if(! function_exists($fn = __NAMESPACE__ . '\\testfn')){
            /** @bar */ function testfn(){}
        }

        self::assertTrue($this->getReader()->getFunctionAnnotations($fn)->get('bar'));
    }

    public function testReadClosureAnnotations()
    {
        /** @foo */
        $closure = function(){};
        self::assertTrue($this->getReader()->getFunctionAnnotations($closure)->get('foo'));
    }

    public function testReadClassAnnotations()
    {
        $annotations = $this->getReader()->getClassAnnotations($this->fixture);
        self::assertTrue($annotations->get('get'));
    }

    public function testReadPropertyAnnotations()
    {
        $annotations = $this->getReader()->getPropertyAnnotations($this->fixture, 'single_values_fixture');
        self::assertEquals('foo', $annotations['param_a']);
        self::assertEquals('bar', $annotations['param_b']);
    }

    public function testReadMethodAnnotations()
    {
        $annotations = $this->getReader()->getMethodAnnotations($this->fixture, 'method_fixture');
        self::assertTrue($annotations->get('post'));
    }

    public function testReadConstantAnnotations()
    {

        // Single constant with annotation
        $annotations = $this->getReader()->getConstantAnnotations($this->fixture, "CONSTANT_FIXTURE");
        self::assertCount(2, $annotations);
        self::assertSame($annotations->get("fix"), 56);
        self::assertTrue($annotations->has("foo"));


        // Many constant under the same const declaration with annotation
        $annotations = $this->getReader()->getConstantAnnotations($this->fixture, "CONSTANT_MANY1");
        self::assertCount(1, $annotations);
        self::assertSame($annotations->get("value"), "foo");
        // second constant has comment
        $annotations = $this->getReader()->getConstantAnnotations($this->fixture, "CONSTANT_MANY2");
        self::assertCount(2, $annotations);
        self::assertSame($annotations->get("value"), "bar");
        self::assertSame($annotations->get("type"), "constant");


        // single const with no anntation
        $annotations = $this->getReader()->getConstantAnnotations($this->fixture, "CONSTANT_EMPTY");
        self::assertCount(0, $annotations);

        // Many constant under the same const declaration with no anntation
        $annotations = $this->getReader()->getConstantAnnotations($this->fixture, "CONSTANT_EMPTY_MANY1");
        self::assertCount(0, $annotations);
        // second constant
        $annotations = $this->getReader()->getConstantAnnotations($this->fixture, "CONSTANT_EMPTY_MANY2");
        self::assertCount(0, $annotations);


        // Test case with comment between doc and constant
        $annotations = $this->getReader()->getConstantAnnotations($this->fixture, "CONSTANT_WITH_COMMENT_BEFORE_DOC");
        self::assertCount(1, $annotations);
        $this->assertSame(true, $annotations->get("withComment"));


        // Test case with one comment before many constants
        $annotations = $this
            ->getReader()
            ->getConstantAnnotations($this->fixture, "CONSTANT_MANY_WITH_COMMENT_BEFORE_FIRST");
        $this->assertCount(1, $annotations);
        $this->assertSame(true, $annotations->get("hasCommentBefore"));
        // Next constant has nothing
        $annotations = $this
            ->getReader()
            ->getConstantAnnotations($this->fixture, "CONSTANT_MANY_WITH_COMMENT_BEFORE_NEXT");
        $this->assertCount(0, $annotations);


        // Test case with no doc comment but with simple comment
        $annotations = $this->getReader()->getConstantAnnotations($this->fixture, "CONSTANT_SIMPLE_COMMENT_ONLY");
        $this->assertCount(0, $annotations);


    }

    public function testCreateFromDefaults()
    {
        $this->assertInstanceOf('Minime\Annotations\Reader', Reader::createFromDefaults());
    }
}
