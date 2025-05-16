<?php
global $user_data, $lang;

/**
 * Affichage Empire - Page Planetes
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyser
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

require_once("includes/ogame.php");

global $server_config;

$user_empire = player_get_empire($player_data['id']);

$user_building   = $user_empire['building'];
$user_defence    = $user_empire['defence'];
$user_technology = $user_empire['technology'];

$nb_planete      = find_nb_planete_user($player_data['user_id']);

$view = isset($pub_view) && $pub_view === 'moons' ? 'moons' : 'planets';

$name = $coordinates = $fields = $temperature_min = $temperature_max = $satellite = "";
//Planète
print_r($user_building);
foreach ($user_building as $planet_id => $planet) {
    $name            .= "'" . $planet['planet_name'] . "', ";
    $coordinates     .= "'" . $planet['coordinates'] . "', ";
    $fields          .= "'" . $planet['fields'] . "', ";
    $temperature_min .= "'" . $planet['temperature_min'] . "', ";
    $temperature_max .= "'" . $planet['temperature_max'] . "', ";
    $satellite       .= "'" . $planet['Sat'] . "', ";
}
/*
for ($i = 201; $i <= $nb_planete + 200; $i++) {
    $name            .= "'" . $user_building[$i]['planet_name'] . " (lune)', ";
    $coordinates     .= "'" . $user_building[$i]['coordinates'] . "', ";
    $fields          .= "'" . $user_building[$i]['fields'] . "', ";
    $temperature_min .= "'" . $user_building[$i]["temperature_min"] . "', ";
    $temperature_max .= "'" . $user_building[$i]["temperature_max"] . "', ";
    $satellite       .= "'" . $user_building[$i]["Sat"] . "', ";
}*/


// place le tag active au besoin
$tagactive = "active";
$tagactiveplanets = "";
$tagactivemoons = "";

switch ($view) {
    case "planets":
        $tagactiveplanets = $tagactive;
        break;
    case "moons":
        $tagactivemoons = $tagactive;
        break;
    default:
        break;
}

?>
<div class="nav-page-menu">
    <div class="nav-page-menu-item nav-page-menu-item-admin-infoserver <?php echo $tagactiveplanets; ?>">
        <a class="nav-page-menu-link" href="index.php?action=home&amp;view=planets">
            <?php echo $lang['HOME_EMPIRE_PLANET']; ?>
        </a>
    </div>
    <div class="nav-page-menu-item nav-page-menu-item-admin-parameter <?php echo $tagactivemoons; ?>">
        <a class="nav-page-menu-link" href="index.php?action=home&amp;view=moons">
            <?php echo $lang['HOME_EMPIRE_MOON']; ?>
        </a>
    </div>
</div>

<?php
// vérification de compte de planete/lune avec la technologie astro
if (!isset($user_technology['Astrophysique']) || $user_technology['Astrophysique'] == '') {
    $user_technology['Astrophysique'] = 0;
}
$astro = astro_max_planete($user_technology['Astrophysique']);
?>
<?php if (((find_nb_planete_user($user_data['id']) > $astro) || (find_nb_moon_user($user_data['id']) > $astro)) && ($user_technology != false)) : ?>
    <div class="og-msg og-msg-danger">
        <h3 class="og-title"><?php echo $lang['HOME_EMPIRE_ERROR']; ?></h3>
        <p class="og-content">
            <?php echo (find_nb_planete_user($user_data['id']) > $astro) ? $lang['HOME_EMPIRE_ERROR_PLANET'] . '<br>' : ''; ?>
            <?php echo (find_nb_moon_user($user_data['id']) > $astro) ? $lang['HOME_EMPIRE_ERROR_MOON'] . '<br>' : ''; ?>
        </p>
    </div>
<?php endif; ?>
<?php // fin vérification de compte de planete/lune avec la technologie astro
?>

