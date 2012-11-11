<?php
/**
 * @package Xtense 2
 * @author Unibozu
 * @licence GNU
 */

abstract class Callback {
	protected $version = '0';
	protected $root = '';
	
	private static $instances = array();
	
	public static function load($root) {
		if (isset(self::$instances[$root])) return self::$instances[$root];
		if (!file_exists('mod/'.$root.'/_xtense.php')) throw new Exception('Le fichier de lien n&#039;existe pas');
		
		require_once('mod/'.$root.'/_xtense.php');
		$class = $root.'_Callback';
		
		if (!class_exists($class)) throw new Exception("La classe '{$call}' n&#039;existe pas dans le fichier de lien");
		
		$call = new $class();
		$call->setRoot($root);
		
		if (!$call instanceof Callback) throw new Exception("La classe '{$call}' doit h&eacute;riter de la classe abstraite 'Callback'");
		if (!$call->validVersion()) throw new Exception('Le mod requiert une version de Xtense plus recente ('.$call->version.')');
		
		self::$instances[$root] = $call;
		
		return $call;
	}
	
	final public function validVersion() {
		return version_compare(PLUGIN_VERSION, $this->version, '>=');
	}
	
	final public function setRoot($root) {
		$this->root = $root;
	}
	
	abstract public function getCallbacks();
}

