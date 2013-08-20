<?php

namespace Minime\Annotations\Traits;

use \ReflectionProperty;

class ReaderTest extends \PHPUnit_Framework_TestCase
{

	private $Fixture;

	public function setUp()
	{
		include_once __DIR__ . "/../../../../lib/AnnotationsFixture.php";
		$this->Fixture =  $this->Fixture = new \AnnotationsFixture;
	}

	public function testTraitReadsAnnotationsFromClass()
	{
		$this->assertTrue($this->Fixture->getClassAnnotations()->get('post'));
		$this->assertSame(['x', 'y', 'z'], $this->Fixture->getClassAnnotations()->get('postParam'));
		$this->assertFalse($this->Fixture->getClassAnnotations()->has('foo'));
	}

	public function testTraitReadsAnnotationsFromProperty()
	{
		$this->assertTrue($this->Fixture->getPropertyAnnotations('sameLineFixture')->get('post'));
		$this->assertSame(['x', 'y', 'z'], $this->Fixture->getPropertyAnnotations('sameLineFixture')->get('postParam'));
		$this->assertFalse($this->Fixture->getPropertyAnnotations('sameLineFixture')->has('foo'));
	}

	public function testTraitReadsAnnotationsFromMethod()
	{
		$this->assertTrue($this->Fixture->getMethodAnnotations('generalMethodFixture')->get('post'));
		$this->assertSame(['x', 'y', 'z'], $this->Fixture->getMethodAnnotations('generalMethodFixture')->get('postParam'));
		$this->assertFalse($this->Fixture->getMethodAnnotations('generalMethodFixture')->has('foo'));
	}

}