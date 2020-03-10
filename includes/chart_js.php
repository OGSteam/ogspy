<?php
/** OGSpy Charts library (Hightcharts)
 * @package OGSpy
 * @subpackage Charts
 * @author Machine
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.1.0
 */
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

/**
 * create_pie()
 * Generate the JS Code for a Pie chart
 * Graph Name = $conteneur
 *
 * @param mixed $_data
 * @param mixed $_legend
 * @param mixed $_title
 * @param mixed $conteneur
 * @param boolean $theme
 * @return string the gerated JS Code
 */
function create_pie($_data, $_legend, $_title, $conteneur, $theme = true)
{
    global $server_config;
    // todo voir si insertion possible que si on genere un graph ( test pas concluant)
    //   $retour = import_js();
    $retour = "";

    // test erreurs donnés
    if (!check_var($_data, "Special", "#^[0-9(_x_)]+$#") || !check_var($_legend,
            "Text") || !check_var($_title, "Text") || !check_var($conteneur, "Text")
    ) {
        $retour .= affiche_error($conteneur, 'erreur 1');
        return $retour;
    }
    //
    //  recuperation des infos
    $data = explode('_x_', $_data);
    $legend = explode('_x_', $_legend);
    $title = $_title;

    // il doit y avoir autant de legende que de valeur
    if (count($data) != count($legend)) {
        $retour .= affiche_error($conteneur, 'erreur 2');
        return $retour;
    }

    // préparation des données
    $i = 0;
    $temp = array();
    while ($i < count($data)) {
        $temp[$i] = "['" . $legend[$i] . "'," . $data[$i] . " ]";
        $i++;
    }
    // format hightchart
    $format_data = implode(" , ", $temp);


    // création du script
    $retour .= "<script type=\"text/javascript\">
var " . $conteneur . ";
$(document).ready(function() {


    " . $conteneur . " = new Highcharts.Chart({
      chart: {
         renderTo: '" . $conteneur . "',
         plotBackgroundColor: null,
         plotBorderWidth: null,
         plotShadow: false
      },
      credits: {
        text: '<b>OGSteam Software</b> v " . $server_config["version"] . " ',
        href: 'http://www.ogsteam.fr'
    },
      title: {
         text: '" . $title . "'
      },
      tooltip: {
         formatter: function() {
            return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
         }
      },
      plotOptions: {
         pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                color: '#666666',
               enabled: true
               
            },
            showInLegend: true
         }
      },
       series: [{
         type: 'pie',
         name: 'Browser share',
         
         data: [
            " . $format_data . "
         ]
      }]
   });
}); ";


    // insertion du theme par defaut
    if ($theme == true) {
        $retour .= graph_theme();
    }


    $retour .= "</script> ";


    return $retour;
}

/**
 * create_pie_numbers()
 * genere le script js d un camenbert
 *  le nom du grah = nom du $conteneur
 *
 * @param mixed $_data
 * @param mixed $_legend
 * @param mixed $_title
 * @param mixed $conteneur
 * @param bool $theme
 * @return string contenant script js
 */
