<?php

namespace Minime\Annotations;

use \Minime\Annotations\Fixtures\AnnotationsFixture;
use \ReflectionProperty;

class AnnotationsBagTest extends \PHPUnit_Framework_TestCase
{

	private $Bag;

	public function setUp()
	{
		$this->Bag = new AnnotationsBag(['bar' => 'baz']);
	}

	/**
	 * @test
	 * @expectedException \InvalidArgumentException
	 */
	public function constructAcceptsOnlyArrays()
	{
		new AnnotationsBag('');
	}

	/**
	 * @test
	 */
	public function nullForUnsetAnnotation()
	{
		$this->assertSame(null, $this->Bag->get('foo'));
		$this->assertSame('baz', $this->Bag->get('bar'));
	}

	/**
	 * @test
	 * @expectedException \InvalidArgumentException
	 */
	public function getAcceptsOnlyStringKeys()
	{
		$this->Bag->get(0);
	}

}