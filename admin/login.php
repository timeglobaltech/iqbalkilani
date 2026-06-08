<?php
require_once '../config.php';

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

// Account lockout settings
$max_attempts = 5;
$lockout_time = 900; // 15 minutes

if (!isset($_SESSION['login_attempts'])) $_SESSION['login_attempts'] = 0;
if (!isset($_SESSION['lockout_until'])) $_SESSION['lockout_until'] = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check lockout
    if ($_SESSION['login_attempts'] >= $max_attempts && time() < $_SESSION['lockout_until']) {
        $remaining = ceil(($_SESSION['lockout_until'] - time()) / 60);
        $error = "Too many failed attempts. Try again in $remaining minute(s).";
    } elseif (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid CSRF token.";
    } else {
        // Reset lockout if time has passed
        if (time() >= $_SESSION['lockout_until']) {
            $_SESSION['login_attempts'] = 0;
        }

        $username = sanitize($_POST['username']);
        $password = $_POST['password'];

        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = :user");
        $stmt->execute([':user' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['lockout_until'] = 0;
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            session_regenerate_id(true);
            header("Location: dashboard.php");
            exit;
        } else {
            $_SESSION['login_attempts']++;
            if ($_SESSION['login_attempts'] >= $max_attempts) {
                $_SESSION['lockout_until'] = time() + $lockout_time;
                $error = "Too many failed attempts. Account locked for 15 minutes.";
            } else {
                $remaining_attempts = $max_attempts - $_SESSION['login_attempts'];
                $error = "Invalid credentials. $remaining_attempts attempt(s) remaining.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Maktaba Quddusia</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
<body class="bg-gray-100 h-screen flex items-center justify-center font-serif">

<div class="bg-white p-8 rounded shadow-md w-full max-w-md border-t-4 border-gold">
    <div class="text-center mb-8">
        <h1 class="text-3xl text-green-deep font-bold">Admin Panel</h1>
        <p class="text-gray-500">Sign in to manage website content</p>
    </div>

    <?php if($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Username</label>
            <input type="text" name="username" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-green-mid focus:ring-1 focus:ring-green-mid" required>
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
            <input type="password" name="password" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-green-mid focus:ring-1 focus:ring-green-mid" required>
        </div>
        <button type="submit" class="w-full bg-green-deep text-white font-bold py-2 px-4 rounded hover:bg-green-mid transition">
            Login
        </button>
    </form>
    <p class="text-center text-xs text-gray-500 mt-6">Contact the administrator for login credentials.</p>
</div>

</body>
</html>
