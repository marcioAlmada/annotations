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

	public function testLoadFromClass()
	{
		$annotations = Facade::getClassAnnotations($this->Fixture);
		$this->assertSame(TRUE, $annotations->get('post'));
	}

	public function testLoadFromProperty()
	{
		$annotations = Facade::getPropertyAnnotations($this->Fixture, 'same_line_fixture');
		$this->assertSame(TRUE, $annotations->get('post'));
	}

	public function testLoadFromMethod()
	{
		$annotations = Facade::getMethodAnnotations($this->Fixture, 'method_fixture');
		$this->assertSame(TRUE, $annotations->get('post'));
	}
}