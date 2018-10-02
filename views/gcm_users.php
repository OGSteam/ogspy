<?php
/**
 * Admin Affichage Liste utilisateurs GCM
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Jedinight
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

?>
<!-- script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script-->
<script type="text/javascript">
    //$(document).ready(function(){ });

    function sendPushNotification(id) {
        //alert("sendPushNotification(" + id + ") : " + document.location.href);
        var url = document.location.href.split("/index.php")[0];
        url += "/gcm/send_message.php";

        var regId = $('#regId' + id).attr("value");
        url += "?regId=" + regId;
        var message = $('#message' + id).attr("value");
        url += "&message=" + message;

        //window.open(url);
        $.ajax(url, {
            type: 'GET',
            success: function (data, textStatus, xhr) {
                $('#message' + id).val("Notification envoyée");
            },
            error: function (xhr, textStatus, errorThrown) {
                $('#message' + id).val("Notification échouée !!!");
            }
        });
        return true;
    }

</script>
<style type="text/css">
    .container {
        width: 80%;
        margin: 0 auto;
        padding: 0;
    }

    h1 {
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        text-align: left;
        font-size: 24px;
        color: white;
    }

    div.clear {
        clear: both;
    }

    ul.devices {
        margin: 0;
        padding: 0;
    }

    ul.devices li {
        float: left;
        list-style: none;
        border: 1px solid #dedede;
        padding: 10px;
        margin: 0 15px 25px 0;
        border-radius: 3px;
        -webkit-box-shadow: 0 1px 5px rgba(0, 0, 0, 0.35);
        -moz-box-shadow: 0 1px 5px rgba(0, 0, 0, 0.35);
        box-shadow: 0 1px 5px rgba(0, 0, 0, 0.35);
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        color: white;
    }

    ul.devices li label, ul.devices li span {
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        font-size: 12px;
        font-style: normal;
        font-variant: normal;
        font-weight: bold;
        color: white;
        display: block;
        float: left;
    }

    ul.devices li label {
        height: 25px;
        width: 100px;
    }

    ul.devices li {
        width: 300px;
    }

    ul.devices li textarea {
        float: left;
        resize: none;
    }

    ul.devices li .send_btn {
        background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#FFC775),
        to(#005DFF));
        background: -webkit-linear-gradient(0% 0%, 0% 100%, from(#FFC775), to(#005DFF));
        background: -moz-linear-gradient(center top, #0096FF, #FF9900);
        background: linear-gradient(#FFC775, #FF9900);
        text-shadow: 0 1px 0 rgba(0, 0, 0, 0.3);
        border-radius: 3px;
        color: #fff;
    }
</style>

<?php
global $db;

//require_once("../includes/functions.php");

$users = getAllGCMUsers();
if ($users != false) {
    $no_of_users = $db->sql_numrows($users);
} else {
    $no_of_users = 0;
}
?>
<div class="container">
    <h1>Nombre d'utilisateurs enregistrés : <?php echo $no_of_users; ?></h1>
    <hr/>
    <ul class="devices">
        <?php
        $i = 0;
        if ($no_of_users > 0) {
            while ($row = $db->sql_fetch_row($users)) {
                $i++;
                $userId = $row[0];
                $name = $row[1];
                $pseudo = $row[2];
                $email = $row[3];
                $gcmRegid = $row[4];
                $created = $row[5];
                $androidVersion = $row[6];
                $ogspyVersion = $row[7];
                $deviceInformation = $row[8];
                ?>
                <li>
                    <form id="form<?php echo $userId; echo $i; ?>" name="form<?php echo $userId; echo $i; ?>"
                          method="post">
                        <label>Membre : </label><span><?php echo $name; if (isset($pseudo)) {
                                echo " <i>(" . $pseudo . ")</i>";
                            } ?></span>

                        <div class="clear"></div>
                        <label>Mail : </label><?php if (isset($email)) {
                            echo "<span>" . $email . "</span>";
                        }?></div>
                        <div class="clear">
                        <label>Enregistré le : </label><?php if (isset($created)) {
                            echo "<span>" . $created . "</span>";
                        }?></div>
                        <div class="clear">
                        <label>OGSPY : </label><?php if (isset($ogspyVersion)) {
                            echo "<span>" . $ogspyVersion . "</span>";
                        } else {
                            echo '<span>nc</span>';
                        }?></div>
                        <div class="clear">
                        <label>Android : </label><?php if (isset($androidVersion)) {
                            echo "<span>" . $androidVersion . "</span>";
                        } else {
                            echo '<span>nc</span>';
                        }?></div>
                        <div class="clear">
                        <label>Appareil : </label><?php if (isset($deviceInformation)) {
                            echo "<span>" . $deviceInformation . "</span>";
                        } else {
                            echo '<span>nc</span>';
                        }?></div>
                        <div class="clear"></div>
                        <div class="send_container">
                            <textarea rows="3" id="message<?php echo $userId; echo $i; ?>" cols="25" class="txt_message"
                                      placeholder="Type message here"></textarea>
                            <input type="hidden" id="regId<?php echo $userId; echo $i; ?>"
                                   value="<?php echo $gcmRegid; ?>"/>
                            <input type="button" class="send_btn" value="Envoyer"
                                   onclick="sendPushNotification('<?php echo $userId; echo $i; ?>')"/>
                        </div>
                    </form>
                </li>
                <?php
            }
        } else { ?>
            <li>No Users Registered Yet!</li>
        <?php } ?>
    </ul>
</div>