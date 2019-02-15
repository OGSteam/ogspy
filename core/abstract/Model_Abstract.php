<?php

namespace Ogsteam\Ogspy\Abstracts;


if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}


class Model_Abstract
{
    protected $db;

    public function __construct()
    {
        global $db; //todo prevoir un get_instance mysql
        $this->db = $db;
    }

    function sql_insertid()
    {
        return $this->db->sql_insertid();
    }

    function sql_affectedrows()
    {
        return $this->db->sql_affectedrows();
    }




}