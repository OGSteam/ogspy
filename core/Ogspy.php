<?php
namespace Ogsteam\Ogspy\Core;


/**
 * Class Principale
 * Logique propre d'ogspy
 *
 */
class Ogspy
{
    private static $instance = null;

    public $Params; //stockage et gestion des __get / __post
    private Static $Configs; //gestion de la config ogspy


    private function __construct()
    {
            $this->Params = new Params();
    }

    public static function getInstance() {

        if(is_null(self::$instance)) {
            self::$instance = new Ogspy();
        }

        return self::$instance;
    }





}