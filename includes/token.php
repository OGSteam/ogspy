<?php

/**
 * Token Class
 * CRSF protect
 * @package OGSpy
 * @subpackage token
 * @author Machine
 * @created 05/01/2018
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

class token
{
    private $lifeTime;
    private $splitter = "____";
    private $saltPath = "parameters";
    private $salt = "&pndmfekdiè_e,frèl'";
    private $token;

    /**
     * version static de getToken
     * @param int $lifetime
     * @param string $formName
     * @param bool $inSession
     * @return string
     */
    public static function staticGetToken($lifetime = 600, $formName = "", $inSession = true)
    {
        $t = new token();
        return  $t->getToken($lifetime, $formName, $inSession);
    }

    /**
     * Static Check Token
     * @param $tokenA
     * @param null $TokenB
     * @return bool
     */
    public static function statiCheckToken($tokenA, $TokenB = null)
    {
        $t = new token();
        return  $t->checkToken($tokenA, $TokenB);
    }


    /**
     * token class constructor.
     */
    public function __construct()
    {
        $this->salt = $this->getSalt();
    }


    //permet l'obtention et le stockage d'un token

    /**
     * Token Generation
     * @param int $lifetime
     * @param string $formName
     * @param bool $inSession
     * @return string
     */
    public function getToken($lifetime = 600, $formName = "", $inSession = true)
    {
        $this->lifeTime = $lifetime;
        $str = $this->getSalt() . "_" . $formName . "_" . microtime(true);
        $this->token = sha1($str) . $this->splitter . ($this->lifeTime + time()); // token de form "sha1()____microtime"
        // on stock le token
        if ($inSession) {
            $this->saveInCookie($this->token);
        }
        return $this->token;
    }


    /**
     * Token Verification
     * @param $tokenA
     * @param null $TokenB
     * @return bool
     */
    public function checkToken($tokenA, $TokenB = null)
    {
        $tokenA = trim((string)$tokenA);
        if (stristr($tokenA, $this->splitter) === false) // si pas de splitteur, ce n'est pas notre token
        {
            $this->resetInCookie();
            return false;
        }
        $date = (int)explode($this->splitter, $tokenA)[1];
        $t = time();
        if ($date < $t) // si périmé [date du token inf a la date en cours (timestamp )]
        {
            $this->resetInCookie();
            return false;
        }

        if ($TokenB == null) {
            $TokenB = $this->getInCookie();
            //stockage session
            if ($tokenA == $TokenB) {
                $this->resetInCookie();
                return true;
            }
        } else {
            $TokenB = trim((string)$TokenB);
            if ($tokenA == $TokenB) {
                $this->resetInCookie();
                return true;
            }
        }
        //tout autre cas, le token n'est pas valide
        $this->resetInCookie();
        return false;
    }

    /**
     * Path to the Salt File
     * @return string Saltpath
     */
    private function get_saltpath()
    {
        return $this->saltPath . '/salt';
    }

    //

    /**
     * Returns curent salt, if not existing, returns the default one
     * @return string
     */
    private function getSalt()
    {
        $path = $this->get_saltpath();
        if (isset($path) && file_exists($path)) {
            $retour = file_get_contents($path);
            $this->salt = $retour;
        } else {
            $this->salt = $this->CreateNewSalt();
        }
        return $this->salt;
    }

    /**
     * Create a new salt token
     * @return string
     */
    public function CreateNewSalt()
    {
        $chaine = '0123456789&é(-è_çà)=abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $nbLettre = rand(10, 20); // le nombre de lettre sera aléatoire ...

        $retour = '';
        for ($i = 0; $i < $nbLettre; $i++) {
            $retour .= $chaine[rand(0, strlen($chaine) - 1)];
        }
        $pathSalt =   $this->get_saltpath();
        file_put_contents($pathSalt, $retour);
        return $retour;
    }


    /**
     * Save token in the Session
     */
    private function saveInCookie()
    {
        $_SESSION['ogspy_token'] = $this->token;
    }

    /**
     * Get the Token from the Cookie
     * @return mixed|null
     */
    private function getInCookie()
    {
        return isset($_SESSION['ogspy_token']) ? $_SESSION['ogspy_token'] : null;
    }

    /**
     *  Remove salt from the session
     */
    private function resetInCookie()
    {
        global $_COOKIE;
        if (isset($_SESSION['ogspy_token'])) {
            unset($_SESSION['ogspy_token']);
        }
        //old plugin token (3.3.4)
        if (isset($_COOKIE["token"])) {
            unset($_COOKIE['token']);
            setcookie("token", $_COOKIE["token"], time() - 1);
        }
    }
}


//usage
//$t= new token();
//
//$ttokengenere = $t->getToken(600,"forumlaireX");
//
//verification
//$t= new token();
//if($t->checkToken($ttokengenere))
//{ ok}
//else
//{ pas ok}

// usage static
//$ttokengenere = token::staticGetToken();
//verification
//if( token::staticCheckToken($ttokengenere))
//{ ok}
//else
//{ pas ok}
