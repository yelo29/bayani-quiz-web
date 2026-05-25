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

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    api_response(['error' => 'Method not allowed'], 405);
}

$type = isset($_GET['type']) ? $_GET['type'] : 'quiz';
$db = getDB();

if ($type === 'battle') {
    $query = "SELECT u.username, u.hero_class, b.battle_wins, b.battles_played, 
                     ROUND((b.battle_wins / NULLIF(b.battles_played, 0)) * 100, 1) as win_rate
              FROM users u
              JOIN battle_stats b ON u.id = b.user_id
              ORDER BY b.battle_wins DESC
              LIMIT 10";
} else {
    $query = "SELECT u.username, u.hero_class, q.quiz_score, q.quizzes_taken,
                     ROUND((q.quiz_score / NULLIF(q.quizzes_taken, 0)), 1) as avg_score
              FROM users u
              JOIN quiz_stats q ON u.id = q.user_id
              ORDER BY q.quiz_score DESC
              LIMIT 10";
}

$stmt = $db->query($query);
$leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);

api_response(['success' => true, 'data' => $leaderboard, 'type' => $type]);
