<?php
require_once '../config.php';

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Fetch stats for sidebar badges
$stats = [];
$stats['pending_fatwas'] = $pdo->query("SELECT COUNT(*) FROM fatwas WHERE status = 'Pending'")->fetchColumn();
$stats['pending_users'] = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'pending'")->fetchColumn();

// Handle Logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'logout') {
    if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
        session_destroy();
        header("Location: login.php");
        exit;
    }
}

// ============================================================
// CREATE TABLES IF NOT EXIST
// ============================================================
$pdo->exec("
    CREATE TABLE IF NOT EXISTS islamic_scholars (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        specialization VARCHAR(255) DEFAULT NULL,
        bio TEXT DEFAULT NULL,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS subjects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        islamic_scholar_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        description TEXT DEFAULT NULL,
        book_id INT DEFAULT NULL,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (islamic_scholar_id) REFERENCES islamic_scholars(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS topics (
        id INT AUTO_INCREMENT PRIMARY KEY,
        subject_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        description TEXT DEFAULT NULL,
        book_id INT DEFAULT NULL,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS concepts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        topic_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        description TEXT DEFAULT NULL,
        book_id INT DEFAULT NULL,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE CASCADE
    );
");

// ============================================================
// HANDLE POST REQUESTS
// ============================================================
$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] !== 'logout') {
    $action = $_POST['action'] ?? '';

    // ISLAMIC SCHOLAR
    if ($action === 'add_scholar') {
        $name = trim($_POST['name'] ?? '');
        $spec = trim($_POST['specialization'] ?? '');
        $bio  = trim($_POST['bio'] ?? '');
        if ($name !== '') {
            $stmt = $pdo->prepare("INSERT INTO islamic_scholars (name, specialization, bio) VALUES (?, ?, ?)");
            $stmt->execute([$name, $spec, $bio]);
            $success = 'Islamic Scholar added successfully.';
        } else {
            $error = 'Scholar name is required.';
        }
    }
    elseif ($action === 'edit_scholar') {
        $id   = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $spec = trim($_POST['specialization'] ?? '');
        if ($id && $name !== '') {
            $pdo->prepare("UPDATE islamic_scholars SET name=?, specialization=? WHERE id=?")->execute([$name, $spec, $id]);
            $success = 'Scholar updated successfully.';
        }
    }
    elseif ($action === 'delete_scholar') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $pdo->prepare("DELETE FROM islamic_scholars WHERE id=?")->execute([$id]);
            $success = 'Scholar deleted successfully.';
        }
    }

    // SUBJECT
    elseif ($action === 'add_subject') {
        $scholar_id = (int)($_POST['islamic_scholar_id'] ?? 0);
        $name       = trim($_POST['name'] ?? '');
        $book_id    = !empty($_POST['book_id']) ? (int)$_POST['book_id'] : null;
        if ($scholar_id && $name !== '') {
            $pdo->prepare("INSERT INTO subjects (islamic_scholar_id, name, book_id) VALUES (?, ?, ?)")->execute([$scholar_id, $name, $book_id]);
            $success = 'Subject added successfully.';
        } else {
            $error = 'Subject name and Scholar are required.';
        }
    }
    elseif ($action === 'edit_subject') {
        $id      = (int)($_POST['id'] ?? 0);
        $name    = trim($_POST['name'] ?? '');
        $book_id = !empty($_POST['book_id']) ? (int)$_POST['book_id'] : null;
        if ($id && $name !== '') {
            $pdo->prepare("UPDATE subjects SET name=?, book_id=? WHERE id=?")->execute([$name, $book_id, $id]);
            $success = 'Subject updated successfully.';
        }
    }
    elseif ($action === 'delete_subject') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $pdo->prepare("DELETE FROM subjects WHERE id=?")->execute([$id]);
            $success = 'Subject deleted successfully.';
        }
    }

    // TOPIC
    elseif ($action === 'add_topic') {
        $subject_id = (int)($_POST['subject_id'] ?? 0);
        $name       = trim($_POST['name'] ?? '');
        $book_id    = !empty($_POST['book_id']) ? (int)$_POST['book_id'] : null;
        if ($subject_id && $name !== '') {
            $pdo->prepare("INSERT INTO topics (subject_id, name, book_id) VALUES (?, ?, ?)")->execute([$subject_id, $name, $book_id]);
            $success = 'Topic added successfully.';
        } else {
            $error = 'Topic name and Subject are required.';
        }
    }
    elseif ($action === 'edit_topic') {
        $id      = (int)($_POST['id'] ?? 0);
        $name    = trim($_POST['name'] ?? '');
        $book_id = !empty($_POST['book_id']) ? (int)$_POST['book_id'] : null;
        if ($id && $name !== '') {
            $pdo->prepare("UPDATE topics SET name=?, book_id=? WHERE id=?")->execute([$name, $book_id, $id]);
            $success = 'Topic updated successfully.';
        }
    }
    elseif ($action === 'delete_topic') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $pdo->prepare("DELETE FROM topics WHERE id=?")->execute([$id]);
            $success = 'Topic deleted successfully.';
        }
    }

    // CONCEPT
    elseif ($action === 'add_concept') {
        $topic_id = (int)($_POST['topic_id'] ?? 0);
        $name     = trim($_POST['name'] ?? '');
        $book_id  = !empty($_POST['book_id']) ? (int)$_POST['book_id'] : null;
        if ($topic_id && $name !== '') {
            $pdo->prepare("INSERT INTO concepts (topic_id, name, book_id) VALUES (?, ?, ?)")->execute([$topic_id, $name, $book_id]);
            $success = 'Concept added successfully.';
        } else {
            $error = 'Concept name and Topic are required.';
        }
    }
    elseif ($action === 'edit_concept') {
        $id      = (int)($_POST['id'] ?? 0);
        $name    = trim($_POST['name'] ?? '');
        $book_id = !empty($_POST['book_id']) ? (int)$_POST['book_id'] : null;
        if ($id && $name !== '') {
            $pdo->prepare("UPDATE concepts SET name=?, book_id=? WHERE id=?")->execute([$name, $book_id, $id]);
            $success = 'Concept updated successfully.';
        }
    }
    elseif ($action === 'delete_concept') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $pdo->prepare("DELETE FROM concepts WHERE id=?")->execute([$id]);
            $success = 'Concept deleted successfully.';
        }
    }
}

