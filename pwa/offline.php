<?php
error_reporting(0);
ini_set('display_errors', 0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../includes/header.php';
?>

<main class="min-h-screen bg-gray-50 py-8 px-4 flex items-center justify-center">
    <div class="max-w-lg w-full">
        <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
            <div class="w-24 h-24 bg-[#0038A8] rounded-full mx-auto mb-6 flex items-center justify-center">
                <i class="fas fa-wifi-slash text-white text-4xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-[#0038A8] mb-4">You're Offline</h2>
            <p class="text-gray-600 mb-6">
                It looks like you're not connected to the internet. Please check your connection and try again.
            </p>
            <div class="bg-yellow-50 border-2 border-yellow-400 rounded-xl p-4 mb-6">
                <p class="text-yellow-800 text-sm">
                    <i class="fas fa-info-circle mr-2"></i>
                    Some features may not work while offline. Make sure you're connected to play quizzes and save your progress.
                </p>
            </div>
            <button onclick="location.reload()" class="bg-[#0038A8] text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-[#002870] transition">
                <i class="fas fa-sync-alt mr-2"></i> Try Again
            </button>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
