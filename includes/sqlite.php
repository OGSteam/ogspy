<?php
/**
* Classe d'acces a la BDD SQlite
* @package OGSpy
* @subpackage SQlite
* @author DarkNoon (Based on Kyser Work)
* @created 15/11/2005
* @copyright Copyright &copy; 2012, http://board.ogsteam.fr/
* @version 3.04b ($Rev: 7224 $)
* @modified $Date: 2011-10-27 15:51:12 +0200 (jeu., 27 oct. 2011) $
* @link $HeadURL: http://svn.ogsteam.fr/trunk/ogspy/includes/mysql.php $
* @ignore Not used in OGSpy yet
* $Id: mysql.php 7224 2011-10-27 13:51:12Z machine $
*/

if (!defined('IN_SPYOGAME')) {
  die("Hacking attempt");
}

/**
* Affichage d'une erreur SQL et sortie du script
* @param string $query Requête SQL fautive
*/
function DieSQLError($query){
  echo "<table align=center border=1>\n";
  echo "<tr><td class='c' colspan='3'>SQlite Database Error</td></tr>\n";
  echo "<tr><th colspan='3'>ErrNo:".sqlite_last_error($sql_db->db_connect_id).":  ".sqlite_error_string($sql_db->db_connect_id)."</th></tr>\n";
  echo "<tr><th colspan='3'><u>Query:</u><br>".$query."</th></tr>\n";
  if (MODE_DEBUG) {
    $i=0;
    foreach (debug_backtrace() as $v) {
      echo "<tr><th width='50' align='center' rowspan='".(isset($v['args']) ? sizeof($v['args'])+1 : "")."'>[".$i."]</th>";
      echo "<th colspan='2'>";
      echo "file => ".$v['file']."<br>";
      echo "ligne => ".$v['line']."<br>";
      echo "fonction => ".$v['function'];
      echo "</th></tr>\n";
      $j=0;
      if (isset($v['args'])) {
        foreach ($v['args'] as $arg) {
          echo "<tr><th align='center'>[".$j."]</td><td>".$arg."</th></tr>\n";
          $j++;
        }
      }
      $i++;
    }
  }

  echo "</table>\n";

  log_("mysql_error", array($query, sqlite_last_error($sql_db>db_connect_id), sqlite_error_string($sql_db->db_connect_id), debug_backtrace()));
  die();
}

/**
* Classe d'accès MySQL
*/
class sql_db {
  private static $_instance = false; //(singleton)
  var $db_connect_id;
  var $result;
  var $nb_requete = 0;
    
/**
* recuperation de l instance en cours, ou création le cas echeant
* (singleton)
*/   
    public static function getInstance($sqlserver, $sqluser, $sqlpassword, $database){  
       if( self::$_instance === false ){  
           self::$_instance = new sql_db($sqlserver, $sqluser, $sqlpassword, $database);  
       }  
  
       return self::$_instance;  
   }  
    
    
    
/**
* Constructeur
* passage en fonction privé (singleton)
*/
 
    private  function sql_db($sqlserver, $sqluser, $sqlpassword, $database) {
    global $sql_timing;
    $sql_start = benchmark();
/*
    $this->user = $sqluser;
    $this->password = $sqlpassword;
    $this->server = $sqlserver;
    $this->dbname = $database;*/

    $this->db_connect_id = @sqlite_open('ogspydb', 0666, $sqliteerror);

    if($this->db_connect_id) {
      return $this->db_connect_id;
    }
    else {
      return false;
    }

    $sql_timing += benchmark() - $sql_start;
  }

/**
* empeche fonction magique __clone
*/    
    public function __clone(){  
       throw new Exception('Cet objet ne peut pas être cloné');
       die();  
   }  

/**
* Fermeture de la BDD
*/
  function sql_close() {
		unset($this->result);
		$result = @sqlite_close($this->db_connect_id); //deconnection
		self::$_instance=false;


  }
/**
* Requête SQL
*/
  function sql_query($query = "", $Auth_dieSQLError = true, $save = true) {
    global $sql_timing, $server_config;
    
    $sql_start = benchmark();

    if ($Auth_dieSQLError) {
      $this->result = @sqlite_query($query, $this->db_connect_id) or dieSQLError($query);
    }
    else {
      $this->result = @sqlite_query($query, $this->db_connect_id);
    }

    if ($save) {
      $type = substr($query, 0, 6);
      if ($server_config["debug_log"] == "1") {
        if (!preg_match("/^select/i", $query) && !preg_match("/^show table status/i", $query)) {
          $fichier = "sql_".date("ymd").".sql";
          $date = date("d/m/Y H:i:s");
          $ligne = "/* ".$date." - ".$_SERVER["REMOTE_ADDR"]." */ ".$query.";";
          write_file(PATH_LOG_TODAY.$fichier, "a", $ligne);
        }
      }
    }

    $sql_timing += benchmark() - $sql_start;
    
    $this->nb_requete += 1;
    return $this->result;
  }
/**
* Récupération d'une ligne d'enregistrement
*/
  function sql_fetch_row($query_id = 0) {
    if(!$query_id) {
      $query_id = $this->result;
    }
    if($query_id) {
      return @sqlite_fetch_array($query_id,SQLITE_NUM);
    }
    else {
      return false;
    }
    
  }
/**
* Récupération d'un tableau associatif d'une ligne d'enregistrement
*/
  function sql_fetch_assoc($query_id = 0) {
    if(!$query_id) {
      $query_id = $this->result;
    }
    if($query_id) {
      return @sqlite_fetch_array($query_id,SQLITE_ASSOC);
    }
    else {
      return false;
    }
  }
/**
* Nombre de lignes concernées par la dernière requête
*/
  function sql_numrows($query_id = 0) {
    if(!$query_id) {
      $query_id = $this->result;
    }
    if($query_id) {
      $result = @sqlite_num_rows($query_id);
      return $result;
    }
    else {
      return false;
    }
  }
/**
* Nombre de ligne affectés
*/
  function sql_affectedrows() {
    if($this->db_connect_id) {
      $result = @sqlite_changes($this->db_connect_id);
      return $result;
    }
    else {
      return false;
    }
  }
/**
* Identificateur unique de la dernière insertion SQL
*/
  function sql_insertid(){
    if($this->db_connect_id) {
      $result = @sqlite_last_insert_rowid($this->db_connect_id);
      return $result;
    }
    else {
      return false;
    }
  }
/**
* Libération des ressources
*/
  function sql_free_result($query_id = 0) {
    /*mysql_free_result($query_id);*/
  }
/**
* renvoie sous forme d'un tableau de la dernière erreur SQL
*/
  function sql_error($query_id = 0) {
    $result["message"] = @sqlite_error_string($this->db_connect_id);
    $result["code"] = @sqlite_last_error($this->db_connect_id);

    return $result;
  }
  
 /**
* renvoie le nombre de requete
*/
  function sql_nb_requete() {
        return $this->nb_requete;
  }
  
 /**
* Protège les caractères spéciaux SQL
*/
  function sql_escape_string($str) {
    if(isset($str)) {
     return  sqlite_escape_string($str);
    }
    else {
      return false;
    }
    
    
  }
  
  
}
?>