// ============================================================
// FETCH ALL DATA WITH HIERARCHY
// ============================================================
$all_books = $pdo->query("SELECT id, title, title_urdu FROM books ORDER BY id DESC")->fetchAll();
$book_map = [];
foreach ($all_books as $b) {
    $book_map[$b['id']] = $b['title_urdu'] ? $b['title_urdu'] : $b['title'];
}

$scholars = $pdo->query("SELECT * FROM islamic_scholars ORDER BY name")->fetchAll();

foreach ($scholars as &$scholar) {
    $stmt = $pdo->prepare("SELECT s.*, b.title as b_title, b.title_urdu as b_urdu FROM subjects s LEFT JOIN books b ON s.book_id = b.id WHERE s.islamic_scholar_id = ? ORDER BY s.name");
    $stmt->execute([$scholar['id']]);
    $scholar['subjects'] = $stmt->fetchAll();

    foreach ($scholar['subjects'] as &$subject) {
        $stmt = $pdo->prepare("SELECT t.*, b.title as b_title, b.title_urdu as b_urdu FROM topics t LEFT JOIN books b ON t.book_id = b.id WHERE t.subject_id = ? ORDER BY t.name");
        $stmt->execute([$subject['id']]);
        $subject['topics'] = $stmt->fetchAll();

        foreach ($subject['topics'] as &$topic) {
            $stmt = $pdo->prepare("SELECT c.*, b.title as b_title, b.title_urdu as b_urdu FROM concepts c LEFT JOIN books b ON c.book_id = b.id WHERE c.topic_id = ? ORDER BY c.name");
            $stmt->execute([$topic['id']]);
            $topic['concepts'] = $stmt->fetchAll();
        }
    }
}
unset($scholar, $subject, $topic);

// Flat lists for List View
$all_subjects = [];
foreach ($scholars as $sc) {
    foreach ($sc['subjects'] as $sub) {
        $sub['scholar_name'] = $sc['name'];
        $all_subjects[] = $sub;
    }
}

$all_topics = [];
foreach ($scholars as $sc) {
    foreach ($sc['subjects'] as $sub) {
        foreach ($sub['topics'] as $top) {
            $top['subject_name'] = $sub['name'];
            $top['scholar_name'] = $sc['name'];
            $all_topics[] = $top;
        }
    }
}

$all_concepts = [];
foreach ($scholars as $sc) {
    foreach ($sc['subjects'] as $sub) {
        foreach ($sub['topics'] as $top) {
            foreach ($top['concepts'] as $con) {
                $con['topic_name']   = $top['name'];
                $con['subject_name'] = $sub['name'];
                $all_concepts[] = $con;
            }
        }
    }
}

