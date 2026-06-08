<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once 'includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $error = 'Password must contain both letters and numbers.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email is already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, status) VALUES (?, ?, ?, 'pending')");
            if ($stmt->execute([$name, $email, $hash])) {
                $success = 'Registration successful! Your account is pending admin approval. You will be able to login once approved.';
            } else {
                $error = 'Something went wrong. Please try again.';
            }
        }
    }
}
?>

<div class="bg-beige min-h-screen flex items-center justify-center py-20 px-4">
    <div class="bg-white max-w-md w-full p-8 rounded-lg shadow-lg border-t-4 border-gold">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-english text-green-deep font-bold">Create an Account</h2>
            <p class="text-gray-500 text-sm mt-2">Join Maktaba Quddusia to enroll in courses</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm font-semibold text-center"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm font-semibold text-center"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php" class="space-y-5">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name</label>
                <input type="text" name="name" required class="w-full border border-gray-300 rounded px-4 py-2 focus:border-green-mid outline-none transition" placeholder="Ali Raza">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" required class="w-full border border-gray-300 rounded px-4 py-2 focus:border-green-mid outline-none transition" placeholder="ali@example.com">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required class="w-full border border-gray-300 rounded px-4 py-2 focus:border-green-mid outline-none transition" placeholder="••••••••">
            </div>
            <button type="submit" class="w-full bg-green-deep hover:bg-green-mid text-white font-bold py-3 rounded transition shadow-md">Sign Up</button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">Already have an account? <a href="login.php" class="text-gold font-bold hover:underline">Log in</a></p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
