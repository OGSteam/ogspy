<?php

namespace Ogsteam\Ogspy\Core;


class Params
{
    private $data;
    private static $nbInstance;

    public function __construct()
    {
        $this->data = array();

        if ((int)self::$nbInstance > 0) {
            //TODO si plus d'une instance LOG warning => class ogspy doit en etre le conteneur
        }

        //hydratation
        $tVart = array("post" => $_POST, "get" => $_GET, "cookie" => $_COOKIE);
        foreach ($tVart as $type => $var) {
            foreach ($var as $key => $value) {
                //insertion
                $this->setParam($key, $value, $type);
            }
        }

        //compteur de session
        self::$nbInstance = (int)(self::$nbInstance) + 1;

    }


    //retourne un paramettre ($post/$get ou custom)
    public function getParam($key, $default = null)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key]["value"];
        }
        return $default;
    }

    //retourne un tous les param en mémoire
    public function getAllParams()
    {
        return $this->data;
    }

    public function getAllParamsLegacy()
    {
        $tPub = $this->getAllParams();
        $pub = array();

        foreach ($tPub as $var) {
            $key = $var["key"];
            $value = $var["value"];


            $pub[$key] = (new class($value, $key)
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
                    log_("depreciate", "pub_" . $this->key);
                    return $this->value;
                }
            });

        }
        return $pub;
    }



//insert un paramettre ($post/$get/coockie ou custom)
    // type : poste/get/custom
    public function setParam($key, $value, $type = "custom")
    {
        if (isset($this->data[$value])) {
            //TODO Creer warning, on ecrase une entrée
            /// peut etre fait volontairement ?
        }
        //filtre
        $key = $this->sanitize($key);
        $value = $this->sanitize($value);
        $type = $this->sanitize($type);

        $this->data[$key] = array("key" => $key, "value" => $value, "type=" => $type);
    }


    public function __set($key, $value)
    {
        $this->setParam($key, $value, "custom");
    }

    public function __get($key)
    {
        return $this->getParam($key);
    }


    public function sanitize($str, $type = null)
    {
        if ($type == "post") {
            // TODO est ce encore utile ?
            if (!$this->check_postvalue($str)) {
                die("I don't like you...");
            }
        } else {
            if (!$this->check_getvalue($str)) {
                die("I don't like you...");
            }
        }
        $str = htmlentities($str);
        return $str;
    }


    /**
     * Security : HTTP GET Data verifications
     * @param string $secvalue The value to be checked
     * @return boolean true if the verification is ok
     */
    private function check_getvalue($secvalue)
    {
        if (!is_array($secvalue)) {
            if ((preg_match("/<[^>]*script*\"?[^>]*>/i", $secvalue)) || (preg_match("/<[^>]*object*\"?[^>]*>/i",
                    $secvalue)) || (preg_match("/<[^>]*iframe*\"?[^>]*>/i", $secvalue)) || (preg_match("/<[^>]*applet*\"?[^>]*>/i", $secvalue)) || (preg_match("/<[^>]*meta*\"?[^>]*>/i",
                    $secvalue)) || (preg_match("/<[^>]*style*\"?[^>]*>/i", $secvalue)) || (preg_match("/<[^>]*form*\"?[^>]*>/i", $secvalue)) || (preg_match("/<[^>]*img*\"?[^>]*>/i",
                    $secvalue)) || (preg_match("/\([^>]*\"?[^)]*\)/i", $secvalue)) || (preg_match("/\"/i",
                    $secvalue))
            ) {
                return false;
            }
        } else {
            foreach ($secvalue as $subsecvalue) {
                if (!check_getvalue($subsecvalue)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Security : HTTP POST Data verifications
     * @param string $secvalue The value to be checked
     * @return boolean true if the verification is ok
     */
    private function check_postvalue($secvalue)
    {
        if (!is_array($secvalue)) {
            if ((preg_match("/<[^>]*script*\"?[^>]*>/", $secvalue)) || (preg_match("/<[^>]*style*\"?[^>]*>/",
                    $secvalue))
            ) {
                return false;
            }
        } else {
            foreach ($secvalue as $subsecvalue) {
                if (!check_postvalue($subsecvalue)) {
                    return false;
                }
            }
        }
        return true;
    }





    //TODO eventuellement prevoir fonction
    //getAllPost
    //getrallGet


}