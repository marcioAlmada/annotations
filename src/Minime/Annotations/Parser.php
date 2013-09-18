<?php

namespace Minime\Annotations;

use StrScan\StringScanner;

class Parser
{
	/**
	 * The DocBlock extracted from source
	 * @var string
	 */
	private $raw_doc_block;

	/**
	 * @param string $raw_doc_block A DocBlock string to be parsed
	 */
	public function __construct($raw_doc_block)
	{
		$this->raw_doc_block = $raw_doc_block;
	}

	/**
	 * Iterates through raw DocBlock line by line and uses a tokenizer
	 * to parse and extract all found annotations
	 * 
	 * @return Minime\Annotations\AnnotationsBag Annotations collection
	 */
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

		$parameters = $this->condense($parameters);

		return new AnnotationsBag($parameters);
	}

	private function condense($parameters)
	{
		foreach ($parameters as &$value)
		{
			if(!is_bool($value))
			{
				if(count($value) === 1)
				{
					$value = $value[0];
				}			
			}
		}

		return $parameters;
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
			return $this->parseInteger($value);
		}

		else if($type === "float")
		{
			return $this->parseFloat($value);
		}

		else if($type === "json")
		{
			return $this->parseJSON($value);
		}

		return $value;
	}

	private function parseInteger($value)
	{
		if(!filter_var($value, FILTER_VALIDATE_INT))
		{
			throw new ParserException("Raw value must be integer. Invalid value '{$value}' given.");
		}
		return intval($value);
	}

	private function parseFloat($value)
	{
		if(!filter_var($value, FILTER_VALIDATE_FLOAT))
		{
			throw new ParserException("Raw value must be float. Invalid value '{$value}' given.");
		}
		return floatval($value);
	}

	private function parseJSON($value)
	{
		$json_decoded = json_decode($value);
		if( $json_decoded === NULL)
		{
			throw new ParserException("Invalid JSON string supplied.");	
		}
		return $json_decoded;
	}
}