<?php

namespace Minime\Annotations;

use StrScan\StringScanner;

/**
 * Represents a specialized StrScan\StringScanner. This class contains top level
 * scan methods to facilitate annotations parsing.
 *
 * @package Annotations
 * @author  Márcio Almada and the Minime Community
 * @license MIT
 *
 */
class Scanner extends StringScanner
{

    public function skipDocblockLineStart()
    {
        if (! $this->skipLineDocblockMiddle()) {
            if (! $this->skipLineDocblockStart()) {
                $this->terminate(); // terminates earlier in case none of the skip strategies work
            }
        }
    }

    public function skipLineDocblockMiddle()
    {
        return $this->skip('/(\*\s*)/');
    }

    public function skipLineDocblockStart()
    {
        $signal = $this->skip('/(\/\*{2}\s*)/');
        if ($signal) { // tries to skip /**s
            $remainder = trim(str_replace('*/', '', $this->getRemainder()));
            $this->__construct($remainder);
        }

        return $signal;
    }

    public function skipBlankSpace()
    {
        $this->skip('/\s+/');
    }

    public function scanKey($pattern, $identifier)
    {
        $key = $this->scan($pattern);
        if ($key) {
            $this->skipBlankSpace();

            return substr($key, strlen($identifier));
        }
    }

    public function scanImplicitBoolean($identifier)
    {
        return ('' == $this->peek() || $this->check('/\\'.$identifier.'/'));
    }

    public function scanType($types_pattern, $fallback)
    {
        $type = $fallback;
        if ($this->check($types_pattern)) { // if strong typed
            $type = $this->scan('/\w+/');
            $this->skipBlankSpace();
        }

        return $type;
    }

}
