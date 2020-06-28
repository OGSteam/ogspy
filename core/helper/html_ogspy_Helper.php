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
    static protected $description ="bibliotheque de fonction d'aide a la contruction d element HTML OGSpy complexe et réutilisable" ;
    static protected $version ="0.0.2" ;

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
     * Retourne une vue html des derniers classements d une alliance
     *
     * @param string $allyname nom de l'alliance recherché
     * @param array $classtag tag a ajouter au tableau
     *
     * @return string
     */
    public function show_html_ranking_unique_ally($allyname,$classtag = array() )
    {
        global $lang;
        $tags = implode(' ' , $classtag);
        $individual_ranking = galaxy_show_ranking_unique_ally($allyname);
        $html="";
        $html .= "<table class=\"".$tags."\">";
        $html .= "<thead><tr><th colspan='3'>" . $lang['GALAXY_ALLY'] . " " . $allyname . "</th></tr></thead>";
        $html .= "<tbody>";
        while ($ranking = current($individual_ranking)) {
            $datadate = strftime("%d %b %Y %H:%M", key($individual_ranking));
            $general_rank = isset($ranking["general"]) ? formate_number($ranking["general"]["rank"]) : '&nbsp;';
            $general_points = isset($ranking["general"]) ? formate_number($ranking["general"]["points"]) : '&nbsp;';
            $eco_rank = isset($ranking["eco"]) ? formate_number($ranking["eco"]["rank"]) : '&nbsp;';
            $eco_points = isset($ranking["eco"]) ? formate_number($ranking["eco"]["points"]) : '&nbsp;';
            $techno_rank = isset($ranking["techno"]) ? formate_number($ranking["techno"]["rank"]) : '&nbsp;';
            $techno_points = isset($ranking["techno"]) ? formate_number($ranking["techno"]["points"]) : '&nbsp;';
            $military_rank = isset($ranking["military"]) ? formate_number($ranking["military"]["rank"]) : '&nbsp;';
            $military_points = isset($ranking["military"]) ? formate_number($ranking["military"]["points"]) : '&nbsp;';
            $military_b_rank = isset($ranking["military_b"]) ? formate_number($ranking["military_b"]["rank"]) : '&nbsp;';
            $military_b_points = isset($ranking["military_b"]) ? formate_number($ranking["military_b"]["points"]) : '&nbsp;';
            $military_l_rank = isset($ranking["military_l"]) ? formate_number($ranking["military_l"]["rank"]) : '&nbsp;';
            $military_l_points = isset($ranking["military_l"]) ? formate_number($ranking["military_l"]["points"]) : '&nbsp;';
            $military_d_rank = isset($ranking["military_d"]) ? formate_number($ranking["military_d"]["rank"]) : '&nbsp;';
            $military_d_points = isset($ranking["military_d"]) ? formate_number($ranking["military_d"]["points"]) : '&nbsp;';
            $honnor_rank = isset($ranking["honnor"]) ? formate_number($ranking["honnor"]["rank"]) : '&nbsp;';
            $honnor_points = isset($ranking["honnor"]) ? formate_number($ranking["honnor"]["points"]) : '&nbsp;';

            $html .= '<tr><th colspan="3">' . $lang['GALAXY_RANK'] . ' ' . $datadate . '</th></tr>';
            $html .= '<tr><th>' . $lang['GALAXY_RANK_GENERAL'] . '</th><td>' . $general_rank . '</td><td>' . $general_points . '</td></tr>';
            $html .= '<tr><th>' . $lang['GALAXY_RANK_ECONOMY'] . '</th><td>' . $eco_rank . '</td><td>' . $eco_points . '</td></tr>';
            $html .= '<tr><th>' . $lang['GALAXY_RANK_LAB'] . '</th><td>' . $techno_rank . '</td><td>' . $techno_points . '</td></tr>';
            $html .= '<tr><th>' . $lang['GALAXY_RANK_MILITARY'] . '</th><td>' . $military_rank . '</td><td>' . $military_points . '</td></tr>';
            $html .= '<tr><th>' . $lang['GALAXY_RANK_MILITARY_BUILT'] . '</th><td>' . $military_b_rank . '</td><td>' . $military_b_points . '</td></tr>';
            $html .= '<tr><th>' . $lang['GALAXY_RANK_MILITARY_LOST'] . '</th><td>' . $military_l_rank . '</td><td>' . $military_l_points . '</td></tr>';
            $html .= '<tr><th>' . $lang['GALAXY_RANK_MILITARY_DESTROYED'] . '</th><td>' . $military_d_rank . '</td><td>' . $military_d_points . '</td></tr>';
            $html .= '<tr><th>' . $lang['GALAXY_RANK_MILITARY_HONNOR'] . '</th><td>' . $honnor_rank . '</td><td>' . $honnor_points . '</td></tr>';
            $html .= '<tr><th colspan="3">' . formate_number($ranking["number_member"]) . " " . $lang['GALAXY_MEMBERS'] . '</th></tr>';
            break;
        }
        $html .= '<tr><td colspan="3"><a href="index.php?action=search&amp;type_search=ally&amp;string_search=' . $allyname . '&strict=on">' . $lang['GALAXY_SEE_DETAILS'] . '</a></td></tr>';



        $html .= "</tbody>";
        $html .= "</table>";
        return $html;
    }


    /**
     * Retourne une vue html des derniers classements d un joueur
     *
     * @param string $playername nom de l'alliance recherché
     * @param array $classtag tag a ajouter au tableau
     *
     * @return string
     */
    public function show_html_ranking_unique_player($playername,$classtag = array() )
    {
        global $lang;
        $tags = implode(' ' , $classtag);
        $individual_ranking = galaxy_show_ranking_unique_player($playername);
        $html="";
        $html .= "<table class=\"".$tags."\">";
        $html .= "<thead><tr><th colspan='3'>" . $lang['GALAXY_PLAYER'] . " " . $playername . "</th></tr></thead>";
        $html .= "<tbody>";
        while ($ranking = current($individual_ranking)) {
            $datadate = strftime("%d %b %Y %H:%M", key($individual_ranking));
            $general_rank = isset($ranking["general"]) ? formate_number($ranking["general"]["rank"]) : '&nbsp;';
            $general_points = isset($ranking["general"]) ? formate_number($ranking["general"]["points"]) : '&nbsp;';
            $eco_rank = isset($ranking["eco"]) ? formate_number($ranking["eco"]["rank"]) : '&nbsp;';
            $eco_points = isset($ranking["eco"]) ? formate_number($ranking["eco"]["points"]) : '&nbsp;';
            $techno_rank = isset($ranking["techno"]) ? formate_number($ranking["techno"]["rank"]) : '&nbsp;';
            $techno_points = isset($ranking["techno"]) ? formate_number($ranking["techno"]["points"]) : '&nbsp;';
            $military_rank = isset($ranking["military"]) ? formate_number($ranking["military"]["rank"]) : '&nbsp;';
            $military_points = isset($ranking["military"]) ? formate_number($ranking["military"]["points"]) : '&nbsp;';
            $military_b_rank = isset($ranking["military_b"]) ? formate_number($ranking["military_b"]["rank"]) : '&nbsp;';
            $military_b_points = isset($ranking["military_b"]) ? formate_number($ranking["military_b"]["points"]) : '&nbsp;';
            $military_l_rank = isset($ranking["military_l"]) ? formate_number($ranking["military_l"]["rank"]) : '&nbsp;';
            $military_l_points = isset($ranking["military_l"]) ? formate_number($ranking["military_l"]["points"]) : '&nbsp;';
            $military_d_rank = isset($ranking["military_d"]) ? formate_number($ranking["military_d"]["rank"]) : '&nbsp;';
            $military_d_points = isset($ranking["military_d"]) ? formate_number($ranking["military_d"]["points"]) : '&nbsp;';
            $honnor_rank = isset($ranking["honnor"]) ? formate_number($ranking["honnor"]["rank"]) : '&nbsp;';
            $honnor_points = isset($ranking["honnor"]) ? formate_number($ranking["honnor"]["points"]) : '&nbsp;';

            $html .= '<tr><td colspan="3">' . $lang['GALAXY_RANK'] . " " . $datadate . '</td></tr>';
            $html .= '<tr><td>' . $lang['GALAXY_RANK_GENERAL'] . '</td><th style="width:30px">' . $general_rank . '</th><th>' . $general_points . '</th></tr>';
            $html .= '<tr><td>' . $lang['GALAXY_RANK_ECONOMY'] . '</td><th>' . $eco_rank . '</th><th>' . $eco_points . '</th></tr>';
            $html .= '<tr><td>' . $lang['GALAXY_RANK_LAB'] . '</td><th>' . $techno_rank . '</th><th>' . $techno_points . '</th></tr>';
            $html .= '<tr><td>' . $lang['GALAXY_RANK_MILITARY'] . '</td><th style="width:30px">' . $military_rank . '</th><th>' . $military_points . '</th></tr>';
            $html .= '<tr><td>' . $lang['GALAXY_RANK_MILITARY_BUILT'] . '</td><th>' . $military_b_rank . '</th><th>' . $military_b_points . '</th></tr>';
            $html .= '<tr><td>' . $lang['GALAXY_RANK_MILITARY_LOST'] . '</td><th>' . $military_l_rank . '</th><th>' . $military_l_points . '</th></tr>';
            $html .= '<tr><td>' . $lang['GALAXY_RANK_MILITARY_DESTROYED'] . '</td><th>' . $military_d_rank . '</th><th>' . $military_d_points . '</th></tr>';
            $html .= '<tr><td >' . $lang['GALAXY_RANK_MILITARY_HONNOR'] . '</td><th>' . $honnor_rank . '</th><th>' . $honnor_points . '</th></tr>';
            break;
        }
        $html .= '<tr><tdcolspan="3"><a href="index.php?action=search&amp;type_search=player&amp;string_search=' . $playername . '&amp;strict=on">' . $lang['GALAXY_SEE_DETAILS'] . '</a></td></tr>';


        $html .= "</tbody>";
        $html .= "</table>";
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