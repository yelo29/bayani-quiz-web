<?php
error_reporting(0);
ini_set('display_errors', 0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/db.php';

// Get current page for active state
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Dashboard'; ?> - Bayani World Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-900 text-gray-100">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 border-r border-gray-700 flex flex-col">
            <!-- Logo -->
            <div class="p-6 border-b border-gray-700">
                <h1 class="text-2xl font-bold text-[#0038A8]">
                    <i class="fas fa-shield-halved mr-2"></i>Bayani World
                </h1>
                <p class="text-gray-400 text-sm mt-1">Admin Dashboard</p>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="/dashboard/index.php" 
                           class="flex items-center px-4 py-3 rounded-lg transition <?php echo $current_page === 'index.php' ? 'bg-[#0038A8] text-white' : 'text-gray-300 hover:bg-gray-700'; ?>">
                            <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="/dashboard/users.php" 
                           class="flex items-center px-4 py-3 rounded-lg transition <?php echo $current_page === 'users.php' ? 'bg-[#0038A8] text-white' : 'text-gray-300 hover:bg-gray-700'; ?>">
                            <i class="fas fa-users mr-3"></i>Users
                        </a>
                    </li>
                    <li>
                        <a href="/dashboard/questions.php" 
                           class="flex items-center px-4 py-3 rounded-lg transition <?php echo $current_page === 'questions.php' ? 'bg-[#0038A8] text-white' : 'text-gray-300 hover:bg-gray-700'; ?>">
                            <i class="fas fa-question-circle mr-3"></i>Questions
                        </a>
                    </li>
                    <li>
                        <a href="/dashboard/analytics.php" 
                           class="flex items-center px-4 py-3 rounded-lg transition <?php echo $current_page === 'analytics.php' ? 'bg-[#0038A8] text-white' : 'text-gray-300 hover:bg-gray-700'; ?>">
                            <i class="fas fa-chart-line mr-3"></i>Analytics
                        </a>
                    </li>
                    <li>
                        <a href="/dashboard/items.php" 
                           class="flex items-center px-4 py-3 rounded-lg transition <?php echo $current_page === 'items.php' ? 'bg-[#0038A8] text-white' : 'text-gray-300 hover:bg-gray-700'; ?>">
                            <i class="fas fa-box mr-3"></i>Items
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- Admin Info -->
            <div class="p-4 border-t border-gray-700">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-[#0038A8] rounded-full flex items-center justify-center">
                        <i class="fas fa-user-shield text-white"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-bold text-white"><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></p>
                        <p class="text-xs text-gray-400">Administrator</p>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="flex-1 flex flex-col">
            <!-- Top Bar -->
            <header class="bg-gray-800 border-b border-gray-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-white"><?php echo $page_title ?? 'Dashboard'; ?></h2>
                    <a href="/dashboard/logout.php" class="text-gray-300 hover:text-red-400 transition">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </header>
            
            <!-- Content Area -->
            <div class="flex-1 p-6 overflow-auto">
