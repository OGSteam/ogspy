<?php
/**
* OGSpy RSS Feed
* @package OGSpy
* @subpackage Rss
* @author DarkNoon
* @copyright Copyright &copy; 2015, http://www.ogsteam.fr/
* @version 3.2.0 ($Rev: 7690 $)
*/
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
/**
* OGSpy RSS Class
* @package OGSpy
* @subpackage Rss
*/
class rss_feed {
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
* Get the current class database instance. Creates it if dosen't exists (singleton)
*/   
    public static function getInstance(){  

		if( self::$_instance === false ){  
           self::$_instance = new rss_feed();  
		}  
  
       return self::$_instance;  
   }  
    
    public function generate_file(){
    
    if(!file_exists("rss.xml")) unlink("rss.xml");
  
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<rss version="2.0">';
        $xml .= '<channel>';
        $xml .= ' <title>Flux RSS OGSpy</title>';
        $xml .= ' <link>http://www.ogsteam.fr</link>';
        $xml .= ' <description>Le flux RSS de votre serveur OGSPY</description>';
        $xml .= ' <image>';
        $xml .= '   <title>Titre de l\'image</title>';
        $xml .= '   <url>http://i39.servimg.com/u/f39/09/04/37/37/logo_o10.png</url> ';
        $xml .= '   <link>http:///www.ogsteam.fr</link> ';
        $xml .= '   <description>Visitez le forum de l\'ogsteam !</description>';
        $xml .= '   <width>80</width>';
        $xml .= '   <height>80</height>';
        $xml .= ' </image>';
        $xml .= ' <language>fr</language>';
        $xml .= ' <copyright>OGSteam.fr</copyright>';
        $xml .= ' <managingEditor>ogsteam@gmail.com</managingEditor>';
        $xml .= ' <category>Informations</category>';
        $xml .= ' <generator>PHP/MySQL</generator>';
        $xml .= ' <docs>http://wiki.ogsteam.fr</docs>';



        /*  Maintenant, nous allons nous connecter à notre base de données afin d'aller chercher les
          items à insérer dans le flux RSS.
        */

        //on lit les 25 premiers éléments à partir du dernier ajouté dans la base de données
        $index_selection = 0;
        $limitation = 25;

        //On se connecte à notre base de données (pensez à mettre les bons logins)
        
        
        $log_data[] = ['title' => "Bienvenue", 'link' => "http://ogsteam.fr", 'pubDate' => date(DATE_RFC2822), 'description'=> "Ce site est magnifique !"];
        $log_data[] = ['title' => "Bienvenue2", 'link' => "http://ogsteam.fr", 'pubDate' => date(DATE_RFC2822), 'description'=> "Ce site est moyen !"];
        

        //Une fois les informations récupérées, on ajoute un à un les items à notre fichier
        foreach ($log_data as $donnees)
        {
          $xml .= '<item>';
          $xml .= '<title>'.stripcslashes($donnees['title']).'</title>';
          $xml .= '<link>'.$donnees['link'].'</link>';
          $xml .= '<guid isPermaLink="true">'.$donnees['link'].'</guid>';
          $xml .= '<pubDate>'.(date("D, d M Y H:i:s O", strtotime($donnees['pubDate']))).'</pubDate>';
          $xml .= '<description>'.stripcslashes($donnees['description']).'</description>';
          $xml .= '</item>';
        }

        //Et on ferme le channel et le flux RSS.
        $xml .= '</channel>';
        $xml .= '</rss>';

        /*  Tout notre fichier RSS est maintenant contenu dans la variable $xml.
          Nous allons maintenant l'écrire dans notre fichier XML et ainsi mettre à jour notre flux.
          Pour cela, nous allons utiliser les fonctions de php File pour écrire dans le fichier.

          Notez que l'adresse URL du fichier doit être relative obligatoirement !
        */

        //On ouvre le fichier en mode écriture
        $fp = fopen("rss.xml", 'w+');

        //On écrit notre flux RSS
        fputs($fp, $xml);

        //Puis on referme le fichier
        fclose($fp);

        } //Fermeture de la fonction    
        
        
}

?>