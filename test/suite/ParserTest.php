<?php

namespace Minime\Annotations;

use \ReflectionProperty;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Minime\Annotations\Fixtures\AnnotationsFixture;

require_once __DIR__ . '/DynamicParserTestCase.php';

/**
 * ParserTest
 * 
 */
class ParserTest extends DynamicParserTestCase
{
    public function setUp(): void
    {
        parent::setup();
        $this->parser = new Parser;
    }

    #[Test]
    public function parseConcreteFixture()
    {
        $annotations = $this->getFixture('concrete_fixture');
        self::assertInstanceOf(
          'Minime\Annotations\Fixtures\AnnotationConstructInjection',
          $annotations['Minime\Annotations\Fixtures\AnnotationConstructInjection'][0]
        );
        self::assertInstanceOf(
          'Minime\Annotations\Fixtures\AnnotationConstructInjection',
          $annotations['Minime\Annotations\Fixtures\AnnotationConstructInjection'][1]
        );
        self::assertSame(
          '{"foo":"bar","bar":"baz"}',
          json_encode($annotations['Minime\Annotations\Fixtures\AnnotationConstructInjection'][0])
        );
        self::assertSame(
          '{"foo":"bar","bar":"baz"}',
          json_encode($annotations['Minime\Annotations\Fixtures\AnnotationConstructInjection'][1])
        );
        self::assertSame(
          '{"foo":"foo","bar":"bar"}',
          json_encode($annotations['Minime\Annotations\Fixtures\AnnotationConstructSugarInjection'][0])
        );
        self::assertSame(
          '{"foo":"baz","bar":"bar"}',
          json_encode($annotations['Minime\Annotations\Fixtures\AnnotationConstructSugarInjection'][1])
        );
        self::assertInstanceOf(
          'Minime\Annotations\Fixtures\AnnotationSetterInjection',
          $annotations['Minime\Annotations\Fixtures\AnnotationSetterInjection'][0]
        );
        self::assertInstanceOf(
          'Minime\Annotations\Fixtures\AnnotationSetterInjection',
          $annotations['Minime\Annotations\Fixtures\AnnotationSetterInjection'][1]
        );
        self::assertSame(
          '{"foo":"bar"}',
          json_encode($annotations['Minime\Annotations\Fixtures\AnnotationSetterInjection'][0])
        );
        self::assertSame(
          '{"foo":"bar"}',
          json_encode($annotations['Minime\Annotations\Fixtures\AnnotationSetterInjection'][1])
        );
    }

    #[Test]
    #[DataProvider('invalidConcreteAnnotationFixtureProvider')]
    public function parseInvalidConcreteFixture($fixture)
    {
        $this->expectException(\Minime\Annotations\ParserException::class);
        $this->getFixture($fixture);
    }

    public static function invalidConcreteAnnotationFixtureProvider()
    {
      return [
        ['bad_concrete_fixture'],
        ['bad_concrete_fixture_method_schema']
      ];
    }

    #[Test]
    public function parseStrongTypedFixture()
    {
        $annotations = $this->getFixture('strong_typed_fixture');
        $declarations = $annotations['value'];
        self::assertNotEmpty($declarations);
        self::assertSame(
            [
            "abc", "45", // string
            45, -45, // integer
            .45, 0.45, 45.0, -4.5, 4., // float
            ],
            $declarations
        );

        $declarations = $annotations['json_value'];
        self::assertEquals(
            [
            ["x", "y"], // json
            json_decode('{"x": {"y": "z"}}'),
            json_decode('{"x": {"y": ["z", "p"]}}')
            ],
            $declarations
        );
    }

    #[Test]
    public function parseReservedWordsAsValue()
    {
        $annotations = $this->getFixture('reserved_words_as_value_fixture');
        $expected = ['string','integer','float','json'];
        self::assertSame($expected, $annotations['value']);
        self::assertSame($expected, $annotations['value_with_trailing_space']);
    }

    #[Test]
    public function tolerateUnrecognizedTypes()
    {
        $annotations = $this->getFixture('non_recognized_type_fixture');
        self::assertEquals(
          "footype Tolerate me. DockBlocks can't be evaluated rigidly.", $annotations['value']);
    }

    #[Test]
    public function exceptionWithBadJsonValue()
    {
        $this->expectException(\Minime\Annotations\ParserException::class);
        $this->getFixture('bad_json_fixture');
    }

    #[Test]
    public function exceptionWithBadIntegerValue()
    {
        $this->expectException(\Minime\Annotations\ParserException::class);
        $this->getFixture('bad_integer_fixture');
    }

    #[Test]
    public function exceptionWithBadFloatValue()
    {
        $this->expectException(\Minime\Annotations\ParserException::class);
        $this->getFixture('bad_float_fixture');
    }

    #[Test]
    public function testTypeRegister()
    {
        $docblock = '/** @value foo bar */';

        self::assertSame(['value' => 'foo bar'], $this->parser->parse($docblock));
        $this->parser->registerType('\Minime\Annotations\Fixtures\FooType', 'foo');
        self::assertSame(['value' => 'this foo is bar'], $this->parser->parse($docblock));
        $this->parser->unregisterType('\Minime\Annotations\Fixtures\FooType');
        self::assertSame(['value' => 'foo bar'], $this->parser->parse($docblock));
    }
}
