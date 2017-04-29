<?php

namespace Minime\Annotations;

/**
 * @property Types\IntegerType $integerType
 * @property Types\StringType $stringType
 * @property Types\FloatType $floatType
 * @property Types\JsonType $jsonType
 * @property Types\ConcreteType $concreteType
 */
class TypeContainer
{
    /**
     * Stores the lambda functions for each type.
     *
     * @var array
     */
    private $builders;

    /**
     * Stores built types.
     *
     * @var array
     */
    private $types;

    /**
     * Add a lambda function.
     *
     * @param string $name
     */
    public function add($name)
    {
        $this->builders[$name] = function() use($name) {
            $type = 'Minime\\Annotations\\Types\\' . ucfirst($name);

            // do we have in default configuration setup?
            if (!class_exists($type)) {
                $type = $name;
            }

            if (is_callable($type . '::getType')) {
                $typeClass = call_user_func($type . '::getType');
            } else {
                $typeClass = new $type;
            }

            return new $typeClass;
        };
    }

    /**
     * Remove a lambda function.
     *
     * @param string $name
     */
    public function remove($name)
    {
        unset($this->builders[$name]);
        unset($this->types[$name]);
    }

    /**
     * @param string $name
     *
     * @return TypeInterface
     */
    public function __get($name)
    {
        if (!isset($this->types[$name]) && isset($this->builders[$name])) {
            $this->types[$name] = $this->builders[$name]();
        }

        return $this->types[$name];
    }
}
