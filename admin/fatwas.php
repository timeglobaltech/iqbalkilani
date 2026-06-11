<?php
require_once '../config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header("Location: login.php"); exit; }

$message = '';

// Answer / Update Fatwa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'answer') {
    $stmt = $pdo->prepare("UPDATE fatwas SET answer_text = ?, status = 'Published', answered_at = NOW() WHERE id = ?");
    $stmt->execute([$_POST['answer_text'], $_POST['id']]);
    $message = "Fatwa answered and published successfully.";
}

// Fetch all fatwas
$fatwas = $pdo->query("SELECT * FROM fatwas ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Manage Fatwas - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex h-screen">
    <?php include 'sidebar.php'; ?>
    <main class="flex-1 p-8 overflow-y-auto">
        <h1 class="text-3xl font-bold mb-6">Manage Fatwa Q&A</h1>
        <?php if($message) echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'>$message</div>"; ?>
        
        <div class="space-y-6">
            <?php foreach($fatwas as $f): ?>
            <div class="bg-white p-6 rounded shadow border-l-4 <?php echo $f['status'] == 'Pending' ? 'border-red-500' : 'border-green-500'; ?>">
                <div class="flex justify-between mb-2">
                    <span class="font-mono text-gray-500">Ref: <?php echo htmlspecialchars($f['reference_no']); ?> | User: <?php echo htmlspecialchars($f['user_name']); ?></span>
                    <span class="px-2 py-1 text-xs rounded <?php echo $f['status'] == 'Pending' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'; ?>"><?php echo htmlspecialchars($f['status']); ?></span>
                </div>
                <h3 class="text-lg font-bold mb-4 border-b pb-2">Q: <?php echo htmlspecialchars($f['question_text']); ?></h3>
                
                <?php if($f['status'] == 'Pending'): ?>
                    <form method="POST" class="mt-4">
                        <input type="hidden" name="action" value="answer">
                        <input type="hidden" name="id" value="<?php echo $f['id']; ?>">
                        <textarea name="answer_text" rows="4" required class="w-full border rounded p-3 mb-3 focus:outline-none focus:ring focus:border-[#C9960A]" placeholder="Write the answer here..."></textarea>
                        <button type="submit" class="bg-[#1B3C2E] hover:bg-[#2E6B4F] text-white px-6 py-2 rounded font-bold">Publish Answer</button>
                    </form>
                <?php else: ?>
                    <div class="bg-gray-50 p-4 rounded text-gray-700 border-l-2 border-[#C9960A]">
                        <strong>A:</strong> <?php echo nl2br(htmlspecialchars($f['answer_text'])); ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>
