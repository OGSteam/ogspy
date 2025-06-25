<?php

/** OGSpy Charts library (Hightcharts)
 * @package OGSpy
 * @subpackage Charts
 * @author Machine
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.1.0
 */

use Ogsteam\Ogspy\Model\Player_Model;

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

/**
 * Generates and returns a pie chart script based on specified data, legends, and title.
 *
 * @param string $_data A serialized string representing the data values, separated by "_x_". Must match the specified format.
 * @param string $_legend A serialized string representing the legends for the data values, separated by "_x_". Must match the specified format.
 * @param string $_title The title of the pie chart. Must be a valid text string.
 * @param string $conteneur The container ID where the pie chart will be rendered. Must be a valid text string.
 * @param bool $theme Indicates whether to include the default theme in the output. Defaults to true.
 * @return string A formatted string containing the JavaScript code for the pie chart or error messages if the inputs are invalid.
 */
function create_pie($_data, $_legend, $_title, $conteneur, $theme = true)
{
    global $server_config;
    // todo voir si insertion possible que si on genere un graph ( test pas concluant)
    //   $retour = import_js();
    $retour = "";

    // test erreurs donnés
    if (
        !check_var($_data, "Special", "#^[0-9(_x_)]+$#") || !check_var(
            $_legend,
            "Text"
        ) || !check_var($_title, "Text") || !check_var($conteneur, "Text")
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
        href: 'https://www.ogsteam.eu'
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
    if ($theme) {
        $retour .= graph_theme();
    }


    $retour .= "</script> ";


    return $retour;
}

/**
 * Creates and returns a pie chart script based on the provided data, legends, and title.
 *
 * @param mixed $_data A formatted string representing numerical data values, separated by "_x_". Must match the specified format.
 * @param mixed $_legend A formatted string representing labels for the data, separated by "_x_". Must match the specified format.
 * @param mixed $_title The title of the pie chart. Must be a textual value.
 * @param mixed $conteneur The name of the HTML element or container where the chart will be rendered.
 * @param bool $theme Optional. Determines whether the default theme should be applied to the chart. Default is true.
 * @return string A complete JavaScript string including the Highcharts pie chart configuration or an error message in case of invalid inputs.
 */
function create_pie_numbers($_data, $_legend, $_title, $conteneur, $theme = true)
{
    global $server_config;
    // todo voir si insertion possible que si on genere un graph ( test pas concluant)
    //   $retour = import_js();
    $retour = "";

    // test erreurs donnés
    if (
        !check_var($_data, "Special", "#^[0-9(_x_)]+$#") || !check_var(
            $_legend,
            "Text"
        ) || !check_var($_title, "Text") || !check_var($conteneur, "Text")
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
            href: 'https://www.ogsteam.eu'
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
 * Generates and returns curves based on a specified range of dates and player data.
 *
 * @param mixed $_player The primary player for whom the curves are being created. Must be specified.
 * @param mixed $conteneur The container that manages the context of the errors or results.
 * @param mixed $_date_min The starting date for the data range. Must be numeric.
 * @param mixed $_date_max The ending date for the data range. Must be numeric.
 * @param mixed $_comp An optional secondary player for comparison. If empty, curves are generated for the primary player only.
 * @return string A formatted string containing the generated curves or error messages in case of invalid inputs.
 */

function create_curves($_player, $conteneur, $_date_min, $_date_max, $_comp)
{
    $retour = "";

    if (!isset($_player)) {
        $retour .= affiche_error($conteneur, 'erreur 3');
        return $retour;
    }

    if (
        !isset($_date_min) || !is_numeric($_date_min) || !isset($_date_max) || !is_numeric($_date_max)
    ) {
        $retour .= affiche_error($conteneur, 'erreur 4');
        return $retour;
    }

    $player = $_player;
    $date_min = $_date_min;
    $date_max = $_date_max;
    $player_comp = $_comp;

    $playerId = (new Player_Model())->getPlayerId($player);
    if (!empty($player_comp)) {
        $playerCompId = (new Player_Model())->getPlayerId($player);
    }

    // récuperation des datas
    $name = [];
    $data = galaxy_show_ranking_unique_player_forJS($playerId, $date_min, $date_max);
    $nametpl = array('general', 'Economique', 'Recherche', 'Militaire', 'Militaire Construits', 'Perte militaire', 'destruction', 'honorifique');


    if (empty($player_comp)) {
        // formatages des noms pour un joueur
        foreach ($nametpl as $n) {
            $name[] = $n . " (" . $player . ")";
        }
    } else {
        $dataplayer_comp = galaxy_show_ranking_unique_player_forJS($playerCompId, $date_min, $date_max);

        // fusion des datas
        $names = array("rank", "points");
        foreach ($names as $n) {
            foreach ($dataplayer_comp["points"] as $key => $value) {
                $data[$n][$key] = $value;
            }
        }

        // formatages des noms pour deux joueurs
        $players = array($player, $player_comp);
        foreach ($players as $p) {
            foreach ($nametpl as $n) {
                $name[] = $n . " (" . $p . ")";
            }
        }
    }

    if (isset($data['points'])) {
        $retour .= create_multi_curve(
            "Points",
            $player,
            $data['points'],
            $name,
            $conteneur
        ); // points
    }

    if (isset($data['rank'])) {
        $retour .= create_multi_curve(
            "Classement",
            $player,
            $data['rank'],
            $name,
            $conteneur
        ); // rank

    }
    return $retour;
}


/**
 * affiche_error()
 * Generates a JavaScript snippet to display an error message within a specified HTML container.
 *
 * @param string $conteneur The ID of the HTML container where the error message will be displayed.
 * @param string $error The error message to be displayed.
 * @return string The generated JavaScript code as a string.
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
 * Defines a dark blue theme for Highcharts JS and returns the configuration script as a string.
 *
 * @return string The JavaScript code defining the Highcharts dark blue theme.
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
 * Generate the JavaScript code for rendering a multi-curve chart using Highcharts.
 *
 * @param string $titre The title of the chart.
 * @param string $sous_titre The subtitle of the chart.
 * @param array $data The dataset for the chart, where keys represent data categories and values are their corresponding numerical data.
 * @param array $names The list of dataset names to include in the chart.
 * @param string $conteneur The ID of the HTML container where the chart will be rendered.
 * @param bool $theme Optional. Whether to apply the default theme to the chart. Defaults to true.
 * @return string The generated JavaScript code to create and render the Highcharts chart.
 */
function create_multi_curve(string $titre, string $sous_titre, array $data, array $names, string $conteneur, $theme = true)
{
    global $zoom, $server_config; // on recupere le zoom s il existe

    $series[] = ""; // initialisation du tableau de series

    // traitement des datas recu

    foreach ($names as $name) {
        if (isset($data[$name])) { //au moins 2 résultats

            $series[] = "{ name: '" . $name . "',data: [" . implode(",", $data[$name]) . "]}";
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
        href: 'https://www.ogsteam.eu'
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
    if ($theme) {
        $retour .= graph_theme();
    }


    $retour .= "</script> ";

    return $retour;
}
