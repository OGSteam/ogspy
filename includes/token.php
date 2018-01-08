<?php
/**
* Token Class
 * CRSF protect
* @package OGSpy
* @subpackage token
* @author Machine
* @created 05/01/2018
* @copyright Copyright &copy; 2007, http://ogsteam.fr/
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
class token
{
    private $lifeTime;
    private $splitter="____";
    private $saltPath ="parameters";
    private $salt = "&pndmfekdiè_e,frèl'";
    private $token;

    //version static de getToken
    public static function staticGetToken($lifetime=600,$formName = "",$inSession=true)
    {
        $t= new token();
        return  $t->getToken($lifetime,$formName,$inSession);
    }
    //version static de checkToken
    public static function statiCheckToken($tokenA,$TokenB=null)
    {
        $t= new token();
        return  $t->checkToken($tokenA,$TokenB);
    }



    public function __construct()
    {
        $this->salt=$this->getSalt();
    }


    //permet l'obtention et le stockage d'un token
    public function getToken($lifetime=600,$formName = "",$inSession=true)
    {
        $this->lifeTime = $lifetime;
        $str = $this->getSalt()."_".$formName."_".microtime(true);
        $this->token= sha1($str).$this->splitter.($this->lifeTime+time()); // token de form "sha1()____microtime"
        // on stock le token
        if ($inSession)
        {
           $this->saveInCookie();
        }
        return $this->token;


    }


    //permet de verifier un token
    public function checkToken($tokenA,$TokenB=null)
    {
        global $HTTP_COOKIE_VARS;

        $tokenA = trim((string)$tokenA);
        if(stristr($tokenA, $this->splitter) === FALSE) // si pas de splitteur, ce n'est pas notre token
        {
            return false;
        }
        $date = (int)explode($this->splitter,$tokenA)[1];
        $t=time();
        if($t < $date) // si périmé
        {
            return false;
        }


        if ($TokenB == null )
        {
            $TokenB = $this->getInCookie();
            //stockage session
            if ($tokenA == $TokenB )
            {
                $this->resetInCookie();
                return true;
            }
        }
        else
        {
            $TokenB = trim((string)$TokenB);
            if ($tokenA == $TokenB )
            {
                return true;
            }
        }
        //tout autre cas, le token n'est pas valide
        $this->resetInCookie();
        return false;


    }


        //chemin d'acces du sel
    private function _saltPath()
    {
        return $this->saltPath.'/salt';
    }

    //retour le sel si existe sinon on utilise celui par defaut
    private function getSalt()
    {
        $path = $this->_saltPath;
        if(isset($path))
        {
           $retour =  file_get_contents($path);
            $this->salt =  $retour;
        }
    }

    // permet la création d'un sel pour le token
    public function CreateNewSalt()
    {
        $chaine = '0123456789&é(-è_çà)=abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $nbLettre = rand (10 , 20 );// le nombre de lettre sera aléatoire ...

        $retour = '';
        for ($i = 0; $i < $nbLettre; $i++) {
            $retour .= $chaine[rand(0, strlen($chaine)-1)];
        }
        $pathSalt =   $this->_saltPath();
        file_put_contents($pathSalt,$retour);
    }


    private function saveInCookie($content)
    {
        setcookie("token", $this->token);
    }

    private function getInCookie($content)
    {
        global $HTTP_COOKIE_VARS;
        return $HTTP_COOKIE_VARS['token'];
    }

    private function resetInCookie()
    {
        global $_COOKIE ;
        if (isset($_COOKIE["token"]))
        {
            $t = $_COOKIE["token"];
            setcookie("token", $t,time()-1);
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
