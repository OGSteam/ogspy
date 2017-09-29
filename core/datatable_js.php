<?php
/** OGSpy Charts library (Hightcharts)
 * @package OGSpy
 * @subpackage datatable
 * @author Machine
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.4.0
 */

namespace Ogsteam\Ogspy;

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}


class datatable_js
{

    private $tableId = '';
    private $features;


    public function __construct($idHtmlTable)
    {
        $this->tableId = $idHtmlTable;

        //default config
        $this->enableFeatures(array("AutoWidth","Info", "LengthChange","Ordering","Paging","Searching"));
        $this->disableFeatures(array("ScrollX","ScrollY"));




    }


    /** Retourne le script JS datatable
     * @return string
     */
    public function getHtml()
    {
        $script = "<script>";
        $script .= "$(document).ready(function() {\n";
        $script .= "    $('#" . $this->tableId . "').DataTable({\n";
        $script .= "            \"language\": {\n";
        $script .= "                \"url\": \"./assets/js/dataTables.french.lang.json\",\n";//todo a faire i18
//todo a faire separateur (fonction format to a priori) / si gereé via php plus consideré comme des nombres (fn de tri + espace vide !!!!!)
        $script .= "            },\n";
        $script .= $this->getScriptFeatures();
        $script .= "\"lengthMenu\": [[100, 500, 1000, -1], [100, 500, 1000, \"All\"]]"; //todo a faire gestion pagination
        $script .= "        });\n";
        $script .= "    } );\n";
        $script .= "</script>\n";

        return $script;

        //todo dynamiquement cacher une colonne
    }

    private function getScriptFeatures()
    {
        $tab = array("AutoWidth", "Info", "LengthChange", "Ordering", "Paging", "ScrollX", "ScrollY", "Searching");
        $script = "";
        foreach ($this->features as $key => $value) {
            $valueName = "features" . $value; // recuperation du nom de la valeur
            $value = ($value) ? "true" : "false";
            $script .= "            \"" . strtolower($key) . "\": " . $value . ",\n";
        }
        return $script;
    }

    public function setLangage($lang)
    {
        $tab = array("english", "french", "german", "italian", "spanish");
        //todo voir composer pour ajouter js lang et supprimer celle du dossier js


    }

    public function disableFeatures($names)
    {
        $this ->setFeature($names,false);
    }

    public function enableFeatures($names)
    {
        $this ->setFeature($names,true);
    }

    private function setFeature($data, $value)
    {
        $tab = array("AutoWidth", "Info", "LengthChange", "Ordering", "Paging", "ScrollX", "ScrollY", "Searching");

        if (!is_array($data)) {
            if (in_array($data, $tab)) {
                $this->features[$data] = ($value == true) ? "true" : "false";
            }
        } else {
            foreach ($data as $content) {
                if (in_array($content, $tab)) {
                    $this->features[$content] = ($content == true) ? "true" : "false";
                }
            }
        }

    }


}


