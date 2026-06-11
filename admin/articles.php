<?php
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    // Logout
    if ($_POST['action'] === 'logout') {
        if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
            session_destroy();
            header("Location: login.php");
            exit;
        }
    }

    // Publish Article
    if ($_POST['action'] === 'publish') {
        $title    = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $date     = trim($_POST['date'] ?? '');
        $excerpt  = trim($_POST['excerpt'] ?? '');
        $content  = trim($_POST['content'] ?? '');

        if ($title && $category && $date) {
            $stmt = $pdo->prepare("INSERT INTO articles (title, category, date_published, excerpt, content) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $category, $date, $excerpt, $content]);
        }
        header("Location: articles.php");
        exit;
    }

    // Edit Article
    if ($_POST['action'] === 'edit') {
        $id       = (int)($_POST['id'] ?? 0);
        $title    = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $date     = trim($_POST['date'] ?? '');
        $excerpt  = trim($_POST['excerpt'] ?? '');
        $content  = trim($_POST['content'] ?? '');

        if ($id && $title && $category && $date) {
            $stmt = $pdo->prepare("UPDATE articles SET title=?, category=?, date_published=?, excerpt=?, content=? WHERE id=?");
            $stmt->execute([$title, $category, $date, $excerpt, $content, $id]);
        }
        header("Location: articles.php");
        exit;
    }

    // Delete Article
    if ($_POST['action'] === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $pdo->prepare("DELETE FROM articles WHERE id=?")->execute([$id]);
        }
        header("Location: articles.php");
        exit;
    }
}

// Fetch all articles
$articles = $pdo->query("SELECT * FROM articles ORDER BY date_published DESC")->fetchAll();

// Fetch pending fatwas count for sidebar badge
$pending_fatwas = $pdo->query("SELECT COUNT(*) FROM fatwas WHERE status = 'Pending'")->fetchColumn();
$pending_users  = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'pending'")->fetchColumn();

// Today's date for min date
$today = date('Y-m-d');

// Edit prefill — if ?edit=ID passed via GET
$edit_article = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id=?");
    $stmt->execute([$eid]);
    $edit_article = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Articles - Maktaba Quddusia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'green-deep': '#1B3C2E',
                        'green-mid':  '#2E6B4F',
                        gold:         '#C9960A',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 font-sans">

