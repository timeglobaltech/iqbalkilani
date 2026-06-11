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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=Lateef:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        cream: '#F7F3EC',
                        beige: '#EDE5D5',
                        'green-deep': '#1B3C2E',
                        'green-mid': '#2E6B4F',
                        gold: '#C9960A',
                        'gold-light': '#E8B840',
                        'gold-pale': '#F5E4A8',
                        muted: '#7A6B56',
                    },
                    fontFamily: {
                        english: ['"Cormorant Garamond"', 'serif'],
                        arabic: ['Lateef', 'serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Cormorant Garamond', serif; }
        .arabic-text { font-family: 'Lateef', serif; }
        @keyframes fadeUp { from { opacity:0; transform: translateY(28px); } to { opacity:1; transform: translateY(0); } }
        .animate-fade-up { animation: fadeUp 0.6s ease-out both; }
        @keyframes float { 0%,100%{ transform: translateY(0); } 50%{ transform: translateY(-8px); } }
        .animate-float { animation: float 3.5s ease-in-out infinite; }
        .login-bg {
            background-color: #1B3C2E;
            background-image:
                linear-gradient(rgba(27,60,46,0.90), rgba(27,60,46,0.90)),
                url('../image/hero-bg.png');
            background-size: cover;
            background-position: center;
        }
        .input-field { transition: all 0.25s ease; }
        .input-field:focus { border-color:#C9960A; box-shadow: 0 0 0 3px rgba(201,150,10,0.18); }
    </style>
</head>
<body class="login-bg min-h-screen flex items-center justify-center font-english p-4">

<div class="bg-white p-8 md:p-10 rounded-2xl shadow-2xl w-full max-w-md border-t-4 border-gold animate-fade-up">
    <!-- Icon + Heading -->
    <div class="text-center mb-8">
        <img src="../image/logo-t.png" alt="Maktaba Quddusia" class="animate-float mx-auto h-24 w-auto mb-3">
        <h3 class="arabic-text text-gold text-2xl mb-1">انتظامیہ</h3>
        <h1 class="font-english text-3xl text-green-deep font-bold">Admin Panel</h1>
        <p class="text-muted text-sm mt-1">Sign in to manage website content</p>
    </div>

    <?php if($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-5 text-sm flex items-center gap-2 animate-fade-up">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span><?php echo $error; ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" action="" class="space-y-5">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div>
            <label class="block text-green-deep text-sm font-bold mb-2">Username</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </span>
                <input type="text" name="username" required class="input-field w-full pl-11 pr-3 py-3 border border-gray-300 rounded-lg outline-none">
            </div>
        </div>
        <div>
            <label class="block text-green-deep text-sm font-bold mb-2">Password</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                </span>
                <input type="password" name="password" required class="input-field w-full pl-11 pr-3 py-3 border border-gray-300 rounded-lg outline-none">
            </div>
        </div>
        <button type="submit" id="loginBtn" class="w-full bg-green-deep text-white font-bold py-3 px-4 rounded-lg hover:bg-green-mid hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0 transition-all duration-300">
            Login
        </button>
    </form>
    <p class="text-center text-xs text-gray-400 mt-6">Contact the administrator for login credentials.</p>
</div>

<script>
// Login click par button loading transition + card fade-out
document.querySelector('form').addEventListener('submit', function() {
    var btn = document.getElementById('loginBtn');
    if (!btn) return;
    btn.disabled = true;
    btn.classList.add('opacity-80', 'cursor-not-allowed');
    btn.innerHTML = '<span class="inline-flex items-center justify-center gap-2">' +
        '<svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">' +
        '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>' +
        '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.37 0 0 5.37 0 12h4z"></path>' +
        '</svg> Signing in...</span>';
});
</script>

</body>
</html>
