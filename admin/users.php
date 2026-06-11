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
    <script>
        tailwind.config = { theme: { extend: { colors: {
            'green-deep':'#1B3C2E','green-mid':'#2E6B4F','gold':'#C9960A','gold-light':'#E8B840'
        }}}}
    </script>
</head>
<body class="bg-gray-100 flex h-screen">
    <?php include 'sidebar.php'; ?>

    <main class="flex-1 p-8 overflow-y-auto">
        <h1 class="text-3xl font-bold text-green-deep mb-6">Manage Admin Users</h1>

        <?php if($message): ?>
            <div class="bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded-lg mb-5 flex items-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-lg mb-5 flex items-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Add New Admin -->
        <div class="bg-white p-6 md:p-7 rounded-xl shadow-sm border border-gray-100 mb-8">
            <h2 class="text-xl font-bold text-green-deep mb-5 flex items-center gap-2"><span class="w-1.5 h-6 bg-gold rounded-sm"></span> Create New Admin User</h2>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="add">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-green-deep mb-1.5">Username</label>
                        <input type="text" name="username" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition" placeholder="e.g., admin2">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-green-deep mb-1.5">Password</label>
                        <input type="password" name="password" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition" placeholder="Min 8 chars, letters+numbers">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-green-deep mb-1.5">Confirm Password</label>
                        <input type="password" name="password_confirm" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition">
                    </div>
                </div>
                <button type="submit" class="bg-green-deep hover:bg-green-mid text-white px-6 py-2.5 rounded-lg font-bold shadow-sm hover:shadow-md transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Create Admin
                </button>
            </form>
        </div>

        <!-- List All Admins -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                        <th class="p-4 font-semibold">Username</th>
                        <th class="p-4 font-semibold">Created</th>
                        <th class="p-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $u): ?>
                    <tr class="border-b border-gray-100 last:border-b-0 hover:bg-gold/5 transition">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-green-deep to-green-mid text-gold flex items-center justify-center text-sm font-bold flex-shrink-0"><?php echo strtoupper(substr(htmlspecialchars($u['username']), 0, 1)); ?></div>
                                <span class="font-semibold text-gray-800"><?php echo htmlspecialchars($u['username']); ?></span>
                                <?php echo $u['id'] === $_SESSION['admin_user_id'] ? '<span class="text-xs bg-gold/10 text-green-deep border border-gold/20 px-2 py-0.5 rounded-full font-medium">You</span>' : ''; ?>
                            </div>
                        </td>
                        <td class="p-4 text-sm text-gray-500"><?php echo date('d M Y', strtotime($u['created_at'])); ?></td>
                        <td class="p-4">
                            <div class="flex gap-1.5 justify-end">
                                <button onclick="openPasswordModal(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars($u['username']); ?>')" class="inline-flex items-center gap-1.5 bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                    Change Password
                                </button>
                                <?php if($u['id'] !== $_SESSION['admin_user_id']): ?>
                                    <a href="?delete=<?php echo $u['id']; ?>" onclick="return confirm('Delete this admin user?')" class="inline-flex items-center gap-1.5 bg-red-50 hover:bg-red-500 text-red-600 hover:text-white border border-red-200 hover:border-red-500 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Delete
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($users)): ?>
                    <tr><td colspan="3" class="p-6 text-center text-gray-400">No admin users found.</td></tr>
                    <?php endif; ?>
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
