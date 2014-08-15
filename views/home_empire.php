<?php
/***************************************************************************
*	filename	: home_empire.php
*	desc.		:
*	Author		: Kyser - http://ogsteam.fr/
*	created		: 17/12/2005
*	modified	: 30/04/2007 03:40:00
***************************************************************************/

if (!defined('IN_SPYOGAME')) {
	die("Hacking attempt");
}

require_once("includes/ogame.php");

$user_empire = user_get_empire();
$user_building = $user_empire["building"];
$user_defence = $user_empire["defence"];
$user_technology = $user_empire["technology"];
if(!isset($pub_view) || $pub_view=="") $view = "planets";
elseif ($pub_view == "planets" || $pub_view == "moons") $view = $pub_view;
else $view = "planets";
$start = $view=="planets" ? 101 : 201;
$view_ratio = false; // vue prod reel si ratio inf a 0

/* Restes du Lang Empire :-) */
$technology_requirement["Esp"] = array(3);
$technology_requirement["Ordi"] = array(1);
$technology_requirement["Armes"] = array(4);
$technology_requirement["Bouclier"] = array(6, "NRJ" => 3);
$technology_requirement["Protection"] = array(2);
$technology_requirement["NRJ"] = array(1);
$technology_requirement["Hyp"] = array(1, "NRJ" => 3, "Bouclier" => 5);
$technology_requirement["RC"] = array(1, "NRJ" => 1);
$technology_requirement["RI"] = array(2, "NRJ" => 1);
$technology_requirement["PH"] = array(7, "HYP" => 3);
$technology_requirement["Laser"] = array(1, "NRJ" => 2);
$technology_requirement["Ions"] = array(4, "Laser" => 5, "NRJ" => 4);
$technology_requirement["Plasma"] = array(4, "NRJ" => 8, "Laser" => 10, "Ions" => 5);
$technology_requirement["RRI"] = array(10, "Ordi" => 8, "Hyp" => 8);
$technology_requirement["Graviton"] = array(12);
$technology_requirement["Astrophysique"] = array(3, "Esp" => 4, "RI" => 3);
 
?>

<!-- DEBUT DU SCRIPT -->
<script language="JavaScript">
<?php
if(isset($pub_alert_empire) && $pub_alert_empire) echo 'message("Pensez à renseigner, si besoin est, les noms de planètes et les températures\nqui ne peuvent pas être récupérées par la page Empire d\'OGame.");';

$nb_planete = find_nb_planete_user();

$name = $coordinates = $fields = $temperature_min = $temperature_max = $satellite = "";
for ($i=101 ; $i<=$nb_planete+100 ; $i++) {
	$name .= "'".$user_building[$i]["planet_name"]."', ";
	$coordinates .= "'".$user_building[$i]["coordinates"]."', ";
	$fields .= "'".$user_building[$i]["fields"]."', ";
	$temperature_min .= "'".$user_building[$i]["temperature_min"]."', ";
	$temperature_max .= "'".$user_building[$i]["temperature_max"]."', ";
	$satellite .= "'".$user_building[$i]["Sat"]."', ";
}

for ($i=201 ; $i<=$nb_planete+200 ; $i++) {
	$name .= "'Lune', ";
	$coordinates .= "'', ";
	$fields .= "'1', ";
	$temperature_min .= "'".$user_building[$i]["temperature_min"]."', ";
	$temperature_max .= "'".$user_building[$i]["temperature_max"]."', ";
	$satellite .= "'".$user_building[$i]["Sat"]."', ";
}

echo "var name = new Array(".substr($name, 0, strlen($name)-2).");"."\n";
echo "var coordinates = new Array(".substr($coordinates, 0, strlen($coordinates)-2).");"."\n";
echo "var fields = new Array(".substr($fields, 0, strlen($fields)-2).");"."\n";
echo "var temperature_min = new Array(".substr($temperature_min, 0, strlen($temperature_min)-2).");"."\n";
echo "var temperature_max = new Array(".substr($temperature_max, 0, strlen($temperature_max)-2).");"."\n";
echo "var satellite = new Array(".substr($satellite, 0, strlen($satellite)-2).");"."\n";
?>
var select_planet = false;

