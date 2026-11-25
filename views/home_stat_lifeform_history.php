<?php

if (!defined('IN_SPYOGAME')) {
    die('Hacking attempt');
}

$lifeform_history_labels = [
    'title' => $lang['HOME_STATS_LIFEFORM_HISTORY'] ?? 'Population and food history',
    'planet' => $lang['HOME_STATS_LIFEFORM_HISTORY_PLANET'] ?? 'Planet',
    'period' => $lang['HOME_STATS_LIFEFORM_HISTORY_PERIOD'] ?? 'Period',
    'days' => $lang['HOME_STATS_LIFEFORM_HISTORY_DAYS'] ?? '%d days',
    'no_data' => $lang['HOME_STATS_LIFEFORM_HISTORY_NO_DATA'] ?? 'Not enough history to display a trend',
    'population' => $lang['HOME_EMPIRE_LIFEFORM_POPULATION'] ?? 'Population',
    'food' => $lang['HOME_EMPIRE_LIFEFORM_FOOD'] ?? 'Food',
];

$lifeform_history_planets = [];
foreach ($player_planets as $planet_id => $planet) {
    $planet_label = trim((string)($planet['name'] ?? ''));
    if ($planet_label === '') {
        $planet_label = sprintf(
            '[%s:%s:%s]',
            $planet['galaxy'] ?? '-',
            $planet['system'] ?? '-',
            $planet['row'] ?? '-'
        );
    }

    $points = [];
    foreach ($player_lifeform_history[$planet_id] ?? [] as $observation) {
        $points[] = [
            'date' => $observation['observation_day'],
            'population' => $observation['population'],
            'food' => $observation['food'],
        ];
    }

    $lifeform_history_planets[] = [
        'label' => $planet_label,
        'points' => $points,
    ];
}
?>
<table class="og-table og-full-table og-table-ranking">
    <thead>
    <tr>
        <th><?php echo htmlspecialchars($lifeform_history_labels['title'], ENT_QUOTES, 'UTF-8'); ?></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>
            <div id="lifeform-resource-history">
                <p>
                    <label for="lifeform-history-planet">
                        <?php echo htmlspecialchars($lifeform_history_labels['planet'], ENT_QUOTES, 'UTF-8'); ?>
                    </label>
                    <select id="lifeform-history-planet">
                        <?php foreach ($lifeform_history_planets as $planet_index => $planet_history): ?>
                            <option value="<?php echo $planet_index; ?>">
                                <?php echo htmlspecialchars($planet_history['label'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span><?php echo htmlspecialchars($lifeform_history_labels['period'], ENT_QUOTES, 'UTF-8'); ?>:</span>
                    <?php foreach ([7, 30, 90] as $history_days): ?>
                        <button type="button" class="og-button og-button-little lifeform-history-period"
                                data-days="<?php echo $history_days; ?>"
                                aria-pressed="<?php echo $history_days === 30 ? 'true' : 'false'; ?>">
                            <?php echo htmlspecialchars(
                                sprintf($lifeform_history_labels['days'], $history_days),
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>
                        </button>
                    <?php endforeach; ?>
                </p>
                <p id="lifeform-history-no-data" class="og-alert" role="status" hidden>
                    <?php echo htmlspecialchars($lifeform_history_labels['no_data'], ENT_QUOTES, 'UTF-8'); ?>
                </p>
                <div style="min-height: 280px;">
                    <canvas id="lifeform-history-chart"></canvas>
                </div>
            </div>
            <script type="application/json" id="lifeform-history-data"><?php
                echo json_encode(
                    $lifeform_history_planets,
                    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR
                );
            ?></script>
            <script type="text/javascript">
            (function () {
                'use strict';

                const dataElement = document.getElementById('lifeform-history-data');
                const planetSelect = document.getElementById('lifeform-history-planet');
                const canvas = document.getElementById('lifeform-history-chart');
                const noData = document.getElementById('lifeform-history-no-data');
                const periodButtons = document.querySelectorAll('.lifeform-history-period');
                const planets = JSON.parse(dataElement.textContent || '[]');
                let selectedDays = 30;
                let chart = null;

                function formatInteger(value) {
                    if (value === null || value === undefined) {
                        return '-';
                    }
                    try {
                        return new Intl.NumberFormat().format(BigInt(value));
                    } catch {
                        return String(value);
                    }
                }

                function renderHistory() {
                    const planet = planets[Number(planetSelect.value)] || {points: []};
                    const today = new Date();
                    const startOfToday = Date.UTC(today.getUTCFullYear(), today.getUTCMonth(), today.getUTCDate());
                    const cutoff = startOfToday - ((selectedDays - 1) * 86400000);
                    const points = planet.points.filter(function (point) {
                        return Date.parse(point.date + 'T00:00:00Z') >= cutoff;
                    });

                    if (chart !== null) {
                        chart.destroy();
                        chart = null;
                    }

                    const datedPoints = points.filter(function (point) {
                        return point.population !== null || point.food !== null;
                    });
                    const population = datedPoints
                        .filter(function (point) { return point.population !== null; })
                        .map(function (point) {
                            return {x: Date.parse(point.date + 'T00:00:00Z'), y: point.population};
                        });
                    const food = datedPoints
                        .filter(function (point) { return point.food !== null; })
                        .map(function (point) {
                            return {x: Date.parse(point.date + 'T00:00:00Z'), y: point.food};
                        });
                    const hasTrend = population.length >= 2 || food.length >= 2;
                    noData.hidden = hasTrend;
                    canvas.hidden = !hasTrend;
                    if (!hasTrend) {
                        return;
                    }

                    chart = new Chart(canvas, {
                        type: 'line',
                        data: {
                            datasets: [
                                {
                                    label: <?php echo json_encode($lifeform_history_labels['population']); ?>,
                                    data: population,
                                    borderColor: '#4da6ff',
                                    backgroundColor: 'transparent',
                                    yAxisID: 'population',
                                    tension: 0.1
                                },
                                {
                                    label: <?php echo json_encode($lifeform_history_labels['food']); ?>,
                                    data: food,
                                    borderColor: '#f0a000',
                                    backgroundColor: 'transparent',
                                    yAxisID: 'food',
                                    tension: 0.1
                                }
                            ]
                        },
                        options: {
                            parsing: true,
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {mode: 'index', intersect: false},
                            plugins: {
                                legend: {labels: {color: '#C0C0C0'}},
                                tooltip: {
                                    callbacks: {
                                        label: function (context) {
                                            return context.dataset.label + ': ' + formatInteger(context.raw.y);
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    type: 'linear',
                                    ticks: {
                                        color: '#A0A0A0',
                                        callback: function (value) {
                                            return new Date(value).toLocaleDateString();
                                        }
                                    },
                                    grid: {color: '#333333'}
                                },
                                population: {
                                    beginAtZero: true,
                                    position: 'left',
                                    ticks: {color: '#4da6ff', precision: 0},
                                    grid: {color: '#333333'}
                                },
                                food: {
                                    beginAtZero: true,
                                    position: 'right',
                                    ticks: {color: '#f0a000', precision: 0},
                                    grid: {drawOnChartArea: false}
                                }
                            }
                        }
                    });
                }

                planetSelect.addEventListener('change', renderHistory);
                periodButtons.forEach(function (button) {
                    button.addEventListener('click', function () {
                        selectedDays = Number(button.dataset.days);
                        periodButtons.forEach(function (periodButton) {
                            periodButton.setAttribute('aria-pressed', periodButton === button ? 'true' : 'false');
                        });
                        renderHistory();
                    });
                });

                const firstPlanetWithHistory = planets.findIndex(function (planet) {
                    return planet.points.length >= 2;
                });
                if (firstPlanetWithHistory >= 0) {
                    planetSelect.value = String(firstPlanetWithHistory);
                }
                renderHistory();
            }());
            </script>
        </td>
    </tr>
    </tbody>
</table>
