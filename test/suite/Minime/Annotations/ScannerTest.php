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
        (new Scanner)->setSource([]);
    }
}
