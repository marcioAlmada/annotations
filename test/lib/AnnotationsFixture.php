<?php

class AnnotationsFixture
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