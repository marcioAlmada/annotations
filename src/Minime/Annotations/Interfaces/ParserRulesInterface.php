<?php

namespace Minime\Annotations\Interfaces;

interface ParserRulesInterface
{
    public function isValidKey($key);

    public function getRegexAnnotationName();

    public function getRegexAnnotationIdentifier();
}
