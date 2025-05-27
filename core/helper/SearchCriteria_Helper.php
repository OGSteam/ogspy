<?php

/**
 * Entity Universe
 *
 * @package OGSpy
 * @subpackage Entity\Universe
 * @author Itori
 * @copyright Copyright &copy; 2016, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.4.0
 */

namespace Ogsteam\Ogspy\Helper;

use Ogsteam\Ogspy\Abstracts\Helper_Abstract;

class SearchCriteria_Helper extends Helper_Abstract
{
    protected static string $name = "Search Criteria";
    protected static string $description = "";
    protected static string $version = "0.0.1";

    private $server_config;
    private $player_name;
    private $ally_name;
    private $planet_name;
    private $galaxy_up;
    private $galaxy_down;
    private $system_up;
    private $system_down;
    private $row_down;
    private $row_up;
    private $is_moon = false;
    private $is_inactive = false;
    private $is_spied = false;

    /**
     * Search_Criteria constructor.
     * @param $server_config
     */
    public function __construct($server_config)
    {
        $this->server_config = $server_config;
    }

    /**
     * @return string
     */
    public function getPlanetName()
    {
        return $this->planet_name;
    }

    /**
     * @param string $planet_name
     * @return Search_Criteria
     */
    public function setPlanetName($planet_name)
    {
        $this->planet_name = $planet_name;
        return $this;
    }

    /**
     * @return int
     */
    public function getGalaxyUp()
    {
        return $this->galaxy_up;
    }

    /**
     * @param int $galaxy_up
     * @return Search_Criteria
     */
    public function setGalaxyUp($galaxy_up)
    {
        $this->galaxy_up = $galaxy_up;
        return $this;
    }

    /**
     * @return int
     */
    public function getGalaxyDown()
    {
        return $this->galaxy_down;
    }

    /**
     * @param int $galaxy_down
     * @return Search_Criteria
     */
    public function setGalaxyDown($galaxy_down)
    {
        $this->galaxy_down = $galaxy_down;
        return $this;
    }

    /**
     * @return int
     */
    public function getSystemUp()
    {
        return $this->system_up;
    }

    /**
     * @param int $system_up
     * @return Search_Criteria
     */
    public function setSystemUp($system_up)
    {
        $this->system_up = $system_up;
        return $this;
    }

    /**
     * @return int
     */
    public function getSystemDown()
    {
        return $this->system_down;
    }

    /**
     * @param int $system_down
     * @return Search_Criteria
     */
    public function setSystemDown($system_down)
    {
        $this->system_down = $system_down;
        return $this;
    }

    /**
     * @return int
     */
    public function getRowDown()
    {
        return $this->row_down;
    }

    /**
     * @param int $row_down
     * @return Search_Criteria
     */
    public function setRowDown($row_down)
    {
        $this->row_down = $row_down;
        return $this;
    }

    /**
     * @return int
     */
    public function getRowUp()
    {
        return $this->row_up;
    }

    /**
     * @param int $row_up
     * @return Search_Criteria
     */
    public function setRowUp($row_up)
    {
        $this->row_up = $row_up;
        return $this;
    }

    /**
     * @return string
     */
    public function getAllyName()
    {
        return $this->ally_name;
    }

    /**
     * @param string $ally_name
     * @return Search_Criteria
     */
    public function setAllyName($ally_name)
    {
        $this->ally_name = $ally_name;
        return $this;
    }

    /**
     * @return string
     */
    public function getPlayerName()
    {
        return $this->player_name;
    }

    /**
     * @param string $player_name
     * @return Search_Criteria
     */
    public function setPlayerName($player_name)
    {
        $this->player_name = $player_name;
        return $this;
    }

    /**
     * @param boolean $is_moon
     * @return Search_Criteria
     */
    public function setIsMoon($is_moon)
    {
        $this->is_moon = $is_moon;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsMoon()
    {
        return $this->is_moon;
    }

    /**
     * @return boolean
     */
    public function getIsInactive()
    {
        return $this->is_inactive;
    }

    /**
     * @param boolean $is_inactive
     * @return Search_Criteria
     */
    public function setIsInactive($is_inactive)
    {
        $this->is_inactive = $is_inactive;
        return $this;
    }
    /**
     * @return boolean
     */

    //Binu : ajout de fonctions relatives aux espionnages
    public function getIsSpied()
    {
        return $this->is_spied;
    }

    /**
     * @param boolean $is_spied
     * @return Search_Criteria
     */

    public function setIsSpied($is_spied)
    {
        $this->is_spied = $is_spied;
        return $this;
    }

    //Fin

    public function isValid()
    {
        if ($this->galaxy_up != null || $this->galaxy_down != null) {
            if (!is_int($this->galaxy_up) || $this->galaxy_up < 1 || $this->galaxy_up > $this->server_config['num_of_galaxies']) {
                return false;
            }
            if (!is_int($this->galaxy_down) || $this->galaxy_down < 1 || $this->galaxy_down > $this->server_config['num_of_galaxies']) {
                return false;
            }
        }
        if ($this->system_up != null || $this->system_down != null) {
            if (!is_int($this->system_up) || $this->system_up < 1 || $this->system_up > $this->server_config['num_of_systems']) {
                return false;
            }
            if (!is_int($this->system_down) || $this->system_down < 1 || $this->system_down > $this->server_config['num_of_systems']) {
                return false;
            }
        }
        if ($this->row_down != null || $this->row_up != null) {
            if (!is_int($this->row_down) || $this->row_down < 1 || $this->row_down > 15) {
                return false;
            }
            if (!is_int($this->row_up) || $this->row_up < 1 || $this->row_up > 15) {
                return false;
            }
        }
        if (!is_bool($this->is_moon)) {
            return false;
        }
        if (!is_bool($this->is_inactive)) {
            return false;
        }
        if (!is_bool($this->is_spied)) {
            return false;
        }
        return true;
    }

    //Binu : ajout d'une fonction pour convertir les coordonn�es de la table universe en coordonn�es de la table spy
    public function getArrayCoordinates()
    {
        $galaxy = [
            $this->galaxy_down ?? 1,
            $this->galaxy_up ?? $this->server_config['num_of_galaxies']
        ];
        $system = [
            $this->system_down ?? 1,
            $this->system_up ?? $this->server_config['num_of_systems']
        ];
        $row = [
            $this->row_down ?? 1,
            $this->row_up ?? 15
        ];

        $coordinates = [];
        foreach (range($galaxy[0], $galaxy[1]) as $i) {
            foreach (range($system[0], $system[1]) as $j) {
                foreach (range($row[0], $row[1]) as $k) {
                    $coordinates[] = "$i:$j:$k";
                }
            }
        }
        return $coordinates;
    }
    //Fin
}