// Get active tab from query string (default: hierarchy)
$active_tab = $_GET['tab'] ?? 'hierarchy';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Knowledge Hierarchy - Admin Dashboard</title>
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
    <style>
        /* Fix scrolling: sidebar fixed, main content scrolls independently */
        .admin-container {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }
        
        .sidebar-fixed {
            width: 16rem;
            flex-shrink: 0;
            overflow-y: auto;
            height: 100vh;
            position: sticky;
            top: 0;
        }
        
        .main-scroll {
            flex: 1;
            overflow-y: auto;
            height: 100vh;
        }
        
        .legend { display: flex; gap: 16px; margin-bottom: 16px; flex-wrap: wrap; }
        .leg-item { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #444; }
        .leg-dot { width: 10px; height: 10px; border-radius: 50%; }

        /* Tree System */
        .tree-wrap { background: #fff; border: 1px solid #d1d5db; border-radius: 8px; padding: 24px; width: 100%; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06); }
        .add-root-btn { border: 1.5px dashed #5DCAA5; background: #E1F5EE; color: #0F6E56; padding: 8px 20px; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s; }
        .add-root-btn:hover { background: #C8EBDC; border-color: #0F6E56; }

        .scholar-block { margin-bottom: 20px; }
        .node-row { display: flex; align-items: center; gap: 6px; margin-bottom: 4px; width: 100%; }

        .node-scholar { display: flex; align-items: center; justify-content: space-between; padding: 10px 18px; background: #E1F5EE; border: 1px solid #5DCAA5; border-radius: 8px; color: #085041; font-size: 14px; font-weight: 600; flex: 1; }
        .node-subject  { display: flex; align-items: center; justify-content: space-between; padding: 8px 16px; background: #EEEDFE; border: 1px solid #AFA9EC; border-radius: 6px; color: #3C3489; font-size: 13px; font-weight: 500; flex: 1; }
        .node-topic    { display: flex; align-items: center; justify-content: space-between; padding: 8px 16px; background: #FAEEDA; border: 1px solid #EF9F27; border-radius: 6px; color: #633806; font-size: 13px; font-weight: 500; flex: 1; }
        .node-concept  { display: flex; align-items: center; justify-content: space-between; padding: 8px 16px; background: #FAECE7; border: 1px solid #F0997B; border-radius: 6px; color: #712B13; font-size: 13px; flex: 1; }
        .node-count { font-size: 11px; font-weight: 400; opacity: 0.7; margin-left: 8px; }
        .book-badge { font-size: 10px; background: rgba(0,0,0,0.05); padding: 2px 6px; border-radius: 12px; margin-left: 8px; }

        .acts { display: flex; gap: 6px; flex-shrink: 0; }
        .act-edit { background: #1B3C2E; color: #fff; border: none; border-radius: 4px; padding: 4px 12px; font-size: 11px; cursor: pointer; transition: background 0.2s; }
        .act-edit:hover { background: #2E6B4F; }
        .act-del  { background: #ef4444; color: #fff; border: none; border-radius: 4px; padding: 4px 12px; font-size: 11px; cursor: pointer; transition: background 0.2s; }
        .act-del:hover { background: #dc2626; }

        .children-l1 { padding-left: 30px; border-left: 2px solid #c8e6c9; margin-left: 12px; margin-top: 8px; margin-bottom: 8px; }
        .children-l2 { padding-left: 30px; border-left: 2px solid #d1c4e9; margin-left: 12px; margin-top: 8px; margin-bottom: 8px; }
        .children-l3 { padding-left: 30px; border-left: 2px solid #ffe0b2; margin-left: 12px; margin-top: 8px; margin-bottom: 8px; }

        .add-btn-sub { border: 1.5px dashed #AFA9EC; background: #EEEDFE; color: #534AB7; padding: 6px 16px; border-radius: 6px; font-size: 12px; font-weight: 500; cursor: pointer; margin-bottom: 8px; transition: all 0.2s; }
        .add-btn-sub:hover { background: #DDDDFD; border-color: #534AB7; }
        .add-btn-top { border: 1.5px dashed #EF9F27; background: #FAEEDA; color: #854F0B; padding: 6px 16px; border-radius: 6px; font-size: 12px; font-weight: 500; cursor: pointer; margin-bottom: 8px; transition: all 0.2s; }
        .add-btn-top:hover { background: #F6E2C2; border-color: #854F0B; }
        .add-btn-con { border: 1.5px dashed #F0997B; background: #FAECE7; color: #993C1D; padding: 6px 16px; border-radius: 6px; font-size: 12px; font-weight: 500; cursor: pointer; margin-bottom: 8px; transition: all 0.2s; }
        .add-btn-con:hover { background: #F5DACF; border-color: #993C1D; }

        /* Tab styles */
        .tab-container { margin-bottom: 24px; }
        .tab-buttons { display: flex; gap: 0; border-bottom: 2px solid #e5e7eb; margin-bottom: 24px; flex-wrap: wrap; background: white; border-radius: 12px 12px 0 0; padding: 0 4px; }
        .tab-btn { padding: 12px 28px; font-size: 15px; font-weight: 600; color: #6b7280; cursor: pointer; border-bottom: 3px solid transparent; margin-bottom: -2px; transition: all 0.2s; background: none; border-top: none; border-left: none; border-right: none; }
        .tab-btn:hover { color: #1B3C2E; background: #f9fafb; }
        .tab-btn.active { color: #1B3C2E; border-bottom-color: #1B3C2E; background: #f0fdf4; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        /* Table styles */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { padding: 12px 16px; text-align: left; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #374151; border-bottom: 1px solid #e5e7eb; background: #f9fafb; }
        .data-table td { padding: 12px 16px; font-size: 13px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table tr:hover td { background: #f9fafb; }

        .section-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); margin-bottom: 24px; overflow: hidden; }
        .section-header { display: flex; justify-content: space-between; align-items: center; padding: 18px 24px; border-bottom: 1px solid #f3f4f6; }
        .section-header h2 { font-size: 18px; font-weight: 700; color: #1f2937; margin: 0; }
        .section-body { padding: 0; overflow-x: auto; }

        /* Color-coded section headers */
        .hdr-scholar { background: linear-gradient(90deg, #E1F5EE 0%, #fff 100%); border-left: 4px solid #1D9E75; }
        .hdr-subject  { background: linear-gradient(90deg, #EEEDFE 0%, #fff 100%); border-left: 4px solid #7F77DD; }
        .hdr-topic    { background: linear-gradient(90deg, #FAEEDA 0%, #fff 100%); border-left: 4px solid #EF9F27; }
        .hdr-concept  { background: linear-gradient(90deg, #FAECE7 0%, #fff 100%); border-left: 4px solid #D85A30; }
        .hdr-hierarchy { background: linear-gradient(90deg, #E0F2FE 0%, #fff 100%); border-left: 4px solid #0284C7; }

        .badge-scholar { background: #E1F5EE; color: #085041; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block; }
        .badge-subject { background: #EEEDFE; color: #3C3489; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block; }
        .badge-topic   { background: #FAEEDA; color: #633806; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block; }
        .badge-count   { background: #f3f4f6; color: #374151; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 500; }

        .action-buttons { display: flex; gap: 8px; }
        
        /* Modal styles */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center; }
        .modal-overlay.open { display: flex; }
        .modal-box { background: #fff; border-radius: 12px; padding: 28px; width: 480px; max-width: 95%; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04); max-height: 90vh; overflow-y: auto; }
        .modal-box h3 { font-size: 20px; font-weight: 700; margin-bottom: 20px; color: #1a1a1a; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 13px; color: #555; margin-bottom: 6px; font-weight: 600; }
        .form-group input,
        .form-group textarea,
        .form-group select { width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none; background: #fff; transition: border-color 0.2s; }
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus { border-color: #1B3C2E; outline: none; ring: 2px solid #1B3C2E; }
        .modal-actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; }
        .btn-cancel { background: #f4f4f6; color: #555; border: 1px solid #ddd; border-radius: 8px; padding: 8px 20px; font-size: 14px; cursor: pointer; transition: all 0.2s; font-weight: 500; }
        .btn-cancel:hover { background: #e5e5e7; }
        .btn-save   { background: #1B3C2E; color: #fff; border: none; border-radius: 8px; padding: 8px 24px; font-size: 14px; cursor: pointer; font-weight: 600; transition: background 0.2s; }
        .btn-save:hover { background: #2E6B4F; }
        
        .btn-add { background: #1B3C2E; color: white; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 500; border: none; cursor: pointer; transition: background 0.2s; }
        .btn-add:hover { background: #2E6B4F; }
        
        /* Hide scrollbar for cleaner look */
        .sidebar-fixed::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-fixed::-webkit-scrollbar-track {
            background: #1B3C2E;
        }
        .sidebar-fixed::-webkit-scrollbar-thumb {
            background: #2E6B4F;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="admin-container">
    <!-- Sidebar - Fixed / Sticky -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content - Scrollable -->
    <main class="main-scroll bg-gray-100">
        <div class="p-8">
            <header class="flex justify-between items-center mb-8 border-b pb-4">
                <h1 class="text-3xl font-bold text-gray-800">Settings - Knowledge Hierarchy</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'admin'); ?></strong></span>
                    <form method="POST" action="" class="inline">
                        <input type="hidden" name="action" value="logout">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm transition">Logout</button>
                    </form>
                </div>
            </header>

            <div class="max-w-7xl mx-auto">

                <?php if ($success): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <!-- Tab Buttons -->
                <div class="tab-container">
                    <div class="tab-buttons">
                        <button class="tab-btn <?php echo $active_tab === 'hierarchy' ? 'active' : ''; ?>" data-tab="hierarchy">🌲 Hierarchy View</button>
                        <button class="tab-btn <?php echo $active_tab === 'scholars' ? 'active' : ''; ?>" data-tab="scholars">🎓 Islamic Scholars</button>
                        <button class="tab-btn <?php echo $active_tab === 'subjects' ? 'active' : ''; ?>" data-tab="subjects">📚 Subjects</button>
                        <button class="tab-btn <?php echo $active_tab === 'topics' ? 'active' : ''; ?>" data-tab="topics">📖 Topics</button>
                        <button class="tab-btn <?php echo $active_tab === 'concepts' ? 'active' : ''; ?>" data-tab="concepts">💡 Concepts</button>
                    </div>

                    <!-- Tab: Hierarchy View (Tree Structure) -->
                    <div id="tab-hierarchy" class="tab-content <?php echo $active_tab === 'hierarchy' ? 'active' : ''; ?>">
                        <div class="legend mb-4">
                            <div class="leg-item"><div class="leg-dot" style="background:#1D9E75"></div> Islamic Scholar</div>
                            <div class="leg-item"><div class="leg-dot" style="background:#7F77DD"></div> Subject</div>
                            <div class="leg-item"><div class="leg-dot" style="background:#EF9F27"></div> Topic</div>
                            <div class="leg-item"><div class="leg-dot" style="background:#D85A30"></div> Concept</div>
                        </div>

                        <div class="tree-wrap">
                            <div class="mb-4">
                                <button class="add-root-btn" onclick="openModal('addScholar')">+ Add Islamic Scholar</button>
                            </div>

                            <?php foreach ($scholars as $scholar): ?>
                            <div class="scholar-block">
                                <div class="node-row">
                                    <div class="node-scholar">
                                        <span><?= htmlspecialchars($scholar['name']) ?></span>
                                        <span class="node-count">(<?= count($scholar['subjects']) ?> subjects)</span>
                                    </div>
                                    <div class="acts">
                                        <button class="act-edit" onclick="openEditModal('scholar', <?= $scholar['id'] ?>, '<?= htmlspecialchars($scholar['name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($scholar['specialization'] ?? '', ENT_QUOTES) ?>')">Edit</button>
                                        <form method="POST" style="display:inline" onsubmit="return confirm('Delete this scholar and all its nested data?')">
                                            <input type="hidden" name="action" value="delete_scholar">
                                            <input type="hidden" name="id" value="<?= $scholar['id'] ?>">
                                            <button type="submit" class="act-del">Del</button>
                                        </form>
                                    </div>
                                </div>

                                <div class="children-l1">
                                    <div class="mb-2">
                                        <button class="add-btn-sub" onclick="openHierarchyModal('addSubject', <?= $scholar['id'] ?>)">+ Add Subject under this Scholar</button>
                                    </div>

                                    <?php foreach ($scholar['subjects'] as $subject): ?>
                                    <div class="mb-3">
                                        <div class="node-row">
                                            <div class="node-subject">
                                                <span><?= htmlspecialchars($subject['name']) ?></span>
                                                <span class="node-count">(<?= count($subject['topics']) ?> topics)</span>
                                                <?php if(!empty($subject['book_id'])): ?>
                                                <span class="book-badge">📖 <?= htmlspecialchars($subject['b_urdu'] ?: $subject['b_title']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="acts">
                                                <button class="act-edit" onclick="openEditModal('subject', <?= $subject['id'] ?>, '<?= htmlspecialchars($subject['name'], ENT_QUOTES) ?>', '', '<?= $subject['book_id'] ?>')">Edit</button>
                                                <form method="POST" style="display:inline" onsubmit="return confirm('Delete this subject?')">
                                                    <input type="hidden" name="action" value="delete_subject">
                                                    <input type="hidden" name="id" value="<?= $subject['id'] ?>">
                                                    <button type="submit" class="act-del">Del</button>
                                                </form>
                                            </div>
                                        </div>

                                        <div class="children-l2">
                                            <div class="mb-2">
                                                <button class="add-btn-top" onclick="openHierarchyModal('addTopic', <?= $subject['id'] ?>)">+ Add Topic under this Subject</button>
                                            </div>

                                            <?php foreach ($subject['topics'] as $topic): ?>
                                            <div class="mb-3">
                                                <div class="node-row">
                                                    <div class="node-topic">
                                                        <span><?= htmlspecialchars($topic['name']) ?></span>
                                                        <span class="node-count">(<?= count($topic['concepts']) ?> concepts)</span>
                                                        <?php if(!empty($topic['book_id'])): ?>
                                                        <span class="book-badge">📖 <?= htmlspecialchars($topic['b_urdu'] ?: $topic['b_title']) ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="acts">
                                                        <button class="act-edit" onclick="openEditModal('topic', <?= $topic['id'] ?>, '<?= htmlspecialchars($topic['name'], ENT_QUOTES) ?>', '', '<?= $topic['book_id'] ?>')">Edit</button>
                                                        <form method="POST" style="display:inline" onsubmit="return confirm('Delete this topic?')">
                                                            <input type="hidden" name="action" value="delete_topic">
                                                            <input type="hidden" name="id" value="<?= $topic['id'] ?>">
                                                            <button type="submit" class="act-del">Del</button>
                                                        </form>
                                                    </div>
                                                </div>

                                                <div class="children-l3">
                                                    <div class="mb-2">
                                                        <button class="add-btn-con" onclick="openHierarchyModal('addConcept', <?= $topic['id'] ?>)">+ Add Concept under this Topic</button>
                                                    </div>

                                                    <?php foreach ($topic['concepts'] as $concept): ?>
                                                    <div class="mb-2">
                                                        <div class="node-row">
                                                            <div class="node-concept">
                                                                <?= htmlspecialchars($concept['name']) ?>
                                                                <?php if(!empty($concept['book_id'])): ?>
                                                                <span class="book-badge">📖 <?= htmlspecialchars($concept['b_urdu'] ?: $concept['b_title']) ?></span>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="acts">
                                                                <button class="act-edit" onclick="openEditModal('concept', <?= $concept['id'] ?>, '<?= htmlspecialchars($concept['name'], ENT_QUOTES) ?>', '', '<?= $concept['book_id'] ?>')">Edit</button>
                                                                <form method="POST" style="display:inline" onsubmit="return confirm('Delete this concept?')">
                                                                    <input type="hidden" name="action" value="delete_concept">
                                                                    <input type="hidden" name="id" value="<?= $concept['id'] ?>">
                                                                    <button type="submit" class="act-del">Del</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>

                            <?php if (empty($scholars)): ?>
                            <div class="text-center text-gray-400 py-8 text-sm">No scholars added yet. Click "+ Add Islamic Scholar" to start.</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Tab: Islamic Scholars (List View) -->
                    <div id="tab-scholars" class="tab-content <?php echo $active_tab === 'scholars' ? 'active' : ''; ?>">
                        <div class="section-card">
                            <div class="section-header hdr-scholar">
                                <h2>🎓 Islamic Scholars <span class="badge-count ml-2"><?= count($scholars) ?></span></h2>
                                <button class="btn-add" onclick="openModal('addScholar')">+ Add New Scholar</button>
                            </div>
                            <div class="section-body">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Specialization</th>
                                            <th style="text-align:center;">Subjects</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($scholars as $scholar): ?>
                                        <tr>
                                            <td class="text-gray-400"><?= $scholar['id'] ?></td>
                                            <td class="font-medium text-gray-900"><?= htmlspecialchars($scholar['name']) ?></td>
                                            <td class="text-gray-500"><?= htmlspecialchars($scholar['specialization'] ?? '—') ?></td>
                                            <td style="text-align:center;"><span class="badge-count"><?= count($scholar['subjects']) ?></span></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button class="act-edit" onclick="openEditModal('scholar', <?= $scholar['id'] ?>, '<?= htmlspecialchars($scholar['name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($scholar['specialization'] ?? '', ENT_QUOTES) ?>')">Edit</button>
                                                    <form method="POST" style="display:inline" onsubmit="return confirm('Delete this scholar and ALL nested data?')">
                                                        <input type="hidden" name="action" value="delete_scholar">
                                                        <input type="hidden" name="id" value="<?= $scholar['id'] ?>">
                                                        <button type="submit" class="act-del">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($scholars)): ?>
                                        <tr><td colspan="5" class="text-center text-gray-400 py-8">No scholars found. Click "+ Add New Scholar" to get started.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Subjects (List View) -->
                    <div id="tab-subjects" class="tab-content <?php echo $active_tab === 'subjects' ? 'active' : ''; ?>">
                        <div class="section-card">
                            <div class="section-header hdr-subject">
                                <h2>📚 Subjects <span class="badge-count ml-2"><?= count($all_subjects) ?></span></h2>
                                <button class="btn-add" onclick="openModal('addSubjectList')">+ Add New Subject</button>
                            </div>
                            <div class="section-body">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Subject Name</th>
                                            <th>Scholar</th>
                                            <th>Book</th>
                                            <th style="text-align:center;">Topics</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($all_subjects as $subject): ?>
                                        <tr>
                                            <td class="text-gray-400"><?= $subject['id'] ?></td>
                                            <td class="font-medium text-gray-900"><?= htmlspecialchars($subject['name']) ?></td>
                                            <td><span class="badge-scholar"><?= htmlspecialchars($subject['scholar_name']) ?></span></td>
                                            <td class="text-gray-500 text-xs"><?= !empty($subject['book_id']) ? htmlspecialchars($subject['b_urdu'] ?: $subject['b_title']) : '—' ?></td>
                                            <td style="text-align:center;"><span class="badge-count"><?= count($subject['topics']) ?></span></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button class="act-edit" onclick="openEditModal('subject', <?= $subject['id'] ?>, '<?= htmlspecialchars($subject['name'], ENT_QUOTES) ?>', '', '<?= $subject['book_id'] ?>')">Edit</button>
                                                    <form method="POST" style="display:inline" onsubmit="return confirm('Delete this subject? It will also delete all topics and concepts under it.')">
                                                        <input type="hidden" name="action" value="delete_subject">
                                                        <input type="hidden" name="id" value="<?= $subject['id'] ?>">
                                                        <button type="submit" class="act-del">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($all_subjects)): ?>
                                        <tr><td colspan="6" class="text-center text-gray-400 py-8">No subjects found. Click "+ Add New Subject" to get started.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Topics (List View) -->
                    <div id="tab-topics" class="tab-content <?php echo $active_tab === 'topics' ? 'active' : ''; ?>">
                        <div class="section-card">
                            <div class="section-header hdr-topic">
                                <h2>📖 Topics <span class="badge-count ml-2"><?= count($all_topics) ?></span></h2>
                                <button class="btn-add" onclick="openModal('addTopicList')">+ Add New Topic</button>
                            </div>
                            <div class="section-body">
                                <table class="data-table">
                                    <thead>
                                        <tr style="background:#fffbeb;">
                                            <th>ID</th>
                                            <th>Topic Name</th>
                                            <th>Subject</th>
                                            <th>Scholar</th>
                                            <th>Book</th>
                                            <th style="text-align:center;">Concepts</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($all_topics as $topic): ?>
                                        <tr>
                                            <td class="text-gray-400"><?= $topic['id'] ?></td>
                                            <td class="font-medium text-gray-900"><?= htmlspecialchars($topic['name']) ?></td>
                                            <td><span class="badge-subject"><?= htmlspecialchars($topic['subject_name']) ?></span></td>
                                            <td class="text-gray-500 text-xs"><?= htmlspecialchars($topic['scholar_name']) ?></td>
                                            <td class="text-gray-500 text-xs"><?= !empty($topic['book_id']) ? htmlspecialchars($topic['b_urdu'] ?: $topic['b_title']) : '—' ?></td>
                                            <td style="text-align:center;"><span class="badge-count"><?= count($topic['concepts']) ?></span></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button class="act-edit" onclick="openEditModal('topic', <?= $topic['id'] ?>, '<?= htmlspecialchars($topic['name'], ENT_QUOTES) ?>', '', '<?= $topic['book_id'] ?>')">Edit</button>
                                                    <form method="POST" style="display:inline" onsubmit="return confirm('Delete this topic? It will also delete all concepts under it.')">
                                                        <input type="hidden" name="action" value="delete_topic">
                                                        <input type="hidden" name="id" value="<?= $topic['id'] ?>">
                                                        <button type="submit" class="act-del">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($all_topics)): ?>
                                        <tr><td colspan="7" class="text-center text-gray-400 py-8">No topics found. Click "+ Add New Topic" to get started.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Concepts (List View) -->
                    <div id="tab-concepts" class="tab-content <?php echo $active_tab === 'concepts' ? 'active' : ''; ?>">
                        <div class="section-card">
                            <div class="section-header hdr-concept">
                                <h2>💡 Concepts <span class="badge-count ml-2"><?= count($all_concepts) ?></span></h2>
                                <button class="btn-add" onclick="openModal('addConceptList')">+ Add New Concept</button>
                            </div>
                            <div class="section-body">
                                <table class="data-table">
                                    <thead>
                                        <tr style="background:#fff7f5;">
                                            <th>ID</th>
                                            <th>Concept Name</th>
                                            <th>Topic</th>
                                            <th>Subject</th>
                                            <th>Book</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($all_concepts as $concept): ?>
                                        <tr>
                                            <td class="text-gray-400"><?= $concept['id'] ?></td>
                                            <td class="font-medium text-gray-900"><?= htmlspecialchars($concept['name']) ?></td>
                                            <td><span class="badge-topic"><?= htmlspecialchars($concept['topic_name']) ?></span></td>
                                            <td class="text-gray-500 text-xs"><?= htmlspecialchars($concept['subject_name']) ?></td>
                                            <td class="text-gray-500 text-xs"><?= !empty($concept['book_id']) ? htmlspecialchars($concept['b_urdu'] ?: $concept['b_title']) : '—' ?></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button class="act-edit" onclick="openEditModal('concept', <?= $concept['id'] ?>, '<?= htmlspecialchars($concept['name'], ENT_QUOTES) ?>', '', '<?= $concept['book_id'] ?>')">Edit</button>
                                                    <form method="POST" style="display:inline" onsubmit="return confirm('Delete this concept?')">
                                                        <input type="hidden" name="action" value="delete_concept">
                                                        <input type="hidden" name="id" value="<?= $concept['id'] ?>">
                                                        <button type="submit" class="act-del">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($all_concepts)): ?>
                                        <tr><td colspan="6" class="text-center text-gray-400 py-8">No concepts found. Click "+ Add New Concept" to get started.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- ============================================================
     MODALS (Same as before)
     ============================================================ -->

<!-- Add Scholar Modal -->
<div class="modal-overlay" id="modal-addScholar">
    <div class="modal-box">
        <h3>🎓 Add Islamic Scholar</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add_scholar">
            <div class="form-group">
                <label>Name *</label>
                <input type="text" name="name" placeholder="Scholar name" required>
            </div>
            <div class="form-group">
                <label>Specialization</label>
                <input type="text" name="specialization" placeholder="e.g. Fiqh, Hadith">
            </div>
            <div class="form-group">
                <label>Bio</label>
                <textarea name="bio" rows="3" placeholder="Short biography..."></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('addScholar')">Cancel</button>
                <button type="submit" class="btn-save">Add Scholar</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Subject Modal (with scholar dropdown for list view) -->
<div class="modal-overlay" id="modal-addSubjectList">
    <div class="modal-box">
        <h3>📚 Add Subject</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add_subject">
            <div class="form-group">
                <label>Select Scholar *</label>
                <select name="islamic_scholar_id" required>
                    <option value="">-- Select Scholar --</option>
                    <?php foreach($scholars as $sc): ?>
                    <option value="<?= $sc['id'] ?>"><?= htmlspecialchars($sc['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Subject Name *</label>
                <input type="text" name="name" placeholder="e.g. Fiqh, Aqeedah" required>
            </div>
            <div class="form-group">
                <label>Related Book (Optional)</label>
                <select name="book_id">
                    <option value="">-- No Book --</option>
                    <?php foreach($all_books as $b): ?>
                    <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['title_urdu'] ?: $b['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('addSubjectList')">Cancel</button>
                <button type="submit" class="btn-save">Add Subject</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Topic Modal (with subject dropdown for list view) -->
<div class="modal-overlay" id="modal-addTopicList">
    <div class="modal-box">
        <h3>📖 Add Topic</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add_topic">
            <div class="form-group">
                <label>Select Subject *</label>
                <select name="subject_id" required>
                    <option value="">-- Select Subject --</option>
                    <?php foreach($all_subjects as $sub): ?>
                    <option value="<?= $sub['id'] ?>"><?= htmlspecialchars($sub['name']) ?> (<?= htmlspecialchars($sub['scholar_name']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Topic Name *</label>
                <input type="text" name="name" placeholder="e.g. Salah, Zakat" required>
            </div>
            <div class="form-group">
                <label>Related Book (Optional)</label>
                <select name="book_id">
                    <option value="">-- No Book --</option>
                    <?php foreach($all_books as $b): ?>
                    <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['title_urdu'] ?: $b['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('addTopicList')">Cancel</button>
                <button type="submit" class="btn-save">Add Topic</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Concept Modal (with topic dropdown for list view) -->
<div class="modal-overlay" id="modal-addConceptList">
    <div class="modal-box">
        <h3>💡 Add Concept</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add_concept">
            <div class="form-group">
                <label>Select Topic *</label>
                <select name="topic_id" required>
                    <option value="">-- Select Topic --</option>
                    <?php foreach($all_topics as $top): ?>
                    <option value="<?= $top['id'] ?>"><?= htmlspecialchars($top['name']) ?> (<?= htmlspecialchars($top['subject_name']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Concept Name *</label>
                <input type="text" name="name" placeholder="e.g. Wajib, Halal" required>
            </div>
            <div class="form-group">
                <label>Related Book (Optional)</label>
                <select name="book_id">
                    <option value="">-- No Book --</option>
                    <?php foreach($all_books as $b): ?>
                    <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['title_urdu'] ?: $b['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('addConceptList')">Cancel</button>
                <button type="submit" class="btn-save">Add Concept</button>
            </div>
        </form>
    </div>
</div>

<!-- Hidden modals for hierarchy view (with parent ID) -->
<div class="modal-overlay" id="modal-addSubject">
    <div class="modal-box">
        <h3>📚 Add Subject</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add_subject">
            <input type="hidden" name="islamic_scholar_id" id="hierarchy-scholar-id">
            <div class="form-group">
                <label>Subject Name *</label>
                <input type="text" name="name" placeholder="e.g. Fiqh, Aqeedah" required>
            </div>
            <div class="form-group">
                <label>Related Book (Optional)</label>
                <select name="book_id">
                    <option value="">-- No Book --</option>
                    <?php foreach($all_books as $b): ?>
                    <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['title_urdu'] ?: $b['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('addSubject')">Cancel</button>
                <button type="submit" class="btn-save">Add Subject</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="modal-addTopic">
    <div class="modal-box">
        <h3>📖 Add Topic</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add_topic">
            <input type="hidden" name="subject_id" id="hierarchy-subject-id">
            <div class="form-group">
                <label>Topic Name *</label>
                <input type="text" name="name" placeholder="e.g. Salah, Zakat" required>
            </div>
            <div class="form-group">
                <label>Related Book (Optional)</label>
                <select name="book_id">
                    <option value="">-- No Book --</option>
                    <?php foreach($all_books as $b): ?>
                    <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['title_urdu'] ?: $b['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('addTopic')">Cancel</button>
                <button type="submit" class="btn-save">Add Topic</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="modal-addConcept">
    <div class="modal-box">
        <h3>💡 Add Concept</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add_concept">
            <input type="hidden" name="topic_id" id="hierarchy-topic-id">
            <div class="form-group">
                <label>Concept Name *</label>
                <input type="text" name="name" placeholder="e.g. Wajib, Halal" required>
            </div>
            <div class="form-group">
                <label>Related Book (Optional)</label>
                <select name="book_id">
                    <option value="">-- No Book --</option>
                    <?php foreach($all_books as $b): ?>
                    <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['title_urdu'] ?: $b['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('addConcept')">Cancel</button>
                <button type="submit" class="btn-save">Add Concept</button>
            </div>
        </form>
    </div>
</div>

<!-- Global Edit Modal -->
<div class="modal-overlay" id="modal-edit">
    <div class="modal-box">
        <h3 id="edit-modal-title">Edit</h3>
        <form method="POST">
            <input type="hidden" name="action" id="edit-action">
            <input type="hidden" name="id"     id="edit-id">
            <div class="form-group">
                <label>Name *</label>
                <input type="text" name="name" id="edit-name" required>
            </div>
            <div class="form-group" id="edit-spec-group" style="display:none">
                <label>Specialization</label>
                <input type="text" name="specialization" id="edit-specialization">
            </div>
            <div class="form-group" id="edit-book-group" style="display:none">
                <label>Related Book (Optional)</label>
                <select name="book_id" id="edit-book-id">
                    <option value="">-- No Book --</option>
                    <?php foreach($all_books as $b): ?>
                    <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['title_urdu'] ?: $b['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('edit')">Cancel</button>
                <button type="submit" class="btn-save">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================
     JAVASCRIPT
     ============================================================ -->
<script>
// Tab switching functionality
document.querySelectorAll('.tab-btn').forEach(button => {
    button.addEventListener('click', function() {
        const tabName = this.getAttribute('data-tab');
        
        // Update URL without reload
        const url = new URL(window.location.href);
        url.searchParams.set('tab', tabName);
        window.history.pushState({}, '', url);
        
        // Update active states for buttons
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');
        
        // Update active states for content
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        document.getElementById(`tab-${tabName}`).classList.add('active');
    });
});

// Modal functions
function openModal(type) {
    document.getElementById('modal-' + type).classList.add('open');
}

// For hierarchy view - opens modal with parent ID
function openHierarchyModal(type, parentId) {
    if (type === 'addSubject') {
        document.getElementById('hierarchy-scholar-id').value = parentId;
        document.getElementById('modal-addSubject').classList.add('open');
    } else if (type === 'addTopic') {
        document.getElementById('hierarchy-subject-id').value = parentId;
        document.getElementById('modal-addTopic').classList.add('open');
    } else if (type === 'addConcept') {
        document.getElementById('hierarchy-topic-id').value = parentId;
        document.getElementById('modal-addConcept').classList.add('open');
    } else {
        openModal(type);
    }
}

function closeModal(type) {
    const modal = document.getElementById('modal-' + type);
    if (modal) modal.classList.remove('open');
}

function openEditModal(type, id, name, extra, book_id) {
    var titles = { scholar: 'Edit Islamic Scholar', subject: 'Edit Subject', topic: 'Edit Topic', concept: 'Edit Concept' };
    var actions = { scholar: 'edit_scholar', subject: 'edit_subject', topic: 'edit_topic', concept: 'edit_concept' };
    
    document.getElementById('edit-modal-title').textContent = titles[type] || 'Edit';
    document.getElementById('edit-action').value = actions[type];
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-name').value = name;
    
    var specGroup = document.getElementById('edit-spec-group');
    if (type === 'scholar') {
        specGroup.style.display = 'block';
        document.getElementById('edit-specialization').value = extra || '';
    } else {
        specGroup.style.display = 'none';
    }
    
    var bookGroup = document.getElementById('edit-book-group');
    if (type !== 'scholar') {
        bookGroup.style.display = 'block';
        document.getElementById('edit-book-id').value = book_id || '';
    } else {
        bookGroup.style.display = 'none';
    }
    
    document.getElementById('modal-edit').classList.add('open');
}

// Close modal when clicking dark overlay
document.querySelectorAll('.modal-overlay').forEach(function(overlay) {
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) overlay.classList.remove('open');
    });
});
</script>

</body>
</html>