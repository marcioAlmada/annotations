<?php

namespace Minime\Annotations;

class AnnotationsBagTest extends \PHPUnit_Framework_TestCase
{

	private $Bag;

	public function setUp()
	{
		$this->Bag = new AnnotationsBag([
			'get' => true,
			'post' => false,
			'put' => false,
			'val.max' => 16,
			'val.min' => 6,
			'val.regex' => "/[A-z0-9\_\-]+/",
			'config.container' => 'Some\Collection',
			'config.export' => ['json', 'csv']
		]);
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
		$this->assertSame(FALSE, $this->Bag->get('post'));
		$this->assertSame(NULL , $this->Bag->get('bar'));
	}

	/**
	 * @test
	 * @expectedException \InvalidArgumentException
	 */
	public function grep()
	{
		$this->assertCount(8, $this->Bag->export());
		$this->assertCount(3, $this->Bag->grep('val')->export());
		$this->assertCount(2, $this->Bag->grep('config')->export());

		# grep that always matches nothing
		$this->assertCount(0, $this->Bag->grep('^$')->export());

		# chained grep
		$this->assertSame(['val.max' => 16], $this->Bag->grep('max$')->export());
		$this->assertSame(['config.export' => ['json', 'csv']], $this->Bag->grep('export$')->export());

		# should throw exception
		$this->Bag->grep([]);
	}

	/**
	 * @test
	 */
	public function isTraversable()
	{
		foreach ($this->Bag as $annotation => $value)
		{
			$this->assertEquals($value, $this->Bag->get($annotation));
		}

		$this->Bag = new AnnotationsBag([
			'min' => 1,
			'max' => 2,
			'medium' => 3
		]);
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