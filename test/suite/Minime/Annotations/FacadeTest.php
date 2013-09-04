<?php

namespace Minime\Annotations;

use \Minime\Annotations\Fixtures\AnnotationsFixture;

class FacadeTest extends \PHPUnit_Framework_TestCase
{
	
	protected $Fixture;

	public function setUp()
	{
		$this->Fixture = new AnnotationsFixture;
	}

	/**
	 * @test
	 */
	public function getClassAnnotations()
	{
		$annotations = Facade::getClassAnnotations($this->Fixture);
		$this->assertSame(TRUE, $annotations->get('post'));
	}

	/**
	 * @test
	 */
	public function getPropertyAnnotations()
	{
		$annotations = Facade::getPropertyAnnotations($this->Fixture, 'same_line_fixture');
		$this->assertSame(TRUE, $annotations->get('post'));
	}

	/**
	 * @test
	 */
	public function getMethodAnnotations()
	{
		$annotations = Facade::getMethodAnnotations($this->Fixture, 'method_fixture');
		$this->assertSame(TRUE, $annotations->get('post'));
	}
}