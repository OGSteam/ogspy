<?php
	require_once("mod/{$root}/includes/Callback.php");

	// Vidange de la table
	$db->sql_query('TRUNCATE TABLE `'.TABLE_XTENSE_CALLBACKS.'`');
	
	
	$insert = array(); 
	$callInstall = array('errors' => array(), 'success' => array()); 
	 
	$query = $db->sql_query('SELECT action, root, id, title FROM '.TABLE_MOD.' WHERE active = 1');
	while ($data = $db->sql_fetch_assoc($query)) { 
	        if (!file_exists('mod/'.$data['root'].'/_xtense.php')) continue;	
	        try { 
	                $call = Callback::load($data['root']); 
					$error = false;
	        } catch (Exception $e) { 
	                $callInstall['errors'][] = $data['title'].' (erreur lors du chargement du lien) : '.$e->getMessage(); 
					$error = true;
	        } 
	        if(!$error)
	        foreach ($call->getCallbacks() as $k => $c) { 
	                try { 
	                        if (empty($c)) continue; 
	                        if (!isset($c['function'], $c['type'])) throw new Exception('Donn&eacute;es sur le lien invalides : '.$k); 
	                        if (!in_array($c['type'], $callbackTypesNames)) throw new Exception('Type de lien ('.$c['type'].') invalide'); 
	                        if (!isset($c['active'])) $c['active'] = 1; 
	                        if (!method_exists($call, $c['function'])) throw new Exception('La m&eacute;thode "'.$c['function'].'" n&#039;existe pas'); 
	                        $insert[] = '('.$data['id'].', "'.$c['function'].'", "'.$c['type'].'", '.$c['active'].')'; 
	                        $callInstall['success'][] = $data['title'].' (#'.$k.') : '.$c['type']; 
	                } catch (Exception $e) { 
	                        $callInstall['errors'][] = $data['title'].' : '.$e->getMessage(); 
	                } 
	        } 
	} 
	 
	if (!empty($insert)) { 
	        $db->sql_query('REPLACE INTO '.TABLE_XTENSE_CALLBACKS.' (mod_id, function, type, active) VALUES '.implode(', ', $insert)); 
	} 
	return $callInstall; 
?>