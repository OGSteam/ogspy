<?php
/**
 * @package Xtense 2
 * @author Unibozu
 * @licence GNU
 */

if (!defined('IN_SPYOGAME') && !defined('IN_UNISPY2')) exit;


class Io {
	/**
	 * Données renvoyées au plugin
	 */
	protected $args = array();
	
	/**
	 * Liste des id des appels déjà enregistrés
	 */
	protected $calls = array();
	protected $names = array();
	
	const WARNING = 1;
	const ERROR = 2;
	const NORMAL = 3;
	const SUCCESS = 4;
	
	public function __construct() {
		$args['status'] = 1;
		$this->names = array(self::WARNING => 'warning',self::ERROR => 'error', self::SUCCESS => 'success');
	}
	
	public function set($name, $value = null) {
		if (is_array($name)) foreach ($name as $n => $v) $this->set($n, $v);
		else {
			if (is_array($value) && is_string(key($value))) {
				$value = (object) $value;
			}
			$this->args[$name] = $value;
		}
	}
	
	public function del($name) {
		unset($args[$name]);
	}
	
	public function flush() {
		$this->args = array();
	}
	
	protected function parse($value) {
		$str = '';
		
		if (is_numeric($value)) $str .= $value;
		elseif (is_null($value)) $str .= 'NULL';
		elseif (is_bool($value)) $str .= ($value ? 'true' : 'false');
		elseif (is_array($value)) {
			$str .= '[';
			$max = count($value)-1;
			$i = 0;
			
			foreach ($value as $v) {
				$str .= $this->parse($v);
				if ($i < $max) $str .= ',';
				$i++;
			}
			
			$str .= ']';
		}
		elseif (is_object($value)) {
			$str .= '{';
			$vars = get_object_vars($value);
			$max = count($vars)-1;
			$i = 0;
			
			foreach ($vars as $k => $v) {
				$str .= '"'.$k.'": '.$this->parse($v);
				if ($i < $max) $str .= ',';
				$i ++;
			}
			
			$str .= '}';
		}
		else $str .= '"'.str_replace("\n", '\\n', str_replace('"', '\\"',$value)).'"';
		
		return $str;
	}
	
	public function status($status) {
		$this->args['status'] = $status;
	}
	
	public function send($status = null, $exit = false) {
		if (!is_null($status)) $this->status($status);
		echo '('.$this->parse((object) $this->args).')';
		if ($exit) exit;
	}
	
	public function append_call($call, $status = self::SUCCESS) {
		if (in_array($call['id'], $this->calls)) return;
		if (!isset($this->args['calls'])) $this->args['calls'] = (object)array($this->names[self::SUCCESS] => array(), $this->names[self::WARNING] => array(), $this->names[self::ERROR] => array());
		
		if ($status === true) $status = self::SUCCESS;
		if ($status === false) $status = self::WARNING;
		
		if (!in_array($status, array_flip($this->names))) $status = self::WARNING;
		
		$this->calls[] = $call['id'];
		$name = $this->names[$status];
		array_push($this->args['calls']->{$this->names[$status]}, $call['title']);
	}
	
	public function append_call_message($message, $type = self::SUCCESS, $callback = null) {
		global $call;
		$this->args['call_messages'][] = (object) array(
			'mod' => ($callback === null ? $call->currentCallback['title'] : $callback['title']),
			'message' => $message,
			'type' => $this->names[$type]
		);
	}
	
	public function append_call_error($call, $message, Exception $e = null) {
		$this->append_call($call, self::ERROR);
		$this->append_call_message($message, self::ERROR, $call);
		
		if (DEBUG) {
			echo "* CALL ERROR ({$call['root']}):\n  $message\n";
			
			if ($e !== null) {
				echo "    Exception Stacktrace\n";
				$stacktrace = str_replace("\n", "\n      ", $e->getTraceAsString());
				if(isset($db_password)) {
					$stacktrace = str_replace($db_passord, "*****", $stacktrace);
				}
				echo "      ".$stacktrace;
			}
			
			echo "\n\n";
		}
	}
}
