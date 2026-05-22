<?php
session_start();
require_once 'includes/functions.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get region ID from URL
$regionId = isset($_GET['region_id']) ? (int)$_GET['region_id'] : null;
if (!$regionId) {
    header('Location: mundo.php');
    exit;
}

// Get region details
$pdo = getDB();
$stmt = $pdo->prepare("SELECT * FROM regions WHERE id = ?");
$stmt->execute([$regionId]);
$region = $stmt->fetch();

if (!$region) {
    header('Location: mundo.php');
    exit;
}

// Check if region is locked
$isLocked = $_SESSION['level'] < $region['min_level'];

// Get enemies for this region
$stmt = $pdo->prepare("SELECT * FROM enemies WHERE region_id = ? ORDER BY id ASC");
$stmt->execute([$regionId]);
$enemies = $stmt->fetchAll();

// Get player progress for this region
$stmt = $pdo->prepare("SELECT battles_won, completed FROM region_progress WHERE user_id = ? AND region_id = ?");
$stmt->execute([$_SESSION['user_id'], $regionId]);
$progress = $stmt->fetch() ?: ['battles_won' => 0, 'completed' => 0];

// Region-specific data (maps, history background, enemy images)
$regionData = [
    1 => [
        'map_image' => 'https://images.pexels.com/photos/33076681/pexels-photo-33076681.jpeg', // Add your Manila map image URL here
        'history' => 'Maynila (Manila) is the capital city of the Philippines and has been the center of Philippine history for centuries. Founded in 1571 by Spanish conquistador Miguel López de Legazpi, it became the seat of Spanish colonial government in Asia. The city witnessed key events including the Cry of Balintawak, the Philippine Revolution against Spain, and the Battle of Manila during World War II. Intramuros, the historic walled city, stands as a testament to Spanish colonial architecture and the resilience of the Filipino people.',
        'enemy_images' => [
            '', // Add enemy 1 image URL here
            '', // Add enemy 2 image URL here
            ''  // Add enemy 3 image URL here
        ]
    ],
    2 => [
        'map_image' => 'https://images.pexels.com/photos/36703366/pexels-photo-36703366.jpeg', // Add your Cebu map image URL here
        'history' => 'Cebu is known as the "Queen City of the South" and holds a special place in Philippine history as the site of the first Spanish settlement and the baptism of the first Filipino Christians. It was here in 1521 that Ferdinand Magellan arrived, only to be defeated by the local chieftain Lapu-Lapu in the Battle of Mactan - the first successful resistance against foreign invaders. Cebu became a center of trade and Christianity, with the Basilica Minore del Santo Niño housing the oldest religious relic in the Philippines.',
        'enemy_images' => [
            '', // Add enemy 1 image URL here
            '', // Add enemy 2 image URL here
            ''  // Add enemy 3 image URL here
        ]
    ],
    3 => [
        'map_image' => 'https://images.pexels.com/photos/32047037/pexels-photo-32047037.jpeg', // Add your Davao map image URL here
        'history' => 'Davao is the largest city in the Philippines by land area and serves as the gateway to Mindanao. Home to Mount Apo, the country\'s highest peak, Davao has a rich history of indigenous culture and resistance. During World War II, it was a major battleground between Filipino and American forces against Japanese occupation. The city is known for its diverse cultural heritage, blending indigenous Lumad, Muslim, and Christian traditions. Today, it stands as a symbol of Mindanao\'s resilience and natural beauty.',
        'enemy_images' => [
            '', // Add enemy 1 image URL here
            '', // Add enemy 2 image URL here
            ''  // Add enemy 3 image URL here
        ]
    ],
    4 => [
        'map_image' => 'https://images.pexels.com/photos/4175000/pexels-photo-4175000.jpeg', // Add your Vigan map image URL here
        'history' => 'Vigan is a UNESCO World Heritage Site renowned for its well-preserved Spanish colonial architecture. Founded in 1572 by Juan de Salcedo, it became a center of trade and culture in Northern Luzon. The city\'s Calle Crisologo, with its cobblestone streets and ancestral houses, offers a glimpse into the Philippines\' colonial past. Vigan was also the birthplace of notable figures including Father Jose Burgos, one of the GOMBURZA martyrs who inspired the Philippine Revolution.',
        'enemy_images' => [
            '', // Add enemy 1 image URL here
            '', // Add enemy 2 image URL here
            ''  // Add enemy 3 image URL here
        ]
    ],
    5 => [
        'map_image' => 'https://preview.redd.it/zamboanga-del-sur-v0-qlcr3tksmkee1.jpg?width=1080&crop=smart&auto=webp&s=9a100f9461b605b39ade42ac7d8705796e1bfc39', // Add your Zamboanga map image URL here
        'history' => 'Zamboanga, known as the "City of Flowers," is a melting pot of cultures with strong Spanish, Muslim, and indigenous influences. Founded in 1635 as a military fort to defend against Moro raids, it became the southernmost outpost of Spanish colonial rule. The city\'s Fort Pilar, built in 1718, stands as a symbol of Spanish colonial presence. Zamboanga\'s unique Chavacano language, a Spanish-based creole, reflects its rich cultural heritage and historical significance as a crossroads of civilizations.',
        'enemy_images' => [
            '', // Add enemy 1 image URL here
            '', // Add enemy 2 image URL here
            ''  // Add enemy 3 image URL here
        ]
    ]
];

$data = $regionData[$regionId] ?? [
    'map_image' => '',
    'history' => 'No historical information available.',
    'enemy_images' => []
];

require_once 'includes/header.php';
?>

