<?php
$baseUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/api/v1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bayani World API v1 Documentation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .code-block {
            background: #1e293b;
            border-radius: 8px;
            padding: 16px;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            color: #e2e8f0;
        }
        .method-get { background: #10b981; }
        .method-post { background: #3b82f6; }
        .method-put { background: #f59e0b; }
        .method-delete { background: #ef4444; }
    </style>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-[#0038A8] mb-2">
                <i class="fas fa-code mr-3"></i>Bayani World API
            </h1>
            <p class="text-xl text-gray-400">REST API v1 Documentation</p>
            <div class="mt-4 flex justify-center gap-4">
                <span class="px-3 py-1 bg-[#0038A8] rounded-full text-sm">Version 1.0</span>
                <span class="px-3 py-1 bg-green-600 rounded-full text-sm">Production Ready</span>
            </div>
        </div>

        <!-- Base URL -->
        <div class="bg-gray-800 rounded-xl p-6 mb-8">
            <h2 class="text-xl font-bold mb-3 text-[#0038A8]"><i class="fas fa-server mr-2"></i>Base URL</h2>
            <code class="text-green-400 text-lg"><?php echo $baseUrl; ?></code>
        </div>

        <!-- Authentication -->
        <div class="bg-gray-800 rounded-xl p-6 mb-8">
            <h2 class="text-xl font-bold mb-3 text-[#0038A8]"><i class="fas fa-lock mr-2"></i>Authentication</h2>
            <p class="text-gray-300 mb-4">Most endpoints require session-based authentication. Use the <code class="bg-gray-700 px-2 py-1 rounded">/auth</code> endpoint to login.</p>
            <div class="bg-yellow-900/30 border border-yellow-600 rounded-lg p-4">
                <p class="text-yellow-400"><i class="fas fa-exclamation-triangle mr-2"></i>Protected endpoints return <code class="bg-gray-700 px-2 py-1 rounded">401 Unauthorized</code> if session is invalid.</p>
            </div>
        </div>

        <!-- Endpoints -->
        <div class="space-y-6">
            <!-- Auth Endpoint -->
            <div class="bg-gray-800 rounded-xl p-6">
                <div class="flex items-center gap-3 mb-4">
                    <span class="method-post px-3 py-1 rounded font-bold text-white text-sm">POST</span>
                    <span class="method-get px-3 py-1 rounded font-bold text-white text-sm">GET</span>
                    <code class="text-xl text-green-400">/auth</code>
                </div>
                <h3 class="text-2xl font-bold mb-3">Authentication</h3>
                <p class="text-gray-300 mb-4">Login to establish session or check current session status.</p>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-bold text-[#0038A8] mb-2">POST - Login</h4>
                        <p class="text-sm text-gray-400 mb-2">Body (JSON):</p>
                        <div class="code-block mb-4">
{
    "username": "your_username",
    "password": "your_password"
}
                        </div>
                        <p class="text-sm text-gray-400 mb-2">Response (200):</p>
                        <div class="code-block">
{
    "success": true,
    "data": {
        "id": 1,
        "username": "your_username",
        "hero_class": "mandirigma",
        "level": 5,
        "xp": 1500,
        "coins": 100
    },
    "message": "Login successful"
}
                        </div>
                    </div>
                    <div>
                        <h4 class="font-bold text-[#0038A8] mb-2">GET - Session Status</h4>
                        <p class="text-sm text-gray-400 mb-2">Response (200):</p>
                        <div class="code-block">
{
    "success": true,
    "authenticated": true,
    "data": {
        "id": 1,
        "username": "your_username",
        "hero_class": "mandirigma"
    }
}
                        </div>
                        <p class="text-sm text-gray-400 mt-4">Response (401):</p>
                        <div class="code-block">
{
    "success": false,
    "authenticated": false
}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Questions Endpoint -->
            <div class="bg-gray-800 rounded-xl p-6">
                <div class="flex items-center gap-3 mb-4">
                    <span class="method-get px-3 py-1 rounded font-bold text-white text-sm">GET</span>
                    <code class="text-xl text-green-400">/questions</code>
                </div>
                <h3 class="text-2xl font-bold mb-3">Questions</h3>
                <p class="text-gray-300 mb-4">Retrieve quiz questions with optional filters.</p>
                
                <div class="mb-4">
                    <h4 class="font-bold text-[#0038A8] mb-2">Query Parameters</h4>
                    <ul class="list-disc list-inside text-gray-400 space-y-1">
                        <li><code class="bg-gray-700 px-2 py-1 rounded">category_id</code> (optional) - Filter by category ID</li>
                        <li><code class="bg-gray-700 px-2 py-1 rounded">difficulty</code> (optional) - Filter by difficulty (easy, medium, hard)</li>
                        <li><code class="bg-gray-700 px-2 py-1 rounded">limit</code> (optional) - Number of questions to return (default: 10)</li>
                    </ul>
                </div>
                
                <p class="text-sm text-gray-400 mb-2">Example: <code class="bg-gray-700 px-2 py-1 rounded">/questions?category_id=1&difficulty=medium&limit=5</code></p>
                <p class="text-sm text-gray-400 mb-2">Response (200):</p>
                <div class="code-block">
{
    "success": true,
    "data": [
        {
            "id": 1,
            "category_id": 1,
            "question": "What year did the Philippine Revolution start?",
            "option_a": "1896",
            "option_b": "1898",
            "option_c": "1900",
            "option_d": "1890",
            "difficulty": "medium",
            "era": "spanish_colonial"
        }
    ]
}
                </div>
            </div>

            <!-- Categories Endpoint -->
            <div class="bg-gray-800 rounded-xl p-6">
                <div class="flex items-center gap-3 mb-4">
                    <span class="method-get px-3 py-1 rounded font-bold text-white text-sm">GET</span>
                    <code class="text-xl text-green-400">/categories</code>
                </div>
                <h3 class="text-2xl font-bold mb-3">Categories</h3>
                <p class="text-gray-300 mb-4">Retrieve all quiz categories with question counts.</p>
                
                <p class="text-sm text-gray-400 mb-2">Response (200):</p>
                <div class="code-block">
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Spanish Colonial Era",
            "description": "Questions about Spanish colonization period",
            "era": "spanish_colonial",
            "question_count": 25
        }
    ]
}
                </div>
            </div>

            <!-- Leaderboard Endpoint -->
            <div class="bg-gray-800 rounded-xl p-6">
                <div class="flex items-center gap-3 mb-4">
                    <span class="method-get px-3 py-1 rounded font-bold text-white text-sm">GET</span>
                    <code class="text-xl text-green-400">/leaderboard</code>
                </div>
                <h3 class="text-2xl font-bold mb-3">Leaderboard</h3>
                <p class="text-gray-300 mb-4">Retrieve top 10 players for quiz or battle rankings.</p>
                
                <div class="mb-4">
                    <h4 class="font-bold text-[#0038A8] mb-2">Query Parameters</h4>
                    <ul class="list-disc list-inside text-gray-400 space-y-1">
                        <li><code class="bg-gray-700 px-2 py-1 rounded">type</code> (optional) - "quiz" or "battle" (default: quiz)</li>
                    </ul>
                </div>
                
                <p class="text-sm text-gray-400 mb-2">Example: <code class="bg-gray-700 px-2 py-1 rounded">/leaderboard?type=battle</code></p>
                <p class="text-sm text-gray-400 mb-2">Response (200):</p>
                <div class="code-block">
{
    "success": true,
    "data": [
        {
            "username": "player1",
            "hero_class": "mandirigma",
            "battle_wins": 45,
            "battles_played": 50,
            "win_rate": 90.0
        }
    ],
    "type": "battle"
}
                </div>
            </div>

            <!-- Battle Endpoint -->
            <div class="bg-gray-800 rounded-xl p-6">
                <div class="flex items-center gap-3 mb-4">
                    <span class="method-get px-3 py-1 rounded font-bold text-white text-sm">GET</span>
                    <span class="method-post px-3 py-1 rounded font-bold text-white text-sm">POST</span>
                    <code class="text-xl text-green-400">/battle</code>
                </div>
                <h3 class="text-2xl font-bold mb-3">Battle</h3>
                <p class="text-gray-300 mb-4">Get random enemy or process battle round. <span class="text-red-400">Requires authentication.</span></p>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-bold text-[#0038A8] mb-2">GET - Random Enemy</h4>
                        <p class="text-sm text-gray-400 mb-2">Query Parameters:</p>
                        <ul class="list-disc list-inside text-gray-400 space-y-1 mb-4">
                            <li><code class="bg-gray-700 px-2 py-1 rounded">region</code> (optional) - Enemy region (default: spanish_colonial)</li>
                        </ul>
                        <p class="text-sm text-gray-400 mb-2">Response (200):</p>
                        <div class="code-block">
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Spanish Soldier",
        "era": "spanish_colonial",
        "hp": 100,
        "attack": 25,
        "defense": 10,
        "region": "spanish_colonial"
    }
}
                        </div>
                    </div>
                    <div>
                        <h4 class="font-bold text-[#0038A8] mb-2">POST - Battle Round</h4>
                        <p class="text-sm text-gray-400 mb-2">Body (JSON):</p>
                        <div class="code-block mb-4">
{
    "enemy_id": 1,
    "answer": "A",
    "question_id": 5
}
                        </div>
                        <p class="text-sm text-gray-400 mb-2">Response (200):</p>
                        <div class="code-block">
{
    "success": true,
    "is_correct": true,
    "damage_to_enemy": 15,
    "damage_to_player": 0,
    "enemy_hp_remaining": 85,
    "player_hp_remaining": 100,
    "enemy_defeated": false,
    "player_defeated": false
}
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Endpoint -->
            <div class="bg-gray-800 rounded-xl p-6">
                <div class="flex items-center gap-3 mb-4">
                    <span class="method-get px-3 py-1 rounded font-bold text-white text-sm">GET</span>
                    <code class="text-xl text-green-400">/user</code>
                </div>
                <h3 class="text-2xl font-bold mb-3">User Stats</h3>
                <p class="text-gray-300 mb-4">Retrieve current user's stats and equipped items. <span class="text-red-400">Requires authentication.</span></p>
                
                <p class="text-sm text-gray-400 mb-2">Response (200):</p>
                <div class="code-block">
{
    "success": true,
    "data": {
        "id": 1,
        "username": "player1",
        "email": "player@example.com",
        "hero_class": "mandirigma",
        "level": 5,
        "xp": 1500,
        "coins": 100,
        "hp": 100,
        "max_hp": 100,
        "attack": 20,
        "defense": 10,
        "weapon_bonus": 5,
        "armor_bonus": 3,
        "magic_bonus": 0,
        "equipped_items": [
            {
                "id": 1,
                "name": "Bolo",
                "type": "weapon",
                "power": 5,
                "rarity": "common"
            }
        ],
        "created_at": "2024-01-01 00:00:00"
    }
}
                </div>
            </div>
        </div>

        <!-- Error Responses -->
        <div class="bg-gray-800 rounded-xl p-6 mt-8">
            <h2 class="text-xl font-bold mb-3 text-[#0038A8]"><i class="fas fa-exclamation-circle mr-2"></i>Error Responses</h2>
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <span class="bg-red-600 px-2 py-1 rounded text-sm font-bold">400</span>
                    <code class="text-gray-400">{"error": "Bad Request"}</code>
                </div>
                <div class="flex items-center gap-3">
                    <span class="bg-red-600 px-2 py-1 rounded text-sm font-bold">401</span>
                    <code class="text-gray-400">{"error": "Unauthorized"}</code>
                </div>
                <div class="flex items-center gap-3">
                    <span class="bg-red-600 px-2 py-1 rounded text-sm font-bold">404</span>
                    <code class="text-gray-400">{"error": "Not Found"}</code>
                </div>
                <div class="flex items-center gap-3">
                    <span class="bg-red-600 px-2 py-1 rounded text-sm font-bold">405</span>
                    <code class="text-gray-400">{"error": "Method not allowed"}</code>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-12 text-gray-500">
            <p>Bayani World API v1.0 | Built with PHP & MySQL</p>
            <p class="mt-2"><i class="fas fa-heart text-red-500"></i> Philippine History Educational Game</p>
        </div>
    </div>
</body>
</html>
