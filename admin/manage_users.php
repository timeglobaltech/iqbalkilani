<?php
require_once '../config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$message = '';
$error = '';

// Approve User
if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    if ($id > 0) {
        $pdo->prepare("UPDATE users SET status = 'active' WHERE id = ?")->execute([$id]);
        header("Location: manage_users.php?msg=User+approved+successfully");
        exit;
    }
}

// Reject User
if (isset($_GET['reject'])) {
    $id = (int)$_GET['reject'];
    if ($id > 0) {
        $pdo->prepare("DELETE FROM users WHERE id = ? AND status = 'pending'")->execute([$id]);
        header("Location: manage_users.php?msg=User+registration+rejected");
        exit;
    }
}

// Block / Unblock User
if (isset($_GET['block'])) {
    $id = (int)$_GET['block'];
    if ($id > 0) {
        $pdo->prepare("UPDATE users SET status = 'blocked' WHERE id = ?")->execute([$id]);
        header("Location: manage_users.php?msg=User+blocked+successfully");
        exit;
    }
}

if (isset($_GET['unblock'])) {
    $id = (int)$_GET['unblock'];
    if ($id > 0) {
        $pdo->prepare("UPDATE users SET status = 'active' WHERE id = ?")->execute([$id]);
        header("Location: manage_users.php?msg=User+unblocked+successfully");
        exit;
    }
}

// Delete User
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0) {
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
        header("Location: manage_users.php?msg=User+deleted+successfully");
        exit;
    }
}

// Reset Password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset_password') {
    $user_id = (int)$_POST['user_id'];
    $new_password = $_POST['new_password'];

    if (strlen($new_password) < 8) {
        $error = "Password must be at least 8 characters.";
    } else {
        $hash = password_hash($new_password, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?")->execute([$hash, $user_id]);
        header("Location: manage_users.php?msg=Password+reset+successfully");
        exit;
    }
}

// Edit User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_user') {
    $user_id = (int)$_POST['user_id'];
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);

    if (empty($name) || empty($email)) {
        $error = "Name and email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?")->execute([$name, $email, $user_id]);
        header("Location: manage_users.php?msg=User+updated+successfully");
        exit;
    }
}

// Add New User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_user') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } else {
        // Check if email exists
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $error = "Email already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare("INSERT INTO users (name, email, password_hash, status) VALUES (?, ?, ?, 'active')")->execute([$name, $email, $hash]);
            header("Location: manage_users.php?msg=User+added+successfully");
            exit;
        }
    }
}

// Read message from redirect
if (isset($_GET['msg'])) {
    $message = htmlspecialchars($_GET['msg']);
}

// Search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Fetch users
if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY created_at DESC");
    $stmt->execute(["%$search%", "%$search%"]);
    $users = $stmt->fetchAll();
} else {
    $users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
}

// Stats
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$pending_users = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'pending'")->fetchColumn();
$active_users = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active'")->fetchColumn();
$blocked_users = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'blocked'")->fetchColumn();