<table class="og-table og-full-table og-table-empire">
    <thead>
        <tr>
            <th colspan="<?php print ($nb_planete < 10) ? '10' : $nb_planete + 1 ?>">
                <?php echo ($lang['HOME_EMPIRE_SUMMARY']); ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>&nbsp;</td>
            <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                <?php $user_production[$i] = ogame_production_planet($user_building[$i], $user_technology, $user_data, $server_config); ?>
                <td>
                    <?php if (!isset($pub_view) || $pub_view == "planets") : ?>
                        <input type='image' class="og-button og-button-image" title='<?php echo $lang['HOME_EMPIRE_MOVELEFT'] . " " . $user_building[$i]["planet_name"]; ?>' src='images/previous.png'>
                        <input type='image' class="og-button og-button-image og-button-danger" title='<?php echo $lang['HOME_EMPIRE_DELETE_PLANET'] . " " . $user_building[$i]["planet_name"]; ?>' src='images/drop.png' onclick="window.location = 'index.php?action=del_planet&amp;planet_id=<?php echo $i ?>&amp;view=<?php echo $view; ?>';">
                        <input type='image' class="og-button og-button-image" title='<?php echo $lang['HOME_EMPIRE_MOVERIGHT'] . " " . $user_building[$i]["planet_name"]; ?>' src='images/next.png'>
                    <?php else : ?>
                        <input type='image' class="og-button og-button-image" title='<?php $lang['HOME_EMPIRE_DELETE_MOON'] . " " . $user_building[$i]["planet_name"]; ?>' src='images/drop.png' onclick="window.location = 'index.php?action=del_planet&amp;planet_id=<?php echo $i; ?>&amp;view=<?php echo $view; ?>';">
                    <?php endif; ?>
                </td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_EMPIRE_NAME']); ?>
            </td>
            <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                <td class="tdcontent">
                    <span class="og-highlight">
                        <?php echo ($user_building[$i]["planet_name"] == "") ?  "&nbsp;" : $user_building[$i]["planet_name"]; ?>
                    </span>

                </td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_EMPIRE_COORD']); ?>
            </td>
            <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                <td class="tdcontent">
                    [<?php echo ($user_building[$i]["coordinates"] == "" || ($user_building[$i]["planet_name"] == "" && $view == "moons"))  ?  "&nbsp;" : $user_building[$i]["coordinates"]; ?>]
                </td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_EMPIRE_FIELDS']); ?>
            </td>
            <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                <td class="tdcontent">
                    <?php $fields = ($user_building[$i]["fields"] == "0") ? 0 : $user_building[$i]["fields"]; ?>
                    <?php echo $user_building[$i]["fields_used"] . " / " . $fields; ?>
                </td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_EMPIRE_MINTEMP']); ?>
            </td>
            <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                <td class="tdcontent">
                    <?php echo ($user_building[$i]["temperature_min"] == "") ?  "&nbsp;" : $user_building[$i]["temperature_min"]; ?>
                </td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_EMPIRE_MAXTEMP']); ?>
            </td>
            <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                <td class="tdcontent">
                    <?php echo ($user_building[$i]["temperature_max"] == "") ?  "&nbsp;" : $user_building[$i]["temperature_max"]; ?>
                </td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_EMPIRE_EXTENSION']); ?>
            </td>
            <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                <td class="tdcontent">
                    <?php $booster = "&nbsp;"; ?>
                    <?php $booster_tab = booster_decode($user_building[$i]["boosters"]); ?>
                    <?php if ($view == "planets") : ?>
                        <?php echo $booster_tab['extention_p']; ?>
                    <?php else : ?>
                        <?php echo  $booster_tab['extention_m']; ?>
                    <?php endif; ?>
                </td>
            <?php endfor; ?>
        </tr>
    </tbody>


    <?php if ($view == "planets") :  // si view = planets
    ?>
        <thead>
            <thead>
                <tr>
                    <th colspan="<?php print ($nb_planete < 10) ? '10' : $nb_planete + 1 ?>">
                        <?php echo ($lang['HOME_EMPIRE_PRODUCTION_EXPECTED']); ?>
                    </th>
                </tr>
            </thead>
        </thead>
        <tbody>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_METAL']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <?php echo ($M = $user_building[$i]['M'] == "") ?  "&nbsp;" : $user_production[$i]['prod_theorique']['M']; ?>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_CRYSTAL']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <?php echo ($M = $user_building[$i]['C'] == "") ?  "&nbsp;" : $user_production[$i]['prod_theorique']['C']; ?>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_DEUT']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <?php echo ($M = $user_building[$i]['D'] == "") ?  "&nbsp;" : $user_production[$i]['prod_theorique']['D']; ?>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_ENERGY']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <?php $NRJ = (!isset($user_production[$i]['NRJ'])) ? "&nbsp;" : $user_production[$i]['NRJ']; ?>
                        <?php echo number_format($NRJ, 0, ',', ' ') ;  ?>
                    </td>
                <?php endfor; ?>
            </tr>
        </tbody>
        <thead>
            <tr>
                <th colspan="<?php print ($nb_planete < 10) ? '10' : $nb_planete + 1 ?>">
                    <?php echo ($lang['HOME_EMPIRE_PRODUCTION_REAL']); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="tdname"><?php echo ($lang['HOME_EMPIRE_RATIO']); ?></td>

                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <?php $user_production[$i]['ratio'] = (!isset($user_production[$i]['ratio'])) ? 0 : $user_production[$i]['ratio']; ?>
                        <?php if ($user_production[$i]['ratio'] != 1) : ?>
                            <span class="og-alert"><?php echo number_format(round($user_production[$i]['ratio'], 3), 3, ',', ' '); ?></span>
                        <?php else : ?>
                            <span class="og-success"><?php echo number_format(round($user_production[$i]['ratio'], 3), 3, ',', ' '); ?></span>
                        <?php endif; ?>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_METAL']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <?php if ($user_building[$i]['M'] != "") : ?>
                            <?php echo number_format(floor($user_production[$i]['prod_reel']['M']), 0, ',', ' '); ?>
                        <?php endif; ?>
                    </td>
                <?php endfor; ?>
            </tr>

            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_CRYSTAL']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <?php if ($user_building[$i]['C'] != "") : ?>
                            <?php echo number_format(floor($user_production[$i]['prod_reel']['C']), 0, ',', ' '); ?>
                        <?php endif; ?>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_DEUT']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <?php if ($user_building[$i]['D'] != "") : ?>
                            <?php echo number_format(floor($user_production[$i]['prod_reel']['D']), 0, ',', ' '); ?>
                        <?php endif; ?>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_BOOSTER']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <?php $booster_tab = booster_decode($user_building[$i]["boosters"]); ?>
                        m:<?php echo $booster_tab['booster_m_val']; ?>%, c:<?php echo $booster_tab['booster_c_val']; ?>, d:<?php echo $booster_tab['booster_d_val']; ?>, e:<?php echo $booster_tab['booster_e_val']; ?>%

                    </td>
                <?php endfor; ?>
            </tr>
        </tbody>
        <thead>
            <thead>
                <tr>
                    <th colspan="<?php print ($nb_planete < 10) ? '10' : $nb_planete + 1 ?>">
                        <?php echo ($lang['HOME_EMPIRE_BUILDINGS']); ?>
                    </th>
                </tr>
            </thead>
        <tbody>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_MINE_METAL']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <?php $M = ($user_building[$i]["M"] == "") ? "&nbsp;" : $user_building[$i]["M"]; ?>
                        <span id='15<?php echo ($i + 1 - $start) ?>'>
                            <?php echo $M ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_MINE_CRYSTAL']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <?php $C = ($user_building[$i]["C"] == "") ? "&nbsp;" : $user_building[$i]["C"]; ?>
                        <span id='16<?php echo ($i + 1 - $start) ?>'>
                            <?php echo $C ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_MINE_DEUT']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <?php $D = ($user_building[$i]["D"] == "") ? "&nbsp;" : $user_building[$i]["D"]; ?>
                        <span id='17<?php echo ($i + 1 - $start) ?>'>
                            <?php echo $D ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_SOLAR_PLANT']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <?php $CES = ($user_building[$i]["CES"] == "") ? "&nbsp;" : $user_building[$i]["CES"]; ?>
                        <span id='20<?php echo ($i + 1 - $start) ?>'>
                            <?php echo $CES ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_FUSION_PLANT']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <?php $CEF = ($user_building[$i]["CEF"] == "") ? "&nbsp;" : $user_building[$i]["CEF"]; ?>
                        <span id='21<?php echo ($i + 1 - $start) ?>'>
                            <?php echo $CEF ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>
        </tbody>
    <?php else :  // autre que  view = planets ;
    ?>
        <thead>
            <thead>
                <tr>
                    <th colspan="<?php print ($nb_planete < 10) ? '10' : $nb_planete + 1 ?>">
                        <?php echo ($lang['HOME_EMPIRE_BUILDINGS']); ?>
                    </th>
                </tr>
            </thead>
        </thead>

    <?php endif; // FIN  view = planets ;
    ?>
    <tboby>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_EMPIRE_ROBOTS_PLANT']); ?>
            </td>
            <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                <td class="tdcontent">
                    <?php $UdR = ($user_building[$i]["UdR"] == "") ? "&nbsp;" : $user_building[$i]["UdR"]; ?>
                    <span id='1<?php echo ($i + 1 - $start) ?>'>
                        <?php echo $UdR ?>
                    </span>
                </td>
            <?php endfor; ?>
        </tr>
        <?php if ($view == "planets") :  ?>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_NANITES_PLANT']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <?php $UdN = ($user_building[$i]["UdN"] == "") ? "&nbsp;" : $user_building[$i]["UdN"]; ?>
                        <span id='22<?php echo ($i + 1 - $start) ?>'>
                            <?php echo $UdN ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>
        <?php endif; ?>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_EMPIRE_SHIPYARD']); ?>
            </td>
            <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                <td class="tdcontent">
                    <?php $CSp = ($user_building[$i]["CSp"] == "") ? "&nbsp;" : $user_building[$i]["CSp"]; ?>
                    <span id='2<?php echo ($i + 1 - $start) ?>'>
                        <?php echo $CSp ?>
                    </span>
                </td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_EMPIRE_METALSTORAGE']); ?>
            </td>
            <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                <td class="tdcontent">
                    <?php $HM = ($user_building[$i]["HM"] == "") ? "&nbsp;" : $user_building[$i]["HM"]; ?>
                    <span id='3<?php echo ($i + 1 - $start) ?>'>
                        <?php echo $HM ?>
                    </span>
                </td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_EMPIRE_CRYSTALSTORAGE']); ?>
            </td>
            <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                <td class="tdcontent">
                    <?php $HC = ($user_building[$i]["HC"] == "") ? "&nbsp;" : $user_building[$i]["HC"]; ?>
                    <span id='4<?php echo ($i + 1 - $start) ?>'>
                        <?php echo $HC ?>
                    </span>
                </td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_EMPIRE_DEUTSTORAGE']); ?>
            </td>
            <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                <td class="tdcontent">
                    <?php $HD = ($user_building[$i]["HD"] == "") ? "&nbsp;" : $user_building[$i]["HD"]; ?>
                    <span id='5<?php echo ($i + 1 - $start) ?>'>
                        <?php echo $HD ?>
                    </span>
                </td>
            <?php endfor; ?>
        </tr>
        <?php if ($view == "planets") :  ?>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_RESEARCHLAB']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <?php $Lab = ($user_building[$i]["Lab"] == "") ? "&nbsp;" : $user_building[$i]["Lab"]; ?>
                        <span id='23<?php echo ($i + 1 - $start) ?>'>
                            <?php echo $Lab ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>

            <?php if ($server_config['ddr'] == 1) :  ?>
                <tr>
                    <td class="tdname">
                        <?php echo ($lang['HOME_EMPIRE_ALLIANCEDEPOT']); ?>
                    </td>
                    <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                        <td class="tdcontent">
                            <?php $DdR = ($user_building[$i]["DdR"] == "") ? "&nbsp;" : $user_building[$i]["DdR"]; ?>
                            <span id='42<?php echo ($i + 1 - $start) ?>'>
                                <?php echo $DdR ?>
                            </span>
                        </td>
                    <?php endfor; ?>
                </tr>
            <?php endif; ?>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_TERRAFORMER']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <?php $Ter = ($user_building[$i]["Ter"] == "") ? "&nbsp;" : $user_building[$i]["Ter"]; ?>
                        <span id='24<?php echo ($i + 1 - $start) ?>'>
                            <?php echo $Ter ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_MISSILESSILO']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <?php $Silo = ($user_building[$i]["Silo"] == "") ? "&nbsp;" : $user_building[$i]["Silo"]; ?>
                        <span id='25<?php echo ($i + 1 - $start) ?>'>
                            <?php echo $Silo ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_DOCK']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <?php $Dock = ($user_building[$i]["Dock"] == "") ? "&nbsp;" : $user_building[$i]["Dock"]; ?>
                        <!-- todo ID ??? -->
                        <span id='25<?php echo ($i + 1 - $start) ?>'>
                            <?php echo $Dock ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>
        <?php else : ?>
            <?php echo "<!--autre que view == \"planets\" -->" ?>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_LUNARSTATION']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <?php $BaLu = ($user_building[$i]["BaLu"] == "") ? "&nbsp;" : $user_building[$i]["BaLu"]; ?>
                        <span id='15<?php echo ($i + 1 - $start) ?>'>
                            <?php echo $BaLu ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_LUNARPHALANX']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <?php $Pha = ($user_building[$i]["Pha"] == "") ? "&nbsp;" : $user_building[$i]["Pha"]; ?>
                        <span id='16<?php echo ($i + 1 - $start) ?>'>
                            <?php echo $Pha ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_LUNARJUMPGATE']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <?php $PoSa = ($user_building[$i]["PoSa"] == "") ? "&nbsp;" : $user_building[$i]["PoSa"]; ?>
                        <span id='17<?php echo ($i + 1 - $start) ?>'>
                            <?php echo $PoSa ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>
            <?php echo "<!--fin de sinon view == \"planets\" -->" ?>
        <?php endif; ?>
    </tboby>

    <thead>
        <tr>
            <th colspan="<?php print ($nb_planete < 10) ? '10' : $nb_planete + 1 ?>">
                <?php echo ($lang['HOME_EMPIRE_OTHERS']); ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_EMPIRE_SATELLITES']); ?>
            </td>
            <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                <td class="tdcontent">
                    <?php $Sat = ($user_building[$i]["Sat"] == "") ? "&nbsp;" : $user_building[$i]["Sat"]; ?>
                    <span id='6<?php echo ($i + 1 - $start) ?>'>
                        <?php echo $Sat ?>
                    </span>
                </td>
            <?php endfor; ?>
        </tr>
        <?php if ($view == "planets") :  ?>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_CRAWLER']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <?php $For = ($user_building[$i]["FOR"] == "") ? "&nbsp;" : number_format($user_building[$i]["FOR"], 0, ',', ' '); ?>
                        <?php $class_collect = ($player_data['user_class'] === 'COL') ? '1' : '0'; ?>
                        <?php $nb_max = foreuse_max($user_building[$i]['M'], $user_building[$i]['C'], $user_building[$i]['D'], $user_data['off_geologue'], $class_collect); ?>
                        <span id=43<?php echo ($i + 1 - $start); ?>>
                            <?php echo $For . " / " . $nb_max; ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>


        <?php endif; ?>
    </tbody>
    <?php if ($view == "planets") : ?>
        <thead>
            <tr>
                <th colspan="<?php print ($nb_planete < 10) ? '10' : $nb_planete + 1 ?>">
                    <?php echo ($lang['HOME_EMPIRE_TECHNOS']); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_TECHNOS_SPY']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <span id='26<?php echo (($i + 1 - $start)); ?>'>
                            <?php if (prerequis_Valid("Esp",  $user_building[$i], $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["Esp"] != "") ? $user_technology["Esp"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_TECHNOS_COMPUTER']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <span id='27<?php echo (($i + 1 - $start)); ?>'>
                            <?php if (prerequis_Valid("Ordi",  $user_building[$i], $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["Ordi"] != "") ? $user_technology["Ordi"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_TECHNOS_WEAPONS']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <span id='28<?php echo (($i + 1 - $start)); ?>'>
                            <?php if (prerequis_Valid("Armes",  $user_building[$i], $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["Armes"] != "") ? $user_technology["Armes"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_TECHNOS_SHIELD']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <span id='29<?php echo (($i + 1 - $start)); ?>'>
                            <?php if (prerequis_Valid("Bouclier",  $user_building[$i], $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["Bouclier"] != "") ? $user_technology["Bouclier"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_TECHNOS_PROTECTION']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <span id='30<?php echo (($i + 1 - $start)); ?>'>
                            <?php if (prerequis_Valid("Protection",  $user_building[$i], $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["Protection"] != "") ? $user_technology["Protection"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="tdname">
                    <?= $lang['HOME_EMPIRE_TECHNOS_ENERGY'] ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <span id='31<?php echo (($i + 1 - $start)); ?>'>
                            <?php if (prerequis_Valid("NRJ",  $user_building[$i], $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["NRJ"] != "") ? $user_technology["NRJ"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_TECHNOS_HYPERSPACE']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <span id='32<?php echo (($i + 1 - $start)); ?>'>
                            <?php if (prerequis_Valid("Hyp",  $user_building[$i], $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["Hyp"] != "") ? $user_technology["Hyp"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_TECHNOS_COMBUSTION_DRIVE']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <span id='33<?php echo (($i + 1 - $start)); ?>'>
                            <?php if (prerequis_Valid("RC",  $user_building[$i], $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["RC"] != "") ? $user_technology["RC"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_TECHNOS_IMPULSE_DRIVE']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <span id='34<?php echo (($i + 1 - $start)); ?>'>
                            <?php if (prerequis_Valid("RI",  $user_building[$i], $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["RI"] != "") ? $user_technology["RI"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_TECHNOS_HYPER_DRIVE']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <span id='35<?php echo (($i + 1 - $start)); ?>'>
                            <?php if (prerequis_Valid("PH",  $user_building[$i], $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["PH"] != "") ? $user_technology["PH"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_TECHNOS_LASER']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <span id='36<?php echo (($i + 1 - $start)); ?>'>
                            <?php if (prerequis_Valid("Laser",  $user_building[$i], $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["Laser"] != "") ? $user_technology["Laser"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_TECHNOS_IONS']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <span id='37<?php echo (($i + 1 - $start)); ?>'>
                            <?php if (prerequis_Valid("Ions",  $user_building[$i], $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["Ions"] != "") ? $user_technology["Ions"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_TECHNOS_PLASMA']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <span id='38<?php echo (($i + 1 - $start)); ?>'>
                            <?php if (prerequis_Valid("Plasma",  $user_building[$i], $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["Plasma"] != "") ? $user_technology["Plasma"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_TECHNOS_RESEARCH_NETWORK']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <span id='39<?php echo (($i + 1 - $start)); ?>'>
                            <?php if (prerequis_Valid("RRI",  $user_building[$i], $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["RRI"] != "") ? $user_technology["RRI"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_TECHNOS_ASTRO']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <span id='41<?php echo (($i + 1 - $start)); ?>'>
                            <?php if (prerequis_Valid("Astrophysique",  $user_building[$i], $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["Astrophysique"] != "") ? $user_technology["Astrophysique"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>

            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_TECHNOS_GRAVITY']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <span id='40<?php echo (($i + 1 - $start)); ?>'>
                            <?php if (prerequis_Valid("Graviton",  $user_building[$i], $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["Graviton"] != "") ? $user_technology["Graviton"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>
        </tbody>
    <?php endif; ?>

    <thead>
        <tr>
            <th colspan="<?php print ($nb_planete < 10) ? '10' : $nb_planete + 1 ?>">
                <?php echo ($lang['HOME_EMPIRE_WEAPONS_TITLE']); ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_EMPIRE_WEAPONS_MISSILES']); ?>
            </td>
            <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                <td class="tdcontent">
                    <?php $LM = ($user_defence[$i]["LM"] == "") ? "0" : $user_defence[$i]["LM"]; ?>
                    <span id='7<?php echo ($i + 1 - $start) ?>'>
                        <?php echo number_format($LM, 0, ',', ' '); ?>
                    </span>
                </td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_EMPIRE_WEAPONS_LLASERS']); ?>
            </td>
            <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                <td class="tdcontent">
                    <?php $LLE = ($user_defence[$i]["LLE"] == "") ? "0" : $user_defence[$i]["LLE"]; ?>
                    <span id='8<?php echo ($i + 1 - $start) ?>'>
                        <?php echo number_format($LLE, 0, ',', ' '); ?>
                    </span>
                </td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_EMPIRE_WEAPONS_HLASERS']); ?>
            </td>
            <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                <td class="tdcontent">
                    <?php $LLO = ($user_defence[$i]["LLO"] == "") ? "0" : $user_defence[$i]["LLO"]; ?>
                    <span id='9<?php echo ($i + 1 - $start) ?>'>
                        <?php echo number_format($LLO, 0, ',', ' '); ?>
                    </span>
                </td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_EMPIRE_WEAPONS_GAUSS']); ?>
            </td>
            <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                <td class="tdcontent">
                    <?php $CG = ($user_defence[$i]["CG"] == "") ? "0" : $user_defence[$i]["CG"]; ?>
                    <span id='10<?php echo ($i + 1 - $start) ?>'>
                        <?php echo number_format($CG, 0, ',', ' '); ?>
                    </span>
                </td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_EMPIRE_WEAPONS_IONS']); ?>
            </td>
            <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                <td class="tdcontent">
                    <?php $AI = ($user_defence[$i]["AI"] == "") ? "0" : $user_defence[$i]["AI"]; ?>
                    <span id='11<?php echo ($i + 1 - $start) ?>'>
                        <?php echo number_format($AI, 0, ',', ' '); ?>
                    </span>
                </td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_EMPIRE_WEAPONS_PLASMA']); ?>
            </td>
            <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                <td class="tdcontent">
                    <?php $LP = ($user_defence[$i]["LP"] == "") ? "0" : $user_defence[$i]["LP"]; ?>
                    <span id='12<?php echo ($i + 1 - $start) ?>'>
                        <?php echo number_format($LP, 0, ',', ' '); ?>
                    </span>
                </td>
            <?php endfor; ?>
        </tr>

        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_EMPIRE_WEAPONS_SMALLSHIELD']); ?>
            </td>
            <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                <td class="tdcontent">
                    <?php $PB = ($user_defence[$i]["PB"] == "") ? "0" : $user_defence[$i]["PB"]; ?>
                    <span id='13<?php echo ($i + 1 - $start) ?>'>
                        <?php echo number_format($PB, 0, ',', ' '); ?>
                    </span>
                </td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_EMPIRE_WEAPONS_LARGESHIELD']); ?>
            </td>
            <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                <td class="tdcontent">
                    <?php $GB = ($user_defence[$i]["GB"] == "") ? "0" : $user_defence[$i]["GB"]; ?>
                    <span id='14<?php echo ($i + 1 - $start) ?>'>
                        <?php echo number_format($GB, 0, ',', ' '); ?>
                    </span>
                </td>
            <?php endfor; ?>
        </tr>
        <?php if ($view == "planets") : ?>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_WEAPONS_ANTI']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <?php $MIC = ($user_defence[$i]["MIC"] == "") ? "0" : $user_defence[$i]["MIC"]; ?>
                        <span id='19<?php echo ($i + 1 - $start) ?>'>
                            <?php echo number_format($MIC, 0, ',', ' '); ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td class="tdname">
                    <?php echo ($lang['HOME_EMPIRE_WEAPONS_INTER']); ?>
                </td>
                <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                    <td class="tdcontent">
                        <?php $MIP = ($user_defence[$i]["MIP"] == "") ? "0" : $user_defence[$i]["MIP"]; ?>
                        <span id='18<?php echo ($i + 1 - $start) ?>'>
                            <?php echo number_format($MIP, 0, ',', ' '); ?>
                        </span>
                    </td>
                <?php endfor; ?>
            </tr>
        <?php endif; ?>

        <thead>
            <tr>
                <th colspan="<?php print ($nb_planete < 10) ? '10' : $nb_planete + 1 ?>">
                    <?php echo ($lang['HOME_EMPIRE_POINTS_TITLE']); ?>
                </th>
            </tr>
        </thead>
    <tbody>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_EMPIRE_BUILDINGS']); ?>
            </td>
            <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                <td class="tdcontent">
                    <?php $point = all_building_cumulate(array(1 => $user_building[$i])); ?>
                    <?php $point = round($point / 1000); ?>

                    <span id='19<?php echo ($i + 1 - $start) ?>'>
                        <?php echo number_format($point, 0, ',', ' '); ?>
                    </span>
                </td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_EMPIRE_WEAPONS_TITLE']); ?>
            </td>
            <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                <td class="tdcontent">
                    <?php $point = all_defence_cumulate(array(1 => $user_defence[$i])); ?>
                    <?php $point = round($point / 1000); ?>
                    <span id='20<?php echo ($i + 1 - $start) ?>'>
                        <?php echo number_format($point, 0, ',', ' '); ?>
                    </span>
                </td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_EMPIRE_FLEET_TITLE']); ?>
            </td>
            <!-- // Pour le moment seulement les FOR et les SAT !! -->
            <?php for ($i = $start; $i <= $start + $nb_planete - 1; $i++) : ?>
                <td class="tdcontent">
                    <?php $point = all_fleet_cumulate(array(1 => $user_building[$i])); //FOR et Sat
                    ?>
                    <?php $point = round($point / 1000); ?>
                    <span id='20<?php echo ($i + 1 - $start) ?>'>
                        <?php echo number_format($point, 0, ',', ' '); ?>
                    </span>
                </td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_EMPIRE_TECHNOS']); ?>
            </td>
            <td colspan="<?php print ($nb_planete < 10) ? '9' : $nb_planete ?>" class="tdcontent">
                <span id='21'>
                    <?php $point = all_technology_cumulate($user_technology); ?>
                    <?php $point = round($point / 1000); ?>
                    <?php echo number_format($point, 0, ',', ' '); ?>
                </span>
            </td>
        </tr>
    </tbody>
</table>





<?php
/**
 * @param $txt
 * @param $nb_planete
 * @return string
 */
function read_th($txt, $nb_planete)
{
    $retour = "";
    if ($nb_planete > 9) {
        for ($i = 10; $i <= $nb_planete; $i++) {
            $retour = $retour . $txt;
        }
    }
    return $retour;
}


/**
 * Indique si les prerequis sont valides
 *
 * @param $ogame_element_name nom de l'element recherché
 * @param $user_building_list liste des buildings situé sur le meme astre que l'element recherché
 * @param $user_technology_list liste des technologies
 *
 *  */
function prerequis_Valid($ogame_element_name, $user_building_list, $user_technology_list)
{
    // recuperation des prerequis pour l element indiqué
    $reqs = ogame_elements_requirement($ogame_element_name);

    foreach ($reqs as $reqName => $reqValue) {
        //prerequis recherche
        if (ogame_is_a_research($reqName) && $reqValue > 0) {
            if ($reqValue > $user_technology_list[$reqName]) {
                //prerequis non ok
                return false;
            }
        }
        // prerequis bat
        if (ogame_is_a_building($reqName) && $reqValue > 0) {
            if ($reqValue > $user_building_list[$reqName]) {
                //prerequis non ok
                return false;
            }
        }
    }
    // tout autre cas, les prerequis sont bons
    return true;
}
