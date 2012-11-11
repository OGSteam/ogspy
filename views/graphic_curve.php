<?php
/***************************************************************************
*	filename	: graphic_curve.php
*	desc.		:
*	Author		: Ben.12 - http://ogsteam.fr/
*	created		:
*	modified	: 22/06/2006 00:13:20
*	modified	: 30/07/2006 00:00:00
***************************************************************************/

if (!defined('IN_SPYOGAME')) {
	die("Hacking attempt");
}

if(!isset($pub_player)) exit;

if(!isset($pub_start) || !is_numeric($pub_start)) exit;

if(!isset($pub_end) || !is_numeric($pub_end)) exit;

if(!isset($pub_graph)) exit;
$pub_graph = explode("_", $pub_graph);
if(sizeof($pub_graph) != 2) exit;

if(!isset($pub_player_comp)) $pub_player_comp="";

if(!isset($pub_titre)) $pub_titre="";

if(!isset($pub_zoom)) $pub_zoom = "true";

if (!check_var($pub_player, "Text") || !check_var($pub_start, "Num") || !check_var($pub_end, "Num") || !check_var($pub_graph[0], "Text") || !check_var($pub_graph[1], "Text") ||
!check_var($pub_player_comp, "Text") || !check_var($pub_titre, "Text") || !check_var($pub_zoom, "Char")) {
	exit;
}

$player = $pub_player;
$start = $pub_start;
$end = $pub_end;
$graph = $pub_graph;
$player_comp = $pub_player_comp;
$titre = $pub_titre;
$zoom = $pub_zoom;

switch ($graph[0]) {
	case "points":
	$table = TABLE_RANK_PLAYER_POINTS;
	break;

	case "eco":
	$table = TABLE_RANK_PLAYER_ECO;
	break;

	case "techno":
	$table = TABLE_RANK_PLAYER_TECHNOLOGIE;
	break;

	case "military":
	$table = TABLE_RANK_PLAYER_MILITARY;
	break;

	case "militaryl":
	$table = TABLE_RANK_PLAYER_MILITARY_LOOSE;
	break;

	case "militaryd":
	$table = TABLE_RANK_PLAYER_MILITARY_DESTRUCT;
	break;

	case "honnor":
	$table = TABLE_RANK_PLAYER_HONOR;
	break;

  

}

//on recupère le classement
$ranking_1 = array();
$ranking_2 = array();
$dates = array();
$dates2 = array();

$request = "select player, datadate, ".$graph[1];
$request .= " from ".$table;
$request .= " where (player = '".$db->sql_escape_string($player)."' or player = '".$db->sql_escape_string($player_comp)."')";
$request .= " and datadate between ".$start." and ".$end;
$request .= " order by datadate asc";
$result = $db->sql_query($request, false, false);
while (list($player_name, $datadate, $score) = $db->sql_fetch_row($result)) {
	switch(strtolower($player_name)) {
		case strtolower($player) :
		$ranking_1[$datadate] = $score;
		$dates[] = $datadate;
		break;
		case strtolower($player_comp) :
		$ranking_2[$datadate] = $score;
		$dates2[] = $datadate;
	}
}

$db->sql_close();

$dates = sizeof($dates) > sizeof($dates2) ? $dates : $dates2;

// je definis les fonctions dont j'ai besoin
function setDate($value) {
	global $data_date;
	return $data_date[$value];
}

function setValue($value) {;
if($value >= 100) return round($value);
else return ($value != NULL ? round($value,1) : NULL);
}

function setY($value) {
	if($value >= 1000) return round($value);
	elseif($value >= 100) return round($value,1);
	else return round($value,2);
}

// je mets les données à l'echelle
$data_date = array();
$data = array();
$data2 = array();

if( sizeof($ranking_1) > 0 && sizeof($ranking_2) > 0) {
	$min_data = min($ranking_1) > min($ranking_2) ? min($ranking_2) : min($ranking_1);
	$max_data = max($ranking_1) < max($ranking_2) ? max($ranking_2) : max($ranking_1);
} elseif( sizeof($ranking_1) > 0) {
	$min_data = min($ranking_1);
	$max_data = max($ranking_1);
} elseif( sizeof($ranking_2) > 0) {
	$min_data = min($ranking_2);
	$max_data = max($ranking_2);
} else die("exit");

$size = ceil((max($dates) - min($dates)) / (60*60*8))+1;
$j = 0;