function create_pie_numbers($_data, $_legend, $_title, $conteneur, $theme = true)
{
    global $server_config;
    // todo voir si insertion possible que si on genere un graph ( test pas concluant)
    //   $retour = import_js();
    $retour = "";

    // test erreurs donnés
    if (!check_var($_data, "Special", "#^[0-9(_x_)]+$#") || !check_var($_legend,
            "Text") || !check_var($_title, "Text") || !check_var($conteneur, "Text")
    ) {
        $retour .= affiche_error($conteneur, 'erreur 1');
        return $retour;
    }
    //
    //  recuperation des infos
    $data = explode('_x_', $_data);
    $legend = explode('_x_', $_legend);
    $title = $_title;

    // il doit y avoir autant de legende que de valeur
    if (count($data) != count($legend)) {
        $retour .= affiche_error($conteneur, 'erreur 2');
        return $retour;
    }

    // préparation des données
    $i = 0;
    $temp = array();
    while ($i < count($data)) {
        $temp[$i] = "['" . $legend[$i] . "'," . $data[$i] . " ]";
        $i++;
    }
    // format hightchart
    $format_data = implode(" , ", $temp);


    // création du script
    $retour .= "<script type=\"text/javascript\">
	var " . $conteneur . ";
	$(document).ready(function() {


    " . $conteneur . " = new Highcharts.Chart({
		chart: {
	        renderTo: '" . $conteneur . "',
	        plotBackgroundColor: null,
	        plotBorderWidth: null,
			plotShadow: false
		},
      	credits: {
			text: '<b>OGSteam Software</b> v " . $server_config["version"] . " ',
        	href: 'http://www.ogsteam.fr'
	  	},
		title: {
			text: '" . $title . "'
        },
        tooltip: {
            formatter: function() {
				return '<b>' + this.point.name + '</b>: ' + number_format(this.point.y, 0, ',', ' ');
            }
        },
      	plotOptions: {
        	pie: {
	            allowPointSelect: true,
	            cursor: 'pointer',
	            dataLabels: {
	                color: '#FFFFFF',
	                enabled: true
	            },
	            showInLegend: true
         	}
		},
        series: [{
        	type: 'pie',
         	name: 'Browser share',
         	data: [" . $format_data . "]
      	}]
   	});
}); ";


    // insertion du theme par defaut
    if ($theme == true) {
        $retour .= graph_theme();
    }

    $retour .= "</script> ";


    return $retour;
}


/**
 * create_curves()
 * Generate the JS Code for a Curves chart
 *
 * @param string $_player
 * @param int $_date_min
 * @param int $_date_max
 * @param string $_comp
 * @return string the gerated JS Code
 * @todo Revoir les erreurs : la variable $conteneur semble incorrecte
 */

function create_curves($_player, $_date_min, $_date_max, $_comp)
{
    global $db;
    $retour = "";

    // todo quel est ce $contenur ?
    if (!isset($_player)) {
        $retour .= affiche_error($conteneur, 'erreur 3');
        return $retour;
    }

    if (!isset($_date_min) || !is_numeric($_date_min) || !isset($_date_max) || !is_numeric($_date_max)
    ) {
        $retour .= affiche_error($conteneur, 'erreur 4');
        return $retour;
    }

    $player = $_player;
    $date_min = $_date_min;
    $date_max = $_date_max;
    $player_comp = $_comp;

    // récuperation des datas
    $name = array();
    $data = galaxy_show_ranking_unique_player_forJS($player ,$date_min , $date_max);
    $nametpl = array('general', 'Economique', 'Recherche', 'Militaire', 'Militaire Construits', 'Perte militaire', 'destruction', 'honorifique');

    if ($player_comp == "") {
        // formatages des noms pour un joueur
        foreach ($nametpl as $n) {
            $name[] = $n . " (" . $player . ")";
        }
    } else {
        $dataplayer_comp = galaxy_show_ranking_unique_player_forJS($player_comp,$date_min , $date_max);

        // fusion des datas
        $names = array("rank", "points");
        foreach ($names as $n) {
            foreach ($dataplayer_comp["points"] as $key => $value) {
                $data[$n][$key] = $value;
            }
        }

        // formatages des noms pour deux joueurs
        $players = array($player,$player_comp);
        $name=array();
        foreach ($players as $p)
        {
            foreach ($nametpl as $n) {
                $name[] = $n . " (" . $p . ")";
            }

        }
    }

    if (isset($data['points'])) {
        $retour .= create_multi_curve("Points", $player, $data['points'], $name,
            "points"); // points
    }

    if (isset($data['rank'])) {
        $retour .= create_multi_curve("Classement", $player, $data['rank'], $name,
            "rank"); // rank

    }
    return $retour;
}


/**
 * affiche_error()
 * Displays an error message in the selected container
 *
 * @param mixed $conteneur
 * @param string $error
 * @return string js
 */
function affiche_error($conteneur, $error)
{
    $retour = '<script language="javascript">';
    $retour .= '$(document).ready(function() {';
    $retour .= '$("#' . $conteneur . '").empty();';
    $retour .= '$("#' . $conteneur . '").append("' . $error . '");';
    $retour .= '$("#' . $conteneur . '").fadeIn(1000);';
    $retour .= '})';
    $retour .= '</script>';

    return $retour;
}

/**
 * graph_theme()
 * Returns all the css for the OGSpy Graph
 *
 */
