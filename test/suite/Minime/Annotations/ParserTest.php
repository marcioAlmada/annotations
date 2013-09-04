<?php

namespace Minime\Annotations;

use \Minime\Annotations\Fixtures\AnnotationsFixture;
use \ReflectionProperty;

class ReaderTest extends \PHPUnit_Framework_TestCase
{

	private $Fixture;

	public function setUp()
	{
		$this->Fixture = new AnnotationsFixture;
	}

	public function testParseGeneralFixture()
	{
		$reflection = new ReflectionProperty($this->Fixture, 'generalFixture');
		$reader = new Reader($reflection->getDocComment());
		$parameters = $reader->export();

		$this->assertNotEmpty($parameters);

		$this->assertArrayHasKey('number', $parameters);
		$this->assertArrayHasKey('string', $parameters);
		$this->assertArrayHasKey('array', $parameters);
		$this->assertArrayHasKey('object', $parameters);
		$this->assertArrayHasKey('nested', $parameters);
		$this->assertArrayHasKey('nestedArray', $parameters);
		$this->assertArrayHasKey('trueVar', $parameters);
		$this->assertArrayHasKey('null-var', $parameters);
		$this->assertArrayHasKey('booleanTrue', $parameters);
		$this->assertArrayHasKey('booleanFalse', $parameters);
		$this->assertArrayHasKey('booleanNull', $parameters);
		$this->assertArrayNotHasKey('non_existent_key', $parameters);

		$this->assertSame(1, $parameters['number']);
		$this->assertSame("123", $parameters['string']);
		$this->assertSame("abc", $parameters['string2']);
		$this->assertSame(array("a", "b"), $parameters['array']);
		$this->assertSame(array("x" => "y"), $parameters['object']);
		$this->assertSame(array("x" => array("y" => "z")), $parameters['nested']);
		$this->assertSame(array("x" => array("y" => array("z", "p"))), $parameters['nestedArray']);
		$this->assertSame(TRUE, $parameters['trueVar']);
		$this->assertSame(NULL, $parameters['null-var']);

		$this->assertSame(TRUE, $parameters['booleanTrue']);
		$this->assertSame(TRUE, $parameters['booleanTrue2']);
		$this->assertSame(FALSE, $parameters['booleanFalse']);
		$this->assertSame(NULL, $parameters['booleanNull']);

		$this->assertSame(1, $reader->get('number'));
		$this->assertSame("123", $reader->get('string'));
		$this->assertSame(array("x" => array("y" => array("z", "p"))),
		$reader->get('nestedArray'));

		$this->assertSame(NULL, $reader->get('nullVar'));
		$this->assertSame(NULL, $reader->get('null-var'));
		$this->assertSame(NULL, $reader->get('non-existent'));
	}

	public function testParseEmptyFixture()
	{
		$reflection = new ReflectionProperty($this->Fixture, 'emptyFixture');
		$reader = new Reader($reflection->getDocComment());
		$this->assertSame(array(), $reader->export());
	}


	public function testParseMultipleValuesFixture()
	{
		$reflection = new ReflectionProperty($this->Fixture, 'multipleValuesFixture');
		$reader = new Reader($reflection->getDocComment());
		$parameters = $reader->export();

		$this->assertNotEmpty($parameters);
		$this->assertArrayHasKey('param', $parameters);
		$this->assertArrayHasKey('var', $parameters);

		$this->assertSame("x",$parameters["var"]);
		$this->assertSame(1024,$parameters["var2"]);

		$this->assertSame(
			array("string x", "integer y", "array z"),
			$parameters["param"]);

	}



	public function testParseSameLineFixture()
	{
		$reflection = new ReflectionProperty($this->Fixture, 'sameLineFixture');
		$reader = new Reader($reflection->getDocComment());

		$this->assertSame(TRUE, $reader->get('get'));
		$this->assertSame(TRUE, $reader->get('post'));
		$this->assertSame(TRUE, $reader->get('ajax'));
	}

	public function testVariableDeclarationsFixture()
	{
		$reflection = new ReflectionProperty($this->Fixture, 'variableDeclarationsFixture');
		$reader = new Reader($reflection->getDocComment());
		$declarations = $reader->getVariableDeclarations("param");
		$this->assertNotEmpty($declarations);

		$this->assertSame(array(
				array("type"=>"string", "name" => "var1"),
				array("type"=>"integer", "name" => 45),
				array("type"=>"integer", "name" => -45),
				array("type"=>"float", "name" => .45),
				array("type"=>"float", "name" => 0.45),
				array("type"=>"float", "name" => 45.0),
				array("type"=>"float", "name" => -4.5),
				array("type"=>"float", "name" => 4.0)
			), $declarations);
	}

	/**
	 * @expectedException Minime\Annotations\ReaderException
	 */
	public function testBadIntegerValue()
	{
		$reflection = new ReflectionProperty($this->Fixture, 'badIntegerValueFixture');
		$reader = new Reader($reflection->getDocComment());
		$declarations = $reader->getVariableDeclarations("param");
	}

	/**
	 * @expectedException Minime\Annotations\ReaderException
	 */
	public function testBadFloatValue()
	{
		$reflection = new ReflectionProperty($this->Fixture, 'badFloatValueFixture');
		$reader = new Reader($reflection->getDocComment());
		$declarations = $reader->getVariableDeclarations("param");
	}

	/**
	 * @expectedException Minime\Annotations\ReaderException
	 */
	public function testBadTypeDeclaration()
	{
		$reflection = new ReflectionProperty($this->Fixture, 'badTypeDeclarationFixture');
		$reader = new Reader($reflection->getDocComment());
		$declarations = $reader->getVariableDeclarations("param");
	}

	/**
	 * @dataProvider badVariableDataProvider
	 * @expectedException Minime\Annotations\ReaderException
	 */
	public function testBadVariableDeclarations($methodName)
	{
		$reflection = new ReflectionProperty($this->Fixture, $methodName);
		$reader = new Reader($reflection->getDocComment());
		$declarations = $reader->getVariableDeclarations("param");
	}

	public function badVariableDataProvider()
	{
		return array(
			array('badVariableDeclarationFixtureOne'),
			array('badVariableDeclarationFixtureTwo')
		);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testGetMethodAcceptsOnlyStringKeys()
	{
		$reflection = new ReflectionProperty($this->Fixture, 'generalFixture');
		$reader = new Reader($reflection->getDocComment());
		$declarations = $reader->get(0);
	}
}