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

    $query = "SELECT c.id, c.name, c.description, c.icon, c.color, COUNT(q.id) as question_count 
             FROM categories c 
             LEFT JOIN questions q ON c.id = q.category_id 
             GROUP BY c.id 
             ORDER BY c.id";

    $stmt = $db->query($query);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    api_response(['success' => true, 'data' => $categories]);

} catch (PDOException $e) {
    api_response(['error' => 'Database error', 'message' => $e->getMessage()], 500);
}