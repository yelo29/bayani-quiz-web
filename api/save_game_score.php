<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

// Get POST data
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

if ($input === null) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON input', 'raw' => $rawInput]);
    exit;
}

$game_type = $input['game_type'] ?? '';
$score = $input['score'] ?? 0;
$total = $input['total'] ?? 0;
$xp = $input['xp'] ?? 0;
$coins = $input['coins'] ?? 0;

if (empty($game_type)) {
    echo json_encode(['success' => false, 'error' => 'Missing game_type']);
    exit;
}

// Save score to database
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

try {
    if ($user_id) {
        // Logged in user
        $pdo = getDB();

        // Get current time in UTC
        $now = new DateTime('now', new DateTimeZone('UTC'));
        $createdAt = $now->format('Y-m-d H:i:s');

        // Save using total_questions column (same as quiz)
        $stmt = $pdo->prepare("INSERT INTO scores (user_id, game_type, score, total_questions, category_id, created_at) VALUES (?, ?, ?, ?, NULL, ?)");
        $stmt->execute([$user_id, $game_type, $score, $total, $createdAt]);

        // Update XP
        updateUserXP($user_id, $xp);

        // Award coins
        if ($coins > 0) {
            $stmt = $pdo->prepare("UPDATE users SET coins = coins + ? WHERE id = ?");
            $stmt->execute([$coins, $user_id]);
            $_SESSION['coins'] = ($_SESSION['coins'] ?? 0) + $coins;
        }
    } else {
        // Guest user - save to session
        if (!isset($_SESSION['guest_scores'])) {
            $_SESSION['guest_scores'] = [];
        }
        $_SESSION['guest_scores'][$game_type] = [
            'score' => $score,
            'total' => $total,
            'xp' => $xp,
            'coins' => $coins
        ];
    }

    echo json_encode(['success' => true, 'score' => $score, 'total' => $total, 'xp' => $xp, 'coins' => $coins]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
