<?php
/**
 * MySql database Managment Class
 * @package OGSpy
 * @subpackage MySql
 * @author Kyser
 * @created 15/11/2005
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @version 3.04b ($Rev: 7692 $)
 * @modified $Date: 2012-08-19 23:54:07 +0200 (Sun, 19 Aug 2012) $
 * @link $HeadURL: http://svn.ogsteam.fr/trunk/ogspy/includes/mysql.php $
 * $Id: mysql.php 7692 2012-08-19 21:54:07Z darknoon $
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

/**
 * Displays an Error message and exits OGSpy
 * @param string $query Faulty SQL Request
 */
function DieSQLError($query)
{
    echo "<table align=center border=1>\n";
    echo "<tr><td class='c' colspan='3'>Database MySQL Error</td></tr>\n";
    echo "<tr><th colspan='3'>ErrNo:" . mysql_errno() . ":  " . mysql_error() . "</th></tr>\n";
    echo "<tr><th colspan='3'><u>Query:</u><br>" . $query . "</th></tr>\n";
    if (MODE_DEBUG) {
        $i = 0;
        foreach (debug_backtrace() as $v) {
            echo "<tr><th width='50' align='center' rowspan='" . (isset($v['args']) ? sizeof($v['args']) + 1 : "") . "'>[" . $i . "]</th>";
            echo "<th colspan='2'>";
            echo "file => " . $v['file'] . "<br>";
            echo "ligne => " . $v['line'] . "<br>";
            echo "fonction => " . $v['function'];
            echo "</th></tr>\n";
            $j = 0;
            if (isset($v['args'])) {
                foreach ($v['args'] as $arg) {
                    echo "<tr><th align='center'>[" . $j . "]</td><td>" . $arg . "</th></tr>\n";
                    $j++;
                }
            }
            $i++;
        }
    }

    echo "</table>\n";

    log_("mysql_error", array($query, mysql_errno(), mysql_error(), debug_backtrace()));
    die();
}

/**
 * OGSpy MySQL Database Class
 * @package OGSpy
 * @subpackage MySql
 */
class sql_db
{
    /**
     * Instance variable
     * @access private
     * @var int
     */
    private static $_instance = false; //(singleton)
    /**
     * Connection ID
     * @var int
     */
    var $db_connect_id;
    /**
     * DB Result
     * @var mixed
     */
    var $result;
    /**
     * Nb of Queries done
     * @var int
     */
    var $nb_requete = 0;
    /**
     * last query
     * @var int
     */
    var $last_query;

    /**
     * Get the current class database instance. Creates it if dosen't exists (singleton)
     * @param string $sqlserver MySQL Server Name
     * @param string $sqluser MySQL User Name
     * @param string $sqlpassword MySQL User Password
     * @param string $database MySQL Database Name
     */
    public static function getInstance($sqlserver, $sqluser, $sqlpassword, $database)
    {

        if (self::$_instance === false) {
            self::$_instance = new sql_db($sqlserver, $sqluser, $sqlpassword, $database);
        }

        return self::$_instance;
    }

    /**
     * Class Constructor
     * @param string $sqlserver MySQL Server Name
     * @param string $sqluser MySQL User Name
     * @param string $sqlpassword MySQL User Password
     * @param string $database MySQL Database Name
     * @return True if the connection has been created sucessfully
     */

    private function sql_db($sqlserver, $sqluser, $sqlpassword, $database)
    {
        global $sql_timing;
        $sql_start = benchmark();

        $this->user = $sqluser;
        $this->password = $sqlpassword;
        $this->server = $sqlserver;
        $this->dbname = $database;

        $this->db_connect_id = new mysqli($this->server, $this->user, $this->password, $this->dbname);
	
		
		/* Vérification de la connexion */
		if ($this->db_connect_id->connect_errno) {
			echo("Échec de la connexion : ".$this->db_connect_id->connect_error);
			exit();
		}
		
		if (!$this->db_connect_id->set_charset("utf8")) {
			printf("Erreur lors du chargement du jeu de caractères utf8 : %s\n", $this->db_connect_id->error);
		} else {
			/*printf("Jeu de caractères courant : %s\n", $this->db_connect_id->character_set_name());*/
		}

        $sql_timing += benchmark() - $sql_start;
    }

