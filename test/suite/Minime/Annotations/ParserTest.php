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
    }

    public function tearDown()
    {
        $this->Fixture = null;
    }

    private function getParser($fixture)
    {
        $reflection = new ReflectionProperty($this->Fixture, $fixture);

        return new Parser($reflection->getDocComment(), new ParserRules);
    }

    /**
     * @test
     * @expectedException PHPUnit_Framework_Error
     */
    public function parserRequiredAParserRules()
    {
        new Parser('hello world!');
    }

    /**
     * @test
     */
    public function parseEmptyFixture()
    {
        $annotations = $this->getParser('empty_fixture')->parse();
        $this->assertSame([], $annotations);
    }

    /**
     * @test
     */
    public function parseNullFixture()
    {
        $annotations = $this->getParser('null_fixture')->parse();
        $this->assertSame([null, null, ''], $annotations['value']);
    }

    /**
     * @test
     */
    public function parseBooleanFixture()
    {
        $annotations = $this->getParser('boolean_fixture')->parse();
        $this->assertSame([true, false, true, false, "true", "false"], $annotations['value']);
    }

    /**
     * @test
     */
    public function parseImplicitBooleanFixture()
    {
        $annotations = $this->getParser('implicit_boolean_fixture')->parse();
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
        $annotations = $this->getParser('string_fixture')->parse();
        $this->assertSame(['abc', 'abc', 'abc ', '123'], $annotations['value']);
        $this->assertSame(['abc', 'abc', 'abc ', '123'], $annotations['value']);
    }

    /**
     * @test
     */
    public function parseIdentifierFixture()
    {
        $annotations = $this->getParser('identifier_parsing_fixture')->parse();
        $this->assertSame(['bar' => 'test@example.com', 'toto' => true, 'tata' => true, 'number' => 2.1], $annotations);
    }

    /**
     * @test
     */
    public function parseIntegerFixture()
    {
        $annotations = $this->getParser('integer_fixture')->parse();
        $this->assertSame([123, 23, -23], $annotations['value']);
    }

    /**
     * @test
     */
    public function parseFloatFixture()
    {
        $annotations = $this->getParser('float_fixture')->parse();
        $this->assertSame([.45, 0.45, 45., -4.5], $annotations['value']);
    }

    /**
     * @test
     */
    public function parseJsonFixture()
    {
        $annotations = $this->getParser('json_fixture')->parse();
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
        $annotations = $this->getParser('eval_fixture')->parse();
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
    public function parseConcreteFixture()
    {
        $annotations = $this->getParser('concrete_fixture')->parse();
        $this->assertInstanceOf(
          'Minime\Annotations\Fixtures\AnnotationConstructInjection',
          $annotations['Minime\Annotations\Fixtures\AnnotationConstructInjection']
        );
        $this->assertSame(
          '{"foo":"bar"}',
          json_encode($annotations['Minime\Annotations\Fixtures\AnnotationConstructInjection'])
        );
        $this->assertSame(
          '{"foo":"bar"}',
          json_encode($annotations['Minime\Annotations\Fixtures\AnnotationSetterInjection'])
        );
    }

    /**
     * @test
     * @expectedException Minime\Annotations\ParserException
     */
    public function parseInvalidConcreteFixture()
    {
        $annotations = $this->getParser('bad_concrete_fixture')->parse();
    }


    /**
     * @test
     */
    public function parseSingleValuesFixture()
    {
        $annotations = $this->getParser('single_values_fixture')->parse();
        $this->assertEquals('foo', $annotations['param_a']);
        $this->assertEquals('bar', $annotations['param_b']);
    }

    /**
     * @test
     */
    public function parseMultipleValuesFixture()
    {
        $annotations = $this->getParser('multiple_values_fixture')->parse();
        $this->assertEquals(['x', 'y', 'z'], $annotations['value']);
    }

    /**
     * @test
     */
    public function parseParseSameLineFixture()
    {
        $annotations = $this->getParser('same_line_fixture')->parse();
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
    public function parseMultilineValueFixture()
    {
        $annotations = $this->getParser('multiline_value_fixture')->parse();
        $string = "Lorem ipsum dolor sit amet, consectetur adipiscing elit.\n"
                  ."Etiam malesuada mauris justo, at sodales nisi accumsan sit amet.\n\n"
                  ."Morbi imperdiet lacus non purus suscipit convallis.\n"
                  ."Suspendisse egestas orci a felis imperdiet, non consectetur est suscipit.";
        $this->assertSame($string, $annotations['multiline_string']);

        $cowsay = "------\n< moo >\n------ \n        \   ^__^\n         ".
                  "\  (oo)\_______\n            (__)\       )\/\\\n                ".
                  "||----w |\n                ||     ||";
        $this->assertSame($cowsay, $annotations['multiline_indented_string']);

        $this->assertEquals(json_decode('{"x": {"y": ["z", "p"]}}'), $annotations['multiline_json']);
    }

    /**
     * @test
     */
    public function namespacedAnnotations()
    {
        $annotations = $this->getParser('namespaced_fixture')->parse();

        $this->assertSame('cheers!', $annotations['path.to.the.treasure']);
        $this->assertSame('the cake is a lie', $annotations['path.to.the.cake']);
        $this->assertSame('foo', $annotations['another.path.to.cake']);
    }

    /**
     * @test
     */
    public function parseStrongTypedFixture()
    {
        $annotations = $this->getParser('strong_typed_fixture')->parse();
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
        $annotations = $this->getParser('reserved_words_as_value_fixture')->parse();
        $expected = ['string','integer','float','json','eval'];
        $this->assertSame($expected, $annotations['value']);
        $this->assertSame($expected, $annotations['value_with_trailing_space']);
    }

    /**
     * @test
     */
    public function tolerateUnrecognizedTypes()
    {
        $annotations = $this->getParser('non_recognized_type_fixture')->parse();
        $this->assertEquals(
          "footype Tolerate me. DockBlocks can't be evaluated rigidly.", $annotations['value']);
    }

    /**
     * @test
     */
    public function parseInlineDocblocks()
    {
        $annotations = $this->getParser('inline_docblock_fixture')->parse();
        $this->assertSame('foo', $annotations['value']);

        $annotations = $this->getParser('inline_docblock_implicit_boolean_fixture')->parse();
        $this->assertSame(true, $annotations['alpha']);

        $annotations = $this->getParser('inline_docblock_multiple_implicit_boolean_fixture')->parse();
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
        $this->getParser('bad_json_fixture')->parse();
    }

    /**
     * @test
     * @expectedException Minime\Annotations\ParserException
     */
    public function badEvalValue()
    {
        $this->getParser('bad_eval_fixture')->parse();
    }

    /**
     * @test
     * @expectedException Minime\Annotations\ParserException
     */
    public function badIntegerValue()
    {
        $this->getParser('bad_integer_fixture')->parse();
    }

    /**
     * @test
     * @expectedException Minime\Annotations\ParserException
     */
    public function badFloatValue()
    {
        $this->getParser('bad_float_fixture')->parse();
    }
}
