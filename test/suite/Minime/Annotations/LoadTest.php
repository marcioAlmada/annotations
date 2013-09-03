<?php

namespace Minime\Annotations;

use \Minime\Annotations\Fixtures\AnnotationsFixture;
use \ReflectionProperty;

class LoadTest extends \PHPUnit_Framework_TestCase
{
	
	protected $Fixture;

	public function setUp()
	{
		$this->Fixture = new AnnotationsFixture;
	}

	public function testLoadFromClass()
	{
		$reader = Load::fromClass($this->Fixture);
		$this->assertSame(TRUE, $reader->get('post'));
	}

	public function testLoadFromProperty()
	{
		$reader = Load::fromProperty($this->Fixture, 'generalFixture');
		$this->assertSame(1, $reader->get('number'));
	}

	public function testLoadFromMethod()
	{
		$reader = Load::fromMethod($this->Fixture, 'generalMethodFixture');
		$this->assertSame(TRUE, $reader->get('post'));
	}
}