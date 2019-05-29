<?php

namespace Ogsteam\Ogspy\Helper;

use Ogsteam\Ogspy\Abstracts\Helper_Abstract;

/**
 * Class helloWorld, test
 * @package Ogsteam\Ogspy\Helper
 *
 */
class helloWorld_Helper extends Helper_Abstract
{

    static protected $name ="hello World" ;
    static protected $description ="Helper hello World" ;
    static protected $version ="0.0.1" ;


    /**
     * helloWorld constructor.
     */
    public function __construct()
    {

    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName()." (".$this->version.") [".$this->description."]";
    }

}