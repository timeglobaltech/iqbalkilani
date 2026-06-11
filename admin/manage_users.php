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
    <script>
        tailwind.config = { theme: { extend: { colors: {
            'green-deep':'#1B3C2E','green-mid':'#2E6B4F','gold':'#C9960A','gold-light':'#E8B840'
        }}}}
    </script>
</head>
<body class="bg-gray-100 flex h-screen">
    <?php include 'sidebar.php'; ?>

    <main class="flex-1 p-8 overflow-y-auto">
        <h1 class="text-3xl font-bold text-green-deep mb-6">Registered Users</h1>

        <?php if($message): ?>
            <div class="bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded-lg mb-5 flex items-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-lg mb-5 flex items-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex items-center justify-between">
                <div>
                    <p class="text-3xl font-extrabold text-gray-800"><?php echo $total_users; ?></p>
                    <p class="text-xs text-gray-500 uppercase tracking-wider mt-1">Total Users</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4z"/></svg></div>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex items-center justify-between">
                <div>
                    <p class="text-3xl font-extrabold text-yellow-600"><?php echo $pending_users; ?></p>
                    <p class="text-xs text-gray-500 uppercase tracking-wider mt-1">Pending Approval</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-yellow-100 text-yellow-600 flex items-center justify-center"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex items-center justify-between">
                <div>
                    <p class="text-3xl font-extrabold text-green-600"><?php echo $active_users; ?></p>
                    <p class="text-xs text-gray-500 uppercase tracking-wider mt-1">Active</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-green-100 text-green-600 flex items-center justify-center"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex items-center justify-between">
                <div>
                    <p class="text-3xl font-extrabold text-red-600"><?php echo $blocked_users; ?></p>
                    <p class="text-xs text-gray-500 uppercase tracking-wider mt-1">Blocked</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-red-100 text-red-600 flex items-center justify-center"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg></div>
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
        <div class="bg-white p-6 md:p-7 rounded-xl shadow-sm border border-gray-100 mb-6">
            <h2 class="text-xl font-bold text-green-deep mb-5 flex items-center gap-2"><span class="w-1.5 h-6 bg-gold rounded-sm"></span> Add New User</h2>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <input type="hidden" name="action" value="add_user">
                <div>
                    <label class="block text-sm font-semibold text-green-deep mb-1.5">Full Name</label>
                    <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition" placeholder="Ali Raza">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-green-deep mb-1.5">Email</label>
                    <input type="email" name="email" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition" placeholder="ali@example.com">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-green-deep mb-1.5">Password</label>
                    <input type="password" name="password" required minlength="8" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition" placeholder="Min 8 characters">
                </div>
                <div>
                    <button type="submit" class="w-full bg-green-mid hover:bg-green-deep text-white px-4 py-2.5 rounded-lg font-bold shadow-sm hover:shadow-md transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        Add User
                    </button>
                </div>
            </form>
        </div>

        <!-- Search -->
        <form method="GET" class="mb-6">
            <div class="flex gap-2">
                <div class="relative flex-1">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></span>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by name or email..." class="w-full border border-gray-300 rounded-lg pl-11 pr-4 py-2.5 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition">
                </div>
                <button type="submit" class="bg-green-deep hover:bg-green-mid text-white px-6 py-2.5 rounded-lg font-bold transition">Search</button>
                <?php if($search): ?>
                    <a href="manage_users.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2.5 rounded-lg font-bold transition flex items-center">Clear</a>
                <?php endif; ?>
            </div>
        </form>

        <!-- Users Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                        <th class="p-4 font-semibold">ID</th>
                        <th class="p-4 font-semibold">Name</th>
                        <th class="p-4 font-semibold">Email</th>
                        <th class="p-4 font-semibold">Status</th>
                        <th class="p-4 font-semibold">Registered</th>
                        <th class="p-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $u): ?>
                    <tr class="border-b border-gray-100 last:border-b-0 hover:bg-gold/5 transition">
                        <td class="p-4 text-sm text-gray-400 font-mono">#<?php echo $u['id']; ?></td>
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-green-deep to-green-mid text-gold flex items-center justify-center text-sm font-bold flex-shrink-0"><?php echo strtoupper(substr(htmlspecialchars($u['name']), 0, 1)); ?></div>
                                <span class="font-semibold text-gray-800"><?php echo htmlspecialchars($u['name']); ?></span>
                            </div>
                        </td>
                        <td class="p-4 text-sm text-gray-600"><?php echo htmlspecialchars($u['email']); ?></td>
                        <td class="p-4">
                            <?php $status = $u['status'] ?? 'active'; ?>
                            <?php if($status === 'active'): ?>
                                <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 text-xs px-2.5 py-1 rounded-full font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Active</span>
                            <?php elseif($status === 'pending'): ?>
                                <span class="inline-flex items-center gap-1.5 bg-yellow-100 text-yellow-700 text-xs px-2.5 py-1 rounded-full font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> Pending</span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 bg-red-100 text-red-700 text-xs px-2.5 py-1 rounded-full font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Blocked</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 text-xs text-gray-500"><?php echo date('d M Y', strtotime($u['created_at'])); ?></td>
                        <td class="p-4">
                            <div class="flex gap-1.5 flex-wrap justify-end">
                                <?php if($status === 'pending'): ?>
                                    <a href="?approve=<?php echo $u['id']; ?>" onclick="return confirm('Approve this user?')" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition">Approve</a>
                                    <a href="?reject=<?php echo $u['id']; ?>" onclick="return confirm('Reject this registration?')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition">Reject</a>
                                <?php else: ?>
                                    <button onclick="openEditModal(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars(addslashes($u['name'])); ?>', '<?php echo htmlspecialchars(addslashes($u['email'])); ?>')" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition">Edit</button>
                                    <button onclick="openPasswordModal(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars(addslashes($u['name'])); ?>')" class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition">Reset Pass</button>
                                    <?php if($status === 'active'): ?>
                                        <a href="?block=<?php echo $u['id']; ?>" onclick="return confirm('Block this user?')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition">Block</a>
                                    <?php else: ?>
                                        <a href="?unblock=<?php echo $u['id']; ?>" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition">Unblock</a>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <a href="?delete=<?php echo $u['id']; ?>" onclick="return confirm('Permanently delete this user? This cannot be undone.')" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition">Delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($users)): ?>
                    <tr><td colspan="6" class="p-6 text-center text-gray-400">No users found.</td></tr>
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
