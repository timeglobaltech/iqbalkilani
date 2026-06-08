<?php
require_once 'includes/header.php';

// Fetch all courses
$stmt = $pdo->query("SELECT * FROM courses ORDER BY created_at DESC");
$all_courses = $stmt->fetchAll();
?>

<div class="bg-green-deep py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="font-english text-white text-5xl mb-2">Lectures & Courses</h1>
        <h2 class="arabic-text text-gold text-4xl">دروس و تعلیم</h2>
    </div>
</div>

<section class="py-20 bg-beige min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Filter Tabs (UI only for demo) -->
        <div class="flex flex-wrap justify-center gap-4 mb-12">
            <span class="bg-green-deep text-white px-6 py-2 rounded-full text-sm font-semibold cursor-pointer">All</span>
            <span class="bg-white text-green-deep border border-green-deep hover:bg-green-deep hover:text-white px-6 py-2 rounded-full text-sm font-semibold transition cursor-pointer">Tafsir</span>
            <span class="bg-white text-green-deep border border-green-deep hover:bg-green-deep hover:text-white px-6 py-2 rounded-full text-sm font-semibold transition cursor-pointer">Hadith</span>
            <span class="bg-white text-green-deep border border-green-deep hover:bg-green-deep hover:text-white px-6 py-2 rounded-full text-sm font-semibold transition cursor-pointer">Fiqh</span>
            <span class="bg-white text-green-deep border border-green-deep hover:bg-green-deep hover:text-white px-6 py-2 rounded-full text-sm font-semibold transition cursor-pointer">Seerah</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach($all_courses as $c): ?>
            <div class="bg-white rounded border border-gray-200 overflow-hidden group hover:shadow-lg transition">
                <div class="bg-green-deep h-32 flex items-center justify-center relative">
                    <span class="absolute top-2 right-2 bg-gold text-white text-xs px-2 py-1 rounded"><?php echo htmlspecialchars($c['status']); ?></span>
                    <h4 class="arabic-text text-gold-light text-2xl"><?php echo htmlspecialchars($c['title_ur']); ?></h4>
                </div>
                <div class="p-6">
                    <h3 class="font-english font-semibold text-lg text-green-deep mb-2 h-14 line-clamp-2"><?php echo htmlspecialchars($c['title_en']); ?></h3>
                    <p class="text-sm text-gray-600 mb-4 h-10 line-clamp-2"><?php echo htmlspecialchars($c['description']); ?></p>
                    <div class="text-xs text-muted mb-4 flex justify-between border-b border-gray-100 pb-2">
                        <span><?php echo $c['total_lessons']; ?> Lessons</span>
                        <span><?php echo htmlspecialchars($c['format']); ?></span>
                    </div>
                    <!-- Trigger Modal (for demo we just link to a hash) -->
                    <a href="#enrollModal" onclick="document.getElementById('enrollModal').classList.remove('hidden')" class="block text-center w-full border border-green-mid text-green-mid hover:bg-green-mid hover:text-white py-2 rounded transition text-sm font-semibold">Enroll Free</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Simple Enrollment Modal -->
<div id="enrollModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white p-8 rounded max-w-md w-full relative">
        <button onclick="document.getElementById('enrollModal').classList.add('hidden')" class="absolute top-4 right-4 text-gray-500 hover:text-black">&times;</button>
        <h3 class="text-2xl font-english text-green-deep mb-4">Enroll in Course</h3>
        <form action="#" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="text" placeholder="Full Name" required class="w-full border rounded px-3 py-2">
            <input type="email" placeholder="Email Address" required class="w-full border rounded px-3 py-2">
            <input type="text" placeholder="Phone (Optional)" class="w-full border rounded px-3 py-2">
            <button type="button" onclick="alert('Enrollment submitted! JazakAllah Khayran.'); document.getElementById('enrollModal').classList.add('hidden');" class="w-full bg-gold text-white py-2 rounded font-semibold">Confirm Enrollment</button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
