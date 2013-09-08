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
		
		foreach ( $lines as $line)
		{
			$tokenizer = new StringScanner($line);
			$tokenizer->skip('/\s+\*\s+/');

			while(!$tokenizer->hasTerminated())
			{
				$key = $tokenizer->scan('/\@[A-z0-9\_\-]+/');
				if($key)
				{
					$key = str_replace('@', '', $key);
					$tokenizer->skip('/\s+/');

					# if implicit boolean
					if($tokenizer->peek() === "" || $tokenizer->check('/\@/'))
					{
						$parameters[$key] = true;
					}

					# if strong typed
					else if($tokenizer->check('/(string|integer|float|json)/'))
					{
						$type = $tokenizer->scan('/\w+/');
						$tokenizer->skip('/\s+/');
						$raw_value = $tokenizer->getRemainder();
						$parameters[$key][] = $this->parseStrongTypedValue($raw_value, $type);
					}

					# else weak typed
					else
					{
						$value = $tokenizer->getRemainder();
						$parameters[$key][] = $this->parseWeakTypedValue($value);
					}
				}
				else
				{
					# next line when no annotation is found
					$tokenizer->terminate();
				}
			}
		}

		foreach ($parameters as $key => &$value)
		{
			if(!is_bool($value))
			{
				if(count($value) === 1)
				{
					$value = $value[0];
				}			
			}
		}
		return new AnnotationsBag($parameters);
	}

	private function parseWeakTypedValue($value)
	{
		if($value && $value !== 'null' && $value !== 'NULL')
		{
			$json = json_decode($value);

			if( $json !== NULL)
			{
				$value = $json;
			}
		}
		else
		{
			$value = NULL;
		}
		return $value;
	}

	private function parseStrongTypedValue($value, $type)
	{

		if($type === "integer")
		{
			if(!filter_var($value, FILTER_VALIDATE_INT))
			{
				throw new ParserException("Raw value must be integer. Invalid value '{$value}' given.");
			}
			$value = intval($value);
		}

		else if($type === "float")
		{
			if(!filter_var($value, FILTER_VALIDATE_FLOAT))
			{
				throw new ParserException("Raw value must be float. Invalid value '{$value}' given.");
			}
			$value = floatval($value);
		}

		else if($type === "string")
		{

		}

		else if($type === "json")
		{
			$json = json_decode($value);

			if( $json !== NULL)
			{
				$value = $json;
			}
			else
			{
				throw new ParserException("Invalid JSON string supplied.");	
			}
		}

		return $value;
	}

}