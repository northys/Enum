<?php
/**
MIT License

Copyright (c) 2017 Ondrej Hatala

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
 */
namespace SpareParts\Enum;

use SpareParts\Enum\Exception\InvalidEnumSetupException;
use SpareParts\Enum\Exception\InvalidEnumValueException;
use SpareParts\Enum\Exception\OperationNotAllowedException;

abstract class Enum
{

    /**
     * @var string[]
     */
    protected static $values = [];

    /**
     * @var $this[]
     */
    protected static $instances = [];

    /**
     * @var string
     */
    protected $value;

    /**
     * PROTECTED!! Not callable directly.
     * @see static::instance()
     * @see static::__callStatic()
     * @internal
     *
     * @param string $value
     */
    protected function __construct($value)
    {
        if (empty(static::$values)) {
            throw new InvalidEnumSetupException('Incorrect setup! Enum '.get_called_class().' doesn\'t have its static::$values set.');
        }
        if (!in_array($value, static::$values)) {
            throw new InvalidEnumValueException('Enum '.get_called_class().' does not contain value '.$value.'. Possible values are: '.implode(', ', static::$values));
        }
        $this->value = $value;
    }

    /**
     * String representation
     */
    public function __toString()
    {
        return $this->value;
    }

    /**
     * @param string $method
     * @param array $args
     *
     * @return \SpareParts\Enum\Enum
     */
    public static function __callStatic($method, $args)
    {
        if ($method[0] === '_') {
            $method = substr($method, 1);
        }
        return static::instance($method);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public static function instance($value)
    {
        if (!isset(static::$instances[get_called_class()][$value])) {
            static::$instances[get_called_class()][$value] = new static($value);
        }

        return static::$instances[get_called_class()][$value];
    }

    public function __clone()
    {
        throw new OperationNotAllowedException('Singleton cannot be cloned.');
    }

    public function __sleep()
    {
        throw new OperationNotAllowedException('Singleton cannot be serialized.');
    }

    public function __wakeup()
    {
        throw new OperationNotAllowedException('Singleton cannot be serialized');
    }
}