function autofill(planet_id, planet_selected) {
	document.getElementById('planet_name').style.visibility = 'visible';
	document.getElementById('planet_name').disabled = false;

	document.getElementById('coordinates').style.visibility = 'visible';
	document.getElementById('coordinates').disabled = false;

	document.getElementById('fields').style.visibility = 'visible';
	document.getElementById('fields').disabled = false;

	document.getElementById('temperature_min').style.visibility = 'visible';
	document.getElementById('temperature_min').disabled = false;

	document.getElementById('temperature_max').style.visibility = 'visible';
	document.getElementById('temperature_max').disabled = false;
	
	document.getElementById('satellite').style.visibility = 'visible';
	document.getElementById('satellite').disabled = false;

	//	if (name[(planet_id-1)] == "" && coordinates[(planet_id-1)] == "" && fields[(planet_id-1)] == "" && temperature[(planet_id-1)] == "" && satellite[(planet_id-1)] == "") {
	//		return;
	//	}

	document.getElementById('planet_name').value = name[(planet_id-1)];
	document.getElementById('coordinates').value = coordinates[(planet_id-1)];
	document.getElementById('fields').value = fields[(planet_id-1)];
	document.getElementById('temperature_min').value = temperature_min[(planet_id-1)];
	document.getElementById('temperature_max').value = temperature_max[(planet_id-1)];
	document.getElementById('satellite').value = satellite[(planet_id-1)];

	var i = 1;
	var lign = 0;
	var id = 0;
	var lim = <?php print ($server_config['ddr']==1)?'42':'41' ; ?>;
	if(planet_id > 9) {
		lim = 17;
		planet_id -= 9;
	}
	for(i = 1; i <= 9; i++) {
		for(lign = 1; lign <= lim; lign++) {
			id = lign*10+i;
			document.getElementById(id).style.color = 'lime';
		}
	}

	for(i = 1; i <= lim; i++) {
		id = i*10+planet_id;
		document.getElementById(id).style.color = 'yellow';
	}

	return(true);
}

function clear_text2() {
	if (document.post2.data.value == "Empire & Bâtiments & Laboratoire & Défenses") {
		document.post2.data.value = "";
	}
}

function message(msg) {
	alert("\n"+msg);
}
</script>
<!-- FIN DU SCRIPT -->

<table width="100%">
<tr>
<?php

$colspan = ($nb_planete + 1) / 2;
$colspan_planete = floor($colspan);
$colspan_lune = ceil($colspan);

if ($view == "planets") {
	echo "<th colspan='$colspan_planete'><a>Planètes</a></th>";
	echo "<td class='c' align='center' colspan='$colspan_lune' onClick=\"window.location = 'index.php?action=home&view=moons';\"><a style='cursor:pointer'><font color='lime'>Lunes</font></a></td>";
}
else {
	echo "<td class='c' align='center' colspan='$colspan_planete' onClick=\"window.location = 'index.php?action=home&view=planets';\"><a style='cursor:pointer'><font color='lime'>Planètes</font></a></td>";
	echo "<th colspan='$colspan_lune'><a>Lunes</a></th>";
}
?>
    </tr>

<?php

// verification de compte de planete/lune avec la technologie astro
$astro = astro_max_planete($user_technology['Astrophysique']);

if (((find_nb_planete_user() > $astro ) || (find_nb_moon_user() > $astro)) && ($user_technology != false)) {
	echo '<tr>';
	echo '<td class="c" colspan="'. ($nb_planete < 10 ? '10' : $nb_planete + 1) .'">';
	echo 'Une incohérence a été trouvée dans votre espace personnel<br />';
	echo (find_nb_planete_user() > $astro) ? 'En rapport avec le nombre de vos planetes<br />' : '';
	echo (find_nb_moon_user() > $astro) ? 'En rapport avec le nombre de vos lunes<br />' : '';
	echo '</td>';
	echo '</tr>';
}

?>

<tr>
	<td class="c" colspan="<?php print ($nb_planete <10)?'10':$nb_planete +1 ?>">Vue d'ensemble de votre empire</td>
   </tr>
