<?php

namespace Minime\Annotations;

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
	public function grep()
	{
		$this->Bag = new AnnotationsBag([
			'get' => true,
			'post' => false,
			'put' => false,
			'val.max' => 16,
			'val.min' => 6,
			'val.regex' => "/[A-z0-9\_\-]+/"
		]);
		
		$this->assertCount(3, $this->Bag->grep('val')->export());
		$this->assertCount(1, $this->Bag->grep('^p')->grep('st$')->export());
		$this->assertSame(['val.max' => 16], $this->Bag->grep('max$')->export());
		$this->assertCount(6, $this->Bag->export());
		$this->Bag->grep([]);
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