<?php
namespace Ogsteam\Ogspy\Abstracts;

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}


abstract class Helper_Abstract
{
    protected  $version;
    protected  $name;
    protected  $description;

    public function getVersion()
    {
        return $this->version;
    }

    public function getName()
    {
        return $this->name ;
    }

    public function getDescription()
    {
        return $this->description ;
    }




}