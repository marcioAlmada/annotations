<?php

namespace Minime\Annotations;

use \Minime\Annotations\Fixtures\AnnotationsFixture;
use \ReflectionProperty;

/**
 * @group parser
 */
class ParserTest extends \PHPUnit_Framework_TestCase
{

    private $Fixture;

    public function setUp()
    {
        $this->Fixture = new AnnotationsFixture;
		$this->Parser = new Parser(new ParserRules);
    }

    private function getDocBlock($fixture)
    {
        return (new ReflectionProperty($this->Fixture, $fixture))->getDocComment();
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
        $annotations = $this->Parser->parse($this->getDocBlock('empty_fixture'));
        $this->assertSame([], $annotations);
    }

    /**
     * @test
     */
    public function parseNullFixture()
    {
        $annotations = $this->Parser->parse($this->getDocBlock('null_fixture'));
        $this->assertSame([null, null, ''], $annotations['value']);
    }

    /**
     * @test
     */
    public function parseBooleanFixture()
    {
        $annotations = $this->Parser->parse($this->getDocBlock('boolean_fixture'));
        $this->assertSame([true, false, true, false, "true", "false"], $annotations['value']);
    }

    /**
     * @test
     */
    public function parseImplicitBooleanFixture()
    {
        $annotations = $this->Parser->parse($this->getDocBlock('implicit_boolean_fixture'));
        $this->assertSame(true, $annotations['alpha']);
        $this->assertSame(true, $annotations['beta']);
        $this->assertSame(true, $annotations['gamma']);
        $this->assertArrayNotHasKey('delta', $annotations);
    }

    /**
     * @test
     */
    public function parseStringFixture()
    {
        $annotations = $this->Parser->parse($this->getDocBlock('string_fixture'));
        $this->assertSame(['abc', 'abc', 'abc ', '123'], $annotations['value']);
    }

    /**
     * @test
     */
    public function parseIdentifierFixture()
    {
        $annotations = $this->Parser->parse($this->getDocBlock('identifier_parsing_fixture'));
        $this->assertSame(['bar' => 'test@example.com', 'toto' => true, 'tata' => true, 'number' => 2.1], $annotations);
    }

    /**
     * @test
     */
    public function parseIntegerFixture()
    {
        $annotations = $this->Parser->parse($this->getDocBlock('integer_fixture'));
        $this->assertSame([123, 23, -23], $annotations['value']);
    }

    /**
     * @test
     */
    public function parseFloatFixture()
    {
        $annotations = $this->Parser->parse($this->getDocBlock('float_fixture'));
        $this->assertSame([.45, 0.45, 45., -4.5], $annotations['value']);
    }

    /**
     * @test
     */
    public function parseJsonFixture()
    {
        $annotations = $this->Parser->parse($this->getDocBlock('json_fixture'));
        $this->assertEquals(
            [
                ["x", "y"],
                json_decode('{"x": {"y": "z"}}'),
                json_decode('{"x": {"y": ["z", "p"]}}')
            ],
            $annotations['value']
        );
    }

    /**
     * @test
     */
    public function parseEvalFixture()
    {
        $annotations = $this->Parser->parse($this->getDocBlock('eval_fixture'));
        $this->assertEquals(
            [
                86400000,
                [1, 2, 3],
                101000110111001100110100
            ],
            $annotations['value']
        );
    }

    /**
     * @test
     */
    public function parseSingleValuesFixture()
    {
        $annotations = $this->Parser->parse($this->getDocBlock('single_values_fixture'));
        $this->assertEquals('foo', $annotations['param_a']);
        $this->assertEquals('bar', $annotations['param_b']);
    }

    /**
     * @test
     */
    public function parseMultipleValuesFixture()
    {
        $annotations = $this->Parser->parse($this->getDocBlock('multiple_values_fixture'));
        $this->assertEquals(['x', 'y', 'z'], $annotations['value']);
    }

    /**
     * @test
     */
    public function parseParseSameLineFixture()
    {
        $annotations = $this->Parser->parse($this->getDocBlock('same_line_fixture'));
        $this->assertSame(true, $annotations['get']);
        $this->assertSame(true, $annotations['post']);
        $this->assertSame(true, $annotations['ajax']);
        $this->assertSame(true, $annotations['alpha']);
        $this->assertSame(true, $annotations['beta']);
        $this->assertSame(true, $annotations['gamma']);
        $this->assertArrayNotHasKey('undefined', $annotations);
    }

    /**
     * @test
     */
    public function namespacedAnnotations()
    {
        $annotations = $this->Parser->parse($this->getDocBlock('namespaced_fixture'));

        $this->assertSame('cheers!', $annotations['path.to.the.treasure']);
        $this->assertSame('the cake is a lie', $annotations['path.to.the.cake']);
        $this->assertSame('foo', $annotations['another.path.to.cake']);
    }

    /**
     * @test
     */
    public function parseStrongTypedFixture()
    {
        $annotations = $this->Parser->parse($this->getDocBlock('strong_typed_fixture'));
        $declarations = $annotations['value'];
        $this->assertNotEmpty($declarations);
        $this->assertSame(
            [
            "abc", "45", // string
            45, -45, // integer
            .45, 0.45, 45.0, -4.5, 4., // float
            ],
            $declarations
        );

        $declarations = $annotations['json_value'];
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
    public function parseReservedWordsAsValue()
    {
        $annotations = $this->Parser->parse($this->getDocBlock('reserved_words_as_value_fixture'));
        $expected = ['string','integer','float','json','eval'];
        $this->assertSame($expected, $annotations['value']);
        $this->assertSame($expected, $annotations['value_with_trailing_space']);
    }

    /**
     * @test
     */
    public function tolerateUnrecognizedTypes()
    {
        $annotations = $this->Parser->parse($this->getDocBlock('non_recognized_type_fixture'));
        $this->assertEquals("footype Tolerate me. DockBlocks can't be evaluated rigidly.", $annotations['value']);
    }

    /**
     * @test
     */
    public function parseInlineDocblocks()
    {
        $annotations = $this->Parser->parse($this->getDocBlock('inline_docblock_fixture'));
        $this->assertSame('foo', $annotations['value']);

        $annotations = $this->Parser->parse($this->getDocBlock('inline_docblock_implicit_boolean_fixture'));
        $this->assertSame(true, $annotations['alpha']);

        $annotations = $this->Parser->parse($this->getDocBlock('inline_docblock_multiple_implicit_boolean_fixture'));
        $this->assertSame(true, $annotations['alpha']);
        $this->assertSame(true, $annotations['beta']);
        $this->assertSame(true, $annotations['gama']);
    }

    /**
     * @test
     * @expectedException Minime\Annotations\ParserException
     */
    public function badJSONValue()
    {
        $this->Parser->parse($this->getDocBlock('bad_json_fixture'));
    }

    /**
     * @test
     * @expectedException Minime\Annotations\ParserException
     */
    public function badEvalValue()
    {
        $this->Parser->parse($this->getDocBlock('bad_eval_fixture'));
    }

    /**
     * @test
     * @expectedException Minime\Annotations\ParserException
     */
    public function badIntegerValue()
    {
        $this->Parser->parse($this->getDocBlock('bad_integer_fixture'));
    }

    /**
     * @test
     * @expectedException Minime\Annotations\ParserException
     */
    public function badFloatValue()
    {
        $this->Parser->parse($this->getDocBlock('bad_float_fixture'));
    }
}
