<?php

/**
 * MySql database Managment Class
 * @package OGSpy
 * @subpackage MySql
 * @author Kyser
 * @created 15/11/2005
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.04b ($Rev: 7692 $)
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
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
     * Username
     * @var int
     */
    private $user;
    /**
     * Password
     * @var int
     */
    private $password;
    /**
     * server
     * @var int
     */
    private $server;
    /**
     * dbname
     * @var int
     */
    private $dbname;
    /**
     * Connection ID
     * @var mysqli
     */
    public $db_connect_id;
    /**
     * DB Result
     * @var mixed
     */
    public $result;
    /**
     * Nb of Queries done
     * @var int
     */
    public $nb_requete = 0;
    /**
     * last query
     * @var int
     */
    public $last_query;



    /**
     * Get the current class database instance. Creates it if dosen't exists (singleton)
     * @param string $sqlserver MySQL Server Name
     * @param string $sqluser MySQL User Name
     * @param string $sqlpassword MySQL User Password
     * @param string $database MySQL Database Name
     * @return int|sql_db
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
     */

    private function __construct($sqlserver, $sqluser, $sqlpassword, $database)
    {
        global $benchSQL;
        $benchSQL = new Ogsteam\Ogspy\Helper\Benchmark_Helper('SQL');
        $benchSQL->start();

        $this->user = $sqluser;
        $this->password = $sqlpassword;
        $this->server = $sqlserver;
        $this->dbname = $database;

        $this->db_connect_id = new mysqli($this->server, $this->user, $this->password, $this->dbname);

        /* Vérification de la connexion */
        if ($this->db_connect_id->connect_errno) {
            throw new ErrorException("Échec de la connexion : " . $this->db_connect_id->connect_error);
        }
        if (!$this->db_connect_id->set_charset("utf8")) {
            throw new ErrorException("Erreur lors du chargement du jeu de caractères utf8 : " . $this->db_connect_id->error);
        }
        $benchSQL->stop("initialistion SQL");
    }

    /**
     * Overload the __clone function. To forbid the use of this function for this class.
     */
    public function __clone()
    {
        throw new RuntimeException('Cet objet ne peut pas être cloné');
    }

    /**
     * Closing the Connection with the MySQL Server
     */
    public function sql_close()
    {
        unset($this->result);
        $result = @mysqli_close($this->db_connect_id);
        self::$_instance = false;

        return $result;
    }

    /**
     * MySQL Request Function
     * @param string $query The MySQL Query
     * @return bool|mixed|mysqli_result
     * @throws FileAccessException
     */
    public function sql_query($query = "")
    {
        global $server_config;
        global $benchSQL;
        $benchSQL->start();

        $this->last_query = $query;
        $this->result = $this->db_connect_id->query($query);

        if (isset($server_config["debug_log"]) && $server_config["debug_log"] == 1) {
            $fichier = "sql_" . date("ymd") . ".sql";
            $date = date("d/m/Y H:i:s");
            $ligne = "$date - $query ;";
            write_file(PATH_LOG_TODAY . $fichier, 'a', $ligne);
        }

        $benchSQL->stop("sql_query ".$this->nb_requete." ");
        $this->nb_requete += 1;
        return $this->result;
    }

    /**
     * Gets the result of the Query and returns it in a simple array
     * @param mysqli_result|null $result The Query Result.
     * @return array|bool array containing the Database result
     */
    public function sql_fetch_row(mysqli_result $result = null): array|bool|null
    {
        if (!$result) {
            $result = $this->result;
        }
        if ($result) {
            return $result->fetch_array();
        } else {
            return false;
        }
    }

    /**
     * Gets the result of the Query and returns it in a associative array
     * @param mysqli_result|null $result The Query id.
     * @return array|bool the associative array containing the Database result
     */
    public function sql_fetch_assoc(mysqli_result $result = null): array|bool|null
    {
        if (!$result) {
            $result = $this->result;
        }
        if ($result) {
            return $result->fetch_assoc();
        } else {
            return false;
        }
    }

    /**
     * Gets the number of results returned by the Query
     * @param mysqli_result|null $result
     * @return int|bool the number of results
     */
    public function sql_numrows(mysqli_result $result = null): int|bool
    {
        if (!$result) {
            $result = $this->result;
        }
        if ($result) {
            return $result->num_rows;
        } else {
            return false;
        }
    }

    /**
     * Gets the number of affected rows by the Query
     * @return bool|int the number of affected rows
     */
    public function sql_affectedrows(): int|false
    {
        if ($this->db_connect_id) {
            return $this->db_connect_id->affected_rows;
        } else {
            return false;
        }
    }

    /**
     * Identifier of the last insertion Query
     * @return int|false the id
     */
    public function sql_insertid(): int|false
    {
        if ($this->db_connect_id) {
            return $this->db_connect_id->insert_id;
        } else {
            return false;
        }
    }

    /**
     * Returns the latest Query Error.
     */
    public function sql_error()
    {
        $result["message"] = $this->db_connect_id->connect_error;
        $result["code"] = $this->db_connect_id->connect_errno;
        echo "<h3 style='color: #FF0000;text-align: center'>Erreur lors de la requête MySQL</h3>";
        echo "<b>- " . $result["message"] . "</b>";
        echo $this->last_query;
        exit();
    }

    /**
     * Returns the number of queries done.
     * @return integer number of queries done.
     */
    public function sql_nb_requete()
    {
        return $this->nb_requete;
    }

    /**
     * Escapes all characters to set up the Query
     * @param string $str The string to escape
     * @return string|false escaped string
     */
    public function sql_escape_string($str)
    {
        if (isset($str)) {
            return mysqli_real_escape_string($this->db_connect_id, $str);
        } else {
            return false;
        }
    }

}
