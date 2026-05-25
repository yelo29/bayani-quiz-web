<?php
ob_start();
error_reporting(0);
ini_set('display_errors', 0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$selected = $data['selected_option'] ?? '';
$correct = $data['correct_option'] ?? '';
$questionId = (int)($data['question_id'] ?? 0);
$funFact = $data['fun_fact'] ?? '';
$categoryId = (int)($data['category_id'] ?? 0);
$isCorrect = $selected === $correct;

if ($isCorrect) {
    $_SESSION['quiz_score']++;
}

$_SESSION['quiz_answers'][$questionId] = [
    'question_id' => $questionId,
    'selected' => $selected,
    'correct' => $correct
];

$_SESSION['quiz_current_index']++;

$_SESSION['last_answer'] = [
    'selected' => $selected,
    'correct' => $correct,
    'is_correct' => $isCorrect,
    'fun_fact' => $funFact
];

session_write_close();

$totalQuestions = count($_SESSION['quiz_questions'] ?? []);
$isLastQuestion = $_SESSION['quiz_current_index'] >= $totalQuestions;

echo json_encode([
    'success' => true,
    'is_correct' => $isCorrect,
    'fun_fact' => $funFact,
    'is_last_question' => $isLastQuestion,
    'new_score' => $_SESSION['quiz_score'],
    'new_index' => $_SESSION['quiz_current_index'],
    'redirect' => $isLastQuestion
        ? 'results.php'
        : 'quiz.php?category=' . $categoryId
]);