<tr>
	<th>&nbsp;</th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	echo "<th>";
	if (!isset($pub_view) || $pub_view == "planets") {
		echo "<input type='image' title='Déplacer la planète ".$user_building[$i]["planet_name"]." vers la gauche' src='images/previous.png' onclick=\"window.location = 'index.php?action=move_planet&planet_id=".$i."&view=".$view."&left';\">&nbsp;&nbsp";
		echo "<input type='image' title='Supprimer la planète ".$user_building[$i]["planet_name"]."' src='images/drop.png' onclick=\"window.location = 'index.php?action=del_planet&planet_id=".$i."&view=".$view."';\">&nbsp;&nbsp;";
		echo "<input type='image' title='Déplacer la planète ".$user_building[$i]["planet_name"]." vers la droite' src='images/next.png' onclick=\"window.location = 'index.php?action=move_planet&planet_id=".$i."&view=".$view."&right';\">";
	} else echo "<input type='image' title='Supprimer la lune ".$user_building[$i]["planet_name"]."' src='images/drop.png' onclick=\"window.location = 'index.php?action=del_planet&planet_id=".$i."&view=".$view."';\">&nbsp;&nbsp;";
	echo "</th>";
}
?>
</tr>
<tr>
	<th><a>Nom</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$name = $user_building[$i]["planet_name"];
	if ($name == "") $name = "&nbsp;";

	echo "\t"."<th><a>".$name."</a></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Coordonnées</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$coordinates = $user_building[$i]["coordinates"];
	if ($coordinates == "" || ($user_building[$i]["planet_name"] == "" && $view=="moons")) $coordinates = "&nbsp;";
	else $coordinates = "[".$coordinates."]";

	echo "\t"."<th>".$coordinates."</th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Cases</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$fields = $user_building[$i]["fields"];
	if ($fields == "0") $fields = 0;
	$fields_used = $user_building[$i]["fields_used"];

	echo "\t"."<th>".$fields_used." / ". ($fields!=0 ? $fields : "") ."</th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Température Min.</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$temperature_min = $user_building[$i]["temperature_min"];
	if ($temperature_min == "") $temperature_min = "&nbsp;";

	echo "\t"."<th>".$temperature_min."</th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Température Max.</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$temperature_max = $user_building[$i]["temperature_max"];
	if ($temperature_max == "") $temperature_max = "&nbsp;";

	echo "\t"."<th>".$temperature_max."</th>"."\n";
}

if($view == "planets") {
?>
</tr>
<tr>
	<td class="c" colspan="<?php print ($nb_planete <10)?'10':$nb_planete +1 ?>">Production théorique</td>
  </tr>
<tr>
	<th><a>Métal</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$M = $user_building[$i]["M"];
	if ($M != "") $production = production("M", $M, $user_data['off_geologue'], 0, 0, $user_technology['Plasma']);
	else $production = "&nbsp";

	echo "\t"."<th>".floor($production)."</th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Cristal</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$C = $user_building[$i]["C"];
	if ($C != "") $production = production("C", $C, $user_data['off_geologue'], 0, 0, $user_technology['Plasma']);
	else $production = "&nbsp";

	echo "\t"."<th>".floor($production)."</th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Deutérium</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$D = $user_building[$i]["D"];
	$temperature_max = $user_building[$i]["temperature_max"];
	$CEF = $user_building[$i]["CEF"];
	$CEF_consumption = consumption("CEF", $CEF);
	if ($D != "") $production = production("D", $D, $user_data['off_geologue'], $temperature_max) - $CEF_consumption;
	else $production = "&nbsp";

	echo "\t"."<th>".floor($production)."</th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Energie</a></th>
<?php
//modif 3.0.7
$product= array( "M" => 0, "C" => 0, "D" => 0,"ratio" => 1, "conso_E" => 0, "prod_E" => 0 );
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
    $ratio[$i] = $product;
   $NRJ = $user_technology["NRJ"] != "" ? $user_technology["NRJ"] : "0"; // pour deut !!!! erreur dans ancienne formule ou nrj etait pas prise en compte
    $ratio[$i] = bilan_production_ratio($user_building[$i]["M"],$user_building[$i]["C"],$user_building[$i]["D"],$user_building[$i]["CES"], 
    $user_building[$i]["CEF"],$user_building[$i]["Sat"],$user_building[$i]["temperature_min"],$user_building[$i]["temperature_max"],$NRJ,$user_data['off_ingenieur'],$user_data['off_geologue'], $user_technology['Plasma']);
     }
   
   

for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
    	echo "\t"."<th>".floor($ratio[$i]["prod_E"])."</th>\n\t";

/// implementation vue reel prod si jamais ration inferieur a 1
	//echo "\t"."<th>";
