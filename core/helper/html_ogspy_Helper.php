<?php

namespace Ogsteam\Ogspy\Helper;

use Ogsteam\Ogspy\Abstracts\Helper_Abstract;

/**
 * Class helloWorld, test
 * @package Ogsteam\Ogspy\Helper
 *
 */
class html_ogspy_Helper extends Helper_Abstract
{

    static protected $name ="HTML Helper" ;
    static protected $description ="bibliotheque de fonction d'aide a la contruction d element HTML OGSpy complexe" ;
    static protected $version ="0.0.1" ;

    /**
     * helloWorld constructor.
     */
    public function __construct()
    {

    }




    /**
     * Retourne une barre de navigation
     *
     * @return string
     */
    public function navbarreMenu($name , $datalinks)
    {
        //protection valeur nulle
        $name = is_null($name) ? "" : $name ;

        $html = "<nav class=\"navbarreMenu ".$name." \">";
        $html .= "    <ul>";
        foreach ($datalinks as $link) {
            // protection data
            $tag = isset($link["tag"]) ? $link["tag"] : "" ;
            $linktag = isset($link["tag"]) ? $link["tag"] : "" ;
            $url = isset($link["url"]) ? $link["url"] : "" ;
            $content = isset($link["content"]) ? $link["content"] : "" ;

            //exploitation
            $html .= "        <li class=\"" . $tag . "\">";
            $html .= "          <a class=\"" .$linktag . " \" href=\"" .  $url  . "\">".$content."</a>";
            $html .= "         </li>";
        }
        $html .= "    </ul>";
        $html .= "</nav>";

        return $html;

    }

    /**
     * Retourne une messagebox
     *
     * @param string $title titre de la messagebox.
     * @param string $message texte de la messagebox.
     * @param bool $closebutton possibilité de fermer la boite
     * @param string $type default|alert|success
     *
     * @return string
     */
    public function msgBox($title, $message, $closebutton = false, $type ="default")
    {
        $html = "";
        $html .= "<div class=\"msgbox msgbox".$type."\">\n";
        $html .= "<p class=\"msgboxtitle\">".$title." ";
        if ($closebutton)
        {
            $html .= "<span class=\"msgboxclosebtn\" onclick=\"this.parentElement.parentElement.style.display='none';\">&times;</span>";
        }
        $html .= "</p>\n";
        $html .= "<p class=\"msgcontent\">";
        $html .= $message;
        $html .= "</p>\n";
        $html .= "</div>\n";

        return $html;
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName()." (".$this->version.") [".$this->description."]";
    }

}