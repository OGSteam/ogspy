<?php
/**
 * Classe d'acces a la BDD SQlite
 * @package OGSpy
 * @subpackage SQlite
 * @author Ninety
 * @created 20/02/2012
 * @copyright Copyright &copy; 2012, http://board.ogsteam.fr/
 * @version 3.1.0 ($Rev: 7224 $)
 * @modified $Date: 2011-10-27 15:51:12 +0200 (jeu., 27 oct. 2011) $
 * @link $HeadURL: http://svn.ogsteam.fr/trunk/ogspy/includes/sqlite3.php $
 * @ignore Not used in OGSpy yet
 */

if (!defined('IN_SPYOGAME'))
{
  exit("Hacking attempt");
}

/**
 * Classe d'accès SQLite3
 */
class sql_db
{
  /**
   * Singleton, unique instance de la classe
   */
  private static $instance;

  /**
   * Objet SQLite3, represente la connexion à la BDD
   */
  private $db;

  /**
   * Nombre de requetes executée
   */
  private $query_count;

  /**
   * Resultat de la derniere requete executee
   */
  private $result;

  /**
   * Constructeur
   */
  private function __construct ($db_file_name)
  {
    // Creation de la connection (et du fichier s'il n'existe pas)
    $this->db = new SQLite3($db_file_name);
    $this->query_count = 0;
  }

  /**
   * Interdiction de cloner le singleton
   */
  public function __clone()
  {
    trigger_error('Clonage interdit !', E_USER_ERROR);
  }

  /**
   * Retourne l'instance de la classe
   */
  public static function getInstance ()
  {
    if (!isset(self::$instance))
    {
      self::$instance = new sql_bd('db.sqlite3');
    }

    return self::$instance;
  }

  /**
   * Fermeture de la connexion à la BDD
   */
  public function sql_close ()
  {
    unset(self::$instance);
    $this->$db = $this->$db->close();

    return $this->$db; // true or false
  }

  /**
   * Requête SQL, retourne TRUE, FALSE ou un objet SQLite3Result
   */
  function sql_query ($query, $exit_on_error = true, $log_query = true)
  {
    // Execute la requete
    $r = $this->db->query($query);

    // Creve si t'as pas reussi
    if ($exit_on_error && !$r)
    {
       $this->print_sql_error_and_exit($query);
    }

    global $server_config;

    // Log la requete
    if ($log_query && $server_config['debug_log'] == '1' &&
        !preg_match("/^select/i", $query) &&
        !preg_match("/^show table status/i", $query))
    {
      $fichier = 'sql_'. date('ymd') .'.sql';
      $date = date('d/m/Y H:i:s');
      $ligne = '/* '. $date .' - '. $_SERVER['REMOTE_ADDR'] .' */ '. $query .';';

      write_file(PATH_LOG_TODAY . $fichier, 'a', $ligne);
    }

    $this->query_count++;

    return $r;
  }

  /**
   * Retourne un tableau indexé numeriquement et associativement representant
   * une ligne du resultat donné ou celui de la derniere requete.
   */
  public function sql_fetch_row ($result = 0)
  {
    if ($result)
    {
      return $result->fetchArray();
    }

    return $this->$result->fetchArray();
  }

  /**
   * Retourne un tableau associatif representant une ligne du resultat donné ou
   * celui de la derniere requete.
   */
  public function sql_fetch_assoc ($result = 0)
  {
    if ($result)
    {
      return $result->fetchArray(SQLITE3_ASSOC);
    }

    return $this->$result->fetchArray(SQLITE3_ASSOC);
  }

  /**
   * Nombre de lignes concernées par la dernière requête.
   */
  public function sql_numrows ($query_id = 0)
  {
    // Pas d'equivalent de sqlite_num_rows pour SQLite3
    return 0;
  }

  /**
   * Nombre de lignes modifiées, inserées ou supprimées par la derniere requete.
   */
  public function sql_affectedrows()
  {
    return $this->$db->changes();
  }

  /**
   * Retourne l'ID de la derniere ligne inserée dans la BDD.
   */
  public function sql_insertid ()
  {
    return $this->$db->lastInsertRowID();
  }

  /**
   * Fermeture de la ressource du resultat specifié, ou celui de la derniere
   * requete. Retourne toujours TRUE.
   */
  public function sql_free_result ($result = 0)
  {
    if ($result)
    {
      return $result->finalize();
    }

    return $this->$result->finalize();
  }

  /**
   * Retourne un tableau contenant le message et le code de la derniere erreur.
   */
  public function sql_error ($query_id = 0)
  {
    return array(
      'message' => $this->db->lastErrorMsg(),
      'code'    => $this->$db->lastErrorCode());
  }

  /**
   * Retourne le nombre de requete executées jusqu'a maintenant
   */
  public function sql_nb_requete ()
  {
    return $this->query_count;
  }

  /**
   * Protège les caractères spéciaux SQL
   */
  public function sql_escape_string ($s)
  {
    return $this->$db->escapeString($s);
  }

  /**
   * Affichage d'une erreur SQL et sortie du script.
   */
  private function print_sql_error_and_exit ($query)
  {
    list($err_code, $err_message) = $this->sql_error();

    echo "                                                                     \
    <table align=center border=1>                                              \
      <tr>                                                                     \
        <td class='c' colspan='3'>SQlite3 Database Error</td>                  \
      </tr>                                                                    \
      <tr>                                                                     \
        <th colspan='3'>Error: [$err_code] $err_message</th>                   \
      </tr>                                                                    \
      <tr>                                                                     \
        <th colspan='3'><u>Query:</u><br />$query</th>                         \
      </tr>                                                                    \
    ";

    if (MODE_DEBUG)
    {
      $i = 0;

      foreach (debug_backtrace() as $v)
      {
        $row_span = isset($v['args']) ? "rowspan='". count($v['args']) + 1 ."'" : '';
        $file = $v['file'];
        $line = $v['line'];
        $function = $v['function'];

        echo "                                                                 \
        <tr>                                                                   \
          <th width='50' align='center' $row_span>[$i]</th>                    \
          <th colspan='2'>                                                     \
            file => $file<br />                                                \
            ligne => $line<br />                                               \
            fonction => $function                                              \
          </th>                                                                \
        </tr>                                                                  \
        ";

        $j = 0;

        if (isset($v['args']))
        {
          foreach ($v['args'] as $arg)
          {
            echo "                                                             \
            <tr>                                                               \
              <th align='center'>[$j]</td>                                     \
              <td>$arg</td>                                                    \
            </tr>                                                              \
            ";

            $j++;
          }
        }

        $i++;
      }
    }

    echo "</table>\n";

    log_("mysql_error", array(
      $query,
      $err_code,
      $err_message,
      debug_backtrace()
    ));

    exit();
  }

}

?>
