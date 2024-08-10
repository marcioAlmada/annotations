<?php

namespace Minime\Annotations;

use PHPUnit\Framework\Attributes\Test;

require_once __DIR__ . '/BaseTestCase.php';

/**
 * DynamicParserTest
 * 
 */
class DynamicParserTestCase extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setup();
        $this->parser = new DynamicParser;
    }

    #[Test]
    public function parseEmptyFixture()
    {
        $annotations = $this->getFixture('empty_fixture');
        self::assertSame([], $annotations);
    }

    #[Test]
    public function parseNullFixture()
    {
        $annotations = $this->getFixture('null_fixture');
        self::assertSame([null, ''], $annotations['value']);
    }

    #[Test]
    public function parseBooleanFixture()
    {
        $annotations = $this->getFixture('boolean_fixture');
        self::assertSame([true, false, "true", "false"], $annotations['value']);
    }

    #[Test]
    public function parseImplicitBooleanFixture()
    {
        $annotations = $this->getFixture('implicit_boolean_fixture');
        self::assertSame(true, $annotations['alpha']);
        self::assertSame(true, $annotations['beta']);
        self::assertSame(true, $annotations['gamma']);
        self::assertArrayNotHasKey('delta', $annotations);
    }

    #[Test]
    public function parseStringFixture()
    {
        $annotations = $this->getFixture('string_fixture');
        self::assertSame(['abc', 'abc', 'abc ', '123'], $annotations['value']);
        self::assertSame(['abc', 'abc', 'abc ', '123'], $annotations['value']);
    }

    #[Test]
    public function parseIdentifierFixture()
    {
        $annotations = $this->getFixture('identifier_parsing_fixture');
        self::assertSame(['bar' => 'test@example.com', 'toto' => true, 'tata' => true, 'number' => 2.1], $annotations);
    }

    #[Test]
    public function parseIntegerFixture()
    {
        $annotations = $this->getFixture('integer_fixture');
        self::assertSame([123, 23, -23], $annotations['value']);
    }

    #[Test]
    public function parseFloatFixture()
    {
        $annotations = $this->getFixture('float_fixture');
        self::assertSame([.45, 0.45, 45., -4.5], $annotations['value']);
    }

    #[Test]
    public function parseJsonFixture()
    {
        $annotations = $this->getFixture('json_fixture');
        self::assertEquals(
            [
                ["x", "y"],
                json_decode('{"x": {"y": "z"}}'),
                json_decode('{"x": {"y": ["z", "p"]}}')
            ],
            $annotations['value']
        );
    }

    #[Test]
    public function parseSingleValuesFixture()
    {
        $annotations = $this->getFixture('single_values_fixture');
        self::assertEquals('foo', $annotations['param_a']);
        self::assertEquals('bar', $annotations['param_b']);
    }

    #[Test]
    public function parseMultipleValuesFixture()
    {
        $annotations = $this->getFixture('multiple_values_fixture');
        self::assertEquals(['x', 'y', 'z'], $annotations['value']);
    }

    #[Test]
    public function parseParseSameLineFixture()
    {
        $annotations = $this->getFixture('same_line_fixture');
        self::assertSame(true, $annotations['get']);
        self::assertSame(true, $annotations['post']);
        self::assertSame(true, $annotations['ajax']);
        self::assertSame(true, $annotations['alpha']);
        self::assertSame(true, $annotations['beta']);
        self::assertSame(true, $annotations['gamma']);
        self::assertArrayNotHasKey('undefined', $annotations);
    }

    #[Test]
    public function parseMultilineValueFixture()
    {
        $annotations = $this->getFixture('multiline_value_fixture');
        $string = "Lorem ipsum dolor sit amet, consectetur adipiscing elit.\n"
                  ."Etiam malesuada mauris justo, at sodales nisi accumsan sit amet.\n\n"
                  ."Morbi imperdiet lacus non purus suscipit convallis.\n"
                  ."Suspendisse egestas orci a felis imperdiet, non consectetur est suscipit.";
        self::assertSame($string, $annotations['multiline_string']);

        $cowsay = "------\n< moo >\n------ \n        \   ^__^\n         ".
                  "\  (oo)\_______\n            (__)\       )\/\\\n                ".
                  "||----w |\n                ||     ||";
        self::assertSame($cowsay, $annotations['multiline_indented_string']);
    }

    #[Test]
    public function parseNamespacedAnnotations()
    {
        $annotations = $this->getFixture('namespaced_fixture');

        $this->assertSame('cheers!', $annotations['path.to.the.treasure']);
        $this->assertSame('the cake is a lie', $annotations['path.to.the.cake']);
        $this->assertSame('foo', $annotations['another.path.to.cake']);
    }

    #[Test]
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
     * @link https://github.com/marcioAlmada/annotations/issues/32
     */
    #[Test]
    public function issue32()
    {
      $annotations = $this->getFixture('i32_fixture');
      $this->assertSame(['stringed', 'integers', 'floated', 'jsonable'], $annotations['type']);
    }

    /**
     * @link https://github.com/marcioAlmada/annotations/issues/49
     */
    #[Test]
    public function issue49()
    {
      $annotations = $this->getFixture('i49_fixture');
      $this->assertSame(['return' => 'void'], $annotations);
    }

    /**
     * @link https://github.com/marcioAlmada/annotations/issues/55
     */
    #[Test]
    public function issue55()
    {
      $annotations = $this->parser->parse($this->getDocblock('i55_fixture'));
      $this->assertSame(['name' => 'gsouf'], $annotations);
    }
}