<main class="min-h-screen bg-gray-50 py-8 px-4">
    <div class="max-w-6xl mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="mundo.php" class="inline-flex items-center text-gray-600 hover:text-[#0038A8] transition">
                <i class="fas fa-arrow-left mr-2"></i> Bumalik sa Mundo
            </a>
        </div>

        <!-- Region Header -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
            <div class="p-8" style="background: <?php echo $region['background_color'] ?? '#0038A8'; ?>;">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-4xl font-bold text-white mb-2"><?php echo htmlspecialchars($region['name']); ?></h1>
                        <p class="text-white/80 text-lg mb-4"><?php echo htmlspecialchars($region['province']); ?></p>
                        <div class="flex gap-2">
                            <span class="px-4 py-2 bg-white/20 text-white rounded-full text-sm font-bold uppercase">
                                <?php echo htmlspecialchars($region['island_group']); ?>
                            </span>
                            <span class="px-4 py-2 bg-white/20 text-white rounded-full text-sm">
                                Min Level: <?php echo $region['min_level']; ?>
                            </span>
                        </div>
                    </div>
                    <?php if ($isLocked): ?>
                        <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-lock text-white text-2xl"></i>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ($isLocked): ?>
            <!-- Locked Message -->
            <div class="bg-white rounded-2xl shadow-lg p-8 text-center mb-8">
                <i class="fas fa-lock text-6xl text-gray-400 mb-4"></i>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Rehiyon ay Nakakandado</h2>
                <p class="text-gray-600 mb-4">Kailangan mong umabot sa Level <?php echo $region['min_level']; ?> upang makapasok sa rehiyong ito.</p>
                <p class="text-gray-600">Kasalukuyang Level mo: <?php echo $_SESSION['level']; ?></p>
            </div>
        <?php else: ?>
            <!-- Map Section -->
            <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
                <h2 class="text-2xl font-bold text-[#0038A8] mb-4">
                    <i class="fas fa-image mr-2"></i> Itsura ng Rehiyon
                </h2>
                <?php if ($data['map_image']): ?>
                    <div class="rounded-xl overflow-hidden border-2 border-gray-200" style="height: 400px; cursor: pointer;" onclick="openImageModal('<?php echo htmlspecialchars($data['map_image']); ?>')">
                        <img src="<?php echo htmlspecialchars($data['map_image']); ?>"
                             alt="Image of <?php echo htmlspecialchars($region['name']); ?>"
                             class="w-full h-full object-contain">
                    </div>
                    <p class="text-sm text-gray-500 mt-2 text-center"><i class="fas fa-expand mr-1"></i> Click to view full image</p>
                <?php else: ?>
                    <p class="text-gray-500">Image not available</p>
                <?php endif; ?>
            </div>

            <!-- History Section -->
            <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
                <h2 class="text-2xl font-bold text-[#0038A8] mb-4">
                    <i class="fas fa-book-open mr-2"></i> Kasaysayan
                </h2>
                <p class="text-gray-700 leading-relaxed text-lg">
                    <?php echo htmlspecialchars($data['history']); ?>
                </p>
            </div>

            <!-- Enemies Section -->
            <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-[#0038A8]">
                        <i class="fas fa-skull mr-2"></i> Mga Kaaway
                    </h2>
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-medal mr-1"></i> <?php echo $progress['battles_won']; ?>/<?php echo count($enemies); ?> Panalo
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <?php foreach ($enemies as $index => $enemy): ?>
                        <div class="border-2 border-gray-200 rounded-xl overflow-hidden hover:border-[#0038A8] transition">
                            <?php if (isset($data['enemy_images'][$index])): ?>
                                <div class="h-48 overflow-hidden">
                                    <img src="<?php echo htmlspecialchars($data['enemy_images'][$index]); ?>" 
                                         alt="<?php echo htmlspecialchars($enemy['name']); ?>" 
                                         class="w-full h-full object-cover">
                                </div>
                            <?php endif; ?>
                            <div class="p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-bold text-gray-800 text-lg"><?php echo htmlspecialchars($enemy['name']); ?></h3>
                                    <span class="px-2 py-1 bg-gray-200 text-gray-700 rounded-full text-xs font-bold">
                                        <?php echo htmlspecialchars($enemy['era']); ?>
                                    </span>
                                </div>
                                
                                <div class="flex gap-4 text-sm text-gray-600 mb-3">
                                    <div><i class="fas fa-heart text-red-500 mr-1"></i> <?php echo $enemy['hp']; ?> HP</div>
                                    <div><i class="fas fa-fist-raised text-orange-500 mr-1"></i> <?php echo $enemy['attack']; ?> ATK</div>
                                    <div><i class="fas fa-shield-alt text-blue-500 mr-1"></i> <?php echo $enemy['defense']; ?> DEF</div>
                                </div>
                                
                                <p class="text-gray-600 text-sm mb-4"><?php echo htmlspecialchars($enemy['description']); ?></p>
                                
                                <a href="battle.php?region_id=<?php echo $regionId; ?>&enemy_id=<?php echo $enemy['id']; ?>" 
                                   class="block w-full bg-[#CE1126] text-white py-2 rounded-xl font-bold text-center hover:bg-[#a00d1a] transition">
                                    <i class="fas fa-sword mr-2"></i> Labanan
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-90 hidden items-center justify-center z-50" onclick="closeImageModal()">
    <div class="max-w-5xl max-h-screen p-4">
        <img id="modalImage" src="" alt="Full size image" class="max-w-full max-h-screen object-contain">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white text-3xl hover:text-gray-300">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<script>
function openImageModal(imageUrl) {
    document.getElementById('modalImage').src = imageUrl;
    document.getElementById('imageModal').classList.remove('hidden');
    document.getElementById('imageModal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.getElementById('imageModal').classList.remove('flex');
    document.body.style.overflow = 'auto';
}

// Close modal on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeImageModal();
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
