<?php

namespace Ogsteam\Ogspy\Helper;

use Ogsteam\Ogspy\Abstracts\Helper_Abstract;

/**
 * Class helloWorld, test
 * @package Ogsteam\Ogspy\Helper
 *
 */
class ToolTip_Helper extends Helper_Abstract
{

    static protected $name ="Helper ToolTip" ;
    static protected $description ="Aide à l'usage de tooltip dans le code HTML" ;
    static protected $version ="0.0.1" ;

    static private $content=null;

    public function __construct()
    {
        if (self::$content == null)
        {
            self::$content=array();
        }
    }

    public function addTooltip($key,$value)
    {
        $retour = true;
        if (isset(self::$content[$key]))
        {
            //gestion de l'erreur dans code ogspy et/ou mod
            return false;
        }
        self::$content[$key] = $value;
        return $retour;
    }


    public function GetHTMLContent()
    {
        $contents = self::$content;
        $retour = "";
        $retour .="<div class=\"tooltip_templates\">";
        foreach ($contents as $key => $value )
        {
            $retour .="<span id=\"".$key."\">";
                $retour .="". html_entity_decode($value)."";
            $retour .="</span>";
        }
        $retour .="</div>";

        //reinitialisation des données
        self::$content = array();

        return $retour;
    }



    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName()." (".$this->version.") [".$this->description."]";
    }

}