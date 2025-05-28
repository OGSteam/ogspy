<?php

/**
 * Modèle pour gérer les technologies des joueurs.
 *
 * Cette classe étend la classe abstraite `Model_Abstract` et fournit des méthodes
 * pour interagir avec les données des technologies des joueurs dans la base de données.
 *
 * @package OGSpy
 * @subpackage Model
 * @author DarkNoon
 * @copyright Copyright &copy; 2016, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.4.0
 */

namespace Ogsteam\Ogspy\Model;

use Ogsteam\Ogspy\Abstracts\Model_Abstract;

class Player_Technology_Model  extends Model_Abstract
{
    /**
     * Récupère les technologies d'un joueur spécifique.
     *
     * @param int $player_id L'identifiant unique du joueur.
     * @return array Un tableau associatif contenant les technologies du joueur.
     *               Les clés du tableau incluent : `Esp`, `Ordi`, `Armes`, `Bouclier`,
     *               `Protection`, `NRJ`, `Hyp`, `RC`, `RI`, `PH`, `Laser`, `Ions`,
     *               `Plasma`, `RRI`, `Graviton`, `Astrophysique`.
     */
    public function select_user_technologies(int $player_id)
    {
        $request = "SELECT `Esp`, `Ordi`, `Armes`, `Bouclier`, `Protection`, `NRJ`, `Hyp`, `RC`, `RI`, `PH`, `Laser`, `Ions`, `Plasma`, `RRI`, `Graviton`, `Astrophysique`";
        $request .= " FROM " . TABLE_USER_TECHNOLOGY;
        $request .= " WHERE `player_id` = " . $player_id;
        $result = $this->db->sql_query($request);
        return  $this->db->sql_fetch_assoc($result);
    }
    /**
     * Supprime les technologies d'un joueur spécifique.
     *
     * @param int $player_id L'identifiant unique du joueur.
     *                       Correspond à la clé primaire dans la table des technologies des joueurs.
     * @return void Cette méthode ne retourne aucune valeur.
     */
    public function delete_user_technologies(int $player_id)
    {
        $request = "DELETE FROM " . TABLE_USER_TECHNOLOGY . " WHERE `player_id` = " . $player_id;
        $this->db->sql_query($request);
    }

    /**
     * Met à jour le niveau de la technologie d'espionnage pour un joueur spécifique.
     *
     * @param int $player_id L'identifiant unique du joueur.
     *                       Correspond à la clé primaire dans la table des technologies des joueurs.
     * @param int $level Le nouveau niveau de la technologie d'espionnage.
     *                   Doit être un entier positif représentant le niveau à définir.
     * @return void Cette méthode ne retourne aucune valeur.
     */
    public function update_esp(int $player_id, int $level)
    {
        $request = "UPDATE " . TABLE_USER_TECHNOLOGY . " SET `Esp` = " . $level . " WHERE `player_id` = " . $player_id;
        $this->db->sql_query($request);
    }
}
