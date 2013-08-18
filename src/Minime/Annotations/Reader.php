<?php

namespace Minime\Annotations;

class Reader
{

	private $rawDocBlock;
	private $parameters = [];
	private $keyPattern = "[A-z0-9\_\-]+";
	private $endPattern = "[ ]*(?:@|\r\n|\n)";

	public function __construct($rawDocBlock)
	{
		$this->rawDocBlock = $rawDocBlock;
		$this->parse();
		return $this;
	}

	public function export()
	{
		return $this->parameters;
	}

	public function get($key)
	{
		if(is_string($key))
		{
			if(isset($this->parameters[$key]))
			{
				return $this->parameters[$key];
			}
			return null;
		}
		throw new \InvalidArgumentException('Annotation key must be a string');
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

	private function parse()
	{
		$pattern = "/@(?=(.*)".$this->endPattern.")/U";

		preg_match_all($pattern, $this->rawDocBlock, $matches);

		foreach($matches[1] as $rawParameter)
		{
			if(preg_match("/^(".$this->keyPattern.") (.*)$/", $rawParameter, $match))
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
			else if(preg_match("/^".$this->keyPattern."$/", $rawParameter, $match))
			{
				$this->parameters[$rawParameter] = TRUE;
			}
			else
			{
				$this->parameters[$rawParameter] = NULL;
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
			throw new ReaderException(
				"Raw declaration must be string, $type given. Key='$name'.");
		}

		if(strlen($declaration) === 0)
		{
			throw new ReaderException(
				"Raw declaration cannot have zero length. Key='$name'.");
		}

		$declaration = explode(" ", $declaration);
		if(sizeof($declaration) == 1)
		{
			// string is default type
			array_unshift($declaration, "string");
		}

		// take first two as type and name
		$declaration = array(
			'type' => $declaration[0],
			'name' => $declaration[1]
		);

		return $declaration;
	}

}
