<?php

namespace Minime\Annotations;

use PHPUnit_Framework_TestCase;

/**
 * @group parser
 */
class ScannerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidTypeSource()
    {
        (new Scanner('foo', 'bar'))->setSource([]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIdentifierSetterGetter()
    {
        $scanner = new Scanner('foo', 'bar');
        $scanner->setIdentifier('@');
        $this->assertSame('@', $scanner->getIdentifier());
        $scanner->setIdentifier([]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPatternSetterGetter()
    {
        $scanner = new Scanner('foo', 'bar');
        $scanner->setPattern('@');
        $this->assertSame('@', $scanner->getPattern());
        $scanner->setPattern([]);
    }
}
