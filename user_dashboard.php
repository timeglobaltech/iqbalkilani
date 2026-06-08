<?php
require_once 'config.php';

// Check if user is logged in (before any HTML output)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?msg=Please login to view your dashboard');
    exit;
}

// Logout (must be before header.php)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'logout') {
    if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
        session_unset();
        session_destroy();
        header('Location: index.php');
        exit;
    }
}

require_once 'includes/header.php';

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Fetch user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Change password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (!password_verify($old_password, $user['password_hash'])) {
        $error = "Current password is incorrect.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } elseif (strlen($new_password) < 8) {
        $error = "Password must be at least 8 characters.";
    } elseif (!preg_match('/[A-Za-z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
        $error = "Password must contain letters and numbers.";
    } else {
        $hash = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->execute([$hash, $user_id]);
        $message = "Password changed successfully.";
    }
}

// Logout is handled before header.php include above

// Fetch user enrollments
$stmt = $pdo->prepare("SELECT e.*, c.title_en FROM enrollments e LEFT JOIN courses c ON e.course_id = c.id WHERE e.student_email = ? ORDER BY e.created_at DESC");
$stmt->execute([$user['email']]);
$enrollments = $stmt->fetchAll();

// Count stats
$order_count = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE customer_email = ?");
$order_count->execute([$user['email']]);
$orders = $order_count->fetchColumn();

$fatwa_count = $pdo->prepare("SELECT COUNT(*) FROM fatwas WHERE user_email = ?");
$fatwa_count->execute([$user['email']]);
$fatwas = $fatwa_count->fetchColumn();
?>

<div class="bg-green-deep py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
        <div>
            <h1 class="font-english text-white text-5xl mb-2">My Dashboard</h1>
            <h2 class="arabic-text text-gold text-4xl">میرا ڈیش بورڈ</h2>
        </div>
        <form method="POST" class="inline">
            <input type="hidden" name="action" value="logout">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded font-semibold transition">Logout</button>
        </form>
    </div>
</div>

