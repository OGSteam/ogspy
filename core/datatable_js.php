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
    private $formatNumber;
    private $toggleVisibility;

    public function __construct($idHtmlTable)
    {
        $this->tableId = $idHtmlTable;

        //default config
        $this->enableFeatures(array("AutoWidth","Info", "LengthChange","Ordering","Paging","Searching"));
        $this->disableFeatures(array("ScrollX","ScrollY"));
        $this->setFormatNumber(true);
        $this->toggleVisibility= null;

    }


    /** Retourne le script JS datatable
     * @return string
     */
    public function getHtml()
    {
        $script = "<script>\n";
        $script .= "$(document).ready(function() {\n";
        $script .= "var $this->tableId =    $('#" . $this->tableId . "').DataTable({\n";

        $script .= $this->getScriptFormatNumber();

        $script .= "            \"language\": {\n";
        $script .= "                \"url\": \"./assets/js/dataTables.french.lang.json\",\n";//todo a faire i18


//todo a faire separateur (fonction format to a priori) / si gereé via php plus consideré comme des nombres (fn de tri + espace vide !!!!!)
        $script .= "            },\n";
        $script .= $this->getScriptFeatures();

        $script .=          "\"lengthMenu\": [[100, 500, 1000, -1], [100, 500, 1000, \"All\"]]"; //todo a faire gestion pagination
        $script .= "        });\n";



        $script .=  $this->getScriptToggleVis();
        $script .= "    } );\n";

        $script .= "</script>\n";

        return $script;




        //todo dynamiquement cacher une colonne
    }

    public function setFormatNumber($enabled = true, $thousandSeparator = " ", $decimalSeparator = ".", $decimalPrecision = "0")
    {
        $this->formatNumber = array(
            "enable" => $enabled,
            "thousandSeparator" => $thousandSeparator,
            "decimalSeparator" => $decimalSeparator,
            "decimalPrecision" => $decimalPrecision,
        );
    }

    public function toggleVisibility($datatableToggleVis , $dataColumn)
    {
        $this->toggleVisibility = array(
            "datatableToggleVis" => $datatableToggleVis,
            "dataColumn" => $dataColumn,
        );
    }



    private function getScriptFormatNumber()
    {
        // sera a approfondir
        $script = "";
        if ($this->formatNumber["enable"] == true )
        {
            $script .= "    \"columnDefs\": [\n";
            $script .= "        {\n";
            $script .= "            \"targets\": \"_all\",\n";
            $script .= "               render: $.fn.dataTable.render.number( ' ', '.', 0, '' )\n";
            $script .= "            },\n";
            $script .= "        ],\n";
        }
        return $script;
    }

    private function getScriptToggleVis()
    {
        $script = "";
        if(is_array($this->toggleVisibility))
        {
            $script .= "\n";
            $script .= "$('a.".$this->toggleVisibility["datatableToggleVis"]."').on( 'click', function (e) {\n";
            $script .= "    e.preventDefault();\n";
            $script .= "    //switch <del></del>\n";
            $script .= "    if ( $(this).css('text-decoration') != 'line-through' )\n";
            $script .= "    {\n";
            $script .= "        $(this).attr('style','text-decoration: line-through;');\n";
            $script .= "    }\n";
            $script .= "    else\n";
            $script .= "    {\n";
            $script .= "        $(this).attr('style','none');\n";
            $script .= "    }\n";
            $script .= "    var idColumn = $(this).attr('".$this->toggleVisibility["dataColumn"]."').split(\"|\");\n";
            $script .= "    for (i = 0; i < idColumn.length; i++) {\n";
            $script .= "        var column = ".$this->tableId.".column(idColumn[i]);\n";
            $script .= "        // Toggle the visibility\n";
            $script .= "        column.visible( ! column.visible() );\n";
            $script .= "        }\n";
            $script .= "    } );\n";
            $script .= "\n";

        }

        return $script;
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


