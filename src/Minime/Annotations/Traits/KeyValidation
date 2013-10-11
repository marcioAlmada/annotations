<?php

namespace Minime/Annotations/Traits/KeyValidation;

traits KeyValidation
{

    private $regexAnnotationName = '[a-zA-Z\_][a-zA-Z0-9\_\-\.]*';

    private $regexAnnotationIdentifier = '@';

    public function isValidKey($key)
    {
        return preg_match('/'.$this->regexAnnotationName.'/', $key);
    }

    public function getRegexAnnotationName()
    {
        return $this->regexAnnotationName;
    }

    public function getRegexAnnotationIdentifier()
    {
        return $this->regexAnnotationIdentifier;
    }
}
