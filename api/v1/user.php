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

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    api_response(['error' => 'Method not allowed'], 405);
}

api_auth_check();

$db = getDB();

// Get user stats
$stmt = $db->prepare("SELECT id, username, email, hero_class, level, xp, coins, 
                             player_hp, player_max_hp, player_attack, player_defense,
                             created_at 
                      FROM users 
                      WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    api_response(['error' => 'User not found'], 404);
}

// Get equipped items
$stmt = $db->prepare("SELECT i.id, i.name, i.type, i.power, i.rarity 
                      FROM user_items ui 
                      JOIN items i ON ui.item_id = i.id 
                      WHERE ui.user_id = ? AND ui.equipped = 1");
$stmt->execute([$_SESSION['user_id']]);
$equipped_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate bonuses
$weapon_bonus = 0;
$armor_bonus = 0;
$magic_bonus = 0;

foreach ($equipped_items as $item) {
    if ($item['type'] === 'weapon') {
        $weapon_bonus = $item['power'];
    } elseif ($item['type'] === 'armor') {
        $armor_bonus = $item['power'];
    } elseif ($item['type'] === 'magic') {
        $magic_bonus = $item['power'];
    }
}

$stats = [
    'id' => $user['id'],
    'username' => $user['username'],
    'email' => $user['email'],
    'hero_class' => $user['hero_class'],
    'level' => $user['level'],
    'xp' => $user['xp'],
    'coins' => $user['coins'],
    'hp' => $user['player_hp'],
    'max_hp' => $user['player_max_hp'],
    'attack' => $user['player_attack'],
    'defense' => $user['player_defense'],
    'weapon_bonus' => $weapon_bonus,
    'armor_bonus' => $armor_bonus,
    'magic_bonus' => $magic_bonus,
    'equipped_items' => $equipped_items,
    'created_at' => $user['created_at']
];

api_response(['success' => true, 'data' => $stats]);