// Fetch pending users separately
$pending_list = $pdo->query("SELECT * FROM users WHERE status = 'pending' ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - Admin</title>
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
            <a href="manage_users.php" class="block py-2 px-4 bg-[#2E6B4F] rounded text-white font-medium">Registered Users</a>
            <a href="users.php" class="block py-2 px-4 hover:bg-[#2E6B4F] rounded text-gray-300 transition">Admin Users</a>
            <hr class="my-2 border-[#2E6B4F]">
            <div class="px-4 py-2 text-xs text-gray-400 uppercase tracking-wider">Settings</div>
            <a href="settings.php" class="block py-2 px-4 hover:bg-[#2E6B4F] rounded text-gray-300 transition">Settings</a>
        </nav>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto">
        <h1 class="text-3xl font-bold mb-6">Registered Users</h1>

        <?php if($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-4 rounded shadow border-l-4 border-blue-500">
                <p class="text-xs text-gray-500 uppercase">Total Users</p>
                <p class="text-2xl font-bold"><?php echo $total_users; ?></p>
            </div>
            <div class="bg-white p-4 rounded shadow border-l-4 border-yellow-500">
                <p class="text-xs text-gray-500 uppercase">Pending Approval</p>
                <p class="text-2xl font-bold text-yellow-600"><?php echo $pending_users; ?></p>
            </div>
            <div class="bg-white p-4 rounded shadow border-l-4 border-green-500">
                <p class="text-xs text-gray-500 uppercase">Active</p>
                <p class="text-2xl font-bold text-green-600"><?php echo $active_users; ?></p>
            </div>
            <div class="bg-white p-4 rounded shadow border-l-4 border-red-500">
                <p class="text-xs text-gray-500 uppercase">Blocked</p>
                <p class="text-2xl font-bold text-red-600"><?php echo $blocked_users; ?></p>
            </div>
        </div>

        <!-- Pending Approvals -->
        <?php if(!empty($pending_list)): ?>
        <div class="bg-yellow-50 border border-yellow-300 rounded shadow mb-6 p-6">
            <h2 class="text-xl font-bold text-yellow-800 mb-4">Pending Approvals (<?php echo count($pending_list); ?>)</h2>
            <div class="space-y-3">
                <?php foreach($pending_list as $pu): ?>
                <div class="bg-white rounded p-4 flex items-center justify-between border border-yellow-200">
                    <div>
                        <p class="font-bold text-gray-800"><?php echo htmlspecialchars($pu['name']); ?></p>
                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($pu['email']); ?> — Registered: <?php echo date('d M Y, h:i A', strtotime($pu['created_at'])); ?></p>
                    </div>
                    <div class="flex gap-2">
                        <a href="?approve=<?php echo $pu['id']; ?>" onclick="return confirm('Approve this user?')"
                           class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded font-bold text-sm">Approve</a>
                        <a href="?reject=<?php echo $pu['id']; ?>" onclick="return confirm('Reject and delete this registration?')"
                           class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded font-bold text-sm">Reject</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Add New User -->
        <div class="bg-white p-6 rounded shadow mb-6">
            <h2 class="text-xl font-bold mb-4">Add New User</h2>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <input type="hidden" name="action" value="add_user">
                <div>
                    <label class="block text-sm font-bold mb-1">Full Name</label>
                    <input type="text" name="name" required class="w-full border p-2 rounded" placeholder="Ali Raza">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Email</label>
                    <input type="email" name="email" required class="w-full border p-2 rounded" placeholder="ali@example.com">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Password</label>
                    <input type="password" name="password" required minlength="8" class="w-full border p-2 rounded" placeholder="Min 8 characters">
                </div>
                <div>
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-bold">Add User</button>
                </div>
            </form>
        </div>

        <!-- Search -->
        <form method="GET" class="mb-6">
            <div class="flex gap-2">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by name or email..." class="flex-1 border p-2 rounded">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded font-bold">Search</button>
                <?php if($search): ?>
                    <a href="manage_users.php" class="bg-gray-400 text-white px-4 py-2 rounded font-bold">Clear</a>
                <?php endif; ?>
            </div>
        </form>

        <!-- Users Table -->
        <div class="bg-white rounded shadow">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50">
                    <tr class="border-b">
                        <th class="p-3">ID</th>
                        <th class="p-3">Name</th>
                        <th class="p-3">Email</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Registered</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $u): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 text-sm text-gray-500">#<?php echo $u['id']; ?></td>
                        <td class="p-3 font-bold"><?php echo htmlspecialchars($u['name']); ?></td>
                        <td class="p-3 text-sm"><?php echo htmlspecialchars($u['email']); ?></td>
                        <td class="p-3">
                            <?php $status = $u['status'] ?? 'active'; ?>
                            <?php if($status === 'active'): ?>
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded font-bold">Active</span>
                            <?php elseif($status === 'pending'): ?>
                                <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded font-bold">Pending</span>
                            <?php else: ?>
                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded font-bold">Blocked</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-3 text-xs text-gray-500"><?php echo date('d M Y', strtotime($u['created_at'])); ?></td>
                        <td class="p-3">
                            <div class="flex gap-1 flex-wrap">
                                <?php if($status === 'pending'): ?>
                                    <a href="?approve=<?php echo $u['id']; ?>" onclick="return confirm('Approve this user?')"
                                       class="bg-green-500 text-white px-2 py-1 rounded text-xs">Approve</a>
                                    <a href="?reject=<?php echo $u['id']; ?>" onclick="return confirm('Reject this registration?')"
                                       class="bg-red-500 text-white px-2 py-1 rounded text-xs">Reject</a>
                                <?php else: ?>
                                    <button onclick="openEditModal(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars(addslashes($u['name'])); ?>', '<?php echo htmlspecialchars(addslashes($u['email'])); ?>')"
                                        class="bg-blue-500 text-white px-2 py-1 rounded text-xs">Edit</button>
                                    <button onclick="openPasswordModal(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars(addslashes($u['name'])); ?>')"
                                        class="bg-yellow-500 text-white px-2 py-1 rounded text-xs">Reset Pass</button>
                                    <?php if($status === 'active'): ?>
                                        <a href="?block=<?php echo $u['id']; ?>" onclick="return confirm('Block this user?')"
                                           class="bg-red-500 text-white px-2 py-1 rounded text-xs">Block</a>
                                    <?php else: ?>
                                        <a href="?unblock=<?php echo $u['id']; ?>"
                                           class="bg-green-500 text-white px-2 py-1 rounded text-xs">Unblock</a>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <a href="?delete=<?php echo $u['id']; ?>" onclick="return confirm('Permanently delete this user? This cannot be undone.')"
                                   class="bg-gray-500 text-white px-2 py-1 rounded text-xs">Delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($users)): ?>
                    <tr><td colspan="6" class="p-4 text-center text-gray-500">No users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Edit User Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded shadow-lg max-w-md w-full">
            <h3 class="text-xl font-bold mb-4">Edit User</h3>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="edit_user">
                <input type="hidden" name="user_id" id="editUserId">
                <div>
                    <label class="block text-sm font-bold mb-1">Name</label>
                    <input type="text" name="name" id="editName" required class="w-full border p-2 rounded">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Email</label>
                    <input type="email" name="email" id="editEmail" required class="w-full border p-2 rounded">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded font-bold flex-1">Save</button>
                    <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="bg-gray-400 text-white px-4 py-2 rounded font-bold flex-1">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div id="passwordModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded shadow-lg max-w-md w-full">
            <h3 class="text-xl font-bold mb-2">Reset Password</h3>
            <p class="text-sm text-gray-500 mb-4" id="passwordUserName"></p>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="reset_password">
                <input type="hidden" name="user_id" id="passwordUserId">
                <div>
                    <label class="block text-sm font-bold mb-1">New Password</label>
                    <input type="password" name="new_password" required minlength="8" class="w-full border p-2 rounded" placeholder="Min 8 characters">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded font-bold flex-1">Reset</button>
                    <button type="button" onclick="document.getElementById('passwordModal').classList.add('hidden')" class="bg-gray-400 text-white px-4 py-2 rounded font-bold flex-1">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, name, email) {
            document.getElementById('editUserId').value = id;
            document.getElementById('editName').value = name;
            document.getElementById('editEmail').value = email;
            document.getElementById('editModal').classList.remove('hidden');
        }
        function openPasswordModal(id, name) {
            document.getElementById('passwordUserId').value = id;
            document.getElementById('passwordUserName').textContent = 'Resetting password for: ' + name;
            document.getElementById('passwordModal').classList.remove('hidden');
        }
    </script>
</body>
</html>
