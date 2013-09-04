<?php

namespace Minime\Annotations;

class Reader
{
	private $parameters = [];
	const KEY_PATTERN = "[A-z0-9\_\-]+";
	const END_PATTERN = "[ ]*(?:@|\r\n|\n)";

	public function __construct($rawDocBlock)
	{
		$this->parse($rawDocBlock);
		return $this;
	}

	public function export()
	{
		return $this->parameters;
	}

	public function has($key)
	{
		if(is_string($key))
		{
			if(isset($this->parameters[$key]))
			{
				return true;
			}
			return false;
		}
		throw new \InvalidArgumentException('Annotation key must be a string');
	}

	public function get($key)
	{
		if($this->has($key))
		{
			return $this->parameters[$key];
		}
		return null;
	}

	public function getVariableDeclarations($name)
	{
		$declarations = (array)$this->get($name);

		foreach($declarations as &$declaration)
		{
			$declaration = $this->parseVariableDeclaration($declaration, $name);
		}

		return $declarations;
	}

	private function parse($rawDocBlock)
	{
		$pattern = "/@(?=(.*)".self::END_PATTERN.")/U";

		preg_match_all($pattern, $rawDocBlock, $matches);

		foreach($matches[1] as $rawParameter)
		{
			if(preg_match("/^(".self::KEY_PATTERN.") (.*)$/", $rawParameter, $match))
			{
				if(isset($this->parameters[$match[1]]))
				{
					$this->parameters[$match[1]] = array_merge((array)$this->parameters[$match[1]], (array)$match[2]);
				}
				else
				{
					$this->parameters[$match[1]] = $this->parseValue($match[2]);
				}
			}
			else if(preg_match("/^".self::KEY_PATTERN."$/", $rawParameter, $match))
			{
				$this->parameters[$rawParameter] = TRUE;
			}
		}
	}

	private function parseValue($originalValue)
	{
		if($originalValue && $originalValue !== 'null')
		{
			// try to json decode, if cannot then store as string
			if( ($json = json_decode($originalValue,TRUE)) === NULL)
			{
				$value = $originalValue;
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

	private function parseVariableDeclaration($declaration, $name)
	{
		$type = gettype($declaration);

		if($type !== 'string')
		{
			throw new ReaderException("Raw declaration must be string, $type given. Key='$name'.");
		}

		$declaration = explode(" ", $declaration);

		if($declaration[0] !== "integer" && $declaration[0] !== "string" && $declaration[0] !== "float")
		{
			throw new ReaderException("Type declaration must be 'string' or 'integer'. Invalid type '{$declaration[0]}' given.");
		}

		$declaration[1] = trim($declaration[1]);

		if($declaration[0] === "integer")
		{
			if(!filter_var($declaration[1], FILTER_VALIDATE_INT))
			{
				throw new ReaderException("Raw value must be integer. Invalid value '{$declaration[1]}' given.");
			}
			$declaration[1] = intval($declaration[1]);
		}

		if($declaration[0] === "float")
		{
			if(!filter_var($declaration[1], FILTER_VALIDATE_FLOAT))
			{
				throw new ReaderException("Raw value for must be float. Invalid value '{$declaration[1]}' given.");
			}
			$declaration[1] = floatval($declaration[1]);
		}

		// take first two as type and name
		$declaration = array(
			'type' => $declaration[0],
			'name' => $declaration[1]
		);

		return $declaration;
	}

}
