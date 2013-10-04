<?php

namespace Minime\Annotations;

use \Minime\Annotations\Fixtures\AnnotationsFixture;
use \ReflectionProperty;

class ParserTest extends \PHPUnit_Framework_TestCase
{

    private $Fixture;

    public function setUp()
    {
        $this->Fixture = new AnnotationsFixture;
    }

    /**
     * @test
     */
    public function parseEmptyFixture()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'empty_fixture');
        $annotations = (new Parser($reflection->getDocComment()))->parse();
        $this->assertSame([], $annotations->export());
    }

    /**
     * @test
     */
    public function parseNullFixture()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'null_fixture');
        $annotations = (new Parser($reflection->getDocComment()))->parse();
        $this->assertSame([null, null, ''], $annotations->get('value'));
    }

    /**
     * @test
     */
    public function parseBooleanFixture()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'boolean_fixture');
        $annotations = (new Parser($reflection->getDocComment()))->parse();
        $this->assertSame([true, false, true, false, "true", "false"], $annotations->get('value'));
    }

    /**
     * @test
     */
    public function parseImplicitBooleanFixture()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'implicit_boolean_fixture');
        $annotations = (new Parser($reflection->getDocComment()))->parse();
        $this->assertSame(TRUE, $annotations->get('alpha'));
        $this->assertSame(TRUE, $annotations->get('beta'));
        $this->assertSame(TRUE, $annotations->get('gamma'));
        $this->assertSame(NULL, $annotations->get('delta'));
    }

    /**
     * @test
     */
    public function parseStringFixture()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'string_fixture');
        $annotations = (new Parser($reflection->getDocComment()))->parse();
        $this->assertSame(['abc', 'abc', '123'], $annotations->get('value'));
    }

    /**
     * @test
     */
    public function parseIntegerFixture()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'integer_fixture');
        $annotations = (new Parser($reflection->getDocComment()))->parse();
        $this->assertSame([123, 23, -23], $annotations->get('value'));
    }

    /**
     * @test
     */
    public function parseFloatFixture()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'float_fixture');
        $annotations = (new Parser($reflection->getDocComment()))->parse();
        $this->assertSame([.45, 0.45, 45., -4.5], $annotations->get('value'));
    }

    /**
     * @test
     */
    public function parseJsonFixture()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'json_fixture');
        $annotations = (new Parser($reflection->getDocComment()))->parse();
        $this->assertEquals(
            [
                ["x", "y"],
                json_decode('{"x": {"y": "z"}}'),
                json_decode('{"x": {"y": ["z", "p"]}}')
            ],
        $annotations->get('value'));
    }

    /**
     * @test
     */
    public function parseSingleValuesFixture()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'single_values_fixture');
        $annotations = (new Parser($reflection->getDocComment()))->parse();
        $this->assertEquals('foo', $annotations->get('param_a'));
        $this->assertEquals('bar', $annotations->get('param_b'));
    }

    /**
     * @test
     */
    public function parseMultipleValuesFixture()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'multiple_values_fixture');
        $annotations = (new Parser($reflection->getDocComment()))->parse();
        $this->assertEquals(['x','y','z'], $annotations->get('value'));
    }

    /**
     * @test
     */
    public function parseParseSameLineFixture()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'same_line_fixture');
        $annotations = (new Parser($reflection->getDocComment()))->parse();
        $this->assertSame(TRUE, $annotations->get('get'));
        $this->assertSame(TRUE, $annotations->get('post'));
        $this->assertSame(TRUE, $annotations->get('ajax'));
        $this->assertSame(TRUE, $annotations->get('alpha'));
        $this->assertSame(TRUE, $annotations->get('beta'));
        $this->assertSame(TRUE, $annotations->get('gamma'));
        $this->assertSame(NULL, $annotations->get('undefined'));
    }

    /**
     * @test
     */
    public function namespacedAnnotations()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'namespaced_fixture');
        $annotations = (new Parser($reflection->getDocComment()))->parse();
        
        $this->assertSame('cheers!', $annotations->get('path.to.the.treasure'));
        $this->assertSame('the cake is a lie', $annotations->get('path.to.the.cake'));
        $this->assertSame('foo', $annotations->get('another.path.to.cake'));
    }

    /**
     * @test
     */
    public function parseStrongTypedFixture()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'strong_typed_fixture');
        $annotations = (new Parser($reflection->getDocComment()))->parse();
        $declarations = $annotations->get('value');
        $this->assertNotEmpty($declarations);
        $this->assertSame([ "abc", "45", 45, -45, .45, 0.45, 45.0, -4.5, 4. ], $declarations);
    }

    /**
     * @test
     * @expectedException Minime\Annotations\ParserException
     */
    public function badJSONValue()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'bad_json_fixture');
        $annotations = (new Parser($reflection->getDocComment()))->parse();
    }

    /**
     * @test
     */
    public function tolerateUnrecognizedTypes()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'non_recognized_type_fixture');
        $annotations = (new Parser($reflection->getDocComment()))->parse();
        $this->assertEquals("footype Tolerate me. DockBlocks can't be evaluated rigidly.", $annotations->get('value'));
    }

    /**
     * @test
     * @expectedException Minime\Annotations\ParserException
     */
    public function badIntegerValue()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'bad_integer_fixture');
        $annotations = (new Parser($reflection->getDocComment()))->parse();
    }

    /**
     * @test
     * @expectedException Minime\Annotations\ParserException
     */
    public function badFloatValue()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'bad_float_fixture');
        $annotations = (new Parser($reflection->getDocComment()))->parse();
    }

}