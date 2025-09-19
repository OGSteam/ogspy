<?php
/**
 * API de recherche des joueurs
 * @package OGSpy
 * @author Assistant
 * @version 3.3.8
 */

if (!defined('IN_SPYOGAME')) {
    define('IN_SPYOGAME', true);
}

require_once "../common.php";

use Ogsteam\Ogspy\Model\Player_Model;

// Vérifier que l'utilisateur est connecté
if (!isset($user_data) || empty($user_data)) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Récupérer le terme de recherche
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$limit = isset($_GET['limit']) ? max(1, min(50, (int)$_GET['limit'])) : 10;

if (strlen($search_term) < 1) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Search term too short']);
    exit;
}

try {
    $player_model = new Player_Model();
    $players = $player_model->get_all_players($search_term, $limit);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'players' => $players,
        'count' => count($players)
    ]);
} catch (Exception $e) {
    global $log;
    if (isset($log)) {
        $log->error("Error in player search API", ['error' => $e->getMessage()]);
    }
    
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Internal server error']);
}