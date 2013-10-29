<?php

namespace Minime\Annotations;

use \Minime\Annotations\Fixtures\AnnotationsFixture;
use \ReflectionProperty;

class ParserTest extends \PHPUnit_Framework_TestCase
{

    private $Fixture;

    private $rules;

    public function setUp()
    {
        $this->rules = new ParserRules;
        $this->Fixture = new AnnotationsFixture;
    }

    /**
     * @test
     * @expectedException PHPUnit_Framework_Error
     */
    public function parserRequiredAParserRules()
    {
        new Parser('hellow world!');
    }

    /**
     * @test
     */
    public function parseEmptyFixture()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'empty_fixture');
        $res = (new Parser($reflection->getDocComment(), $this->rules))->parse();
        $annotations = new AnnotationsBag($res, $this->rules);
        $this->assertSame([], $annotations->export());
    }

    /**
     * @test
     */
    public function parseNullFixture()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'null_fixture');
        $res = (new Parser($reflection->getDocComment(), $this->rules))->parse();
        $annotations = new AnnotationsBag($res, $this->rules);
        $this->assertSame([null, null, ''], $annotations->get('value'));
    }

    /**
     * @test
     */
    public function parseBooleanFixture()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'boolean_fixture');
        $res = (new Parser($reflection->getDocComment(), $this->rules))->parse();
        $annotations = new AnnotationsBag($res, $this->rules);
        $this->assertSame([true, false, true, false, "true", "false"], $annotations->get('value'));
    }

    /**
     * @test
     */
    public function parseImplicitBooleanFixture()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'implicit_boolean_fixture');
        $res = (new Parser($reflection->getDocComment(), $this->rules))->parse();
        $annotations = new AnnotationsBag($res, $this->rules);
        $this->assertSame(true, $annotations->get('alpha'));
        $this->assertSame(true, $annotations->get('beta'));
        $this->assertSame(true, $annotations->get('gamma'));
        $this->assertSame(null, $annotations->get('delta'));
    }

    /**
     * @test
     */
    public function parseStringFixture()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'string_fixture');
        $res = (new Parser($reflection->getDocComment(), $this->rules))->parse();
        $annotations = new AnnotationsBag($res, $this->rules);
        $this->assertSame(['abc', 'abc', '123'], $annotations->get('value'));
    }

    /**
     * @test
     */
    public function parseIntegerFixture()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'integer_fixture');
        $res = (new Parser($reflection->getDocComment(), $this->rules))->parse();
        $annotations = new AnnotationsBag($res, $this->rules);
        $this->assertSame([123, 23, -23], $annotations->get('value'));
    }

    /**
     * @test
     */
    public function parseFloatFixture()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'float_fixture');
        $res = (new Parser($reflection->getDocComment(), $this->rules))->parse();
        $annotations = new AnnotationsBag($res, $this->rules);
        $this->assertSame([.45, 0.45, 45., -4.5], $annotations->get('value'));
    }

    /**
     * @test
     */
    public function parseJsonFixture()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'json_fixture');
        $res = (new Parser($reflection->getDocComment(), $this->rules))->parse();
        $annotations = new AnnotationsBag($res, $this->rules);
        $this->assertEquals(
            [
                ["x", "y"],
                json_decode('{"x": {"y": "z"}}'),
                json_decode('{"x": {"y": ["z", "p"]}}')
            ],
            $annotations->get('value')
        );
    }

    /**
     * @test
     */
    public function parseEvalFixture()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'eval_fixture');
        $res = (new Parser($reflection->getDocComment(), $this->rules))->parse();
        $annotations = new AnnotationsBag($res, $this->rules);
        $this->assertEquals(
            [
                86400000,
                [1, 2, 3],
                101000110111001100110100
            ],
            $annotations->get('value')
        );
    }

    /**
     * @test
     */
    public function parseSingleValuesFixture()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'single_values_fixture');
        $res = (new Parser($reflection->getDocComment(), $this->rules))->parse();
        $annotations = new AnnotationsBag($res, $this->rules);
        $this->assertEquals('foo', $annotations->get('param_a'));
        $this->assertEquals('bar', $annotations->get('param_b'));
    }

    /**
     * @test
     */
    public function parseMultipleValuesFixture()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'multiple_values_fixture');
        $res = (new Parser($reflection->getDocComment(), $this->rules))->parse();
        $annotations = new AnnotationsBag($res, $this->rules);
        $this->assertEquals(['x', 'y', 'z'], $annotations->get('value'));
    }

    /**
     * @test
     */
    public function parseParseSameLineFixture()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'same_line_fixture');
        $res = (new Parser($reflection->getDocComment(), $this->rules))->parse();
        $annotations = new AnnotationsBag($res, $this->rules);
        $this->assertSame(true, $annotations->get('get'));
        $this->assertSame(true, $annotations->get('post'));
        $this->assertSame(true, $annotations->get('ajax'));
        $this->assertSame(true, $annotations->get('alpha'));
        $this->assertSame(true, $annotations->get('beta'));
        $this->assertSame(true, $annotations->get('gamma'));
        $this->assertSame(null, $annotations->get('undefined'));
    }

    /**
     * @test
     */
    public function namespacedAnnotations()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'namespaced_fixture');
        $res = (new Parser($reflection->getDocComment(), $this->rules))->parse();
        $annotations = new AnnotationsBag($res, $this->rules);

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
        $res = (new Parser($reflection->getDocComment(), $this->rules))->parse();
        $annotations = new AnnotationsBag($res, $this->rules);
        $declarations = $annotations->get('value');
        $this->assertNotEmpty($declarations);
        $this->assertSame(
            [
            "abc", "45", // string
            45, -45, // integer
            .45, 0.45, 45.0, -4.5, 4., // float
            ],
            $declarations
        );

        $declarations = $annotations->get('json_value');
        $this->assertEquals(
            [
            ["x", "y"], // json
            json_decode('{"x": {"y": "z"}}'),
            json_decode('{"x": {"y": ["z", "p"]}}')
            ],
            $declarations
        );
    }

    /**
     * @test
     */
    public function tolerateUnrecognizedTypes()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'non_recognized_type_fixture');
        $res = (new Parser($reflection->getDocComment(), $this->rules))->parse();
        $annotations = new AnnotationsBag($res, $this->rules);
        $this->assertEquals("footype Tolerate me. DockBlocks can't be evaluated rigidly.", $annotations->get('value'));
    }

    /**
     * @test
     * @expectedException Minime\Annotations\ParserException
     */
    public function badJSONValue()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'bad_json_fixture');
        (new Parser($reflection->getDocComment(), $this->rules))->parse();
    }

    /**
     * @test
     * @expectedException Minime\Annotations\ParserException
     */
    public function badEvalValue()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'bad_eval_fixture');
        (new Parser($reflection->getDocComment(), $this->rules))->parse();
    }

    /**
     * @test
     * @expectedException Minime\Annotations\ParserException
     */
    public function badIntegerValue()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'bad_integer_fixture');
        (new Parser($reflection->getDocComment(), $this->rules))->parse();
    }

    /**
     * @test
     * @expectedException Minime\Annotations\ParserException
     */
    public function badFloatValue()
    {
        $reflection = new ReflectionProperty($this->Fixture, 'bad_float_fixture');
        (new Parser($reflection->getDocComment(), $this->rules))->parse();
    }
}
