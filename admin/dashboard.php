<?php
require_once '../config.php';

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Handle Logout (POST only to prevent CSRF)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'logout') {
    if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
        session_destroy();
        header("Location: login.php");
        exit;
    }
}

// Get current page filename for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF']);

// Fetch stats
$stats = [];
$stats['courses'] = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$stats['enrollments'] = $pdo->query("SELECT COUNT(*) FROM enrollments")->fetchColumn();
$stats['pending_fatwas'] = $pdo->query("SELECT COUNT(*) FROM fatwas WHERE status = 'Pending'")->fetchColumn();
$stats['users'] = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$stats['pending_users'] = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'pending'")->fetchColumn();
$stats['orders'] = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$stats['books'] = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();

// Fetch Pending Fatwas
$pending_fatwas = $pdo->query("SELECT * FROM fatwas WHERE status = 'Pending' ORDER BY created_at ASC LIMIT 10")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Maktaba Quddusia</title>
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
    </script>
</head>
<body>

<div class="admin-container">
    <!-- Sidebar - Fixed -->
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
                <?php if($stats['pending_fatwas'] > 0): ?>
                <span class="bg-gold text-white text-xs px-2 py-0.5 rounded-full ml-2"><?php echo $stats['pending_fatwas']; ?></span>
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
                <?php if($stats['orders'] > 0): ?>
                <span class="bg-gold text-white text-xs px-2 py-0.5 rounded-full ml-2"><?php echo $stats['orders']; ?></span>
                <?php endif; ?>
            </a>
            <a href="audios.php" class="nav-link <?php echo $current_page == 'audios.php' ? 'active' : ''; ?>">
                Audio & Voice
            </a>
            <hr class="my-2 border-green-mid">
            <a href="manage_users.php" class="nav-link <?php echo $current_page == 'manage_users.php' ? 'active' : ''; ?>">
                Registered Users
                <?php if($stats['pending_users'] > 0): ?>
                <span class="bg-yellow-500 text-white text-xs px-2 py-0.5 rounded-full ml-1"><?php echo $stats['pending_users']; ?></span>
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

    <!-- Main Content - Scrollable -->
    <main class="main-scroll">
        <div class="p-8">
            <header class="flex justify-between items-center mb-8 border-b pb-4 bg-white -mt-8 -mx-8 px-8 pt-8 pb-4 mb-8 shadow-sm">
                <h1 class="text-3xl font-bold text-gray-800">Dashboard Overview</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong></span>
                    <form method="POST" action="" class="inline">
                        <input type="hidden" name="action" value="logout">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm transition">Logout</button>
                    </form>
                </div>
            </header>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
                <div class="bg-white p-5 rounded-lg shadow-sm border-l-4 border-purple-500">
                    <h3 class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-1">Registered Users</h3>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $stats['users']; ?></p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border-l-4 border-gold">
                    <h3 class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-1">Total Courses</h3>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $stats['courses']; ?></p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border-l-4 border-blue-500">
                    <h3 class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-1">Enrollments</h3>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $stats['enrollments']; ?></p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border-l-4 border-green-500">
                    <h3 class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-1">Total Books</h3>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $stats['books']; ?></p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border-l-4 border-yellow-500">
                    <h3 class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-1">Book Orders</h3>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $stats['orders']; ?></p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border-l-4 border-red-500">
                    <h3 class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-1">Pending Fatwas</h3>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $stats['pending_fatwas']; ?></p>
                </div>
            </div>

            <!-- Pending Fatwas Table -->
            <div class="bg-white rounded-lg shadow-sm mb-8">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 rounded-t-lg">
                    <h2 class="text-lg font-bold text-gray-800">Recent Pending Fatwas</h2>
                    <?php if($stats['pending_fatwas'] > 0): ?>
                    <a href="fatwas.php" class="text-sm text-gold hover:text-green-mid transition">View All →</a>
                    <?php endif; ?>
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                                <th class="p-4 border-b border-gray-200 font-semibold">Ref No</th>
                                <th class="p-4 border-b border-gray-200 font-semibold">User Name</th>
                                <th class="p-4 border-b border-gray-200 font-semibold">Category</th>
                                <th class="p-4 border-b border-gray-200 font-semibold">Question Preview</th>
                                <th class="p-4 border-b border-gray-200 font-semibold text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($pending_fatwas)): ?>
                                <tr>
                                    <td colspan="5" class="p-4 text-center text-gray-500">No pending fatwas. All fatwas have been answered.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($pending_fatwas as $fatwa): ?>
                                    <tr class="hover:bg-gray-50 transition border-b border-gray-100 last:border-b-0">
                                        <td class="p-4 text-sm font-medium text-green-deep"><?php echo htmlspecialchars($fatwa['reference_no']); ?></td>
                                        <td class="p-4 text-sm text-gray-700"><?php echo htmlspecialchars($fatwa['user_name']); ?></td>
                                        <td class="p-4 text-sm"><span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs"><?php echo htmlspecialchars($fatwa['category']); ?></span></td>
                                        <td class="p-4 text-sm text-gray-600 truncate max-w-xs"><?php echo htmlspecialchars(substr($fatwa['question_text'], 0, 60)); ?>...</td>
                                        <td class="p-4 text-sm text-right">
                                            <a href="fatwas.php?answer=<?php echo $fatwa['id']; ?>" class="bg-green-mid hover:bg-green-deep text-white px-3 py-1 rounded transition text-xs inline-block">Answer</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
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