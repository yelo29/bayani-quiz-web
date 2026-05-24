<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}
?>

<style>
    .construction-container {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .construction-card {
        background: white;
        border-radius: 20px;
        padding: 60px 40px;
        text-align: center;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        max-width: 600px;
    }

    .construction-icon {
        font-size: 80px;
        margin-bottom: 20px;
        animation: bounce 2s infinite;
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }

    .construction-title {
        font-size: 36px;
        font-weight: bold;
        color: #333;
        margin-bottom: 15px;
    }

    .construction-subtitle {
        font-size: 18px;
        color: #666;
        margin-bottom: 30px;
    }

    .construction-features {
        text-align: left;
        margin-bottom: 30px;
    }

    .construction-features li {
        padding: 10px 0;
        color: #555;
        display: flex;
        align-items: center;
    }


    .back-btn {
        display: inline-block;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px 40px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: bold;
        transition: transform 0.3s ease;
    }

    .back-btn:hover {
        transform: scale(1.05);
    }
</style>

<div class="construction-container">
    <div class="construction-card">
        <div class="construction-icon">🚧</div>
        <h1 class="construction-title">Kwento Mode</h1>
        <p class="construction-subtitle">Under Construction</p>
        
        <ul class="construction-features">
            <li>2D Story RPG Experience</li>
            <li>Travel Through Philippine History</li>
            <li>Turn-Based Battle System</li>
            <li>NPC Interactions & Quests</li>
            <li>Multiple Historical Eras</li>
        </ul>

        <a href="/maglaro.php" class="back-btn">
            ← Bumalik sa Mode Selection
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
