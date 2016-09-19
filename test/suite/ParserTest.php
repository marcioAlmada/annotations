<?php

namespace Minime\Annotations;

use \ReflectionProperty;
use Minime\Annotations\Fixtures\AnnotationsFixture;

/**
 * ParserTest
 * 
 * @group parser
 */
class ParserTest extends DynamicParserTest
{
    public function setUp()
    {
        parent::setup();
        $this->parser = new Parser;
    }

    /**
     * @test
     */
    public function parseConcreteFixture()
    {
        $annotations = $this->getFixture('concrete_fixture');
        $this->assertInstanceOf(
          'Minime\Annotations\Fixtures\AnnotationConstructInjection',
          $annotations['Minime\Annotations\Fixtures\AnnotationConstructInjection'][0]
        );
        $this->assertInstanceOf(
          'Minime\Annotations\Fixtures\AnnotationConstructInjection',
          $annotations['Minime\Annotations\Fixtures\AnnotationConstructInjection'][1]
        );
        $this->assertSame(
          '{"foo":"bar","bar":"baz"}',
          json_encode($annotations['Minime\Annotations\Fixtures\AnnotationConstructInjection'][0])
        );
        $this->assertSame(
          '{"foo":"bar","bar":"baz"}',
          json_encode($annotations['Minime\Annotations\Fixtures\AnnotationConstructInjection'][1])
        );
        $this->assertInstanceOf(
          'Minime\Annotations\Fixtures\AnnotationSetterInjection',
          $annotations['Minime\Annotations\Fixtures\AnnotationSetterInjection'][0]
        );
        $this->assertInstanceOf(
          'Minime\Annotations\Fixtures\AnnotationSetterInjection',
          $annotations['Minime\Annotations\Fixtures\AnnotationSetterInjection'][1]
        );
        $this->assertSame(
          '{"foo":"bar"}',
          json_encode($annotations['Minime\Annotations\Fixtures\AnnotationSetterInjection'][0])
        );
        $this->assertSame(
          '{"foo":"bar"}',
          json_encode($annotations['Minime\Annotations\Fixtures\AnnotationSetterInjection'][1])
        );
    }

    /**
     * @test
     * @expectedException \Minime\Annotations\ParserException
     * @dataProvider invalidConcreteAnnotationFixtureProvider
     */
    public function parseInvalidConcreteFixture($fixture)
    {
        $this->getFixture($fixture);
    }

    public function invalidConcreteAnnotationFixtureProvider()
    {
      return [
        ['bad_concrete_fixture'],
        ['bad_concrete_fixture_root_schema'],
        ['bad_concrete_fixture_method_schema']
      ];
    }

    /**
     * @test
     */
    public function parseStrongTypedFixture()
    {
        $annotations = $this->getFixture('strong_typed_fixture');
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
        $annotations = $this->getFixture('reserved_words_as_value_fixture');
        $expected = ['string','integer','float','json'];
        $this->assertSame($expected, $annotations['value']);
        $this->assertSame($expected, $annotations['value_with_trailing_space']);
    }

    /**
     * @test
     */
    public function tolerateUnrecognizedTypes()
    {
        $annotations = $this->getFixture('non_recognized_type_fixture');
        $this->assertEquals(
          "footype Tolerate me. DockBlocks can't be evaluated rigidly.", $annotations['value']);
    }

    /**
     * @test
     * @expectedException \Minime\Annotations\ParserException
     */
    public function exceptionWithBadJsonValue()
    {
        $this->getFixture('bad_json_fixture');
    }

    /**
     * @test
     * @expectedException \Minime\Annotations\ParserException
     */
    public function exceptionWithBadIntegerValue()
    {
        $this->getFixture('bad_integer_fixture');
    }

    /**
     * @test
     * @expectedException \Minime\Annotations\ParserException
     */
    public function exceptionWithBadFloatValue()
    {
        $this->getFixture('bad_float_fixture');
    }

    /**
     * @test
     */
    public function testTypeRegister()
    {
        $docblock = '/** @value foo bar */';

        $this->assertSame(['value' => 'foo bar'], $this->parser->parse($docblock));
        $this->parser->registerType('\Minime\Annotations\Fixtures\FooType', 'foo');
        $this->assertSame(['value' => 'this foo is bar'], $this->parser->parse($docblock));
        $this->parser->unregisterType('\Minime\Annotations\Fixtures\FooType');
        $this->assertSame(['value' => 'foo bar'], $this->parser->parse($docblock));
    }
}
