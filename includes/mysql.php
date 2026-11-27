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
     * Returns l'instance singleton of sql_db. Si elle n'existe pas, elle est created.
     *
     * @param string $sqlserver Adresse of the serveur SQL.
    * @param string $sqluser SQL username.
     * @param string $sqlpassword Mot of passe SQL.
    * @param string $database Database name.
     * @return sql_db Instance singleton of sql_db.
     */
    public static function getInstance($sqlserver, $sqluser, $sqlpassword, $database)
    {

        if (self::$_instance === false) {
            self::$_instance = new sql_db($sqlserver, $sqluser, $sqlpassword, $database);
        }

        return self::$_instance;
    }

    /**
     * Constructeur of the classe.
     * @param string $sqlserver Name of the serveur MySQL
    * @param string $sqluser MySQL username
     * @param string $sqlpassword Mot of passe MySQL
     * @param string $database Name of the base MySQL
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
     * Surcharge of __clone for interdire le clonage of cette classe.
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
     * Executes une query MySQL.
     * @param string $query Requête MySQL
     * @return bool|mixed|mysqli_result
     * @throws FileAccessException
     */
    public function sql_query($query = "")
    {
        global $logSQL, $logSlowSQL;
        global $benchSQL;

        $logSQL->info($query);
        $benchSQL->start();
        $start_time = microtime(true);

        $this->last_query = $query;
        $this->result = $this->db_connect_id->query($query);

        $execution_time = microtime(true) - $start_time;
        if ($execution_time > 0.2) {
            $logSlowSQL->warning("Slow query (".$execution_time."s): ".$query);
        }

        $benchSQL->stop("sql_query ".$this->nb_requete." ");
        $this->nb_requete += 1;
        return $this->result;
    }

    /**
    * Returns a query result as an indexed array.
     * @param mysqli_result|null $result Result of query.
     * @return array|bool Tableau contenant le result of the base
     */
    public function sql_fetch_row(?mysqli_result $result = null): array|bool|null
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
    * Returns a query result as an associative array.
     * @param mysqli_result|null $result Identifier/result of query.
     * @return array|bool Tableau associatif contenant le result
     */
    public function sql_fetch_assoc(?mysqli_result $result = null): array|bool|null
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
     * Returns le nombre of lignes d'un result of query.
     * @param mysqli_result|null $result
     * @return int|bool Number of résultats
     */
    public function sql_numrows(?mysqli_result $result = null): int|bool
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
     * Returns le nombre of lignes affectées par la query.
     * @return bool|int Number of lignes affectées
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
    * Returns the last inserted ID.
     * @return int|false Identifier inséré
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
     * Returns la last error SQL.
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
     * Returns le nombre of requêtes exécutées.
     * @return integer Number of requêtes
     */
    public function sql_nb_requete()
    {
        return $this->nb_requete;
    }

    /**
     * Échappe une string for un usage SQL.
     * @param string $str Chaîne à échapper
     * @return string|false Chaîne échappée
     */
    public function sql_escape_string($str)
    {
        if (isset($str)) {
            return mysqli_real_escape_string($this->db_connect_id, $str);
        } else {
            return false;
        }
    }

    /**
     * Starts ou pilote une transaction MySQL.
     * @param string $mode Mode of transaction ('begin', 'start', 'commit', 'rollback')
     * @return bool Success or failure
     */
    public function sql_transaction($mode = 'begin')
    {
        switch (strtolower($mode)) {
            case 'begin':
            case 'start':
                return mysqli_autocommit($this->db_connect_id, false) &&
                       mysqli_query($this->db_connect_id, "START TRANSACTION");

            case 'commit':
                $result = mysqli_commit($this->db_connect_id);
                mysqli_autocommit($this->db_connect_id, true);
                return $result;

            case 'rollback':
                $result = mysqli_rollback($this->db_connect_id);
                mysqli_autocommit($this->db_connect_id, true);
                return $result;

            default:
                return false;
        }
    }

    /**
     * Returns le name of the base courante.
     * @return string Name of the base courante
     */
    public function getDatabaseName()
    {
        return $this->dbname;
    }

    /**
    * Selects another database.
     * @param string $database Name of the base à sélectionner
     * @return bool Success or failure
     */
    public function sql_select_db($database)
    {
        $result = mysqli_select_db($this->db_connect_id, $database);
        if ($result) {
            $this->dbname = $database;
        }
        return $result;
    }

}