//    echo floor($production);
//    echo ""; // todo
//    echo "</th>"."\n";
   
        if($ratio[$i]['ratio'] != 1) $view_ratio = true ;
		//$view_ratio = false ;
        }

        
 if ($view_ratio == true) {
    ?>
 <tr>
	<td class="c" colspan="<?php print ($nb_planete <10)?'10':$nb_planete +1 ?>">Production Réelle</td>
  </tr>
  
  
   <tr>
	<th><a>Ratio</a></th>
   <?php 
   // ratio
   for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	echo "\t"."<th>";
    echo ($ratio[$i]['ratio'] != 1) ? "<font color='red'>".round($ratio[$i]['ratio'], 3)."</font color]" :  "<font color='lime'>-</font color]" ; 
     echo "</th>"."\n"; 

} ?>
    
  <tr>
	<th><a>Métal</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	echo "\t"."<th>";
 echo ($ratio[$i]['ratio'] != 1) ? floor($ratio[$i]['M']) :  "-" ; 
    echo "</th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Cristal</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
		echo "\t"."<th>";
    echo ($ratio[$i]['ratio'] != 1) ? floor($ratio[$i]['C']) :  "-" ; 
    echo "</th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Deutérium</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	echo "\t"."<th>";
    echo ($ratio[$i]['ratio'] != 1) ? floor($ratio[$i]['D']) :  "-" ; 
    echo "</th>"."\n";
 }

}      

?>
</tr>
<tr>
	<td class="c_batiments" colspan="<?php print ($nb_planete <10)?'10':$nb_planete +1 ?>">Bâtiments</td>
 </tr>
<tr>
	<th><a>Mine de métal</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$M = $user_building[$i]["M"];
	if ($M == "") $M = "&nbsp;";

	echo "\t"."<th><font color='lime' id='15".($i+1-$start)."'>".$M."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Mine de cristal</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$C = $user_building[$i]["C"];
	if ($C == "") $C = "&nbsp;";

	echo "\t"."<th><font color='lime' id='16".($i+1-$start)."'>".$C."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Synthétiseur de deutérium</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$D = $user_building[$i]["D"];
	if ($D == "") $D = "&nbsp;";

	echo "\t"."<th><font color='lime' id='17".($i+1-$start)."'>".$D."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Centrale électrique solaire</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$CES = $user_building[$i]["CES"];
	if ($CES == "") $CES = "&nbsp;";

	echo "\t"."<th><font color='lime' id='20".($i+1-$start)."'>".$CES."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Centrale électrique de fusion</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$CEF = $user_building[$i]["CEF"];
	if ($CEF == "") $CEF = "&nbsp;";

	echo "\t"."<th><font color='lime' id='21".($i+1-$start)."'>".$CEF."</font></th>"."\n";
}

} // fin de si view="planets"
else 
{
echo '</tr><tr> <td class="c" colspan="';
print ($nb_planete <10)?'10':$nb_planete +1 ;
echo '">Bâtiments</td>';
}
?>
</tr>
<tr><th><a>Usine de robots</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$UdR = $user_building[$i]["UdR"];
	if ($UdR == "") $UdR = "&nbsp;";

	echo "\t"."<th><font color='lime' id='1".($i+1-$start)."'>".$UdR."</font></th>"."\n";
}

