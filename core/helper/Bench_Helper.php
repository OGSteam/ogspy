<?php

namespace Ogsteam\Ogspy\Helper;

use Ogsteam\Ogspy\Abstracts\Helper_Abstract;

/**
 * Class helloWorld, test
 * @package Ogsteam\Ogspy\Helper
 *
 */
class Bench_Helper extends Helper_Abstract
{

    static protected $name = "Helper Bench";
    static protected $description = "Aide à l'usage de benchmark";
    static protected $version = "0.0.1";

    static private $BenchData = array();
    static private $BenchKey = array();
    static public $counter = 0;

    private $key = "";
    private $label = "";
    private $start = 0;
    private $stop = 0;
    private $isrunning = false;
    private $listofstep = array();
    private $totalelapsed = 0;


    /**
     * @param Constructeur
     *
     * si un objet est en memoire on va le chercher ... sinon on en créé un nouveau
     */
    public function __construct($key)
    {
        $this->key = $key;
        if (isset(self::$BenchData[$key])) {
            $this->label = self::$BenchData[$key]->getLabel();
            $this->start = self::$BenchData[$key]->getStart();
            $this->stop = self::$BenchData[$key]->getStop();
            $this->isrunning = self::$BenchData[$key]->getIsRunning();
            $this->listofstep = self::$BenchData[$key]->getListOfStep();
            $this->totalelapsed = self::$BenchData[$key]->getTotalElapsed();
        }
    }

    /**
     * @param Lance le timer
     */
    public function StartBench($label)
    {
        self::$counter++;
        if (!$this->isrunning) {
            $this->label = $label;
            $this->start = $this->get_time();
            $this->isrunning = true;

            $this->save();
        } else {
            throw new Exception('Bench deja en cours!');
        }

    }
    /**
     * @param Sotp le timer
     *
     */
    public function StopBench($label)
    {
        if ($this->isrunning) {
            $this->stop = $this->get_time();
            $this->isrunning = false;

            $elpased = $this->stop - $this->start;

            $this->totalelapsed = $this->totalelapsed + $elpased;

            // on sauvegarde dans liste of step
            $this->listofstep[] = array(
                "label" => $this->label,
                "start" => $this->start,
                "stop" => $this->stop,
                "elapsed" => $elpased
            );

            $this->save();
        } else {
            throw new Exception('aucun Bench en cours!');
        }
    }


    function getLabel()
    {
        return $this->label;
    }

    function getStart()
    {
        return $this->start;
    }

    function getTotalElapsed()
    {
        return $this->totalelapsed;
    }

    function getStop()
    {
        return $this->stop;
    }

    function getIsRunning()
    {
        return $this->isrunning;
    }

    function getListOfStep()
    {
        return $this->listofstep;
    }

    function getBenKey()
    {
        return self::$BenchKey;
    }


    function get_time()
    {
        //return microtime(true);
        $mtime = microtime();
        $mtime = explode(" ", $mtime);
        $mtime = $mtime[1] + $mtime[0];

        return $mtime;
    }

    function get_Counter()
    {
        return self::$counter;
    }

    private function save()
    {
        // on check si la cle
        if (!isset(self::$BenchKey[$this->key])) {
            self::$BenchKey[] = $this->key;
        }

        self::$BenchData[$this->key] = $this;
    }

}