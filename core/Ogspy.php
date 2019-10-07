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
    public $Configs; //gestion de la config ogspy

    private $isInstall = null;

    private function __construct()
    {
        $this->Params = new Params();
        $this->Configs = new Configs();
    }

    public static function getInstance() {

        if(is_null(self::$instance)) {
            self::$instance = new Ogspy();
        }

        return self::$instance;
    }



    Public function setIsInstall()
    {
        $this->isInstall=true;
    }
    public function setIsNotInstall()
    {
        $this->isInstall=false;
        //hydratation des fichier configs
        $this->Configs->isNotInstall();

    }




}