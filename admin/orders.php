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
    <script>
        tailwind.config = { theme: { extend: { colors: {
            'green-deep':'#1B3C2E','green-mid':'#2E6B4F','gold':'#C9960A','gold-light':'#E8B840'
        }}}}
    </script>
</head>
<body class="bg-gray-100 flex h-screen">
    <?php include 'sidebar.php'; ?>
    <main class="flex-1 p-8 overflow-y-auto">
        <h1 class="text-3xl font-bold text-green-deep mb-6">Physical Book Orders</h1>
        <?php if($message) echo "<div class='bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded-lg mb-5 flex items-center gap-2'><svg class='w-5 h-5 flex-shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'/></svg><span>$message</span></div>"; ?>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                        <th class="p-4 font-semibold">Order ID</th>
                        <th class="p-4 font-semibold">Customer</th>
                        <th class="p-4 font-semibold">Book</th>
                        <th class="p-4 font-semibold">Address</th>
                        <th class="p-4 font-semibold">Status</th>
                        <th class="p-4 font-semibold">Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($orders as $o): ?>
                    <tr class="border-b border-gray-100 last:border-b-0 hover:bg-gold/5 transition align-top">
                        <td class="p-4 text-sm font-mono text-gray-400">#<?php echo str_pad($o['id'], 5, '0', STR_PAD_LEFT); ?></td>
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-green-deep to-green-mid text-gold flex items-center justify-center text-sm font-bold flex-shrink-0"><?php echo strtoupper(substr(htmlspecialchars($o['customer_name']), 0, 1)); ?></div>
                                <div>
                                    <div class="font-semibold text-gray-800"><?php echo htmlspecialchars($o['customer_name']); ?></div>
                                    <div class="text-xs text-gray-500"><?php echo htmlspecialchars($o['customer_phone']); ?></div>
                                    <div class="text-xs text-gray-500"><?php echo htmlspecialchars($o['customer_email']); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 font-medium text-sm text-gray-700"><?php echo htmlspecialchars($o['book_title']); ?></td>
                        <td class="p-4 text-xs text-gray-500 w-48"><?php echo htmlspecialchars($o['shipping_address']); ?></td>
                        <td class="p-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs rounded-full font-semibold <?php
                                echo $o['status'] === 'Pending' ? 'bg-yellow-100 text-yellow-700' :
                                    ($o['status'] === 'Shipped' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700');
                            ?>"><span class="w-1.5 h-1.5 rounded-full <?php echo $o['status'] === 'Pending' ? 'bg-yellow-500' : ($o['status'] === 'Shipped' ? 'bg-blue-500' : 'bg-green-500'); ?>"></span><?php echo htmlspecialchars($o['status']); ?></span>
                        </td>
                        <td class="p-4">
                            <form method="POST">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                <select name="status" class="border border-gray-300 rounded-lg px-3 py-1.5 text-xs bg-white focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition cursor-pointer" onchange="this.form.submit()">
                                    <option value="Pending" <?php echo $o['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Shipped" <?php echo $o['status'] == 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="Completed" <?php echo $o['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($orders)): ?>
                    <tr><td colspan="6" class="p-6 text-center text-gray-400">No orders placed yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Order Status History -->
        <?php
        $history = $pdo->query("SELECT h.*, o.customer_name FROM order_history h JOIN orders o ON h.order_id = o.id ORDER BY h.changed_at DESC LIMIT 20")->fetchAll();
        ?>
        <?php if(!empty($history)): ?>
        <h2 class="text-2xl font-bold text-green-deep mt-10 mb-4 flex items-center gap-2"><span class="w-1.5 h-6 bg-gold rounded-sm"></span> Status Change History</h2>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                        <th class="p-4 font-semibold">Order</th>
                        <th class="p-4 font-semibold">Customer</th>
                        <th class="p-4 font-semibold">Change</th>
                        <th class="p-4 font-semibold">Changed By</th>
                        <th class="p-4 font-semibold">Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($history as $h): ?>
                    <tr class="border-b border-gray-100 last:border-b-0 hover:bg-gold/5 transition">
                        <td class="p-4 font-mono text-sm text-gray-400">#<?php echo str_pad($h['order_id'], 5, '0', STR_PAD_LEFT); ?></td>
                        <td class="p-4 font-medium text-sm text-gray-700"><?php echo htmlspecialchars($h['customer_name']); ?></td>
                        <td class="p-4 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?php
                                    echo $h['old_status'] === 'Pending' ? 'bg-yellow-100 text-yellow-700' :
                                        ($h['old_status'] === 'Shipped' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700');
                                ?>"><?php echo htmlspecialchars($h['old_status']); ?></span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?php
                                    echo $h['new_status'] === 'Pending' ? 'bg-yellow-100 text-yellow-700' :
                                        ($h['new_status'] === 'Shipped' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700');
                                ?>"><?php echo htmlspecialchars($h['new_status']); ?></span>
                            </div>
                        </td>
                        <td class="p-4 text-sm text-gray-600"><?php echo htmlspecialchars($h['changed_by']); ?></td>
                        <td class="p-4 text-xs text-gray-500"><?php echo date('d M Y, h:i A', strtotime($h['changed_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </main>
</body>
</html>
