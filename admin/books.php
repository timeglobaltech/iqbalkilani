<?php
require_once '../config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header("Location: login.php"); exit; }

$message = '';

// Handle Add/Edit Book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $title = $_POST['title'] ?? '';
    $title_urdu = $_POST['title_urdu'] ?? ''; 
    $language = $_POST['language'] ?? '';
    $price = $_POST['price'] ?? 0;
    $is_free = isset($_POST['is_free']) ? 1 : 0;
    
    if ($is_free == 1) {
        $price = 0;
    }
    
    $cover_image = $_POST['existing_cover'] ?? null;
    $file_path = $_POST['existing_file'] ?? null;

    if (!empty($_FILES['cover_image']['name'])) {
        $check = validate_upload($_FILES['cover_image'],
            ['jpg', 'jpeg', 'png', 'webp'],
            ['image/jpeg', 'image/png', 'image/webp'],
            5 * 1024 * 1024 
        );
        if ($check !== true) { $message = "Cover image error: $check"; } else {
            $ext = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
            $cover_name = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
            if (!is_dir('../uploads/covers/')) mkdir('../uploads/covers/', 0755, true);
            move_uploaded_file($_FILES['cover_image']['tmp_name'], '../uploads/covers/' . $cover_name);
            $cover_image = 'uploads/covers/' . $cover_name;
        }
    }

    if (empty($message) && !empty($_FILES['book_file']['name'])) {
        $check = validate_upload($_FILES['book_file'],
            ['pdf'],
            ['application/pdf'],
            20 * 1024 * 1024 
        );
        if ($check !== true) { $message = "Book file error: $check"; } else {
            $ext = strtolower(pathinfo($_FILES['book_file']['name'], PATHINFO_EXTENSION));
            $file_name = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
            if (!is_dir('../uploads/books/')) mkdir('../uploads/books/', 0755, true);
            move_uploaded_file($_FILES['book_file']['tmp_name'], '../uploads/books/' . $file_name);
            $file_path = 'uploads/books/' . $file_name;
        }
    }

    if (empty($message)) {
        if ($_POST['action'] === 'add') {
            $stmt = $pdo->prepare("INSERT INTO books (title, title_urdu, language, cover_image, file_path, price, is_free) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $title_urdu, $language, $cover_image, $file_path, $price, $is_free]);
            header("Location: books.php?msg=Book+added+successfully");
            exit;
        } elseif ($_POST['action'] === 'edit') {
            $id = $_POST['id'];
            $stmt = $pdo->prepare("UPDATE books SET title=?, title_urdu=?, language=?, cover_image=?, file_path=?, price=?, is_free=? WHERE id=?");
            $stmt->execute([$title, $title_urdu, $language, $cover_image, $file_path, $price, $is_free, $id]);
            header("Location: books.php?msg=Book+updated+successfully");
            exit;
        }
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: books.php?msg=Book+deleted+successfully");
        exit;
    }
}

if (isset($_GET['msg'])) { $message = htmlspecialchars($_GET['msg']); }

$edit_book = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_book = $stmt->fetch();
}

try {
    $pdo->exec("ALTER TABLE books ADD COLUMN IF NOT EXISTS is_free TINYINT(1) DEFAULT 0");
} catch (PDOException $e) {}

$books = $pdo->query("SELECT * FROM books ORDER BY id DESC")->fetchAll();

$current_page = basename($_SERVER['PHP_SELF']);

// Fetch stats for sidebar badges
$pending_fatwas = $pdo->query("SELECT COUNT(*) FROM fatwas WHERE status = 'Pending'")->fetchColumn();
$orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$pending_users = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'pending'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar-fixed {
            width: 16rem;
            flex-shrink: 0;
            background-color: #1B3C2E;
            color: white;
            overflow-y: auto;
            position: sticky;
            top: 0;
            height: 100vh;
        }
        .main-scroll {
            flex: 1;
            overflow-y: auto;
            background-color: #f3f4f6;
        }
        .nav-link {
            display: block;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            transition: all 0.2s;
            color: #d1d5db;
        }
        .nav-link:hover {
            background-color: #2E6B4F;
            color: white;
        }
        .nav-link.active {
            background-color: #2E6B4F;
            color: white;
            font-weight: 500;
        }
        .sidebar-fixed::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-fixed::-webkit-scrollbar-track {
            background: #1B3C2E;
        }
        .sidebar-fixed::-webkit-scrollbar-thumb {
            background: #2E6B4F;
            border-radius: 5px;
        }
        @import url('https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;500;600;700&display=swap');
        .urdu-text {
            font-family: 'Noto Nastaliq Urdu', serif;
            direction: rtl;
            line-height: 1.8;
        }
        .urdu-input {
            font-family: 'Noto Nastaliq Urdu', serif;
            direction: rtl;
            text-align: right;
        }
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 52px;
            height: 28px;
        }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #cbd5e1;
            transition: 0.3s;
            border-radius: 34px;
        }
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 22px; width: 22px;
            left: 3px; bottom: 3px;
            background-color: white;
            transition: 0.3s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        input:checked + .toggle-slider { background-color: #C9960A; }
        input:checked + .toggle-slider:before { transform: translateX(24px); }
        .free-badge {
            background: linear-gradient(135deg, #10B981, #059669);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            color: white;
            display: inline-block;
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'green-deep': '#1B3C2E',
                        'green-mid': '#2E6B4F',
                        gold: '#C9960A',
                    }
                }
            }
        }
        function togglePriceField() {
            const isFreeCheckbox = document.getElementById('is_free_checkbox');
            const priceField = document.getElementById('price_field');
            const priceInput = document.getElementById('price_input');
            if (isFreeCheckbox.checked) {
                priceField.style.display = 'none';
                priceInput.value = '0';
                priceInput.disabled = true;
            } else {
                priceField.style.display = 'block';
                priceInput.disabled = false;
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            const isFreeCheckbox = document.getElementById('is_free_checkbox');
            if (isFreeCheckbox) { togglePriceField(); }
        });
    </script>
