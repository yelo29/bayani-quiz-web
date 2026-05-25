<?php
error_reporting(0);
ini_set('display_errors', 0);

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/includes/api_auth.php';
require_once __DIR__ . '/includes/api_response.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // GET - Return random enemy by region
    $region = isset($_GET['region']) ? $_GET['region'] : 'spanish_colonial';
    
    $query = "SELECT id, name, era, hp, attack, defense, region 
              FROM enemies 
              WHERE region = ? 
              ORDER BY RAND() 
              LIMIT 1";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$region]);
    $enemy = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$enemy) {
        api_response(['error' => 'No enemy found for this region'], 404);
    }
    
    api_response(['success' => true, 'data' => $enemy]);
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POST - Process battle round
    api_auth_check();
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    $enemy_id = isset($input['enemy_id']) ? (int)$input['enemy_id'] : null;
    $answer = isset($input['answer']) ? $input['answer'] : null;
    $question_id = isset($input['question_id']) ? (int)$input['question_id'] : null;
    
    if (!$enemy_id || !$answer || !$question_id) {
        api_response(['error' => 'Missing required parameters'], 400);
    }
    
    // Get question and check answer
    $stmt = $db->prepare("SELECT correct_option FROM questions WHERE id = ?");
    $stmt->execute([$question_id]);
    $question = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$question) {
        api_response(['error' => 'Question not found'], 404);
    }
    
    $is_correct = ($answer === $question['correct_option']);
    
    // Get enemy stats
    $stmt = $db->prepare("SELECT hp, attack, defense FROM enemies WHERE id = ?");
    $stmt->execute([$enemy_id]);
    $enemy = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get player stats
    $stmt = $db->prepare("SELECT player_hp, player_max_hp, player_attack, player_defense FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $player = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Calculate damage
    if ($is_correct) {
        $damage_to_enemy = max(5, $player['player_attack'] - $enemy['defense']);
        $damage_to_player = 0;
    } else {
        $damage_to_enemy = 0;
        $damage_to_player = max(5, $enemy['attack'] - $player['player_defense']);
    }
    
    // Update enemy HP (in session for now, could be stored in battle_sessions table)
    if (!isset($_SESSION['battle_enemy_hp'])) {
        $_SESSION['battle_enemy_hp'] = $enemy['hp'];
    }
    
    $_SESSION['battle_enemy_hp'] -= $damage_to_enemy;
    $new_player_hp = max(0, $player['player_hp'] - $damage_to_player);
    
    // Update player HP in database
    $stmt = $db->prepare("UPDATE users SET player_hp = ? WHERE id = ?");
    $stmt->execute([$new_player_hp, $_SESSION['user_id']]);
    
    // Check if battle ended
    $enemy_defeated = $_SESSION['battle_enemy_hp'] <= 0;
    $player_defeated = $new_player_hp <= 0;
    
    $result = [
        'success' => true,
        'is_correct' => $is_correct,
        'damage_to_enemy' => $damage_to_enemy,
        'damage_to_player' => $damage_to_player,
        'enemy_hp_remaining' => max(0, $_SESSION['battle_enemy_hp']),
        'player_hp_remaining' => $new_player_hp,
        'enemy_defeated' => $enemy_defeated,
        'player_defeated' => $player_defeated
    ];
    
    if ($enemy_defeated) {
        // Award XP and coins
        $xp_award = 50;
        $coin_award = 20;
        
        $stmt = $db->prepare("UPDATE users SET xp = xp + ?, coins = coins + ? WHERE id = ?");
        $stmt->execute([$xp_award, $coin_award, $_SESSION['user_id']]);
        
        // Update battle stats
        $stmt = $db->prepare("UPDATE battle_stats SET battle_wins = battle_wins + 1, battles_played = battles_played + 1 WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        
        $result['xp_awarded'] = $xp_award;
        $result['coins_awarded'] = $coin_award;
        
        // Clear battle session
        unset($_SESSION['battle_enemy_hp']);
    } elseif ($player_defeated) {
        // Restore player HP to 50%
        $restored_hp = ceil($player['player_max_hp'] * 0.5);
        $stmt = $db->prepare("UPDATE users SET player_hp = ? WHERE id = ?");
        $stmt->execute([$restored_hp, $_SESSION['user_id']]);
        
        // Update battle stats
        $stmt = $db->prepare("UPDATE battle_stats SET battles_played = battles_played + 1 WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        
        // Clear battle session
        unset($_SESSION['battle_enemy_hp']);
    }
    
    api_response($result);
    
} else {
    api_response(['error' => 'Method not allowed'], 405);
}
