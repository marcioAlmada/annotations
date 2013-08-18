<?php

namespace Minime\Annotations;

use \ReflectionProperty;

class ReaderTest extends \PHPUnit_Framework_TestCase
{

	private $Fixture;

	public function setUp()
	{
		$this->Fixture = new Fixture();
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
				array("type"=>"integer", "name" => "var2")
			), $declarations);
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

}

class Fixture
{
	/**
	 * @number 1
	 * @string "123"
	 * @string2 abc
	 * @array ["a", "b"]
	 * @object {"x": "y"}
	 * @nested {"x": {"y": "z"}}
	 * @nestedArray {"x": {"y": ["z", "p"]}}
	 *
	 * @trueVar
	 * @null-var null
	 *
	 * @booleanTrue true
	 * @booleanTrue2 tRuE
	 * @booleanFalse false
	 * @booleanNull null
	 * 
	 */
	private $generalFixture;

	/**
	 * @var x
	 * @var2 1024
	 * @param string x
	 * @param integer y
	 * @param array z
	 */
	private $multipleValuesFixture;


	/**
	 * @get @post @ajax
	 * @postParam x
	 * @postParam y
	 * @postParam z
	 */
	private $sameLineFixture;

	private $emptyFixture;

	/**
	 * @param string var1
	 * @param integer var2
	 */
	private $variableDeclarationsFixture;

	/**
	 * @param false
	 */
	private $badVariableDeclarationFixtureOne;

	/**
	 * @param true
	 */
	private $badVariableDeclarationFixtureTwo;
}