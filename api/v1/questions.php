<?php
error_reporting(0);
ini_set('display_errors', 0);

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

try {
    $db = getDB();

    $category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
    $difficulty   = isset($_GET['difficulty'])  ? $_GET['difficulty']        : null;
    $limit        = isset($_GET['limit'])        ? (int)$_GET['limit']       : 10;

    // Cap limit at 50 to prevent abuse
    if ($limit > 50) $limit = 50;

    $query  = "SELECT id, category_id, question, option_a, option_b, option_c, option_d, difficulty, fun_fact 
               FROM questions WHERE 1=1";
    $params = [];

    if ($category_id) {
        $query   .= " AND category_id = ?";
        $params[] = $category_id;
    }

    if ($difficulty && in_array($difficulty, ['easy', 'medium', 'hard'])) {
        $query   .= " AND difficulty = ?";
        $params[] = $difficulty;
    }

    $query   .= " ORDER BY RAND() LIMIT ?";
    $params[] = $limit;

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    api_response(['success' => true, 'count' => count($questions), 'data' => $questions]);

} catch (PDOException $e) {
    api_response(['error' => 'Database error', 'message' => $e->getMessage()], 500);
}