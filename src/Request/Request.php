<?php

/*
 * This file is part of the fw4/skarabee-weblink library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skarabee\Weblink\Request;

use DateTime;

class Request
{
    /** @var string */
    protected $function;

    /** @var array */
    protected $parameters;

    public function __construct(string $function, array $parameters = [])
    {
        $this->setFunction($function);
        $this->setParameters($parameters);
    }

    /**
     * Set the SOAP function name to execute.
     *
     * @param string $function
     *
     * @return self
     */
    public function setFunction(string $function): Request
    {
        $this->function = $function;
        return $this;
    }

    /**
     * Get the SOAP function name to execute.
     *
     * @return string
     */
    public function getFunction(): string
    {
        return $this->function;
    }

    /**
     * Set the SOAP parameters.
     *
     * @param array $parameters Associative array of parameter names and values
     *
     * @return self
     */
    public function setParameters(array $parameters): Request
    {
        $this->parameters = $this->encode($parameters);
        return $this;
    }

    /**
     * Get the SOAP parameters.
     *
     * @return array Associative array of parameter names and values
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Recursively encode a value into a supported format.
     *
     * @param mixed $encodable
     *
     * @return self
     */
    protected function encode($encodable)
    {
        if (is_array($encodable)) {
            foreach ($encodable as $key => $value) {
                $encodable[$key] = $this->encode($value);
            }
        } elseif ($encodable instanceof DateTime) {
            return $encodable->format('c');
        }
        return $encodable;
    }
}
