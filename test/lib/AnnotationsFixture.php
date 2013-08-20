<?php

/**
 * @get @post @ajax
 * @postParam x
 * @postParam y
 * @postParam z
 */
class AnnotationsFixture
{

	use Minime\Annotations\Traits\Reader;

	private $emptyFixture;

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

	/**	
	 * @get @post @ajax
	 * @postParam x
	 * @postParam y
	 * @postParam z
	 */
	private function generalMethodFixture()
	{
	}
}