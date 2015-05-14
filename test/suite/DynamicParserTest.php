<?php

namespace Minime\Annotations;

/**
 * DynamicParserTest
 * 
 * @group parser
 */
class DynamicParserTest extends BaseTest
{
    public function setUp()
    {
        parent::setup();
        $this->parser = new DynamicParser;
    }

    /**
     * @test
     */
    public function parseEmptyFixture()
    {
        $annotations = $this->getFixture('empty_fixture');
        $this->assertSame([], $annotations);
    }

    /**
     * @test
     */
    public function parseNullFixture()
    {
        $annotations = $this->getFixture('null_fixture');
        $this->assertSame([null, ''], $annotations['value']);
    }

    /**
     * @test
     */
    public function parseBooleanFixture()
    {
        $annotations = $this->getFixture('boolean_fixture');
        $this->assertSame([true, false, "true", "false"], $annotations['value']);
    }

    /**
     * @test
     */
    public function parseImplicitBooleanFixture()
    {
        $annotations = $this->getFixture('implicit_boolean_fixture');
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
        $annotations = $this->getFixture('string_fixture');
        $this->assertSame(['abc', 'abc', 'abc ', '123'], $annotations['value']);
        $this->assertSame(['abc', 'abc', 'abc ', '123'], $annotations['value']);
    }

    /**
     * @test
     */
    public function parseIdentifierFixture()
    {
        $annotations = $this->getFixture('identifier_parsing_fixture');
        $this->assertSame(['bar' => 'test@example.com', 'toto' => true, 'tata' => true, 'number' => 2.1], $annotations);
    }

    /**
     * @test
     */
    public function parseIntegerFixture()
    {
        $annotations = $this->getFixture('integer_fixture');
        $this->assertSame([123, 23, -23], $annotations['value']);
    }

    /**
     * @test
     */
    public function parseFloatFixture()
    {
        $annotations = $this->getFixture('float_fixture');
        $this->assertSame([.45, 0.45, 45., -4.5], $annotations['value']);
    }

    /**
     * @test
     */
    public function parseJsonFixture()
    {
        $annotations = $this->getFixture('json_fixture');
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
    public function parseSingleValuesFixture()
    {
        $annotations = $this->getFixture('single_values_fixture');
        $this->assertEquals('foo', $annotations['param_a']);
        $this->assertEquals('bar', $annotations['param_b']);
    }

    /**
     * @test
     */
    public function parseMultipleValuesFixture()
    {
        $annotations = $this->getFixture('multiple_values_fixture');
        $this->assertEquals(['x', 'y', 'z'], $annotations['value']);
    }

    /**
     * @test
     */
    public function parseParseSameLineFixture()
    {
        $annotations = $this->getFixture('same_line_fixture');
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
        $annotations = $this->getFixture('multiline_value_fixture');
        $string = "Lorem ipsum dolor sit amet, consectetur adipiscing elit.\n"
                  ."Etiam malesuada mauris justo, at sodales nisi accumsan sit amet.\n\n"
                  ."Morbi imperdiet lacus non purus suscipit convallis.\n"
                  ."Suspendisse egestas orci a felis imperdiet, non consectetur est suscipit.";
        $this->assertSame($string, $annotations['multiline_string']);

        $cowsay = "------\n< moo >\n------ \n        \   ^__^\n         ".
                  "\  (oo)\_______\n            (__)\       )\/\\\n                ".
                  "||----w |\n                ||     ||";
        $this->assertSame($cowsay, $annotations['multiline_indented_string']);
    }

    /**
     * @test
     */
    public function parseNamespacedAnnotations()
    {
        $annotations = $this->getFixture('namespaced_fixture');

        $this->assertSame('cheers!', $annotations['path.to.the.treasure']);
        $this->assertSame('the cake is a lie', $annotations['path.to.the.cake']);
        $this->assertSame('foo', $annotations['another.path.to.cake']);
    }

    /**
     * @test
     */
    public function parseInlineDocblocks()
    {
        $annotations = $this->getFixture('inline_docblock_fixture');
        $this->assertSame('foo', $annotations['value']);

        $annotations = $this->getFixture('inline_docblock_implicit_boolean_fixture');
        $this->assertSame(true, $annotations['alpha']);

        $annotations = $this->getFixture('inline_docblock_multiple_implicit_boolean_fixture');
        $this->assertSame(true, $annotations['alpha']);
        $this->assertSame(true, $annotations['beta']);
        $this->assertSame(true, $annotations['gama']);
    }

    /**
     * @test for issue #32
     * @link https://github.com/marcioAlmada/annotations/issues/32
     */
    public function issue32()
    {
      $annotations = $this->getFixture('i32_fixture');
      $this->assertSame(['stringed', 'integers', 'floated', 'jsonable'], $annotations['type']);
    }

    /**
     * @test for issue #49
     * @link https://github.com/marcioAlmada/annotations/issues/49
     */
    public function issue49()
    {
      $annotations = $this->getFixture('i49_fixture');
      $this->assertSame(['return' => 'void'], $annotations);
    }

    protected function getFixture($fixture)
    {
        return $this->parser->parse($this->getDocblock($fixture));
    }
}