if($view == "planets") {
?>
</tr>
<tr>
	<th><a>Usine de nanites</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$UdN = $user_building[$i]["UdN"];
	if ($UdN == "") $UdN = "&nbsp;";

	echo "\t"."<th><font color='lime' id='22".($i+1-$start)."'>".$UdN."</font></th>"."\n";
}

} // fin de si view="planets"
?>
</tr>
<tr>
	<th><a>Chantier spatial</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$CSp = $user_building[$i]["CSp"];
	if ($CSp == "") $CSp = "&nbsp;";

	echo "\t"."<th><font color='lime' id='2".($i+1-$start)."'>".$CSp."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Hangar de métal</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$HM = $user_building[$i]["HM"];
	if ($HM == "") $HM = "&nbsp;";

	echo "\t"."<th><font color='lime' id='3".($i+1-$start)."'>".$HM."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Hangar de cristal</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$HC = $user_building[$i]["HC"];
	if ($HC == "") $HC = "&nbsp;";

	echo "\t"."<th><font color='lime' id='4".($i+1-$start)."'>".$HC."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Réservoir de deutérium</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$HD = $user_building[$i]["HD"];
	if ($HD == "") $HD = "&nbsp;";

	echo "\t"."<th><font color='lime' id='5".($i+1-$start)."'>".$HD."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Cachette de Métal</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$CM = $user_building[$i]["CM"];
	if ($CM == "") $CM = "&nbsp;";

	echo "\t"."<th><font color='lime' id='6".($i+1-$start)."'>".$CM."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Cachette de Cristal</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$CC = $user_building[$i]["CC"];
	if ($CC == "") $CC = "&nbsp;";

	echo "\t"."<th><font color='lime' id='7".($i+1-$start)."'>".$CC."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Cachette de Deutérium</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$CD = $user_building[$i]["CD"];
	if ($CD == "") $CD = "&nbsp;";

	echo "\t"."<th><font color='lime' id='8".($i+1-$start)."'>".$CD."</font></th>"."\n";
}
if($view == "planets") {
?>
</tr>
<tr>
	<th><a>Laboratoire de recherche</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$Lab = $user_building[$i]["Lab"];
	if ($Lab == "") $Lab = "&nbsp;";

	echo "\t"."<th><font color='lime' id='23".($i+1-$start)."'>".$Lab."</font></th>"."\n";
}
if ( $server_config['ddr'] == 1 )
{
?>
</tr>
<tr>
	<th><a>D&eacute;p&ocirc;t de ravitaillement</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$DdR = $user_building[$i]["DdR"];
	if ($DdR == "") $DdR = "&nbsp;";

	echo "\t"."<th><font color='lime' id='42".($i+1-$start)."'>".$DdR."</font></th>"."\n";
}
}//Fin de si $server_config['ddr']
?>
</tr>
<tr>
	<th><a>Terraformeur</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$Ter = $user_building[$i]["Ter"];
	if ($Ter == "") $Ter = "&nbsp;";

	echo "\t"."<th><font color='lime' id='24".($i+1-$start)."'>".$Ter."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Silo de missiles</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$Silo = $user_building[$i]["Silo"];
	if ($Silo == "") $Silo = "&nbsp;";

	echo "\t"."<th><font color='lime' id='25".($i+1-$start)."'>".$Silo."</font></th>"."\n";
}

} // fin de si view="planets"
else {
?>
</tr>
<tr>
	<th><a>Base lunaire</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$BaLu = $user_building[$i]["BaLu"];
	if ($BaLu == "") $BaLu = "&nbsp;";

	echo "\t"."<th><font color='lime' id='15".($i+1-$start)."'>".$BaLu."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Phalange de capteur</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$Pha = $user_building[$i]["Pha"];
	if ($Pha == "") $Pha = "&nbsp;";

	echo "\t"."<th><font color='lime' id='16".($i+1-$start)."'>".$Pha."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Porte de saut spatial</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$PoSa = $user_building[$i]["PoSa"];
	if ($PoSa == "") $PoSa = "&nbsp;";

	echo "\t"."<th><font color='lime' id='17".($i+1-$start)."'>".$PoSa."</font></th>"."\n";
}

} // fin de sinon view="planets"
?>
</tr>
<tr>
	<td class="c_satellite" colspan="<?php print ($nb_planete <10)?'10':$nb_planete +1 ;?>">Divers</td>
   </tr>
<tr>
	<th><a>Satellites</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$Sat = $user_building[$i]["Sat"];
	if ($Sat == "") $Sat = "&nbsp;";

	echo "\t"."<th><font color='lime' id='6".($i+1-$start)."'>".$Sat."</font></th>"."\n";
}

