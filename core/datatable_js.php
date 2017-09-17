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
    private $tableId ='';

    public function __construct($idHtmlTable)
    {
        $this->tableId=$idHtmlTable;
    }

    /** Retourne le script JS datatable
     * @return string
     */
    public function getHtml()
    {
        $script = "<script>";
        $script .= "$(document).ready(function() {\n";
        $script .= "    $('#".$this->tableId."').DataTable({\n";
        $script .= "            \"language\": {\n";
        $script .= "            \"url\": \"./assets/js/dataTables.french.lang.json\"\n";
        $script .= "            }\n";
        $script .= "        });\n";
        $script .= "    } );\n";
        $script .= "</script>\n";

        return $script;


    }
}