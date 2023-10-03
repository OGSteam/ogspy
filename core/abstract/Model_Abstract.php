<?php

namespace Ogsteam\Ogspy\Abstracts;


if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}


abstract class Model_Abstract
{
    protected $db;

    /**
     * Constructs a new instance of the class and initializes the database connection.
     *
     * @throws Some_Exception_Class description of exception
     */
    public function __construct()
    {
        global $db; //todo prevoir un get_instance mysql
        $this->db = $db;
    }

    /**
     * Returns the ID generated from the previous INSERT operation.
     *
     * @return int The ID generated from the previous INSERT operation.
     */
    public function sql_insertid()
    {
        return $this->db->sql_insertid();
    }

    /**
     * Returns the number of rows affected by the previous SQL operation.
     *
     * @return int The number of affected rows.
     */
    public function sql_affectedrows()
    {
        return $this->db->sql_affectedrows();
    }
}
