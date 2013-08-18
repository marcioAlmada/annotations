<?php

namespace Minime\Annotations;

use \ReflectionProperty;

class LoadTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @post
	 */
	protected $fixture;

	public function testLoadFromProperty()
	{
		$reader = Load::fromProperty($this, 'fixture');
		$this->assertSame(TRUE, $reader->get('post'));
	}
}