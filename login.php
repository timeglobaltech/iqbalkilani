<?php
require_once 'config.php';

// Redirect if already logged in (before any HTML output)
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

// Account lockout settings
$max_attempts = 5;
$lockout_time = 900; // 15 minutes

if (!isset($_SESSION['user_login_attempts'])) $_SESSION['user_login_attempts'] = 0;
if (!isset($_SESSION['user_lockout_until'])) $_SESSION['user_lockout_until'] = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_SESSION['user_login_attempts'] >= $max_attempts && time() < $_SESSION['user_lockout_until']) {
        $remaining = ceil(($_SESSION['user_lockout_until'] - time()) / 60);
        $error = "Too many failed attempts. Try again in $remaining minute(s).";
    } else {
        if (time() >= $_SESSION['user_lockout_until']) {
            $_SESSION['user_login_attempts'] = 0;
        }

        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if (empty($email) || empty($password)) {
            $error = 'Please fill in all fields.';
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && ($user['status'] ?? 'active') === 'blocked') {
                $error = 'Your account has been blocked. Please contact admin.';
            } elseif ($user && ($user['status'] ?? 'active') === 'pending') {
                $error = 'Your account is pending approval. Please wait for admin to approve your registration.';
            } elseif ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_login_attempts'] = 0;
                $_SESSION['user_lockout_until'] = 0;
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_logged_in'] = true;
                header("Location: index.php");
                exit;
            } else {
                $_SESSION['user_login_attempts']++;
                if ($_SESSION['user_login_attempts'] >= $max_attempts) {
                    $_SESSION['user_lockout_until'] = time() + $lockout_time;
                    $error = "Too many failed attempts. Try again in 15 minutes.";
                } else {
                    $error = 'Invalid email or password.';
                }
            }
        }
    }
}

require_once 'includes/header.php';
?>

<div class="bg-beige min-h-screen flex items-center justify-center py-20 px-4">
    <div class="bg-white max-w-md w-full p-8 rounded-lg shadow-lg border-t-4 border-green-deep">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-english text-green-deep font-bold">Welcome Back</h2>
            <p class="text-gray-500 text-sm mt-2">Log in to continue your Islamic learning journey</p>
        </div>

        <?php if (!empty($_GET['msg'])): ?>
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4 text-sm font-semibold text-center"><?php echo htmlspecialchars($_GET['msg']); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm font-semibold text-center"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php" class="space-y-5">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" required class="w-full border border-gray-300 rounded px-4 py-2 focus:border-green-mid outline-none transition" placeholder="ali@example.com">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required class="w-full border border-gray-300 rounded px-4 py-2 focus:border-green-mid outline-none transition" placeholder="••••••••">
            </div>
            <button type="submit" class="w-full bg-gold hover:bg-gold-light text-white font-bold py-3 rounded transition shadow-md">Login</button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">Don't have an account yet? <a href="register.php" class="text-green-deep font-bold hover:underline">Sign up</a></p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