</head>
<body>

<div class="admin-container">
    <!-- Sidebar - same as dashboard.php -->
    <aside class="sidebar-fixed">
        <div class="p-6 border-b border-green-mid text-center">
            <h2 class="text-2xl font-serif text-gold font-bold">Admin Panel</h2>
            <p class="text-xs text-gray-300 mt-1">Maktaba Quddusia</p>
        </div>
        <nav class="p-4 space-y-2">
            <a href="dashboard.php" class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
                Dashboard
            </a>
            <a href="courses.php" class="nav-link <?php echo $current_page == 'courses.php' ? 'active' : ''; ?>">
                Courses
            </a>
            <a href="fatwas.php" class="nav-link <?php echo $current_page == 'fatwas.php' ? 'active' : ''; ?>">
                Fatwas
                <?php if($pending_fatwas > 0): ?>
                <span class="bg-gold text-white text-xs px-2 py-0.5 rounded-full ml-2"><?php echo $pending_fatwas; ?></span>
                <?php endif; ?>
            </a>
            <a href="articles.php" class="nav-link <?php echo $current_page == 'articles.php' ? 'active' : ''; ?>">
                Articles
            </a>
            <a href="books.php" class="nav-link <?php echo $current_page == 'books.php' ? 'active' : ''; ?>">
                Books
            </a>
            <a href="orders.php" class="nav-link <?php echo $current_page == 'orders.php' ? 'active' : ''; ?>">
                Orders
                <?php if($orders > 0): ?>
                <span class="bg-gold text-white text-xs px-2 py-0.5 rounded-full ml-2"><?php echo $orders; ?></span>
                <?php endif; ?>
            </a>
            <a href="audios.php" class="nav-link <?php echo $current_page == 'audios.php' ? 'active' : ''; ?>">
                Audio & Voice
            </a>
            <hr class="my-2 border-green-mid">
            <a href="manage_users.php" class="nav-link <?php echo $current_page == 'manage_users.php' ? 'active' : ''; ?>">
                Registered Users
                <?php if($pending_users > 0): ?>
                <span class="bg-yellow-500 text-white text-xs px-2 py-0.5 rounded-full ml-1"><?php echo $pending_users; ?></span>
                <?php endif; ?>
            </a>
            <a href="users.php" class="nav-link <?php echo $current_page == 'users.php' ? 'active' : ''; ?>">
                Admin Users
            </a>
            <hr class="my-2 border-green-mid">
            <div class="px-4 py-2 text-xs text-gray-400 uppercase tracking-wider">Settings</div>
            <a href="settings.php" class="nav-link <?php echo $current_page == 'settings.php' ? 'active' : ''; ?>">
                Settings
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-scroll">
        <div class="p-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Manage Books</h1>
                        <p class="text-gray-500 mt-1">Manage your digital library collection</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="bg-white rounded-2xl px-5 py-2 shadow-sm border border-gray-100">
                            <span class="text-xs text-gray-500">Total Books</span>
                            <p class="text-2xl font-bold text-green-deep"><?php echo count($books); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <?php if($message): ?>
                <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 px-5 py-3 rounded-xl mb-6 shadow-sm">
                    <span class="font-medium"><?php echo $message; ?></span>
                </div>
            <?php endif; ?>

            <!-- Add/Edit Book Form -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-8 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <?php echo $edit_book ? 'Edit Book' : 'Add New Book'; ?>
                    </h2>
                </div>
                <div class="p-6">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="<?php echo $edit_book ? 'edit' : 'add'; ?>">
                        <?php if($edit_book): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_book['id']; ?>">
                            <input type="hidden" name="existing_cover" value="<?php echo htmlspecialchars($edit_book['cover_image'] ?? ''); ?>">
                            <input type="hidden" name="existing_file" value="<?php echo htmlspecialchars($edit_book['file_path'] ?? ''); ?>">
                        <?php endif; ?>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2"><span class="text-red-500">*</span> کتاب کا نام (Urdu Title)</label>
                                <input type="text" name="title_urdu" required
                                       class="urdu-input w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:border-gold focus:ring-2 focus:ring-yellow-100 outline-none transition"
                                       value="<?php echo $edit_book ? htmlspecialchars($edit_book['title_urdu']) : ''; ?>"
                                       placeholder="اردو میں کتاب کا نام لکھیں">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2"><span class="text-red-500">*</span> Book Title (English)</label>
                                <input type="text" name="title" required
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:border-gold focus:ring-2 focus:ring-yellow-100 outline-none transition"
                                       value="<?php echo $edit_book ? htmlspecialchars($edit_book['title']) : ''; ?>"
                                       placeholder="Enter book title in English">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2"><span class="text-red-500">*</span> Language</label>
                                <input type="text" name="language" required
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:border-gold focus:ring-2 focus:ring-yellow-100 outline-none transition"
                                       value="<?php echo $edit_book ? htmlspecialchars($edit_book['language']) : ''; ?>"
                                       placeholder="e.g., Urdu, Arabic, English">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Free Book</label>
                                <div class="flex items-center gap-3">
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="is_free_checkbox" name="is_free" value="1" onchange="togglePriceField()" <?php echo ($edit_book && $edit_book['is_free'] == 1) ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                    <span class="text-sm text-gray-500">Mark as free book</span>
                                </div>
                            </div>
                            <div id="price_field" style="<?php echo ($edit_book && $edit_book['is_free'] == 1) ? 'display:none' : 'display:block'; ?>">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Price (PKR)</label>
                                <input type="number" step="0.01" name="price" id="price_input"
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:border-gold focus:ring-2 focus:ring-yellow-100 outline-none transition"
                                       value="<?php echo $edit_book ? htmlspecialchars($edit_book['price']) : '0.00'; ?>"
                                       <?php echo ($edit_book && $edit_book['is_free'] == 1) ? 'disabled' : ''; ?>>
                                <p class="text-xs text-gray-400 mt-1">Set price for physical copy</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Cover Image</label>
                                <input type="file" name="cover_image" accept="image/*"
                                       class="w-full px-3 py-2 border border-gray-200 rounded-xl outline-none transition file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gold file:text-white hover:file:bg-yellow-500">
                                <?php if($edit_book && !empty($edit_book['cover_image'])): ?>
                                    <div class="mt-3 flex items-center gap-3">
                                        <img src="../<?php echo htmlspecialchars($edit_book['cover_image']); ?>" class="w-12 h-16 object-cover rounded-lg border border-gray-200 shadow-sm">
                                        <span class="text-xs text-gray-500">Current: <?php echo basename($edit_book['cover_image']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">PDF File</label>
                                <input type="file" name="book_file" accept=".pdf"
                                       class="w-full px-3 py-2 border border-gray-200 rounded-xl outline-none transition file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gold file:text-white hover:file:bg-yellow-500">
                                <?php if($edit_book && !empty($edit_book['file_path'])): ?>
                                    <div class="mt-2">
                                        <a href="../<?php echo htmlspecialchars($edit_book['file_path']); ?>" target="_blank" class="text-xs text-gold hover:underline">View Current PDF</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mt-8 flex gap-3">
                            <button type="submit" class="bg-green-deep hover:bg-green-mid text-white px-6 py-2.5 rounded-xl font-semibold transition shadow-sm">
                                <?php echo $edit_book ? 'Update Book' : 'Save Book'; ?>
                            </button>
                            <?php if($edit_book): ?>
                                <a href="books.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl font-semibold transition">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Books List Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">All Books</h2>
                    <span class="text-sm text-gray-400"><?php echo count($books); ?> books total</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Cover</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Book Details</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Language</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($books as $b): ?>
                            <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <?php if(!empty($b['cover_image'])): ?>
                                        <img src="../<?php echo htmlspecialchars($b['cover_image']); ?>" class="w-12 h-16 object-cover rounded-lg shadow-sm border border-gray-200">
                                    <?php else: ?>
                                        <div class="w-12 h-16 bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="urdu-text text-base text-green-deep font-bold mb-1"><?php echo htmlspecialchars($b['title_urdu']); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($b['title']); ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-medium"><?php echo htmlspecialchars($b['language']); ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if($b['is_free'] == 1 || $b['price'] == 0): ?>
                                        <span class="free-badge">FREE</span>
                                    <?php else: ?>
                                        <span class="text-sm font-bold text-gray-700">PKR <?php echo number_format($b['price'], 2); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="?edit=<?php echo $b['id']; ?>"
                                           class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 px-3 py-1.5 rounded-lg transition text-xs font-medium border border-emerald-200">
                                            Edit
                                        </a>
                                        <a href="?delete=<?php echo $b['id']; ?>"
                                           class="bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg transition text-xs font-medium border border-red-200"
                                           onclick="return confirm('Are you sure you want to delete this book?');">
                                            Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($books)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                    No books found. Add your first book using the form above.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

</body>
</html>