<div class="min-h-screen flex flex-col md:flex-row">

    <!-- ═══════════════════════════════
         SIDEBAR  (exact copy from dashboard)
    ════════════════════════════════════ -->
    <?php include 'sidebar.php'; ?>

    <!-- ═══════════════════════════════
         MAIN CONTENT
    ════════════════════════════════════ -->
    <main class="flex-1 p-8">

        <!-- Header -->
        <header class="flex justify-between items-center mb-8 border-b pb-4">
            <h1 class="text-3xl font-bold text-gray-800">Manage Articles</h1>
            <div class="flex items-center space-x-4">
                <span class="text-gray-600">Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong></span>
                <form method="POST" action="" class="inline">
                    <input type="hidden" name="action" value="logout">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm transition">Logout</button>
                </form>
            </div>
        </header>

        <!-- ── PUBLISH / EDIT FORM ── -->
        <div class="bg-white rounded shadow mb-8">
            <div class="p-4 border-b border-gray-200 bg-gray-50 rounded-t">
                <h2 class="text-lg font-bold text-gray-800">
                    <?php echo $edit_article ? 'Edit Article' : 'Publish New Article'; ?>
                </h2>
            </div>
            <div class="p-6">
                <form method="POST" action="articles.php">
                    <input type="hidden" name="action" value="<?php echo $edit_article ? 'edit' : 'publish'; ?>">
                    <?php if ($edit_article): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_article['id']; ?>">
                    <?php endif; ?>

                    <!-- Title -->
                    <input
                        type="text"
                        name="title"
                        placeholder="Title"
                        value="<?php echo $edit_article ? htmlspecialchars($edit_article['title']) : ''; ?>"
                        required
                        class="w-full border border-gray-300 rounded px-4 py-2 mb-4 text-sm focus:outline-none focus:border-gold"
                    />

                    <!-- Category + Date -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <select name="category" required class="border border-gray-300 rounded px-4 py-2 text-sm focus:outline-none focus:border-gold bg-white">
                            <option value="" disabled selected>Category</option>
                            <?php
                            $cats = ['Spirituality','Fiqh','Hadith','Quran','Seerah','General'];
                            foreach ($cats as $cat):
                                $sel = ($edit_article && $edit_article['category'] === $cat) ? 'selected' : '';
                            ?>
                                <option value="<?php echo $cat; ?>" <?php echo $sel; ?>><?php echo $cat; ?></option>
                            <?php endforeach; ?>
                        </select>

                        <input
                            type="date"
                            name="date"
                            min="<?php echo $today; ?>"
                            value="<?php echo $edit_article ? htmlspecialchars($edit_article['date_published']) : $today; ?>"
                            required
                            class="border border-gray-300 rounded px-4 py-2 text-sm focus:outline-none focus:border-gold"
                        />
                    </div>

                    <!-- Short Excerpt -->
                    <textarea
                        name="excerpt"
                        placeholder="Short Excerpt"
                        rows="2"
                        class="w-full border border-gray-300 rounded px-4 py-2 mb-4 text-sm focus:outline-none focus:border-gold resize-y"
                    ><?php echo $edit_article ? htmlspecialchars($edit_article['excerpt']) : ''; ?></textarea>

                    <!-- Full Content -->
                    <textarea
                        name="content"
                        placeholder="Full Content"
                        rows="6"
                        class="w-full border border-gray-300 rounded px-4 py-2 mb-4 text-sm focus:outline-none focus:border-gold resize-y"
                    ><?php echo $edit_article ? htmlspecialchars($edit_article['content']) : ''; ?></textarea>

                    <div class="flex gap-3">
                        <button type="submit"
                            class="flex-1 bg-gold hover:bg-yellow-600 text-white font-semibold py-3 rounded transition text-sm">
                            <?php echo $edit_article ? 'Save Changes' : 'Publish Article'; ?>
                        </button>
                        <?php if ($edit_article): ?>
                            <a href="articles.php"
                               class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded transition text-sm flex items-center">
                                Cancel
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- ── ARTICLES TABLE ── -->
        <div class="bg-white rounded shadow">
            <div class="p-4 border-b border-gray-200 bg-gray-50 rounded-t">
                <h2 class="text-lg font-bold text-gray-800">All Articles</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                            <th class="p-4 border-b border-gray-200 font-semibold">Title</th>
                            <th class="p-4 border-b border-gray-200 font-semibold">Category</th>
                            <th class="p-4 border-b border-gray-200 font-semibold">Date</th>
                            <th class="p-4 border-b border-gray-200 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($articles)): ?>
                            <tr>
                                <td colspan="4" class="p-4 text-center text-gray-500">No articles yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($articles as $a): ?>
                                <tr class="hover:bg-gray-50 transition border-b border-gray-100 last:border-b-0">
                                    <td class="p-4 text-sm text-gray-800"><?php echo htmlspecialchars($a['title']); ?></td>
                                    <td class="p-4 text-sm">
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                                            <?php echo htmlspecialchars($a['category']); ?>
                                        </span>
                                    </td>
                                    <td class="p-4 text-sm text-gray-600"><?php echo htmlspecialchars($a['date_published']); ?></td>
                                    <td class="p-4 text-sm text-right">
                                        <div class="flex justify-end gap-2">
                                            <!-- Edit -->
                                            <a href="articles.php?edit=<?php echo $a['id']; ?>"
                                               class="bg-green-mid hover:bg-green-deep text-white px-3 py-1 rounded transition text-xs">
                                                Edit
                                            </a>
                                            <!-- Delete -->
                                            <form method="POST" action="articles.php" onsubmit="return confirm('Delete this article?')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $a['id']; ?>">
                                                <button type="submit"
                                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded transition text-xs">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

</body>
</html>