for ($i=0; $i<$size; $i++) {
	$datadate = min($dates) + (60*60*8*$i);

	if( $dates[$j] > ($datadate - (60*60*2)) && $dates[$j] < ($datadate + (60*60*2)) ) {
		$datadate = $dates[$j];
		$j++;
	}

	$data[]  = isset($ranking_1[$datadate]) ?  $ranking_1[$datadate] : NULL;
	$data2[] = isset($ranking_2[$datadate]) ?  $ranking_2[$datadate] : NULL;
	$data_date[] = strftime("%d %b", min($dates) + (60*60*8*($i)));
}

if($max_data > 999) {
	$titre = $titre." (x1000)";
	$div = true;

	for($i=0; $i < sizeof($data_date); $i++) {
		if($data[$i] != NULL) $data[$i] = $data[$i]/1000;
		if($data2[$i] != NULL) $data2[$i] = $data2[$i]/1000;
	}
}
else $div = false;

if($div) {
	$min_data = $min_data / 1000;
	$max_data = $max_data / 1000;
}

if($size > 10) $label_number = 10;
else $label_number = $size;


require_once("library/artichow/LinePlot.class.php");

// On créée le graphique
$graph = new Graph(400, 200);
//$graph->setTiming(TRUE);		//ca c'est pour connaitre le temps de génération de l'image.
$graph->setAntiAliasing(TRUE);
$graph->title->set($titre);
$graph->title->move(0, -5);
$graph->title->setFont(new Tuffy(12));
$graph->title->setColor(new Color(255, 255, 255, 0));
$graph->setBackgroundColor(new Color(52, 69, 102, 0));


// On créée un groupe
$tplot = new PlotGroup();
$tplot->setBackgroundImage(new FileImage("images/graphic_background.jpg"));

$tplot->grid->setNobackground();
$tplot->grid->setColor(new Color(150, 150, 150, 25));
$tplot->grid->setInterval(1, (round($size/20)>1 ? (round($size*1.5)/40) : 1));
$tplot->grid->setType(2);

$tplot->axis->bottom->setTickInterval((round($size/20)>1 ? (round($size*1.5)/40) : 1));
$tplot->axis->bottom->setLabelNumber($label_number);
$tplot->axis->bottom->label->setCallbackFunction('setDate');
$tplot->axis->bottom->title->set("dates");
$tplot->axis->bottom->title->move(0, -5);
$tplot->axis->bottom->setColor(new Color(150, 150, 150, 0));
$tplot->axis->left->setColor(new Color(150, 150, 150, 0));
$tplot->axis->left->label->setCallbackFunction('setY');
$tplot->axis->left->label->move(3,0);
$tplot->axis->bottom->label->move(0,2);
$tplot->axis->bottom->label->setColor(new Color(255, 255, 255, 0));
$tplot->axis->bottom->title->setColor(new Color(255, 255, 255, 0));
$tplot->axis->left->label->setColor(new Color(255, 255, 255, 0));

if($zoom=="true") {
	$tplot->setYMin($min_data-(0.05*($max_data+$min_data)));
	$tplot->setYMax($max_data+(0.05*($max_data+$min_data)));
	$tplot->setXAxisZero(FALSE);
}
// On créée une courbe avec les données
$plot = new LinePlot($data);
$plot->label->set($data);
$plot->label->setCallbackFunction("setValue");
$plot->label->setColor(new White());
$plot->label->move(0, -9);

$plot->mark->setType(3);
$plot->mark->setImage(new FileImage("images/graphic_blueround.png"));

$plot->setColor(new Color(20, 20, 255, 0));
if(sizeof($ranking_1) > 1)
$plot->setFillGradient(
new LinearGradient(
new Color(100, 70, 150, 50),
new Color(50, 50, 230, 50),
90
)
);
$tplot->add($plot);

// On créée une 2nd courbe avec les données
$plot = new LinePlot($data2);
$plot->label->set($data2);
$plot->label->setCallbackFunction("setValue");
$plot->label->setColor(new Color(180, 180, 180, 20));
$plot->label->move(0, -5);
$plot->setColor(new Color(180, 180, 180, 20));
$tplot->add($plot);

$tplot->setPadding(30, NULL, NULL, 30);

$graph->add($tplot);
// On affiche le graphique à l'écran
$graph->draw();
?>