<section class="py-20 bg-cream min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <?php if($message): ?>
            <div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6'><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6'><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Sidebar: Profile Info -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded shadow-lg p-8 sticky top-8">
                    <div class="text-center mb-6">
                        <div class="w-20 h-20 bg-green-deep text-white rounded-full flex items-center justify-center text-4xl mx-auto mb-4">
                            👤
                        </div>
                        <h2 class="text-2xl font-bold text-green-deep"><?php echo htmlspecialchars($user['name']); ?></h2>
                        <p class="text-gray-600 text-sm mt-1"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>

                    <hr class="my-6">

                    <div class="space-y-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Member Since</p>
                            <p class="text-lg font-semibold text-gray-800"><?php echo date('d M Y', strtotime($user['created_at'])); ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Account Status</p>
                            <p class="text-lg font-semibold text-green-600">✓ Active</p>
                        </div>
                    </div>

                    <button onclick="openPasswordModal()" class="w-full mt-6 bg-blue-500 hover:bg-blue-600 text-white py-2 rounded font-semibold transition">
                        Change Password
                    </button>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">

                <!-- Stats Cards -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-white rounded shadow p-6 text-center">
                        <p class="text-3xl font-bold text-green-deep"><?php echo $enrollments ? count($enrollments) : 0; ?></p>
                        <p class="text-gray-600 text-sm mt-2">Courses Enrolled</p>
                    </div>
                    <div class="bg-white rounded shadow p-6 text-center">
                        <p class="text-3xl font-bold text-blue-600"><?php echo $fatwas; ?></p>
                        <p class="text-gray-600 text-sm mt-2">Questions Asked</p>
                    </div>
                    <div class="bg-white rounded shadow p-6 text-center">
                        <p class="text-3xl font-bold text-purple-600"><?php echo $orders; ?></p>
                        <p class="text-gray-600 text-sm mt-2">Book Orders</p>
                    </div>
                </div>

                <!-- Enrolled Courses -->
                <?php if(!empty($enrollments)): ?>
                <div class="bg-white rounded shadow p-8">
                    <h3 class="text-2xl font-bold text-green-deep mb-6">Enrolled Courses</h3>
                    <div class="space-y-4">
                        <?php foreach($enrollments as $e): ?>
                        <div class="border-l-4 border-gold pl-4 py-2">
                            <h4 class="font-semibold text-lg text-gray-800"><?php echo $e['title_en'] ? htmlspecialchars($e['title_en']) : 'Course'; ?></h4>
                            <p class="text-sm text-gray-600">Enrolled: <?php echo date('d M Y', strtotime($e['created_at'])); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="bg-blue-50 rounded border border-blue-200 p-8 text-center">
                    <p class="text-gray-600 mb-4">You haven't enrolled in any courses yet.</p>
                    <a href="courses.php" class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded font-semibold transition">
                        Browse Courses →
                    </a>
                </div>
                <?php endif; ?>

                <!-- Activity Summary -->
                <div class="bg-white rounded shadow p-8">
                    <h3 class="text-2xl font-bold text-green-deep mb-6">Activity Summary</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b">
                            <span class="text-gray-700">📚 Books Downloaded</span>
                            <span class="font-semibold"><?php echo $orders; ?></span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b">
                            <span class="text-gray-700">❓ Questions to Scholar</span>
                            <span class="font-semibold"><?php echo $fatwas; ?></span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-700">📅 Member for</span>
                            <span class="font-semibold"><?php echo date('Y') - date('Y', strtotime($user['created_at'])); ?> year(s)</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="bg-green-50 rounded border border-green-200 p-8">
                    <h3 class="text-xl font-bold text-green-deep mb-4">Quick Links</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <a href="courses.php" class="block text-center py-3 bg-white rounded hover:bg-gray-50 font-semibold text-green-deep transition">
                            Courses
                        </a>
                        <a href="books.php" class="block text-center py-3 bg-white rounded hover:bg-gray-50 font-semibold text-green-deep transition">
                            Books
                        </a>
                        <a href="fatwa.php" class="block text-center py-3 bg-white rounded hover:bg-gray-50 font-semibold text-green-deep transition">
                            Ask Question
                        </a>
                        <a href="articles.php" class="block text-center py-3 bg-white rounded hover:bg-gray-50 font-semibold text-green-deep transition">
                            Articles
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Password Change Modal -->
<div id="passwordModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 px-4">
    <div class="bg-white p-8 rounded shadow-lg max-w-md w-full">
        <h3 class="text-2xl font-bold text-green-deep mb-6">Change Password</h3>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="change_password">
            <div>
                <label class="block text-sm font-bold mb-2">Current Password</label>
                <input type="password" name="old_password" required class="w-full border border-gray-300 rounded px-3 py-2 focus:border-green-mid outline-none">
            </div>
            <div>
                <label class="block text-sm font-bold mb-2">New Password</label>
                <input type="password" name="new_password" required class="w-full border border-gray-300 rounded px-3 py-2 focus:border-green-mid outline-none" placeholder="Min 8 chars, letters+numbers">
            </div>
            <div>
                <label class="block text-sm font-bold mb-2">Confirm New Password</label>
                <input type="password" name="confirm_password" required class="w-full border border-gray-300 rounded px-3 py-2 focus:border-green-mid outline-none">
            </div>
            <div class="flex gap-2 pt-4">
                <button type="submit" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 rounded font-bold transition">Change</button>
                <button type="button" onclick="closePasswordModal()" class="flex-1 bg-gray-400 hover:bg-gray-500 text-white py-2 rounded font-bold transition">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPasswordModal() {
        document.getElementById('passwordModal').classList.remove('hidden');
    }
    function closePasswordModal() {
        document.getElementById('passwordModal').classList.add('hidden');
    }
</script>

<?php require_once 'includes/footer.php'; ?>
