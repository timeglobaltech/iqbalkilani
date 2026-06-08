<?php
require_once '../config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$message = '';
$error = '';

// Add New Admin User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if (empty($username) || empty($password)) {
        $error = "Username and password are required.";
    } elseif ($password !== $password_confirm) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $error = "Password must contain letters and numbers.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admin_users (username, password_hash) VALUES (?, ?)");
        if ($stmt->execute([$username, $hash])) {
            $message = "Admin user '$username' created successfully.";
        } else {
            $error = "Username already exists or error occurred.";
        }
    }
}

// Change Password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $user_id = (int)$_POST['user_id'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Get current user
    $stmt = $pdo->prepare("SELECT password_hash FROM admin_users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = "User not found.";
    } elseif (!password_verify($old_password, $user['password_hash'])) {
        $error = "Current password is incorrect.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } elseif (strlen($new_password) < 8) {
        $error = "Password must be at least 8 characters.";
    } elseif (!preg_match('/[A-Za-z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
        $error = "Password must contain letters and numbers.";
    } else {
        $hash = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE admin_users SET password_hash = ? WHERE id = ?");
        if ($stmt->execute([$hash, $user_id])) {
            $message = "Password changed successfully.";
        }
    }
}

// Delete Admin User
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Prevent deleting yourself
    if ($id === $_SESSION['admin_user_id']) {
        $error = "You cannot delete your own account.";
    } else {
        $stmt = $pdo->prepare("DELETE FROM admin_users WHERE id = ?");
        if ($stmt->execute([$id])) {
            $message = "Admin user deleted successfully.";
        }
    }
}

// Fetch all admin users
$users = $pdo->query("SELECT id, username, created_at FROM admin_users ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Admin Users - Admin</title>
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
            <a href="courses.php" class="block py-2 px-4 hover:bg-[#2E6B4F] rounded text-gray-300 transition">Courses</a>
            <a href="fatwas.php" class="block py-2 px-4 hover:bg-[#2E6B4F] rounded text-gray-300 transition">Fatwas</a>
            <a href="articles.php" class="block py-2 px-4 hover:bg-[#2E6B4F] rounded text-gray-300 transition">Articles</a>
            <a href="books.php" class="block py-2 px-4 hover:bg-[#2E6B4F] rounded text-gray-300 transition">Books</a>
            <a href="orders.php" class="block py-2 px-4 hover:bg-[#2E6B4F] rounded text-gray-300 transition">Orders</a>
            <a href="audios.php" class="block py-2 px-4 hover:bg-[#2E6B4F] rounded text-gray-300 transition">Audio & Voice</a>
            <hr class="my-2 border-[#2E6B4F]">
            <a href="manage_users.php" class="block py-2 px-4 hover:bg-[#2E6B4F] rounded text-gray-300 transition">Registered Users</a>
            <a href="users.php" class="block py-2 px-4 bg-[#2E6B4F] rounded text-white font-medium">Admin Users</a>
            <hr class="my-2 border-green-mid">
            <div class="px-4 py-2 text-xs text-gray-400 uppercase tracking-wider">Settings</div>
            <a href="settings.php" class="block py-2 px-4 hover:bg-[#2E6B4F] rounded text-gray-300 transition">Settings</a>
        </nav>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto">
        <h1 class="text-3xl font-bold mb-6">Manage Admin Users</h1>

        <?php if($message): ?>
            <div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Add New Admin -->
        <div class="bg-white p-6 rounded shadow mb-8">
            <h2 class="text-xl font-bold mb-4">Create New Admin User</h2>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="add">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold mb-1">Username</label>
                        <input type="text" name="username" required class="w-full border p-2 rounded" placeholder="e.g., admin2">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1">Password</label>
                        <input type="password" name="password" required class="w-full border p-2 rounded" placeholder="Min 8 chars, letters+numbers">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1">Confirm Password</label>
                        <input type="password" name="password_confirm" required class="w-full border p-2 rounded">
                    </div>
                </div>
                <button type="submit" class="bg-green-deep hover:bg-green-mid text-white px-6 py-2 rounded font-bold">Create Admin</button>
            </form>
        </div>

        <!-- List All Admins -->
        <div class="bg-white rounded shadow">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50">
                    <tr class="border-b">
                        <th class="p-3">Username</th>
                        <th class="p-3">Created</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $u): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 font-bold"><?php echo htmlspecialchars($u['username']); ?> <?php echo $u['id'] === $_SESSION['admin_user_id'] ? '<span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">(You)</span>' : ''; ?></td>
                        <td class="p-3 text-sm text-gray-600"><?php echo date('d M Y', strtotime($u['created_at'])); ?></td>
                        <td class="p-3 space-x-2">
                            <button onclick="openPasswordModal(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars($u['username']); ?>')" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs">Change Password</button>
                            <?php if($u['id'] !== $_SESSION['admin_user_id']): ?>
                                <a href="?delete=<?php echo $u['id']; ?>" onclick="return confirm('Delete this admin user?')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs">Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Password Change Modal -->
    <div id="passwordModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded shadow-lg max-w-md w-full">
            <h3 class="text-xl font-bold mb-4">Change Password</h3>
            <form method="POST" id="passwordForm" class="space-y-4">
                <input type="hidden" name="action" value="change_password">
                <input type="hidden" name="user_id" id="userId">
                <div>
                    <label class="block text-sm font-bold mb-1">Current Password</label>
                    <input type="password" name="old_password" required class="w-full border p-2 rounded">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">New Password</label>
                    <input type="password" name="new_password" required class="w-full border p-2 rounded" placeholder="Min 8 chars, letters+numbers">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Confirm New Password</label>
                    <input type="password" name="confirm_password" required class="w-full border p-2 rounded">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded font-bold flex-1">Change</button>
                    <button type="button" onclick="closePasswordModal()" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded font-bold flex-1">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openPasswordModal(userId, username) {
            document.getElementById('userId').value = userId;
            document.getElementById('passwordModal').classList.remove('hidden');
        }
        function closePasswordModal() {
            document.getElementById('passwordModal').classList.add('hidden');
        }
    </script>
</body>
</html>
