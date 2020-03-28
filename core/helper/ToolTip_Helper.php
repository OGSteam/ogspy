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
        $retour .= "<div class=\"tooltip_templates\" style=\"display:none;\">\n";
        foreach ($contents as $key => $value) {
            $retour .= "<div id=\"" . $key . "\">\n";
            $retour .= "" . html_entity_decode($value) . "\n";
            $retour .= "</div>\n";
        }
        $retour .= "\n</div>";

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
        $retour = "\n";
        $retour .= "<script type=\"text/javascript\">\n";
        $retour .= " $(document).ready(function () {\n";
        $retour .= "  $('.tooltip').tooltipster(\n";
        $retour .= "   {\n";
        $retour .= "    animation: 'fade',\n";
        $retour .= "    delay: 400,\n";
        $retour .= "    contentAsHTML: true,\n";
        $retour .= "    theme: ['tooltipster-noir', 'tooltipster-noir-customized'],\n";
        $retour .= "   }\n";
        $retour .= "  );\n";
        $retour .= " });\n";
        $retour .= "</script>\n";

        $retour .= $this->customCSS();

        return $retour;
    }


    public function customCSS()
    {
        $retour = "\n";
        $retour .= "<style type=\"text/css\">\n";
        $retour .= " .tooltipster-sidetip.tooltipster-noir.tooltipster-noir-customized .tooltipster-box {\n";
        $retour .= "  background: rgba(0,0,0,0.8);\n";
        $retour .= "  border: 1px solid black;\n";
        $retour .= "  border-radius: 6px;\n";
        $retour .= "  box-shadow: 5px 5px 2px 0 rgba(0,0,0,0.4);\n";
        $retour .= " }\n";
        $retour .= "</style>\n";
        return $retour;
}


        //.tooltipster-sidetip.tooltipster-noir.tooltipster-noir-customized .tooltipster-box {
    // background: grey;
    //   border: 3px solid red;
//	border-radius: 6px;
//	box-shadow: 5px 5px 2px 0 rgba(0,0,0,0.4);
//}

//.tooltipster-sidetip.tooltipster-noir.tooltipster-noir-customized .tooltipster-content {
    //       color: blue;
    //     padding: 8px;
//}
    //  }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() . " (" . $this->version . ") [" . $this->description . "]";
    }

}