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

    static protected $name = "Helper ToolTip";
    static protected $description = "Aide à l'usage de tooltip dans le code HTML";
    static protected $version = "0.0.1";

    static private $content = null;

    private $currentKey = null;


    /**
     * @param Constructeur
     */
    public function __construct()
    {
        if (self::$content == null) {
            self::$content = array();
        }
    }

    /**
     * Retourne la cle courante si elle existe
     *
     * @return string
     */
    public function getCurentKey()
    {
        return $this->currentKey;
    }

    /**
     * Ajoute une cle/ valeur pour la création d'un tooltip
     */
    public function addTooltip($key, $value)
    {
        $retour = true;
        $key=$this->preventJqueryError($key);
        $this->currentKey=$key;
        if (isset(self::$content[$key])) {
            //gestion de l'erreur dans code ogspy et/ou mod
            $this->currentKey=$key;
            return false;
        }
        self::$content[$key] = $value;
        return $retour;
    }

    /**
     * Retourne Le contenu a ajouter dans la class de l'element html permettant l'affichage du tooltip
     * si la cle n'est pas précisé on retourne le courant
     *
     * @param $tclass contient les differents tags à placer en plus du tooltip
     * @return string
     */
    public function GetHTMLClassContent($tClass=array(),$key = null)
    {
        $retour = "";
        if ($key == null)
        {
            $key=$this->getCurentKey();
        }
        else
        {
            $key = $this->preventJqueryError($key);
        }
        $retour .= "class=\"";
        $retour .= "tooltip";
        foreach ($tClass as $classTag)
        {
            $retour .= " ".$classTag." ";
        }
        $retour .= "\"";
        $retour .= " data-tooltip-content=\"#".$key."\" ";

        return $retour;
    }

    /**
     * Retourne Le contenu Html qui  sera afficher au survol
     * ce contenu nest pas directement visible dans la page html (hide())
     *
     * @return string
     */
    public function GetHTMLHideContent()
    {
        $contents = self::$content;
        $retour = "";
        $retour .= "<div class=\"tooltip_templates\" style=\"display:none;\">";
        foreach ($contents as $key => $value) {
            $retour .= "<span id=\"" . $key . "\">";
            $retour .= "" . html_entity_decode($value) . "";
            $retour .= "</span>";
        }
        $retour .= "</div>";

        //reinitialisation des données
        self::$content = array();

        return $retour;
    }

    /**
     * Retroune une cle compatible avec l'element js de tooltip
     *
     * @return string
     */
    public function preventJqueryError($str)
    {
        // attention si "." et ou " " dans les noms ca bugouille
        $str = str_replace(' ', '', $str);
        $str = str_replace('.', '_', $str);

        return $str;
    }

    //contenu javascript permettant d'ectiver le tootltip
    public function activateJs()
    {
        $retour = "";
        $retour .= "<script>";
        $retour .= "    $(document).ready(function () {";
        $retour .= "        $('.tooltip').tooltipster(";
        $retour .= "            {";
        $retour .= "                animation: 'fade',";
        $retour .= "                delay: 600,";
        $retour .= "                contentAsHTML: true,";
        $retour .= "            }";
        $retour .= "        );";
        $retour .= "    });";
        $retour .= "</script>";

        return $retour;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() . " (" . $this->version . ") [" . $this->description . "]";
    }

}