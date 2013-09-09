<?php

namespace Minime\Annotations;

use StrScan\StringScanner;

class Parser
{
	private $raw_doc_block;

	public function __construct($raw_doc_block)
	{
		$this->raw_doc_block = $raw_doc_block;
	}

	public function parse()
	{
		$parameters = [];
		$lines = explode("\n", $this->raw_doc_block);
		foreach ($lines as $line) {
			$tokenizer = new StringScanner($line);
			$tokenizer->skip('/\s+\*\s+/');
			while (! $tokenizer->hasTerminated()) {
				$key = $tokenizer->scan('/\@[A-z0-9\_\-]+/');
				if (! $key) {
				    // next line when no annotation is found
					$tokenizer->terminate();
					continue;
				}
				$key = str_replace('@', '', $key);
				$tokenizer->skip('/\s+/');
				if ('' == $tokenizer->peek() || $tokenizer->check('/\@/')) { // if implicit boolean
					$parameters[$key] = true;
				} elseif($tokenizer->check('/(string|integer|float|json)/')) { //if strong typed
					$type = $tokenizer->scan('/\w+/');
					$tokenizer->skip('/\s+/');
					$parameters[$key][] = $this->parseStrongTypedValue($tokenizer->getRemainder(), $type);
				} else { //else weak typed
					$parameters[$key][] = $this->parseWeakTypedValue($tokenizer->getRemainder());
				}
			}
		}

		$parameters = $this->condense($parameters);

		return new AnnotationsBag($parameters);
	}

	private function condense($parameters)
	{
		foreach ($parameters as &$value) {
			if (! is_bool($value) && 1 == count($value)) {
				$value = $value[0];
			}
		}
		unset($value);
		return $parameters;
	}

	private function parseWeakTypedValue($value)
	{
	    if (! isset($value) || 'null' == $value || 'NULL' == $value)) {
	        return null;
	    }
	    $json = json_decode($value);
		if (null !== $json) {
		    return $json;
		}
		return $value;
	}

	private function parseStrongTypedValue($value, $type = null)
	{
        $method = 'parse'.ucfirst($type);
        if (! isset($type) || ! method_exists($this, $method)) {
            return $value;
        }
        return $this->{$method}($value);
	}

	private function parseInteger($value)
	{
	    $value = filter_var($value, FILTER_VALIDATE_INT);
		if(false === $value) {
			throw new \InvalidArgumentException("Raw value must be integer. Invalid value '{$value}' given.");
		}
		return $value;
	}

	private function parseFloat($value)
	{
	    $value = filter_var($value, FILTER_VALIDATE_FLOAT);
		if(false === $value) {
			throw new \InvalidArgumentException("Raw value must be float. Invalid value '{$value}' given.");
		}
		return floatval($value);
	}

	private function parseJson($value)
	{
		$json_decoded = json_decode($value);
		$error = json_last_error();
		if ($error != JSON_ERROR_NONE) {
		   throw new \InvalidArgumentException("Invalid JSON string supplied.");	
		}
		return $json_decoded;
	}
}
