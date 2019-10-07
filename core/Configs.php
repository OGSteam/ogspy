<?php


namespace Ogsteam\Ogspy\Core;


class Configs
{

    private $data;
    private static $nbInstance;

    public function __construct()
    {
        $this->data = array();

        if ((int)self::$nbInstance > 0) {
            return null;

        }

        //compteur de session
        self::$nbInstance = (int)(self::$nbInstance) + 1;

    }


    //retourne un paramettre ($post/$get ou custom)
    public function getConfig($key, $default = null)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        return $default;
    }

    //retourne un tous les param en mémoire
    public function getAllConfigs()
    {
        return $this->data;
    }

    public function getAllConfigsLegacy()
    {
        $tConf = $this->getAllConfigs();
        $conf = array();

        foreach ($tConf as $key => $value) {

            $conf[$key] = (new class($value, $key)
            {
                private $value;
                private $key;

                public function __construct($value, $key)
                {
                    $this->key = $key;
                    $this->value = $value;
                }

                public function __toString()
                {
                    log_("depreciate", "server_config[\"" . $this->key."\"]");
                    return $this->value;
                }
            });

        }
        return $conf;
    }



//insert un paramettre ($post/$get/coockie ou custom)
    // type : poste/get/custom
    public function setConfig($key, $value)
    {
       $this->data[$key] = $value;
    }


    public function __set($key, $value)
    {
        $this->setConfig($key, $value);
    }

    public function __get($key)
    {
        return $this->getConfig($key);
    }

    public function isNotInstall()
    {

        $filename = 'cache/cache_config.php';
        $server_config = array();

        if (file_exists($filename)) {
            include $filename;
            // regeneration si besoin
            if ((filemtime($filename) + $server_config['config_cache']) < time()) {
                generate_config_cache();
            }

        } else {
            generate_config_cache();
            if (file_exists($filename)) {
                include $filename; // on reinjecte le fichier s'il existe'
            }
        }
        // hydratationch
        foreach ($server_config as $key =>$value)
        {
            $this->setConfig($key,$value);
            }

    }


}