if($view == "planets") {
?>
</tr>
<tr>
	<td class="c_classement_recherche" colspan="<?php print ($nb_planete <10)?'10':$nb_planete +1 ?>">Technologies</td>
</tr>
<tr>
	<th><a>Technologie Espionnage</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$Lab = $user_building[$i]["Lab"];
	$Esp = "&nbsp;";

	if ($user_building[$i][0] == true) {
		$Esp = $user_technology["Esp"] != "" ? $user_technology["Esp"] : "0";
		$requirement = $technology_requirement["Esp"];

		while ($value = current($requirement)) {
			$key = key($requirement);
			if ($key == 0) {
				if ($Lab < $value) $Esp = "-";
			}
			elseif ($user_technology[$key] < $value) {
				$Esp = "-";
			}
			next($requirement);
		}
	}

	echo "\t"."<th><font color='lime' id='26".(($i+1-$start))."'>".$Esp."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Technologie Ordinateur</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$Lab = $user_building[$i]["Lab"];
	$Ordi = "&nbsp;";

	if ($user_building[$i][0] == true) {
		$Ordi = $user_technology["Ordi"] != "" ? $user_technology["Ordi"] : "0";
		$requirement = $technology_requirement["Ordi"];

		while ($value = current($requirement)) {
			$key = key($requirement);
			if ($key == 0) {
				if ($Lab < $value) $Ordi = "-";
			}
			elseif ($user_technology[$key] < $value) {
				$Ordi = "-";
			}
			next($requirement);
		}
	}

	echo "\t"."<th><font color='lime' id='27".($i+1-$start)."'>".$Ordi."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Technologie Armes</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$Lab = $user_building[$i]["Lab"];
	$Armes = "&nbsp;";

	if ($user_building[$i][0] == true) {
		$Armes = $user_technology["Armes"] != "" ? $user_technology["Armes"] : "0";
		$requirement = $technology_requirement["Armes"];

		while ($value = current($requirement)) {
			$key = key($requirement);
			if ($key == 0) {
				if ($Lab < $value) $Armes = "-";
			}
			elseif ($user_technology[$key] < $value) {
				$Armes = "-";
			}
			next($requirement);
		}
	}

	echo "\t"."<th><font color='lime' id='28".($i+1-$start)."'>".$Armes."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Technologie Bouclier</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$Lab = $user_building[$i]["Lab"];
	$Bouclier = "&nbsp;";

	if ($user_building[$i][0] == true) {
		$Bouclier = $user_technology["Bouclier"] != "" ? $user_technology["Bouclier"] : "0";
		$requirement = $technology_requirement["Bouclier"];

		while ($value = current($requirement)) {
			$key = key($requirement);
			if ($key == 0) {
				if ($Lab < $value) $Bouclier = "-";
			}
			elseif ($user_technology[$key] < $value) {
				$Bouclier = "-";
			}
			next($requirement);
		}
	}

	echo "\t"."<th><font color='lime' id='29".($i+1-$start)."'>".$Bouclier."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Technologie Protection des vaisseaux spatiaux</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$Lab = $user_building[$i]["Lab"];
	$Protection = "&nbsp;";

	if ($user_building[$i][0] == true) {
		$Protection = $user_technology["Protection"] != "" ? $user_technology["Protection"] : "0";
		$requirement = $technology_requirement["Protection"];

		while ($value = current($requirement)) {
			$key = key($requirement);
			if ($key == 0) {
				if ($Lab < $value) $Protection = "-";
			}
			elseif ($user_technology[$key] < $value) {
				$Protection = "-";
			}
			next($requirement);
		}
	}

	echo "\t"."<th><font color='lime' id='30".($i+1-$start)."'>".$Protection."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Technologie Energie</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$Lab = $user_building[$i]["Lab"];
	$NRJ = "&nbsp;";

	if ($user_building[$i][0] == true) {
		$NRJ = $user_technology["NRJ"] != "" ? $user_technology["NRJ"] : "0";
		$requirement = $technology_requirement["NRJ"];

		while ($value = current($requirement)) {
			$key = key($requirement);
			if ($key == 0) {
				if ($Lab < $value) $NRJ = "-";
			}
			elseif ($user_technology[$key] < $value) {
				$NRJ = "-";
			}
			next($requirement);
		}
	}

	echo "\t"."<th><font color='lime' id='31".($i+1-$start)."'>".$NRJ."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Technologie Hyperespace</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$Lab = $user_building[$i]["Lab"];
	$Hyp = "&nbsp;";

	if ($user_building[$i][0] == true) {
		$Hyp = $user_technology["Hyp"] != "" ? $user_technology["Hyp"] : "0";
		$requirement = $technology_requirement["Hyp"];

		while ($value = current($requirement)) {
			$key = key($requirement);
			if ($key == 0) {
				if ($Lab < $value) $Hyp = "-";
			}
			elseif ($user_technology[$key] < $value) {
				$Hyp = "-";
			}
			next($requirement);
		}
	}

	echo "\t"."<th><font color='lime' id='32".($i+1-$start)."'>".$Hyp."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Réacteur à combustion</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$Lab = $user_building[$i]["Lab"];
	$RC = "&nbsp;";

	if ($user_building[$i][0] == true) {
		$RC = $user_technology["RC"] != "" ? $user_technology["RC"] : "0";
		$requirement = $technology_requirement["RC"];

		while ($value = current($requirement)) {
			$key = key($requirement);
			if ($key == 0) {
				if ($Lab < $value) $RC = "-";
			}
			elseif ($user_technology[$key] < $value) {
				$RC = "-";
			}
			next($requirement);
		}
	}

	echo "\t"."<th><font color='lime' id='33".($i+1-$start)."'>".$RC."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Réacteur à impulsion</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$Lab = $user_building[$i]["Lab"];
	$RI = "&nbsp;";

	if ($user_building[$i][0] == true) {
		$RI = $user_technology["RI"] != "" ? $user_technology["RI"] : "0";
		$requirement = $technology_requirement["RI"];

		while ($value = current($requirement)) {
			$key = key($requirement);
			if ($key == 0) {
				if ($Lab < $value) $RI = "-";
			}
			elseif ($user_technology[$key] < $value) {
				$RI = "-";
			}
			next($requirement);
		}
	}

	echo "\t"."<th><font color='lime' id='34".($i+1-$start)."'>".$RI."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Propulsion hyperespace</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$Lab = $user_building[$i]["Lab"];
	$PH = "&nbsp;";

	if ($user_building[$i][0] == true) {
		$PH = $user_technology["PH"] != "" ? $user_technology["PH"] : "0";
		$requirement = $technology_requirement["PH"];

		while ($value = current($requirement)) {
			$key = key($requirement);
			if ($key == 0) {
				if ($Lab < $value) $PH = "-";
			}
			elseif ($user_technology[$key] < $value) {
				$PH = "-";
			}
			next($requirement);
		}
	}

	echo "\t"."<th><font color='lime' id='35".($i+1-$start)."'>".$PH."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Technologie Laser</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$Lab = $user_building[$i]["Lab"];
	$Laser = "&nbsp;";

	if ($user_building[$i][0] == true) {
		$Laser = $user_technology["Laser"] != "" ? $user_technology["Laser"] : "0";
		$requirement = $technology_requirement["Laser"];

		while ($value = current($requirement)) {
			$key = key($requirement);
			if ($key == 0) {
				if ($Lab < $value) $Laser = "-";
			}
			elseif ($user_technology[$key] < $value) {
				$Laser = "-";
			}
			next($requirement);
		}
	}

	echo "\t"."<th><font color='lime' id='36".($i+1-$start)."'>".$Laser."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Technologie Ions</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$Lab = $user_building[$i]["Lab"];
	$Ions = "&nbsp;";

	if ($user_building[$i][0] == true) {
		$Ions = $user_technology["Ions"] != "" ? $user_technology["Ions"] : "0";
		$requirement = $technology_requirement["Ions"];

		while ($value = current($requirement)) {
			$key = key($requirement);
			if ($key == 0) {
				if ($Lab < $value) $Ions = "-";
			}
			elseif ($user_technology[$key] < $value) {
				$Ions = "-";
			}
			next($requirement);
		}
	}

	echo "\t"."<th><font color='lime' id='37".($i+1-$start)."'>".$Ions."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Technologie Plasma</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$Lab = $user_building[$i]["Lab"];
	$Plasma = "&nbsp;";
	if ($user_building[$i][0] == true) {
		$Plasma = $user_technology["Plasma"] != "" ? $user_technology["Plasma"] : "0";
		$requirement = $technology_requirement["Plasma"];

		while ($value = current($requirement)) {
			$key = key($requirement);
			if ($key === 0) {
				if ($Lab < $value){ $Plasma = "-";} 
			}
			elseif ($user_technology[$key] < $value) {
				$Plasma = "-";
			}
			next($requirement);
		}
	}
	echo "\t"."<th><font color='lime' id='38".($i+1-$start)."'>".$Plasma."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Réseau de recherche intergalactique</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$Lab = $user_building[$i]["Lab"];
	$RRI = "&nbsp;";

	if ($user_building[$i][0] == true) {
		$RRI = $user_technology["RRI"] != "" ? $user_technology["RRI"] : "0";
		$requirement = $technology_requirement["RRI"];

		while ($value = current($requirement)) {
			$key = key($requirement);
			if ($key == 0) {
				if ($Lab < $value) $RRI = "-";
			}
			elseif ($user_technology[$key] < $value) {
				$RRI = "-";
			}
			next($requirement);
		}
	}

	echo "\t"."<th><font color='lime' id='39".($i+1-$start)."'>".$RRI."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Technologie Astrophysique</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$Lab = $user_building[$i]["Lab"];
	$Astrophysique = "&nbsp;";

	if ($user_building[$i][0] == true) {
		$Astrophysique = $user_technology["Astrophysique"] != "" ? $user_technology["Astrophysique"] : "0";
		$requirement = $technology_requirement["Astrophysique"];

		while ($value = current($requirement)) {
			$key = key($requirement);
			if ($key == 0) {
				if ($Lab < $value) $Astrophysique = "-";
			}
			elseif ($user_technology[$key] < $value) {
				$Astrophysique = "-";
			}
			next($requirement);
		}
	}

	echo "\t"."<th><font color='lime' id='41".($i+1-$start)."'>".$Astrophysique."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Technologie Graviton</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$Lab = $user_building[$i]["Lab"];
	$Graviton = "&nbsp;";

	if ($user_building[$i][0] == true) {
		$Graviton = $user_technology["Graviton"] != "" ? $user_technology["Graviton"] : "0";
		$requirement = $technology_requirement["Graviton"];

		while ($value = current($requirement)) {
			$key = key($requirement);
			if ($key == 0) {
				if ($Lab < $value) $Graviton = "-";
			}
			elseif ($user_technology[$key] < $value) {
				$Graviton = "-";
			}
			next($requirement);
		}
	}

	echo "\t"."<th><font color='lime' id='40".($i+1-$start)."'>".$Graviton."</font></th>"."\n";
}

} // fin de si view="planets"
?>
</tr>
<tr>
	<td class="c_defense" colspan="<?php print ($nb_planete <10)?'10':$nb_planete +1 ?>">Défenses</td>
  
