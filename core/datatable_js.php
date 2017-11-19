<?php
/** OGSpy Charts library (Hightcharts)
 * @package OGSpy
 * @subpackage datatable
 * @author Machine
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.4.0
 *
 *
 * usage
 * $idHtmlTable = "IDtABLE";     //html id from table
 *$datatable = new \Ogsteam\Ogspy\datatable_js($idHtmlTable); // create object
 * Enjoy
 *
 * More confirguration :
 *
 *
 * --- features---
 * -activate-
 * $datatable->enableFeatures(array("AutoWidth","Info"));
 * -desactivate
 * disableFeatures(array("AutoWidth","Info"));
 * by default activate : "AutoWidth","Info", "LengthChange","Ordering","Paging","Searching",   desactivate "ScrollX","ScrollY"
 * list of feature : "AutoWidth", "Info", "LengthChange", "Ordering", "Paging", "ScrollX", "ScrollY", "Searching"
 *
 * --setFormatNumber--
 * $datatable->$this->setFormatNumber(true); // by default is true
 *
 * --setFormatNumber--
 *  * $datatable->setPaginate(array(100, 500, 1000, -1));; // by default array(100, 500, 1000, -1)
 * if "-1", all rows visible
 *
 *
 * --toggleVisibility--
 * you can hide or unhide colomn with <a> link
 * $datatable->toggleVisibility("datatable-toggle-vis","data-column");
 * -html code-
 * <a class="datatable-toggle-vis" data-column="2">colonne a effacer</a>
 * for multiple colomn
 * <a class="datatable-toggle-vis" data-column="2|3">colonnes a effacer</a>
 *
 * cf. https://datatables.net/
 *
 */

namespace Ogsteam\Ogspy;

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}


class Datatable_js
{
    private $tableId = '';
    private $features;
    private $formatNumber;
    private $toggleVisibility;
    private $arrayPages;

    /**
     * datatable_js constructor.
     * @param $idHtmlTable
     */
    public function __construct($idHtmlTable)
    {
        $this->tableId = $idHtmlTable;

        //default config
        $this->enableFeatures(array("AutoWidth", "Info", "LengthChange", "Ordering", "Paging", "Searching"));
        $this->disableFeatures(array("ScrollX", "ScrollY"));
        $this->setFormatNumber(true);
        $this->setPaginate(array(100, 500, 1000, -1));
        $this->toggleVisibility = null;

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
        $script .= $this->getScriptLangage();
        $script .= $this->getScriptFeatures();
        $script .= $this->getScriptPaginate();
        $script .= "        });\n";

            $script .= $this->getScriptToggleVis();
        $script .= "    } );\n";

        $script .= "</script>\n";

        return $script;

    }

    /**
     * active et parametrage du formatage nombre
     * @param bool $enabled
     * @param string $thousandSeparator
     * @param string $decimalSeparator
     * @param string $decimalPrecision
     */
    public function setFormatNumber($enabled = true, $thousandSeparator = " ", $decimalSeparator = ".", $decimalPrecision = "0")
    {
        $this->formatNumber = array(
            "enable" => $enabled,
            "thousandSeparator" => $thousandSeparator,
            "decimalSeparator" => $decimalSeparator,
            "decimalPrecision" => $decimalPrecision,
        );
    }


        public function setPaginate($arrayPages)
        {
            $return = array();
            if (is_array($arrayPages))
            {
                foreach ($arrayPages as $pages)
                {
                    $return[] = (int) $pages;
                }

            }
            $this->arrayPages = $return;
        }


    /**
     * mise en place de l'affichage ou non de cononne dynamiquement
     * via lien html
     * @param $datatableToggleVis
     * @param $dataColumn
     */
    public function toggleVisibility($datatableToggleVis, $dataColumn)
    {
        $this->toggleVisibility = array(
            "datatableToggleVis" => $datatableToggleVis,
            "dataColumn" => $dataColumn,
        );
    }


