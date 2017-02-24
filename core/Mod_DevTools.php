<?php
/**
 * Functions used for OGSpy Mods
 * @package OGSpy
 * @subpackage mods
 * @author Kyser
 * @created 21/07/2006
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.04b ($Rev: 7692 $)
 */

namespace Ogsteam\Ogspy;

use Ogsteam\Ogspy\Model\Mod_Config_Model;
use Ogsteam\Ogspy\Model\Mod_Model;
use Ogsteam\Ogspy\Model\Mod_User_Config_Model;

/*
 * Mod Tools Class
 * This class is used by Mod developpers and should contain all functions used by Mods
 */

class Mod_DevTools
{
    public $current_mod_name;

    /**
     * Mod_DevTools constructor.
     * @param $param
     */
    public function __construct($param) {
        $this->current_mod_name = $param;
    }

    /**
     * Returns the version number of the current Mod.
     *
     * The function uses the $pub_action value to know what is the current mod
     * @return string Current mod version number
     * @internal param string $mod_name
     */
    function mod_version()
    {
        $modsRepository = new Mod_Model();
        $mods = $modsRepository->find_by(array('root' => $this->current_mod_name));

        if (count($mods) == 1) {
            return $mods[0]['version'];
        }

        return "(ModInconnu:'{$this->current_mod_name}')";
    }

    /**
     * Mod Configs: Add or updates a configuration option for the mod
     * @param string $param Name of the parameter
     * @param string $value Value of the parameter
     * @return boolean returns true if the parameter is correctly saved. false in other cases.
     */
    function mod_set_option($param, $value)
    {
        if (!check_var($param, "Text")) {
            redirection("index.php?action=message&id_message=errordata&info");
        }
        $modModel = new Mod_Config_Model();
        return $modModel->set_mod_config($this->current_mod_name, $param, $value);
    }

    /**
     * Mod Configs: Add or updates a configuration option for the mod
     * @param string $param Name of the parameter
     * @param integer $user_id Id of the user
     * @param string $value Value of the parameter
     * @return bool returns true if the parameter is correctly saved. false in other cases.
     */
    function mod_set_user_option($param, $user_id, $value)
    {

        if (!check_var($param, "Text")) {
            redirection("index.php?action=message&id_message=errordata&info");
        }
        if (!check_var($user_id, "Num")) {
            redirection("index.php?action=message&id_message=errordata&info");
        }

        $modModel = new Mod_User_Config_Model();
        return $modModel->set_mod_config($this->current_mod_name, $param, $user_id, $value);
    }

    /**
     * Mod Configs: Deletes a parameter for a mod
     * @param string $param Name of the parameter
     * @global $db
     * @return boolean returns true if the parameter is correctly saved. false in other cases.
     */
    function mod_del_option($param)
    {
        if (!check_var($param, "Text")) {
            redirection("index.php?action=message&id_message=errordata&info");
        }
        $modModel = new Mod_Config_Model();
        return $modModel->delete_mod_config($this->current_mod_name, $param);
    }

    /**
     * Mod Configs: Deletes a parameter for a mod and a user
     * @param string $param Name of the parameter
     * @param $user_id
     * @return bool returns true if the parameter is correctly saved. false in other cases.
     */
    function mod_del_user_option($param, $user_id)
    {
        if (!check_var($param, "Text")) {
            redirection("index.php?action=message&id_message=errordata&info");
        }
        if (!check_var($user_id, "Num")) {
            redirection("index.php?action=message&id_message=errordata&info");
        }

        $modModel = new Mod_User_Config_Model();
        return $modModel->delete_mod_config($this->current_mod_name, $user_id, $param);
    }

    /**
     * Mod Configs : Reads a parameter value for the current mod
     * @param string $param Name of the parameter
     * @global $db
     * @return string Returns the value of the requested parameter
     */
    function mod_get_option($param)
    {
        if (!check_var($param, "Text")) {
            redirection("index.php?action=message&id_message=errordata&info");
        }

        $modModel = new Mod_Config_Model();
        $result = $modModel->get_mod_config($this->current_mod_name, $param);
        if (count($result) == 0) {
            return -1;
        }

        return $result;
    }

    /**
     * Mod Configs : Reads a parameter value for the current mod and a specific user
     * @param string $param Name of the parameter
     * @param integer $user_id Id of the user
     * @return array Returns an array with the value of the requested parameter
     */
    function mod_get_user_option($user_id, $param = null)
    {
        if (!check_var($param, "Text")) {
            redirection("index.php?action=message&id_message=errordata&info");
        }
        if (!check_var($user_id, "Num")) {
            redirection("index.php?action=message&id_message=errordata&info");
        }

        $modModel = new Mod_User_Config_Model();
        $result = $modModel->get_mod_config($this->current_mod_name, $user_id, $param);

        return $result;
    }


    /**
     * Deletes all configurations for the current mod
     * @return boolean Returns true if at least one entry has been deleted. False if nothing has been removed.
     */
    function mod_del_all_option()
    {
        $modModel = new Mod_Config_Model();
        return $modModel->delete_mod_config($this->current_mod_name);
    }

    /**
     * Deletes all user configurations for the current mod
     * @return boolean Returns true if at least one entry has been deleted. False if nothing has been removed.
     */
    function mod_del_all_user_option()
    {
        $modModel = new Mod_User_Config_Model();
        return $modModel->delete_mod_config($this->current_mod_name);
    }

    /**
     * Function to uninstall an OGSpy Module
     * Add tables provided by the install.php or update.php file
     * @param $table_name
     * @param string $sql_script : Script to create the table
     */
    function mod_create_table($table_name, $sql_script)
    {
        global $db;
        log_("debug", "CREATE TABLE  " . $table_name);
        $db->sql_query($sql_script);
    }

    /**
     * Function to uninstall an OGSpy Module
     * Deletes tables provided by the uninstall.php file
     * @param array $mod_uninstall_tables : List of Database tables to be removed
     */
    function mod_remove_tables($mod_uninstall_tables)
    {
        global $db;
        if (!empty($mod_uninstall_tables)) {
            foreach ($mod_uninstall_tables as $item) {
                log_("debug", "Deleting table named: " . $item);
                $db->sql_query("DROP TABLE IF EXISTS " . $item);
            }
        }
    }
    /**
     * Function Register Xtense Callback
     */
    function mod_register_xtense_callback() {

        //TODO
    }

    /**
     * Function Register Xtense Callback
     */
    function mod_unregister_xtense_callback() {

        //TODO
    }
}