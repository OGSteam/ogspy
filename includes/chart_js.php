<?php

/** OGSpy Charts library (Chart.js)
 * @package OGSpy
 * @subpackage Charts
 * @author Machine
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 4.0.0
 */

use Ogsteam\Ogspy\Model\Player_Model;

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

/**
 * Generates and returns a pie chart script based on specified data, legends, and title.
 * Displays each slice as a percentage of the total.
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

    $data = explode('_x_', $_data);
    $legend = explode('_x_', $_legend);
    $title = json_encode($_title);

    // il doit y avoir autant de legende que de valeur
    if (count($data) != count($legend)) {
        $retour .= affiche_error($conteneur, 'erreur 2');
        return $retour;
    }

    $json_labels = json_encode($legend);
    $json_data = json_encode(array_map('floatval', $data));

    $colors = json_encode(graph_colors());

    $retour .= <<<JS
<script type="text/javascript">
$(document).ready(function() {
    var container = document.getElementById('{$conteneur}');
    container.innerHTML = '';
    var canvas = document.createElement('canvas');
    container.appendChild(canvas);
    new Chart(canvas, {
        type: 'pie',
        data: {
            labels: {$json_labels},
            datasets: [{
                data: {$json_data},
                backgroundColor: {$colors}
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: {$title},
                    color: '#C0C0C0',
                    font: { size: 16, family: '"Trebuchet MS", Verdana, sans-serif', weight: 'bold' }
                },
                legend: {
                    labels: { color: '#C0C0C0' }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var total = context.dataset.data.reduce(function(a, b) { return a + b; }, 0);
                            var percentage = total > 0 ? (context.parsed / total * 100).toFixed(2) : 0;
                            return context.label + ': ' + percentage + ' %';
                        }
                    }
                }
            }
        }
    });
});
</script>
JS;

    return $retour;
}

/**
 * Creates and returns a pie chart script based on the provided data, legends, and title.
 * Displays each slice with its absolute numeric value.
 *
 * @param mixed $_data A formatted string representing numerical data values, separated by "_x_". Must match the specified format.
 * @param mixed $_legend A formatted string representing labels for the data, separated by "_x_". Must match the specified format.
 * @param mixed $_title The title of the pie chart. Must be a textual value.
 * @param mixed $conteneur The name of the HTML element or container where the chart will be rendered.
 * @param bool $theme Optional. Determines whether the default theme should be applied to the chart. Default is true.
 * @return string A complete JavaScript string including the Chart.js pie chart configuration or an error message in case of invalid inputs.
 */
