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
    <?php include 'sidebar.php'; ?>

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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
                <!-- Registered Users -->
                <div onclick="location.href='manage_users.php'" class="relative bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden group cursor-pointer">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-4xl font-extrabold text-gray-800"><?php echo $stats['users']; ?></p>
                            <h3 class="text-gray-500 text-xs font-semibold uppercase tracking-wider mt-1">Registered Users</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-700 text-white flex items-center justify-center shadow-md group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4z"/></svg>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 w-full bg-gradient-to-r from-purple-500 to-purple-700 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></div>
                </div>
                <!-- Total Courses -->
                <div onclick="location.href='courses.php'" class="relative bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden group cursor-pointer">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-4xl font-extrabold text-gray-800"><?php echo $stats['courses']; ?></p>
                            <h3 class="text-gray-500 text-xs font-semibold uppercase tracking-wider mt-1">Total Courses</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-400 to-gold text-white flex items-center justify-center shadow-md group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.25C10.5 5 8.5 4.5 5 4.5v13c3.5 0 5.5.5 7 1.75M12 6.25C13.5 5 15.5 4.5 19 4.5v13c-3.5 0-5.5.5-7 1.75M12 6.25v13"/></svg>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 w-full bg-gradient-to-r from-amber-400 to-gold scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></div>
                </div>
                <!-- Enrollments -->
                <div onclick="location.href='courses.php'" class="relative bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden group cursor-pointer">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-4xl font-extrabold text-gray-800"><?php echo $stats['enrollments']; ?></p>
                            <h3 class="text-gray-500 text-xs font-semibold uppercase tracking-wider mt-1">Enrollments</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center shadow-md group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 w-full bg-gradient-to-r from-blue-500 to-blue-700 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></div>
                </div>
                <!-- Total Books -->
                <div onclick="location.href='books.php'" class="relative bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden group cursor-pointer">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-4xl font-extrabold text-gray-800"><?php echo $stats['books']; ?></p>
                            <h3 class="text-gray-500 text-xs font-semibold uppercase tracking-wider mt-1">Total Books</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-mid to-green-deep text-white flex items-center justify-center shadow-md group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 w-full bg-gradient-to-r from-green-mid to-green-deep scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></div>
                </div>
                <!-- Book Orders -->
                <div onclick="location.href='orders.php'" class="relative bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden group cursor-pointer">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-4xl font-extrabold text-gray-800"><?php echo $stats['orders']; ?></p>
                            <h3 class="text-gray-500 text-xs font-semibold uppercase tracking-wider mt-1">Book Orders</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-orange-400 to-orange-600 text-white flex items-center justify-center shadow-md group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 w-full bg-gradient-to-r from-orange-400 to-orange-600 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></div>
                </div>
                <!-- Pending Fatwas -->
                <div onclick="location.href='fatwas.php'" class="relative bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden group cursor-pointer">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-4xl font-extrabold text-gray-800"><?php echo $stats['pending_fatwas']; ?></p>
                            <h3 class="text-gray-500 text-xs font-semibold uppercase tracking-wider mt-1">Pending Fatwas</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-500 to-red-700 text-white flex items-center justify-center shadow-md group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 w-full bg-gradient-to-r from-red-500 to-red-700 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></div>
                </div>
            </div>

            <!-- Pending Fatwas Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-8 overflow-hidden">
                <div class="p-5 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2"><span class="w-1.5 h-6 bg-gold rounded-sm"></span> Recent Pending Fatwas</h2>
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
                                    <tr class="hover:bg-gold/5 transition border-b border-gray-100 last:border-b-0">
                                        <td class="p-4">
                                            <span class="font-mono text-xs font-bold bg-green-deep/5 text-green-deep px-2.5 py-1 rounded-md"><?php echo htmlspecialchars($fatwa['reference_no']); ?></span>
                                        </td>
                                        <td class="p-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-green-deep to-green-mid text-gold flex items-center justify-center text-sm font-bold flex-shrink-0"><?php echo strtoupper(substr(htmlspecialchars($fatwa['user_name']), 0, 1)); ?></div>
                                                <span class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($fatwa['user_name']); ?></span>
                                            </div>
                                        </td>
                                        <td class="p-4"><span class="bg-gold/10 text-green-deep border border-gold/20 px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap"><?php echo htmlspecialchars(html_entity_decode($fatwa['category'])); ?></span></td>
                                        <td class="p-4 text-sm text-gray-500 italic truncate max-w-xs"><?php echo htmlspecialchars(substr($fatwa['question_text'], 0, 60)); ?>...</td>
                                        <td class="p-4 text-right">
                                            <a href="fatwas.php?answer=<?php echo $fatwa['id']; ?>" class="inline-flex items-center gap-1.5 bg-green-mid hover:bg-green-deep text-white px-4 py-2 rounded-lg transition-all text-xs font-semibold shadow-sm hover:shadow-md hover:-translate-y-0.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                Answer
                                            </a>
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