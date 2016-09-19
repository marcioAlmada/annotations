<?php

namespace Minime\Annotations;

use \ReflectionProperty;
use Minime\Annotations\Fixtures\AnnotationsFixture;

/**
 * BaseTest
 *
 */
abstract class BaseTest extends \PHPUnit_Framework_TestCase
{
    protected $fixture;

    protected $parser;

    public function setUp()
    {
        $this->fixture = new AnnotationsFixture;
    }

    protected function getDocblock($fixture)
    {
        $reflection = new ReflectionProperty($this->fixture, $fixture);

        return $reflection->getDocComment();
    }

    protected function getFixture($fixture)
    {
        return $this->parser->parse($this->getDocblock($fixture));
    }
}
