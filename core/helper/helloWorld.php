<?php

namespace Ogsteam\Ogspy\Helper;

use Ogsteam\Ogspy\Abstracts\Helper_Abstract;

/**
 * Class helloWorld, test
 * @package Ogsteam\Ogspy\Helper
 *
 */
class helloWorld extends Helper_Abstract
{
    /**
     * helloWorld constructor.
     */
    public function __construct()
    {
        $this->name = "hello World";
        $this->description = "hello World";
        $this->version = "0.0.1";
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName()." (".$this->version.") [".$this->description."]";
    }

}