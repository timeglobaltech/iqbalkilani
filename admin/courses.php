<?php
require_once '../config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header("Location: login.php"); exit; }

$message = '';
// Add Course
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $title_en = sanitize($_POST['title_en']);
    $title_ur = sanitize($_POST['title_ur']);
    $description = sanitize($_POST['description']);
    $total_lessons = (int)$_POST['total_lessons'];
    $category = sanitize($_POST['category']);
    $format = sanitize($_POST['format']);
    $status = sanitize($_POST['status']);

    if (empty($title_en) || empty($category)) {
        $message = "Title and category are required.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO courses (title_en, title_ur, description, total_lessons, category, format, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title_en, $title_ur, $description, $total_lessons, $category, $format, $status]);
        header("Location: courses.php?msg=Course+added+successfully");
        exit;
    }
}

// Delete Course
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: courses.php?msg=Course+deleted+successfully");
        exit;
    }
}

if (isset($_GET['msg'])) {
    $message = htmlspecialchars($_GET['msg']);
}

$courses = $pdo->query("SELECT * FROM courses ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Manage Courses - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex h-screen">
    <aside class="w-64 bg-[#1B3C2E] text-white flex-shrink-0 overflow-y-auto">
        <div class="p-6 border-b border-[#2E6B4F] text-center">
            <h2 class="text-2xl font-serif text-[#C9960A] font-bold">Admin Panel</h2>
            <p class="text-xs text-gray-300 mt-1">Maktaba Quddusia</p>
        </div>
        <nav class="p-4 space-y-2">
            <a href="dashboard.php" class="block py-2 px-4 hover:bg-[#2E6B4F] rounded text-gray-300 transition">Dashboard</a>
            <a href="courses.php" class="block py-2 px-4 bg-[#2E6B4F] rounded text-white font-medium">Courses</a>
            <a href="fatwas.php" class="block py-2 px-4 hover:bg-[#2E6B4F] rounded text-gray-300 transition">Fatwas</a>
            <a href="articles.php" class="block py-2 px-4 hover:bg-[#2E6B4F] rounded text-gray-300 transition">Articles</a>
            <a href="books.php" class="block py-2 px-4 hover:bg-[#2E6B4F] rounded text-gray-300 transition">Books</a>
            <a href="orders.php" class="block py-2 px-4 hover:bg-[#2E6B4F] rounded text-gray-300 transition">Orders</a>
            <a href="audios.php" class="block py-2 px-4 hover:bg-[#2E6B4F] rounded text-gray-300 transition">Audio & Voice</a>
            <hr class="my-2 border-[#2E6B4F]">
            <a href="manage_users.php" class="block py-2 px-4 hover:bg-[#2E6B4F] rounded text-gray-300 transition">Registered Users</a>
            <a href="users.php" class="block py-2 px-4 hover:bg-[#2E6B4F] rounded text-gray-300 transition">Admin Users</a>
            <div class="px-4 py-2 text-xs text-gray-400 uppercase tracking-wider">Settings</div>
            <a href="settings.php" class="block py-2 px-4 hover:bg-green-mid rounded text-gray-300 transition">Settings</a>
        </nav>
    </aside>
    <main class="flex-1 p-8 overflow-y-auto">
        <h1 class="text-3xl font-bold mb-6">Manage Courses</h1>
        <?php if($message) echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'>$message</div>"; ?>
        
        <div class="bg-white p-6 rounded shadow mb-8">
            <h2 class="text-xl font-bold mb-4">Add New Course</h2>
            <form method="POST" class="grid grid-cols-2 gap-4">
                <input type="hidden" name="action" value="add">
                <input type="text" name="title_en" placeholder="Title (English)" required class="border p-2 rounded">
                <input type="text" name="title_ur" placeholder="Title (Urdu)" required class="border p-2 rounded" dir="rtl">
                <input type="text" name="category" placeholder="Category (e.g. Tafsir)" required class="border p-2 rounded">
                <input type="number" name="total_lessons" placeholder="Total Lessons" class="border p-2 rounded">
                <input type="text" name="format" placeholder="Format (e.g. Video)" class="border p-2 rounded">
                <select name="status" class="border p-2 rounded">
                    <option>Ongoing</option><option>Completed</option><option>New</option>
                </select>
                <textarea name="description" placeholder="Description" class="border p-2 rounded col-span-2"></textarea>
                <button type="submit" class="bg-[#C9960A] text-white px-4 py-2 rounded font-bold hover:bg-[#E8B840] col-span-2">Save Course</button>
            </form>
        </div>

        <div class="bg-white rounded shadow">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50"><tr class="border-b"><th class="p-3">Title</th><th class="p-3">Urdu</th><th class="p-3">Category</th><th class="p-3">Actions</th></tr></thead>
                <tbody>
                    <?php foreach($courses as $c): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3"><?php echo htmlspecialchars($c['title_en']); ?></td>
                        <td class="p-3 arabic-text text-lg" dir="rtl"><?php echo htmlspecialchars($c['title_ur']); ?></td>
                        <td class="p-3"><?php echo htmlspecialchars($c['category']); ?></td>
                        <td class="p-3"><a href="?delete=<?php echo $c['id']; ?>" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded transition text-xs" onclick="return confirm('Delete?');">Delete</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
