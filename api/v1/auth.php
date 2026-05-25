<?php
error_reporting(0);
ini_set('display_errors', 0);

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/includes/api_response.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POST - Login
    $input = json_decode(file_get_contents('php://input'), true);
    
    $username = isset($input['username']) ? trim($input['username']) : '';
    $password = isset($input['password']) ? $input['password'] : '';
    
    if (!$username || !$password) {
        api_response(['error' => 'Username and password required'], 400);
    }
    
    $stmt = $db->prepare("SELECT id, username, email, password, hero_class, level, xp, coins, 
                                 player_hp, player_max_hp, player_attack, player_defense
                          FROM users 
                          WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user || !password_verify($password, $user['password'])) {
        api_response(['error' => 'Invalid credentials'], 401);
    }
    
    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['hero_class'] = $user['hero_class'];
    
    // Return user stats (without password)
    unset($user['password']);
    
    api_response(['success' => true, 'data' => $user, 'message' => 'Login successful']);
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // GET - Check session status
    if (!isset($_SESSION['user_id'])) {
        api_response(['success' => false, 'authenticated' => false]);
    }
    
    $stmt = $db->prepare("SELECT id, username, email, hero_class, level, xp, coins 
                          FROM users 
                          WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        api_response(['success' => false, 'authenticated' => false]);
    }
    
    api_response(['success' => true, 'authenticated' => true, 'data' => $user]);
    
} else {
    api_response(['error' => 'Method not allowed'], 405);
}
