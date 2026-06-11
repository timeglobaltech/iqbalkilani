<?php
// ===== Shared Admin Sidebar =====
// Har admin page is file ko include karega.
$__cur = basename($_SERVER['PHP_SELF']);

// Badge counts (self-contained — agar $pdo available ho)
$__pf = 0; $__po = 0; $__pu = 0;
if (isset($pdo)) {
    try {
        $__pf = (int)$pdo->query("SELECT COUNT(*) FROM fatwas WHERE status='Pending'")->fetchColumn();
        $__po = (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
        $__pu = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status='pending'")->fetchColumn();
    } catch (Exception $e) {}
}

if (!function_exists('__navClass')) {
    function __navClass($page, $cur) {
        return $page === $cur
            ? 'block py-2 px-4 bg-[#2E6B4F] rounded text-white font-medium transition'
            : 'block py-2 px-4 hover:bg-[#2E6B4F] rounded text-gray-300 transition';
    }
}
?>
<!-- Admin font (Lato) — sab admin pages pe lagta hai -->
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
<style>
    body, .font-serif { font-family: 'Lato', sans-serif !important; }
</style>
<aside class="w-64 bg-[#1B3C2E] text-white flex-shrink-0 overflow-y-auto sticky top-0 h-screen">
    <div class="p-6 border-b border-[#2E6B4F] text-center">
        <img src="../image/logo-t.png" alt="Maktaba Quddusia" class="h-20 w-auto mx-auto mb-2">
        <h2 class="text-xl font-serif text-[#C9960A] font-bold">Admin Panel</h2>
        <p class="text-xs text-gray-300 mt-1">Maktaba Quddusia</p>
    </div>
    <nav class="p-4 space-y-2">
        <a href="dashboard.php" class="<?php echo __navClass('dashboard.php', $__cur); ?>">Dashboard</a>
        <a href="courses.php" class="<?php echo __navClass('courses.php', $__cur); ?>">Courses</a>
        <a href="fatwas.php" class="<?php echo __navClass('fatwas.php', $__cur); ?>">Fatwas
            <?php if($__pf > 0): ?><span class="bg-[#C9960A] text-white text-xs px-2 py-0.5 rounded-full ml-1"><?php echo $__pf; ?></span><?php endif; ?>
        </a>
        <a href="articles.php" class="<?php echo __navClass('articles.php', $__cur); ?>">Articles</a>
        <a href="books.php" class="<?php echo __navClass('books.php', $__cur); ?>">Books</a>
        <a href="orders.php" class="<?php echo __navClass('orders.php', $__cur); ?>">Orders
            <?php if($__po > 0): ?><span class="bg-[#C9960A] text-white text-xs px-2 py-0.5 rounded-full ml-1"><?php echo $__po; ?></span><?php endif; ?>
        </a>
        <a href="audios.php" class="<?php echo __navClass('audios.php', $__cur); ?>">Audio &amp; Voice</a>
        <hr class="my-2 border-[#2E6B4F]">
        <a href="manage_users.php" class="<?php echo __navClass('manage_users.php', $__cur); ?>">Registered Users
            <?php if($__pu > 0): ?><span class="bg-yellow-500 text-white text-xs px-2 py-0.5 rounded-full ml-1"><?php echo $__pu; ?></span><?php endif; ?>
        </a>
        <a href="users.php" class="<?php echo __navClass('users.php', $__cur); ?>">Admin Users</a>
        <div class="px-4 py-2 text-xs text-gray-400 uppercase tracking-wider">Settings</div>
        <a href="settings.php" class="<?php echo __navClass('settings.php', $__cur); ?>">Settings</a>
    </nav>
</aside>
