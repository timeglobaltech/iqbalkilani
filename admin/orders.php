<?php
require_once '../config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header("Location: login.php"); exit; }

$message = '';

// Update Order Status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];

    // Get current status before updating
    $old = $pdo->prepare("SELECT status FROM orders WHERE id = ?");
    $old->execute([$order_id]);
    $old_status = $old->fetchColumn();

    if ($old_status !== $new_status) {
        // Update order
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);

        // Log history
        $hist = $pdo->prepare("INSERT INTO order_history (order_id, old_status, new_status, changed_by) VALUES (?, ?, ?, ?)");
        $hist->execute([$order_id, $old_status, $new_status, $_SESSION['admin_username'] ?? 'admin']);

        $message = "Order #" . str_pad($order_id, 5, '0', STR_PAD_LEFT) . " status changed: $old_status → $new_status";
    }
}

// Fetch Orders
$orders = $pdo->query("SELECT o.*, b.title as book_title FROM orders o JOIN books b ON o.book_id = b.id ORDER BY o.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Manage Orders - Admin</title>
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
            <a href="orders.php" class="block py-2 px-4 bg-[#2E6B4F] rounded text-white font-medium">Orders</a>
            <a href="audios.php" class="block py-2 px-4 hover:bg-[#2E6B4F] rounded text-gray-300 transition">Audio & Voice</a>
            <hr class="my-2 border-[#2E6B4F]">
            <a href="manage_users.php" class="block py-2 px-4 hover:bg-[#2E6B4F] rounded text-gray-300 transition">Registered Users</a>
            <a href="users.php" class="block py-2 px-4 hover:bg-[#2E6B4F] rounded text-gray-300 transition">Admin Users</a>
             <div class="px-4 py-2 text-xs text-gray-400 uppercase tracking-wider">Settings</div>
            <a href="settings.php" class="block py-2 px-4 hover:bg-green-mid rounded text-gray-300 transition">Settings</a>
        </nav>
    </aside>
    <main class="flex-1 p-8 overflow-y-auto">
        <h1 class="text-3xl font-bold mb-6">Physical Book Orders</h1>
        <?php if($message) echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'>$message</div>"; ?>
        
        <div class="bg-white rounded shadow">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50">
                    <tr class="border-b">
                        <th class="p-3">Order ID</th>
                        <th class="p-3">Customer</th>
                        <th class="p-3">Book</th>
                        <th class="p-3">Address</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($orders as $o): ?>
                    <tr class="border-b hover:bg-gray-50 align-top">
                        <td class="p-3 text-sm font-mono text-gray-500">#<?php echo str_pad($o['id'], 5, '0', STR_PAD_LEFT); ?></td>
                        <td class="p-3">
                            <div class="font-bold"><?php echo htmlspecialchars($o['customer_name']); ?></div>
                            <div class="text-xs text-gray-500"><?php echo htmlspecialchars($o['customer_phone']); ?></div>
                            <div class="text-xs text-gray-500"><?php echo htmlspecialchars($o['customer_email']); ?></div>
                        </td>
                        <td class="p-3 font-semibold text-sm"><?php echo htmlspecialchars($o['book_title']); ?></td>
                        <td class="p-3 text-xs w-48"><?php echo htmlspecialchars($o['shipping_address']); ?></td>
                        <td class="p-3">
                            <span class="px-2 py-1 text-xs rounded font-bold <?php 
                                echo $o['status'] === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 
                                    ($o['status'] === 'Shipped' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'); 
                            ?>"><?php echo htmlspecialchars($o['status']); ?></span>
                        </td>
                        <td class="p-3">
                            <form method="POST" class="flex flex-col gap-1">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                <select name="status" class="border rounded px-2 py-1 text-xs" onchange="this.form.submit()">
                                    <option value="Pending" <?php echo $o['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Shipped" <?php echo $o['status'] == 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="Completed" <?php echo $o['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($orders)): ?>
                    <tr><td colspan="6" class="p-4 text-center text-gray-500">No orders placed yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Order Status History -->
        <?php
        $history = $pdo->query("SELECT h.*, o.customer_name FROM order_history h JOIN orders o ON h.order_id = o.id ORDER BY h.changed_at DESC LIMIT 20")->fetchAll();
        ?>
        <?php if(!empty($history)): ?>
        <h2 class="text-2xl font-bold mt-10 mb-4">Status Change History</h2>
        <div class="bg-white rounded shadow">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50">
                    <tr class="border-b">
                        <th class="p-3">Order</th>
                        <th class="p-3">Customer</th>
                        <th class="p-3">Change</th>
                        <th class="p-3">Changed By</th>
                        <th class="p-3">Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($history as $h): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 font-mono text-sm text-gray-500">#<?php echo str_pad($h['order_id'], 5, '0', STR_PAD_LEFT); ?></td>
                        <td class="p-3 font-semibold text-sm"><?php echo htmlspecialchars($h['customer_name']); ?></td>
                        <td class="p-3 text-sm">
                            <span class="px-2 py-1 rounded text-xs font-bold <?php
                                echo $h['old_status'] === 'Pending' ? 'bg-yellow-100 text-yellow-800' :
                                    ($h['old_status'] === 'Shipped' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800');
                            ?>"><?php echo htmlspecialchars($h['old_status']); ?></span>
                            <span class="mx-1 text-gray-400">→</span>
                            <span class="px-2 py-1 rounded text-xs font-bold <?php
                                echo $h['new_status'] === 'Pending' ? 'bg-yellow-100 text-yellow-800' :
                                    ($h['new_status'] === 'Shipped' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800');
                            ?>"><?php echo htmlspecialchars($h['new_status']); ?></span>
                        </td>
                        <td class="p-3 text-sm text-gray-600"><?php echo htmlspecialchars($h['changed_by']); ?></td>
                        <td class="p-3 text-xs text-gray-500"><?php echo date('d M Y, h:i A', strtotime($h['changed_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </main>
</body>
</html>