    /**
     * Overload the __clone function. To forbid the use of this function for this class.
     */
    public function __clone()
    {
        throw new Exception('Cet objet ne peut pas être cloné');
        die();
    }

    /**
     * Closing the Connection with the MySQL Server
     */
    function sql_close()
    {
        unset($this->result);
        $result = @mysqli_close($this->db_connect_id); //deconnection
        self::$_instance = false;
    }

    /**
     * MySQL Request Function
     * @param string $query The MySQL Query
     * @param boolean $Auth_dieSQLError True if a SQL error sneed to stop the application
     * @param boolean $save True to save the Query in the MySQL Logfile (if enabled)
     */
    function sql_query($query = "", $Auth_dieSQLError = true, $save = true)
    {
        global $sql_timing, $server_config;

        $sql_start = benchmark();

        if ($Auth_dieSQLError) {
            $this->result = $this->db_connect_id->query($query) or dieSQLError($query);
        } else {
            $this->last_query = $query;
            $this->result = $this->db_connect_id->query($query);
        }

        if ($save) {
            $type = substr($query, 0, 6);
            if ($server_config["debug_log"] == "1") {
                if (!preg_match("/^select/i", $query) && !preg_match("/^show table status/i", $query)) {
                    $fichier = "sql_" . date("ymd") . ".sql";
                    $date = date("d/m/Y H:i:s");
                    $ligne = "/* " . $date . " - " . $_SERVER["REMOTE_ADDR"] . " */ " . $query . ";";
                    write_file(PATH_LOG_TODAY . $fichier, "a", $ligne);
                }
            }
        }

        $sql_timing += benchmark() - $sql_start;

        $this->nb_requete += 1;
        return $this->result;
    }

    /**
     * Gets the result of the Query and returns it in a simple array
     * @param int $query_id The Query id.
     * @return the array containing the Database result
     */
    function sql_fetch_row($query_id = 0)
    {
        if (!$query_id) {
            $query_id = $this->result;
        }
        if ($query_id) {
            return $query_id->fetch_array();
        } else {
            return false;
        }
    }

    /**
     * Gets the result of the Query and returns it in a associative array
     * @param int $query_id The Query id.
     * @return the associative array containing the Database result
     */
    function sql_fetch_assoc($query_id = 0)
    {
        if (!$query_id) {
            $query_id = $this->result;
        }
        if ($query_id) {
            return $query_id->fetch_assoc();
        } else {
            return false;
        }
    }

    /**
     * Gets the number of results returned by the Query
     * @param int $query_id The Query id.
     * @return the number of results
     */
    function sql_numrows($query_id = 0)
    {
        if (!$query_id) {
            $query_id = $this->result;
        }
        if ($query_id) {
            $result = $query_id->num_rows;
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Gets the number of affected rows by the Query
     * @return the number of affected rows
     */
    function sql_affectedrows()
    {
        if ($this->db_connect_id) {
            $result = $this->db_connect_id->affected_rows;
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Identifier of the last insertion Query
     * @return Returs the id
     */
    function sql_insertid()
    {
        if ($this->db_connect_id) {
            $result = $this->db_connect_id->insert_id;
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Free MySQL ressources on the latest Query result
     * @param int $query_id The Query id.
     */
    function sql_free_result($query_id = 0)
    {
        mysqli_free_result($query_id);
    }

    /**
     * Returns the latest Query Error.
     * @param int $query_id The Query id.
     * @return an array with the error code and the error message
     */
    function sql_error($query_id = 0)
    {
        $result["message"] = $this->db_connect_id->connect_error;
        $result["code"] = $this->db_connect_id->connect_errno;
        echo("<h3 style='color: #FF0000;text-align: center'>Erreur lors de la requête MySQL</h3>");
        echo("<b>- " . $result["message"] . "</b>");
        echo($this->last_query);
        exit();
    }

    /**
     * Returns the number of queries done.
     * @return The number of queries done.
     */
    function sql_nb_requete()
    {
        return $this->nb_requete;
    }

    /**
     * Escapes all characters to set up the Query
     * @param string $str The string to escape
     * @return the escaped string
     */
    function sql_escape_string($str)
    {
        if (isset($str)) {
            return mysqli_real_escape_string($this->db_connect_id, $str);
        } else {
            return false;
        }
    }

}

?>