<?php

namespace Minime\Annotations;

class Parser
{
	private $raw_doc_block;
	const KEY_PATTERN = "[A-z0-9\_\-]+";
	const END_PATTERN = "[ ]*(?:@|\r\n|\n)";

	public function __construct($raw_doc_block)
	{
		$this->raw_doc_block = $raw_doc_block;
	}

	public function parse()
	{
		$pattern = "/@(?=(.*)".self::END_PATTERN.")/U";
		$parameters = [];

		preg_match_all($pattern, $this->raw_doc_block, $matches);

		foreach($matches[1] as $rawParameter)
		{
			if(preg_match("/^(".self::KEY_PATTERN.") (.*)$/", $rawParameter, $match))
			{		
				$key = $match[1];
				$raw_value = $match[2];
				$value = $this->parseValue($raw_value);
				$parameters[$key][] = $value;
			}
			else if(preg_match("/^".self::KEY_PATTERN."$/", $rawParameter, $match))
			{
				$parameters[$rawParameter] = TRUE;
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


	private function parseValue($original_value)
	{
		$original_value = trim($original_value);

		if($original_value && $original_value !== 'null' && $original_value !== 'NULL')
		{
			$json = json_decode($original_value, TRUE);

			if( $json === NULL)
			{
				$value = $this->parseTypedValues($original_value);
			}
			else
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

	private function parseTypedValues($raw_value)
	{

		$value = $raw_value;

		if(preg_match("/^(integer|string|float) /", $raw_value))
		{
			list($type, $value) = explode(" ", $raw_value);
	
			if($type === "integer")
			{
				if(!filter_var($value, FILTER_VALIDATE_INT))
				{
					throw new ParserException("Raw value must be integer. Invalid value '{$value}' given.");
				}
				$value = intval($value);
			}

			if($type === "float")
			{
				if(!filter_var($value, FILTER_VALIDATE_FLOAT))
				{
					throw new ParserException("Raw value must be float. Invalid value '{$value}' given.");
				}
				$value = floatval($value);
			}
		}

		return $value;
	}

}