</tr>
<tr>
	<th><a>Lanceur de missiles</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$LM = $user_defence[$i]["LM"];
	if ($LM == "") $LM = "&nbsp;";

	echo "\t"."<th><font color='lime' id='7".($i+1-$start)."'>".$LM."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Artillerie laser légère</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$LLE = $user_defence[$i]["LLE"];
	if ($LLE == "") $LLE = "&nbsp;";

	echo "\t"."<th><font color='lime' id='8".($i+1-$start)."'>".$LLE."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Artillerie laser lourde</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$LLO = $user_defence[$i]["LLO"];
	if ($LLO == "") $LLO = "&nbsp;";

	echo "\t"."<th><font color='lime' id='9".($i+1-$start)."'>".$LLO."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Canon de Gauss</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$CG = $user_defence[$i]["CG"];
	if ($CG == "") $CG = "&nbsp;";

	echo "\t"."<th><font color='lime' id='10".($i+1-$start)."'>".$CG."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Artillerie à ions</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$AI = $user_defence[$i]["AI"];
	if ($AI == "") $AI = "&nbsp;";

	echo "\t"."<th><font color='lime' id='11".($i+1-$start)."'>".$AI."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Lanceur de plasma</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$LP = $user_defence[$i]["LP"];
	if ($LP == "") $LP = "&nbsp;";

	echo "\t"."<th><font color='lime' id='12".($i+1-$start)."'>".$LP."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Petit bouclier</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$PB = $user_defence[$i]["PB"];
	if ($PB == "") $PB = "&nbsp;";

	echo "\t"."<th><font color='lime' id='13".($i+1-$start)."'>".$PB."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Grand bouclier</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$GB = $user_defence[$i]["GB"];
	if ($GB == "") $GB = "&nbsp;";

	echo "\t"."<th><font color='lime' id='14".($i+1-$start)."'>".$GB."</font></th>"."\n";
}

if($view == "planets") {
?>
</tr>
<tr>
	<th><a>Missile Interception</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$MIC = $user_defence[$i]["MIC"];
	if ($MIC == "") $MIC = "&nbsp;";

	echo "\t"."<th><font color='lime' id='19".($i+1-$start)."'>".$MIC."</font></th>"."\n";
}
?>
</tr>
<tr>
	<th><a>Missile Interplanétaire</a></th>
<?php
for ($i=$start ; $i<=$start+$nb_planete -1 ; $i++) {
	$MIP = $user_defence[$i]["MIP"];
	if ($MIP == "") $MIP = "&nbsp;";

	echo "\t"."<th><font color='lime' id='18".($i+1-$start)."'>".$MIP."</font></th>"."\n";
}

} // fin de si view="planets"
?>
</tr>
</table>

<?php
function read_th($txt,$nb_planete){
    $retour = "";
    if ($nb_planete > 9 )
    {
      for ($i=10 ; $i<=$nb_planete; $i++) {
       $retour=$retour.$txt;
              
    }
    }
	return $retour;
    }