function graph_theme()
{
    $retour = " 
/**
 * Dark blue theme for Highcharts JS
 * @author Torstein Hønsi
 */

Highcharts.theme = {
   colors: [\"#DDDF0D\", \"#55BF3B\", \"#DF5353\", \"#7798BF\", \"#aaeeee\", \"#ff0066\", \"#eeaaee\", 
      \"#55BF3B\", \"#DF5353\", \"#7798BF\", \"#aaeeee\"],
   chart: {
      backgroundColor: {
         linearGradient: [0, 0, 250, 500],
         stops: [
            [0, 'rgb(48, 48, 96)'],
            [1, 'rgb(0, 0, 0)']
         ]
      },
      borderColor: '#000000',
      borderWidth: 2,
      className: 'dark-container',
      plotBackgroundColor: 'rgba(255, 255, 255, .1)',
      plotBorderColor: '#CCCCCC',
      plotBorderWidth: 1
   },
   title: {
      style: {
         color: '#C0C0C0',
         font: 'bold 16px \"Trebuchet MS\", Verdana, sans-serif'
      }
   },
   subtitle: {
      style: { 
         color: '#666666',
         font: 'bold 12px \"Trebuchet MS\", Verdana, sans-serif'
      }
   },
   xAxis: {
      gridLineColor: '#333333',
      gridLineWidth: 1,
      labels: {
         style: {
            color: '#A0A0A0'
         }
      },
      lineColor: '#A0A0A0',
      tickColor: '#A0A0A0',
      title: {
         style: {
            color: '#CCC',
            fontWeight: 'bold',
            fontSize: '12px',
            fontFamily: 'Trebuchet MS, Verdana, sans-serif'

         }            
      }
   },
   yAxis: {
      gridLineColor: '#333333',
      labels: {
         style: {
            color: '#A0A0A0'
         }
      },
      lineColor: '#A0A0A0',
      minorTickInterval: null,
      tickColor: '#A0A0A0',
      tickWidth: 1,
      title: {
         style: {
            color: '#CCC',
            fontWeight: 'bold',
            fontSize: '12px',
            fontFamily: 'Trebuchet MS, Verdana, sans-serif'
         }            
      }
   },
   legend: {
      itemStyle: {
         font: '9pt Trebuchet MS, Verdana, sans-serif',
         color: '#A0A0A0'
      }
   },
   tooltip: {
      backgroundColor: 'rgba(0, 0, 0, 0.75)',
      style: {
         color: '#660066'
      }
   },
   toolbar: {
      itemStyle: { 
         color: 'silver'
      }
   },
   plotOptions: {
      line: {
         dataLabels: {
            color: '#CCC'
         },
         marker: {
            lineColor: '#333'
         }
      },
      spline: {
         marker: {
            lineColor: '#333'
         }
      },
      scatter: {
         marker: {
            lineColor: '#333'
         }
      },
      candlestick: {
         lineColor: 'white'
      }
   },      
   legend: {
      itemStyle: {
         color: '#CCC'
      },
      itemHoverStyle: {
         color: '#FFF'
      },
      itemHiddenStyle: {
         color: '#444'
      }
   },
   credits: {
      style: {
         color: '#666'
      }
   },
   labels: {
      style: {
         color: '#660066'
      }
   },
   
   navigation: {
      buttonOptions: {
         backgroundColor: {
            linearGradient: [0, 0, 0, 20],
            stops: [
               [0.4, '#606060'],
               [0.6, '#333333']
            ]
         },
         borderColor: '#000000',
         symbolStroke: '#C0C0C0',
         hoverSymbolStroke: '#FFFFFF'
      }
   },
   
   exporting: {
      buttons: {
         exportButton: {
            symbolFill: '#55BE3B'
         },
         printButton: {
            symbolFill: '#7797BE'
         }
      }
   },
   
   // scroll charts
   rangeSelector: {
      buttonTheme: {
         fill: {
            linearGradient: [0, 0, 0, 20],
            stops: [
               [0.4, '#888'],
               [0.6, '#555']
            ]
         },
         stroke: '#000000',
         style: {
            color: '#CCC',
            fontWeight: 'bold'
         },
         states: {
            hover: {
               fill: {
                  linearGradient: [0, 0, 0, 20],
                  stops: [
                     [0.4, '#BBB'],
                     [0.6, '#888']
                  ]
               },
               stroke: '#000000',
               style: {
                  color: 'white'
               }
            },
            select: {
               fill: {
                  linearGradient: [0, 0, 0, 20],
                  stops: [
                     [0.1, '#000'],
                     [0.3, '#333']
                  ]
               },
               stroke: '#000000',
               style: {
                  color: 'yellow'
               }
            }
         }               
      },
      inputStyle: {
         backgroundColor: '#333',
         color: 'silver'
      },
      labelStyle: {
         color: 'silver'
      }
   },
   
   navigator: {
      handles: {
         backgroundColor: '#666',
         borderColor: '#AAA'
      },
      outlineColor: '#CCC',
      maskFill: 'rgba(16, 16, 16, 0.5)',
      series: {
         color: '#7798BF',
         lineColor: '#A6C7ED'
      }
   },
   
   scrollbar: {
      barBackgroundColor: {
            linearGradient: [0, 0, 0, 20],
            stops: [
               [0.4, '#888'],
               [0.6, '#555']
            ]
         },
      barBorderColor: '#CCC',
      buttonArrowColor: '#CCC',
      buttonBackgroundColor: {
            linearGradient: [0, 0, 0, 20],
            stops: [
               [0.4, '#888'],
               [0.6, '#555']
            ]
         },
      buttonBorderColor: '#CCC',
      rifleColor: '#FFF',
      trackBackgroundColor: {
         linearGradient: [0, 0, 0, 10],
         stops: [
            [0, '#000'],
            [1, '#333']
         ]
      },
      trackBorderColor: '#666'
   },
   
   // special colors for some of the
   legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
   legendBackgroundColorSolid: 'rgb(35, 35, 70)',
   dataLabelsColor: '#444',
   textColor: '#C0C0C0',
   maskColor: 'rgba(255,255,255,0.3)'
};

// Apply the theme
var highchartsOptions = Highcharts.setOptions(Highcharts.theme); ";
    return $retour;
}

/**
 * create_multi_curve()
 * Generate the JS Code for a Multiple Curves chart
 *
 * @param string $titre
 * @param string $sous_titre
 * @param string $data
 * @param string $names
 * @param string $conteneur
 * @param bool $theme
 * @return string the gerated JS Code
 */
function create_multi_curve($titre, $sous_titre, $data, $names, $conteneur, $theme = true)
{
    global $zoom, $server_config; // on recupere le zoom s il existe

    // traitement des datas recu
    if (isset($names)) {
        foreach ($names as $name) {
            if (isset($data[$name])) { //au moins 2 résultats

                $series[] = "{ name: '" . $name . "',data: [" . implode(',', $data[$name]) .
                    "]}";
            }
        }
    }
    // traitement final des données
    $serie = implode(",", $series);


    // on active ou non le zoom
    if ($zoom === "true") {
        $zoom_yAxis = "";
    } else {
        $zoom_yAxis = "  min: 0";
    }


    $retour = "
    <script type=\"text/javascript\">
var chart3;
$(document).ready(function() {
   chart3 = new Highcharts.Chart({
      chart: {
         renderTo: '" . $conteneur . "',
         zoomType: 'xy'
         
        
      },

    
      credits: {
        text: '<b>OGSteam Software</b> v " . $server_config["version"] . " ',
        href: 'http://www.ogsteam.fr'
    },
    
      title: {
         text: '" . $titre . "'
      },
      subtitle: {
         text: '" . $sous_titre . "'   
      },
      xAxis: {
         type: 'datetime' 
        
      },
      yAxis: {
         title: {
            text: '" . $titre . "'
         },
       " . $zoom_yAxis . "
      },
      
      tooltip: {
         formatter: function() {
               return '<b>'+ this.series.name +'</b><br/>'+
               Highcharts.dateFormat('%e. %b', this.x) +'<br/>" . $titre .
        " : '+ Highcharts.numberFormat(this.y, 0, ' ') +' ' ;
         }
      },
      series: [
      " . $serie . "
      ]
   });
   
   
});";

    // insertion du theme par defaut
    if ($theme == true) {
        $retour .= graph_theme();
    }


    $retour .= "</script> ";

    return $retour;
}