    /**
     * retourne la partie du script JS gérant le formatage des nombres
     * @return string
     */
    private function getScriptFormatNumber()
    {
        // sera a approfondir
        $script = "";
        if ($this->formatNumber["enable"] == true)
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

    /**
     * retourne la partie du script JS gérant dynamiquement l'affichage ou non des colonnes
     * @return string
     */
    private function getScriptToggleVis()
    {
        $script = "";
        if (is_array($this->toggleVisibility))
        {
            $script .= "\n";
            $script .= "$('a." . $this->toggleVisibility["datatableToggleVis"] . "').on( 'click', function (e) {\n";
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
            $script .= "    var idColumn = $(this).attr('" . $this->toggleVisibility["dataColumn"] . "').split(\"|\");\n";
            $script .= "    for (i = 0; i < idColumn.length; i++) {\n";
            $script .= "        var column = " . $this->tableId . ".column(idColumn[i]);\n";
            $script .= "        // Toggle the visibility\n";
            $script .= "        column.visible( ! column.visible() );\n";
            $script .= "        }\n";
            $script .= "    } );\n";
            $script .= "\n";

        }

        return $script;
    }

    /**
     * mise en place de la pagination
     *
     * @return string
     * @internal param $datatableToggleVis
     * @internal param $dataColumn
     */
private function getScriptPaginate()
    {

        $script = "";
        $script .= "\"lengthMenu\": [[" . implode(" , ", $this->arrayPages) . "], [" . implode(" , ", str_replace('-1', '"All"', $this->arrayPages)) . "]]"; //todo a faire gestion pagination
        return $script;
    }


    /**
     * @return string
     */
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

    /**
     * @return string
     */
    private function getScriptLangage()
    {
        global $lang; //import variable i18

        $script = "";
        $script .= "            \"language\": {\n";
        $script .= "                \"sProcessing\":     \"" . $lang['DATATABLE_JS_sProcessing'] . "\",\n";
        $script .= "                \"sSearch\":         \"" . $lang['DATATABLE_JS_sSearch'] . "\",\n";
        $script .= "                \"sLengthMenu\":     \"" . $lang['DATATABLE_JS_sLengthMenu'] . "\",\n";
        $script .= "                \"sInfo\":           \"" . $lang['DATATABLE_JS_sInfo'] . "\",\n";
        $script .= "                \"sInfoEmpty\":      \"" . $lang['DATATABLE_JS_sInfoEmpty'] . "\",\n";
        $script .= "                \"sInfoFiltered\":   \"" . $lang['DATATABLE_JS_sInfoFiltered'] . "\",\n";
        $script .= "                \"sInfoPostFix\":    \"" . $lang['DATATABLE_JS_sInfoPostFix'] . "\",\n";
        $script .= "                \"sLoadingRecords\": \"" . $lang['DATATABLE_JS_sLoadingRecords'] . "\",\n";
        $script .= "                \"sZeroRecords\":    \"" . $lang['DATATABLE_JS_sZeroRecords'] . "\",\n";
        $script .= "                \"sEmptyTable\":     \"" . $lang['DATATABLE_JS_sEmptyTable'] . "\",\n";
        $script .= "                \"oPaginate\": {\n";
        $script .= "                    \"sFirst\":      \"" . $lang['DATATABLE_JS_sFirst'] . "\",\n";
        $script .= "                    \"sPrevious\":   \"" . $lang['DATATABLE_JS_sPrevious'] . "\",\n";
        $script .= "                    \"sNext\":       \"" . $lang['DATATABLE_JS_sNext'] . "\",\n";
        $script .= "                    \"sLast\":       \"" . $lang['DATATABLE_JS_sLast'] . "\"\n";
        $script .= "                    },\n";
        $script .= "                \"oAria\": {\n";
        $script .= "                    \"sSortAscending\":  \"" . $lang['DATATABLE_JS_sSortAscending'] . "\",\n";
        $script .= "                    \"sSortDescending\": \"" . $lang['DATATABLE_JS_sSortDescending'] . "\"\n";
        $script .= "                    }\n";
        $script .= "            },\n";
        return $script;
    }

    public function disableFeatures($names)
    {
        $this ->setFeature($names, false);
    }

    public function enableFeatures($names)
    {
        $this ->setFeature($names, true);
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


