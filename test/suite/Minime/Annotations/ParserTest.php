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
		$this->assertSame(
			[
				["x" => "y"],
				["x" => ["y" => "z"]],
				["x" => ["y" => ["z", "p"]]]
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
		$this->assertSame(NULL, $annotations->get('undefined'));
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
	public function testBadIntegerValue()
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