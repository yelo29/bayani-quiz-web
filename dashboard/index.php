<?php
error_reporting(0);
ini_set('display_errors', 0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$page_title = 'Dashboard';

$db = getDB();

// Get stats with error handling
try {
    $total_users     = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $total_questions = $db->query("SELECT COUNT(*) FROM questions")->fetchColumn();
    $battles_today   = $db->query("SELECT COUNT(*) FROM battle_log WHERE DATE(created_at) = CURDATE()")->fetchColumn();
    $quiz_today      = $db->query("SELECT COUNT(*) FROM scores WHERE DATE(created_at) = CURDATE()")->fetchColumn();
    $new_users_week  = $db->query("SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();

    $popular_category = $db->query("
        SELECT c.name, COUNT(s.id) as plays 
        FROM scores s 
        JOIN categories c ON s.category_id = c.id 
        GROUP BY c.id 
        ORDER BY plays DESC 
        LIMIT 1
    ")->fetch(PDO::FETCH_ASSOC);

    // 7-day user registration data
    $registration_data = [];
    for ($i = 6; $i >= 0; $i--) {
        $date  = date('Y-m-d', strtotime("-$i days"));
        $count = $db->query("SELECT COUNT(*) FROM users WHERE DATE(created_at) = '$date'")->fetchColumn();
        $registration_data[] = [
            'date'  => date('M j', strtotime($date)),
            'count' => (int)$count
        ];
    }

    // Category distribution
    $category_dist = $db->query("
        SELECT c.name, COUNT(q.id) as count 
        FROM categories c 
        LEFT JOIN questions q ON c.id = q.category_id 
        GROUP BY c.id
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $total_users = $total_questions = $battles_today = $quiz_today = $new_users_week = 0;
    $popular_category  = ['name' => 'N/A', 'plays' => 0];
    $registration_data = [];
    $category_dist     = [];
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="space-y-6">
    <!-- Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Total Users</p>
                    <p class="text-3xl font-bold text-white"><?php echo number_format($total_users); ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-500/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-blue-500 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Total Questions</p>
                    <p class="text-3xl font-bold text-white"><?php echo number_format($total_questions); ?></p>
                </div>
                <div class="w-12 h-12 bg-green-500/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-question-circle text-green-500 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Battles Today</p>
                    <p class="text-3xl font-bold text-white"><?php echo number_format($battles_today); ?></p>
                </div>
                <div class="w-12 h-12 bg-red-500/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-swords text-red-500 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Quiz Games Today</p>
                    <p class="text-3xl font-bold text-white"><?php echo number_format($quiz_today); ?></p>
                </div>
                <div class="w-12 h-12 bg-purple-500/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-book-open text-purple-500 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">New Users (7 Days)</p>
                    <p class="text-3xl font-bold text-white"><?php echo number_format($new_users_week); ?></p>
                </div>
                <div class="w-12 h-12 bg-yellow-500/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-plus text-yellow-500 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Popular Category</p>
                    <p class="text-xl font-bold text-white"><?php echo htmlspecialchars($popular_category['name'] ?? 'N/A'); ?></p>
                    <p class="text-sm text-gray-400"><?php echo number_format($popular_category['plays'] ?? 0); ?> plays</p>
                </div>
                <div class="w-12 h-12 bg-[#0038A8]/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-star text-[#FFD700] text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <h3 class="text-lg font-bold text-white mb-4">7-Day User Registrations</h3>
            <canvas id="registrationChart"></canvas>
        </div>

        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <h3 class="text-lg font-bold text-white mb-4">Category Distribution</h3>
            <canvas id="categoryChart"></canvas>
        </div>
    </div>
</div>

<script>
// Registration Chart
const registrationCtx = document.getElementById('registrationChart').getContext('2d');
new Chart(registrationCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_column($registration_data, 'date')); ?>,
        datasets: [{
            label: 'New Users',
            data: <?php echo json_encode(array_column($registration_data, 'count')); ?>,
            borderColor: '#0038A8',
            backgroundColor: 'rgba(0, 56, 168, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1, color: '#9ca3af' },
                grid: { color: 'rgba(255,255,255,0.05)' }
            },
            x: {
                ticks: { color: '#9ca3af' },
                grid: { color: 'rgba(255,255,255,0.05)' }
            }
        }
    }
});

// Category Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode(array_column($category_dist, 'name')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($category_dist, 'count')); ?>,
            backgroundColor: ['#0038A8', '#CE1126', '#FFD700', '#16a34a', '#9333ea']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { color: '#9ca3af' }
            }
        }
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>