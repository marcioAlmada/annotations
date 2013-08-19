<?php

namespace Minime\Annotations;

use \ReflectionProperty;

class LoadTest extends \PHPUnit_Framework_TestCase
{
	
	protected $Fixture;

	public function setUp(){
		include_once __DIR__ . "/../../../lib/AnnotationsFixture.php";
		$this->Fixture = new \AnnotationsFixture;
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