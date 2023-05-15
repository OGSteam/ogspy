<?php

namespace Ogsteam\Ogspy\Helper;

use Ogsteam\Ogspy\Abstracts\Helper_Abstract;

class Benchmark_Helper extends Helper_Abstract {

    protected static $name = "Helper Benchmark";
    protected static $description = "Aide pour definir les temps d'execution";
    protected static $version = "0.0.1";
  
    private $Content = array(); /// conteneur des benchmarkrs de l'objet
    private $BenchName;
    private $startTime;
    private $endTime;
    private $elapsedTime = 0;
    private $info = "";

    public function __construct($benchmarkname) {
        $this->BenchName = $benchmarkname;
        $this->InitBench();
    }

    private function InitBench() {
        $this->startTime = 0;
        $this->endTime = 0;
    }

    public function addCustomBench($start, $stop,  $info) {
        $this->startTime = $start;
        $this->endTime = $stop;
        $this->info = $info;
        $this->addElapsed($this->getCurrentElapsed());
        $this->postBench();
    }

    public function start() {
        if (!$this->isRunning()) {
            $this->InitBench();
            $this->startTime = microtime(true);
        }
    }

    public function stop($info = "") {
        $this->endTime = microtime(true);
        $this->info = $info;
        $this->addElapsed($this->getCurrentElapsed());

        $this->postBench();
    }

    private function postBench() {
        /// stockage pour possible exploiatation
        $toarray = $this->objectToArray();
        $this->Content[] = $toarray; /// Ajout dans conteneur de l'objet
    }

    public function isRunning() {
        if (($this->startTime != 0) && ($this->endTime == 0)) {
            return true;
        }
        return false;
    }

    public function getCurrentElapsed() {
        return $this->endTime - $this->startTime;
    }

    public function getBenchname() {
        return $this->BenchName;
    }

    public function getAllElapsed() {
        return $this->elapsedTime;
    }

    private function addElapsed($time) {
        $this->elapsedTime += $time;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->getName() . " (" . $this->version . ") [" . $this->description . "]";
    }

    private function objectToArray() {
        $tvalue = array();
        $tvalue["BenchName"] = $this->BenchName;
        $tvalue["startTime"] = $this->startTime;
        $tvalue["endTime"] = $this->endTime;
        $tvalue["currentElapsed"] = $this->getCurrentElapsed();
        $tvalue["totalElapsedTime"] = $this->elapsedTime;
        $tvalue["info"] = $this->info;

        return $tvalue;
    }

}
