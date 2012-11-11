<?php
/**
 * @package Xtense 2
 * @author Unibozu
 * @licence GNU
 */

if (!defined('IN_SPYOGAME') && !defined('IN_UNISPY2')) exit;

/**
 * Gestion des fonctions de callback des plugins OGSpyw
 *
 */
class CallbackHandler {
	private $list = array();
	private $calls = array();
	private $types = array();
	private $included = array();
	public $currentCallback = false;
	
	public function add($type, $params) {
		if (empty($params)) return;
		$this->calls[$type][] = $params;
		if (!in_array($type, $this->types)) {
			$this->types[] = $type;
		}
	}
	
	/**
	 * Appels des fonctions des mods
	 *
	 * @param string $type
	 * @param array $params
	 */
	public function apply() {
		global $io, $db, $get_dev, $server_config;
		if (empty($this->calls)) return;
		$success = array();
		$errors = array();
		
		$query = $db->sql_query('SELECT c.id, c.function, c.type, c.mod_id, m.root, m.title FROM '.TABLE_XTENSE_CALLBACKS.' c LEFT JOIN '.TABLE_MOD.' m ON c.mod_id = m.id WHERE c.active = 1 AND m.active = 1 AND c.type IN ("'.implode('", "', $this->types).'")'); 
		while ($call = $db->sql_fetch_assoc($query)) {
			foreach ($this->calls[$call['type']] as $params) {
				$this->currentCallback = $call;
				
				try {
					$instance = Callback::load($call['root']);
					
					if (!method_exists($instance, $call['function']) || !is_callable(array($instance, $call['function']))) throw new Exception('Invalid method "'.$call['function'].'"');
					
					$execReturn = $instance->$call['function']($params);
					
					$io->append_call($call, $execReturn);
				} catch (Mysql_Exception $e) {
					$io->append_call_error($call, 'Erreur MySQL lors de l\'execution'."\n".$e->getFile().' @ '.$e->getLine()."\n".$e->getMessage());
				} catch (Exception $e) {
					$io->append_call_error($call, $e->getMessage(), $e);
				}
				
				$this->currentCallback = false;
			} // Foreach
		} // while
		
	} // Method "apply"
	
	/*
	public function apply() {
		global $io, $db, $get_dev;
		if (empty($this->calls)) return;
		$success = array();
		$errors = array();
		
		$query = $db->sql_query('SELECT c.id, c.function, c.type, c.mod_id, m.root, m.title FROM '.TABLE_XTENSE_CALLBACKS.' c LEFT JOIN '.TABLE_MOD.' m ON c.mod_id = m.id WHERE c.active = 1 AND m.active = 1 AND c.type IN ("'.implode('", "', $this->types).'")');
		while ($call = $db->sql_fetch_assoc($query)) {
			foreach ($this->calls[$call['type']] as $params) {
				if (isset($this->included[$call['mod_id']])) {
					if (function_exists($call['function'])) {
						$this->currentCallback = $call;
						$io->append_call($call, $call['function']($params));
					} elseif ($get_dev) {
						$io->append_call_error($$call, 'Fonction appelée ('.$call['function'].') non définie');
					}
				} else {
					if (file_exists('mod/'.$call['root'].'/_xtense.php')) {
						require_once('mod/'.$call['root'].'/_xtense.php');
						
						if (isset($xtense_version)) {
							if (version_compare($xtense_version, PLUGIN_VERSION) <= 0) {
								$this->included[$call['mod_id']] = 1;
								
								if (function_exists($call['function'])) {
									$this->currentCallback = $call;
									$io->append_call($call, $call['function']($params));
								} elseif ($get_dev){
									$io->append_call_error($$call, 'Fonction appelée ('.$call['function'].') non définie');
								}
							} elseif ($get_dev){
								$io->append_call_error($call, 'Version du plugin Xtense obsolète. Version actuelle : '.PLUGIN_VERSION.', demandée : '.$xtense_version);
							}
							unset($xtense_version);
						} elseif ($get_dev) {
							$io->append_call_error($call, 'Variable $xtense_version non définie');
						}
					} elseif ($get_dev) {
						$io->append_call_error($call, 'Impossible de trouver le fichier _xtense.php');
					}
				}
			} // Foreach
		} // while
		
	} // Method "apply"
	*/
}