function create_pie_numbers($_data, $_legend, $_title, $conteneur, $theme = true)
{
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

    $data = explode('_x_', $_data);
    $legend = explode('_x_', $_legend);
    $title = json_encode($_title);

    // il doit y avoir autant de legende que de valeur
    if (count($data) != count($legend)) {
        $retour .= affiche_error($conteneur, 'erreur 2');
        return $retour;
    }

    $json_labels = json_encode($legend);
    $json_data = json_encode(array_map('floatval', $data));

    $colors = json_encode(graph_colors());

    $retour .= <<<JS
<script type="text/javascript">
$(document).ready(function() {
    var container = document.getElementById('{$conteneur}');
    container.innerHTML = '';
    var canvas = document.createElement('canvas');
    container.appendChild(canvas);
    new Chart(canvas, {
        type: 'pie',
        data: {
            labels: {$json_labels},
            datasets: [{
                data: {$json_data},
                backgroundColor: {$colors}
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: {$title},
                    color: '#C0C0C0',
                    font: { size: 16, family: '"Trebuchet MS", Verdana, sans-serif', weight: 'bold' }
                },
                legend: {
                    labels: { color: '#C0C0C0' }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>
JS;

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
        $playerCompId = (new Player_Model())->getPlayerId($player_comp);
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
        $data = array_merge_recursive($data, $dataplayer_comp);

        // formatages des noms pour deux joueurs
        $players = array($player, $player_comp);
        foreach ($players as $p) {
            foreach ($nametpl as $n) {
                $name[] = $n . " (" . $p . ")";
            }
        }
    }

    if (isset($data['points']) && $conteneur === 'points') {
        $retour .= create_multi_curve(
            "Points",
            $player,
            $data['points'],
            $name,
            $conteneur
        ); // points
    }

    if (isset($data['rank']) && $conteneur === 'rank') {
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
 * graph_colors()
 * Returns the color palette used for Chart.js charts (dark theme compatible).
 *
 * @return array Array of color hex strings.
 */
function graph_colors(): array
{
    return [
        "#DDDF0D", "#55BF3B", "#DF5353", "#7798BF", "#aaeeee",
        "#ff0066", "#eeaaee", "#55BF3B", "#DF5353", "#7798BF", "#aaeeee"
    ];
}

/**
 * graph_theme()
 * Returns an empty string (theme is now applied inline via Chart.js options).
 * Kept for backward compatibility.
 *
 * @return string Empty string.
 */
function graph_theme(): string
{
    return '';
}

/**
 * create_multi_curve()
 * Generate the JavaScript code for rendering a multi-curve chart using Chart.js.
 *
 * @param string $titre The title of the chart.
 * @param string $sous_titre The subtitle of the chart.
 * @param array $data The dataset for the chart, where keys represent data categories and values are their corresponding numerical data.
 * @param array $names The list of dataset names to include in the chart.
 * @param string $conteneur The ID of the HTML container where the chart will be rendered.
 * @param bool $theme Optional. Whether to apply the default theme to the chart. Defaults to true.
 * @return string The generated JavaScript code to create and render the Chart.js chart.
 */
function create_multi_curve(string $titre, string $sous_titre, array $data, array $names, string $conteneur, $theme = true)
{
    global $zoom, $server_config;

    $colors = graph_colors();
    $datasets = [];
    $colorIndex = 0;

    foreach ($names as $name) {
        if (isset($data[$name])) {
            $color = $colors[$colorIndex % count($colors)];
            $pointsJson = implode(",", $data[$name]);
            $datasets[] = "{ label: " . json_encode($name) . ", data: [{$pointsJson}], borderColor: " . json_encode($color) . ", backgroundColor: 'transparent', tension: 0.1, pointRadius: 2 }";
            $colorIndex++;
        }
    }

    $datasetsStr = implode(",\n", $datasets);
    $titreJs = json_encode($titre . ' - ' . $sous_titre);
    $titreYJs = json_encode($titre);
    $yMin = ($zoom !== "true") ? "min: 0," : "";

    $retour = <<<JS
    <script type="text/javascript">
$(document).ready(function() {
    var container = document.getElementById('{$conteneur}');
    container.innerHTML = '';
    var canvas = document.createElement('canvas');
    container.appendChild(canvas);
    new Chart(canvas, {
        type: 'line',
        data: {
            datasets: [
                {$datasetsStr}
            ]
        },
        options: {
            parsing: false,
            backgroundColor: 'rgb(0,0,0)',
            plugins: {
                title: {
                    display: true,
                    text: {$titreJs},
                    color: '#C0C0C0',
                    font: { size: 16, family: '"Trebuchet MS", Verdana, sans-serif', weight: 'bold' }
                },
                legend: {
                    labels: { color: '#C0C0C0' }
                },
                tooltip: {
                    callbacks: {
                        title: function(items) {
                            if (!items.length) return '';
                            return new Date(items[0].parsed.x).toLocaleDateString(undefined, {day: 'numeric', month: 'short'});
                        },
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                x: {
                    type: 'linear',
                    ticks: {
                        color: '#A0A0A0',
                        callback: function(value) {
                            return new Date(value).toLocaleDateString(undefined, {day: 'numeric', month: 'short'});
                        }
                    },
                    grid: { color: '#333333' }
                },
                y: {
                    {$yMin}
                    ticks: { color: '#A0A0A0' },
                    grid: { color: '#333333' },
                    title: { display: true, text: {$titreYJs}, color: '#CCC' }
                }
            }
        }
    });
});
</script>
JS;


    return $retour;
}

