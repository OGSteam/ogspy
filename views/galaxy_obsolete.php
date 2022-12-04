<?php
/**
 * Affichage Galaxie Obsolètes
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyser
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

$obsolete_listing = galaxy_obsolete();
if (!isset($pub_since)) {
    $pub_since = 0;
}
if (!isset($pub_perimeter)) {
    $pub_perimeter = 1;
}
if (!isset($pub_typesearch)) {
    $pub_typesearch = "P";
}

$since = $pub_since;
$perimeter = $pub_perimeter;
$typesearch = $pub_typesearch;


require_once("views/page_header.php");
?>

    <form method="POST" action="index.php?action=galaxy_obsolete">
        <table border="1">
            <tr>
                <td class="c" colspan="2"><?php echo($lang['GALAXY_OLD_TITLE']); ?></td>
            </tr>
            <tr>
                <th colspan="2">
                    <select name="perimeter">
                        <?php
                        for ($i = 1; $i <= intval($server_config['num_of_galaxies']); $i++) {
                                                    print "<option value=\"$i\"" . ($perimeter == $i ? ' selected' : '') . ">Galaxie $i</option>";
                        }
                        ?>
                        <option value="0" <?php if ($perimeter == 0) {
    echo "selected";
}
?>><?php echo($lang['GALAXY_OLD_ALL']); ?></option>
                    </select>
                    &nbsp;&nbsp;
                    <?php echo($lang['GALAXY_OLD_SINCE']); ?>
                    &nbsp;&nbsp;
                    <select name="since">
                        <option value="56" <?php if ($since == 56) {
    echo "selected";
}
?>>8 <?php echo($lang['GALAXY_OLD_WEEKS']); ?></option>
                        <option value="42" <?php if ($since == 42) {
    echo "selected";
}
?>>6 <?php echo($lang['GALAXY_OLD_WEEKS']); ?></option>
                        <option value="28" <?php if ($since == 28) {
    echo "selected";
}
?>>4 <?php echo($lang['GALAXY_OLD_WEEKS']); ?></option>
                        <option value="21" <?php if ($since == 21) {
    echo "selected";
}
?>>3 <?php echo($lang['GALAXY_OLD_WEEKS']); ?></option>
                        <option value="14" <?php if ($since == 14) {
    echo "selected";
}
?>>2 <?php echo($lang['GALAXY_OLD_WEEKS']); ?></option>
                        <option value="7" <?php if ($since == 7) {
    echo "selected";
}
?>>1 <?php echo($lang['GALAXY_OLD_WEEKS']); ?></option>
                    </select>
                    &nbsp;&nbsp;
                </th>
            </tr>
            <tr>
                <th width="50%"><input type="radio" name="typesearch"
                                       value="P" <?php if ($typesearch == "P") {
    echo "checked";
}
?>> <?php echo($lang['GALAXY_OLD_DISPLAYPLANETS']); ?>
                </th>
                <th width="50%"><input type="radio" name="typesearch"
                                       value="M" <?php if ($typesearch == "M") {
    echo "checked";
}
?>> <?php echo($lang['GALAXY_OLD_DISPLAYMOONS']); ?>
                </th>
            </tr>
            <tr>
                <th colspan="2"><input type="submit" value="<?php echo($lang['GALAXY_OLD_SEARCH']); ?>"></th>
            </tr>
        </table>
    </form>
    <br><br>
<?php
if ($since >= 56) {
    ?>
    <table>
        <tr>
            <td class="c" colspan="8"><?php echo($lang['GALAXY_OLD_OLDERTHAN_8WEEKS']); ?></td>
        </tr>
        <tr>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_SOLAR_SYSTEM']); ?></td>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_LAST_UPDATE']); ?></td>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_SOLAR_SYSTEM']); ?></td>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_LAST_UPDATE']); ?></td>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_SOLAR_SYSTEM']); ?></td>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_LAST_UPDATE']); ?></td>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_SOLAR_SYSTEM']); ?></td>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_LAST_UPDATE']); ?></td>
        </tr>
        <?php
        if (isset($obsolete_listing[56])) {
            $obsolete = $obsolete_listing[56];
            $i = $index = 0;
            foreach ($obsolete as $value) {
                $index++;
                if ($i == 4) {
                    $i = 0;
                    echo "</tr>" . "\n";
                    echo "<tr>";
                }
                if ($value["last_update"] != 0) {
                    $date =  date("d F o G:i", $value["last_update"]);
                } else {
                    $date = "-";
                }
                $color = $i & 1 ? "magenta" : "lime";

                $coordinates = $value["galaxy"] . ":" . $value["system"];
                if ($typesearch == "M") {
                    $coordinates .= ":" . $value["row"];
                }
                echo "<th><font color='" . $color . "'>" . $coordinates . "</font></th><th><font color='" . $color . "'>" . $date . "</font></th>";
                $i++;

                if ($index == 50) {
                    echo "<th colspan='4'><span style=\"color: orange; \"><i>" . $lang['GALAXY_OLD_LIMITED_50SYSTEMS'] . "</i></span></th>";
                    $i = 4;
                    break;
                }
            }
            for ($i; $i < 4; $i++) {
                echo "<th>&nbsp;</th><th>&nbsp;</th>";
            }
        }
        ?>
    </table>
    <br>
    <?php
}

if ($since >= 42) {
    ?>
    <table>
        <tr>
            <td class="c" colspan="8"><?php echo($lang['GALAXY_OLD_OLDERTHAN_6WEEKS']); ?></td>
        </tr>
        <tr>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_SOLAR_SYSTEM']); ?></td>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_LAST_UPDATE']); ?></td>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_SOLAR_SYSTEM']); ?></td>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_LAST_UPDATE']); ?></td>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_SOLAR_SYSTEM']); ?></td>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_LAST_UPDATE']); ?></td>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_SOLAR_SYSTEM']); ?></td>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_LAST_UPDATE']); ?></td>
        </tr>
        <?php
        if (isset($obsolete_listing[42])) {
            $obsolete = $obsolete_listing[42];
            $i = $index = 0;
            foreach ($obsolete as $value) {
                $index++;
                if ($i == 4) {
                    $i = 0;
                    echo "</tr>" . "\n";
                    echo "<tr>";
                }
                $date =  date("d F o G:i", $value["last_update"]);
                $color = $i & 1 ? "magenta" : "lime";

                $coordinates = $value["galaxy"] . ":" . $value["system"];
                if ($typesearch == "M") {
                    $coordinates .= ":" . $value["row"];
                }
                echo "<th><span style=\"color:" . $color . "\">" . $coordinates . "</font></th><th><span style=\"color:" . $color . "\">" . $date . "</font></th>";
                $i++;

                if ($index == 50) {
                    echo "<th colspan='4'><span style=\"color: orange; \"><i>" . $lang['GALAXY_OLD_LIMITED_50SYSTEMS'] . "</i></span></th>";
                    $i = 4;
                    break;
                }
            }
            for ($i; $i < 4; $i++) {
                echo "<th>&nbsp;</th><th>&nbsp;</th>";
            }
        }
        ?>
    </table>
    <br>
    <?php
}

if ($since >= 28) {
    ?>
    <table>
        <tr>
            <td class="c" colspan="8"><?php echo($lang['GALAXY_OLD_OLDERTHAN_4WEEKS']); ?></td>
        </tr>
        <tr>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_SOLAR_SYSTEM']); ?></td>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_LAST_UPDATE']); ?></td>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_SOLAR_SYSTEM']); ?></td>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_LAST_UPDATE']); ?></td>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_SOLAR_SYSTEM']); ?></td>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_LAST_UPDATE']); ?></td>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_SOLAR_SYSTEM']); ?></td>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_LAST_UPDATE']); ?></td>
        </tr>
        <?php
        if (isset($obsolete_listing[28])) {
            $obsolete = $obsolete_listing[28];
            $i = $index = 0;
            foreach ($obsolete as $value) {
                $index++;
                if ($i == 4) {
                    $i = 0;
                    echo "</tr>" . "\n";
                    echo "<tr>";
                }
                $date =  date("d F o G:i", $value["last_update"]);
                $color = $i & 1 ? "magenta" : "lime";

                $coordinates = $value["galaxy"] . ":" . $value["system"];
                if ($typesearch == "M") {
                    $coordinates .= ":" . $value["row"];
                }
                echo "<th><span style=\"color:" . $color . "\">" . $coordinates . "</font></th><th><span style=\"color:" . $color . "\">" . $date . "</font></th>";
                $i++;

                if ($index == 50) {
                    echo "<th colspan='4'><span style=\"color: orange; \"><i>" . $lang['GALAXY_OLD_LIMITED_50SYSTEMS'] . "</i></span></th>";
                    $i = 4;
                    break;
                }
            }
            for ($i; $i < 4; $i++) {
                echo "<th>&nbsp;</th><th>&nbsp;</th>";
            }
        }
        ?>
    </table>
    <br>
    <?php
}

if ($since >= 21) {
    ?>
    <table>
        <tr>
            <td class="c" colspan="8"><?php echo($lang['GALAXY_OLD_OLDERTHAN_3WEEKS']); ?></td>
        </tr>
        <tr>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_SOLAR_SYSTEM']); ?></td>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_LAST_UPDATE']); ?></td>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_SOLAR_SYSTEM']); ?></td>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_LAST_UPDATE']); ?></td>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_SOLAR_SYSTEM']); ?></td>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_LAST_UPDATE']); ?></td>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_SOLAR_SYSTEM']); ?></td>
            <td class="c" width="110"><?php echo($lang['GALAXY_OLD_LAST_UPDATE']); ?></td>
        </tr>
        <?php
        if (isset($obsolete_listing[21])) {
            $obsolete = $obsolete_listing[21];
            $i = $index = 0;
            foreach ($obsolete as $value) {
                $index++;
                if ($i == 4) {
                    $i = 0;
                    echo "</tr>" . "\n";
                    echo "<tr>";
                }
                $date =  date("d F o G:i", $value["last_update"]);
                $color = $i & 1 ? "magenta" : "lime";

                $coordinates = $value["galaxy"] . ":" . $value["system"];
                if ($typesearch == "M") {
                    $coordinates .= ":" . $value["row"];
                }
                echo "<th><span style=\"color:" . $color . "\">" . $coordinates . "</span></th><th><span style=\"color:" . $color . "\">" . $date . "</span></th>";
                $i++;

                if ($index == 50) {
                    echo "<th colspan='4'><span style=\"color: orange; \"><i>" . $lang['GALAXY_OLD_LIMITED_50SYSTEMS'] . "</i></span></th>";
                    $i = 4;
                    break;
                }
            }
            for ($i; $i < 4; $i++) {
                echo "<th>&nbsp;</th><th>&nbsp;</th>";
            }
        }
        ?>
    </table>
    <br>
    <?php
}

if ($since >= 14) {
    ?>
    <table>
        <tr>
            <td class="c" colspan="8"><?php echo $lang['GALAXY_OLD_OLDERTHAN_2WEEKS']; ?></td>
        </tr>
        <tr>
            <td class="c" width="110"><?php echo $lang['GALAXY_OLD_SOLAR_SYSTEM']; ?></td>
            <td class="c" width="110"><?php echo $lang['GALAXY_OLD_LAST_UPDATE']; ?></td>
            <td class="c" width="110"><?php echo $lang['GALAXY_OLD_SOLAR_SYSTEM']; ?></td>
            <td class="c" width="110"><?php echo $lang['GALAXY_OLD_LAST_UPDATE']; ?></td>
            <td class="c" width="110"><?php echo $lang['GALAXY_OLD_SOLAR_SYSTEM']; ?></td>
            <td class="c" width="110"><?php echo $lang['GALAXY_OLD_LAST_UPDATE']; ?></td>
            <td class="c" width="110"><?php echo $lang['GALAXY_OLD_SOLAR_SYSTEM']; ?></td>
            <td class="c" width="110"><?php echo $lang['GALAXY_OLD_LAST_UPDATE']; ?></td>
        </tr>
        <?php
        if (isset($obsolete_listing[14])) {
            $obsolete = $obsolete_listing[14];
            $i = $index = 0;
            foreach ($obsolete as $value) {
                $index++;
                if ($i == 4) {
                    $i = 0;
                    echo "</tr>" . "\n";
                    echo "<tr>";
                }
                $date =  date("d F o G:i", $value["last_update"]);
                $color = $i & 1 ? "magenta" : "lime";

                $coordinates = $value["galaxy"] . ":" . $value["system"];
                if ($typesearch == "M") {
                    $coordinates .= ":" . $value["row"];
                }
                echo "<th><span style=\"color:" . $color . "\">" . $coordinates . "</font></th><th><span style=\"color:" . $color . "\">" . $date . "</font></th>";
                $i++;

                if ($index == 50) {
                    echo "<th colspan='4'><span style=\"color: orange; \"><i>" . $lang['GALAXY_OLD_LIMITED_50SYSTEMS'] . "</i></span></th>";
                    $i = 4;
                    break;
                }
            }
            for ($i; $i < 4; $i++) {
                echo "<th>&nbsp;</th><th>&nbsp;</th>";
            }
        }
        ?>
    </table>
    <br>
    <?php
}

if ($since >= 7) {
    ?>
    <table>
        <th>
            <td class="c" colspan="8"><?php echo $lang['GALAXY_OLD_OLDERTHAN_1WEEKS']; ?></td>
        </th>
        <tr>
            <td class="c" width="110"><?php echo $lang['GALAXY_OLD_SOLAR_SYSTEM']; ?></td>
            <td class="c" width="110"><?php echo $lang['GALAXY_OLD_LAST_UPDATE']; ?></td>
            <td class="c" width="110"><?php echo $lang['GALAXY_OLD_SOLAR_SYSTE']; ?></td>
            <td class="c" width="110"><?php echo $lang['GALAXY_OLD_LAST_UPDATE']; ?></td>
            <td class="c" width="110"><?php echo $lang['GALAXY_OLD_SOLAR_SYSTEM']; ?></td>
            <td class="c" width="110"><?php echo $lang['GALAXY_OLD_LAST_UPDATE']; ?></td>
            <td class="c" width="110"><?php echo $lang['GALAXY_OLD_SOLAR_SYSTEM']; ?></td>
            <td class="c" width="110"><?php echo $lang['GALAXY_OLD_LAST_UPDATE']; ?></td>
        </tr>
        <?php
        if (isset($obsolete_listing[7])) {
            $obsolete = $obsolete_listing[7];
            $i = $index = 0;
            foreach ($obsolete as $value) {
                $index++;
                if ($i == 4) {
                    $i = 0;
                    echo "</tr>" . "\n";
                    echo "<tr>";
                }
                $date =  date("d F o G:i", $value["last_update"]);
                $color = $i & 1 ? "magenta" : "lime";

                $coordinates = $value["galaxy"] . ":" . $value["system"];
                if ($typesearch == "M") {
                    $coordinates .= ":" . $value["row"];
                }
                echo "<th><span style=\"color:" . $color . "\">" . $coordinates . "</font></th><th><span style=\"color:" . $color . "\">" . $date . "</font></th>";
                $i++;

                if ($index == 50) {
                    echo "<th colspan='4'><span style=\"color: orange; \"><i>" . $lang['GALAXY_OLD_LIMITED_50SYSTEMS'] . "</i></span></th>";
                    $i = 4;
                    break;
                }
            }
            for ($i; $i < 4; $i++) {
                echo "<th>&nbsp;</th><th>&nbsp;</th>";
            }
        }
        ?>
    </table>
    <?php
}
?>
<?php
require_once("views/page_tail.php");
?>