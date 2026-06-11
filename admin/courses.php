<?php
require_once '../config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header("Location: login.php"); exit; }

$message = '';
// Add Course
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $title_en = sanitize($_POST['title_en']);
    $title_ur = sanitize($_POST['title_ur']);
    $description = sanitize($_POST['description']);
    $total_lessons = (int)$_POST['total_lessons'];
    $category = sanitize($_POST['category']);
    $format = sanitize($_POST['format']);
    $status = sanitize($_POST['status']);

    if (empty($title_en) || empty($category)) {
        $message = "Title and category are required.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO courses (title_en, title_ur, description, total_lessons, category, format, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title_en, $title_ur, $description, $total_lessons, $category, $format, $status]);
        header("Location: courses.php?msg=Course+added+successfully");
        exit;
    }
}

// Update Course
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = (int)$_POST['course_id'];
    $title_en = sanitize($_POST['title_en']);
    $title_ur = sanitize($_POST['title_ur']);
    $description = sanitize($_POST['description']);
    $total_lessons = (int)$_POST['total_lessons'];
    $category = sanitize($_POST['category']);
    $format = sanitize($_POST['format']);
    $status = sanitize($_POST['status']);

    if ($id > 0 && !empty($title_en) && !empty($category)) {
        $stmt = $pdo->prepare("UPDATE courses SET title_en=?, title_ur=?, description=?, total_lessons=?, category=?, format=?, status=? WHERE id=?");
        $stmt->execute([$title_en, $title_ur, $description, $total_lessons, $category, $format, $status, $id]);
        header("Location: courses.php?msg=Course+updated+successfully");
        exit;
    }
}

// Delete Course
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: courses.php?msg=Course+deleted+successfully");
        exit;
    }
}

if (isset($_GET['msg'])) {
    $message = htmlspecialchars($_GET['msg']);
}

// Fetch course for editing
$edit_course = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([$eid]);
    $edit_course = $stmt->fetch();
}

$courses = $pdo->query("SELECT * FROM courses ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Manage Courses - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;500;600&display=swap" rel="stylesheet">
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
        <h1 class="text-3xl font-bold text-green-deep mb-6">Manage Courses</h1>
        <?php if($message) echo "<div class='bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded-lg mb-5 flex items-center gap-2'><svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'/></svg>$message</div>"; ?>
        
        <?php $ec = $edit_course; ?>
        <div class="bg-white p-6 md:p-8 rounded-xl shadow-sm border <?php echo $ec ? 'border-gold/40 ring-1 ring-gold/20' : 'border-gray-100'; ?> mb-8">
            <h2 class="text-xl font-bold text-green-deep mb-6 flex items-center gap-2"><span class="w-1.5 h-6 bg-gold rounded-sm"></span> <?php echo $ec ? 'Edit Course' : 'Add New Course'; ?></h2>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <input type="hidden" name="action" value="<?php echo $ec ? 'update' : 'add'; ?>">
                <?php if($ec): ?><input type="hidden" name="course_id" value="<?php echo $ec['id']; ?>"><?php endif; ?>
                <div>
                    <label class="block text-sm font-semibold text-green-deep mb-1.5">Title (English)</label>
                    <input type="text" name="title_en" value="<?php echo $ec ? htmlspecialchars($ec['title_en']) : ''; ?>" placeholder="e.g. Tafsir al-Qur'an Series" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-green-deep mb-1.5">Title (Urdu)</label>
                    <input type="text" name="title_ur" value="<?php echo $ec ? htmlspecialchars($ec['title_ur']) : ''; ?>" placeholder="عنوان" required dir="rtl" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-green-deep mb-1.5">Category</label>
                    <select name="category" required class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition">
                        <option value="">-- Select Category --</option>
                        <?php
                        $cats = ['Tafsir','Hadith','Fiqh','Aqeedah','Seerah','Quran','Arabic Language','Islamic History','Tazkiyah'];
                        $curCat = $ec ? $ec['category'] : '';
                        foreach($cats as $cat) {
                            echo '<option '.($curCat === $cat ? 'selected' : '').'>'.$cat.'</option>';
                        }
                        if ($ec && $curCat && !in_array($curCat, $cats)) {
                            echo '<option selected>'.htmlspecialchars($curCat).'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-green-deep mb-1.5">Total Lessons</label>
                    <input type="number" name="total_lessons" value="<?php echo $ec ? (int)$ec['total_lessons'] : ''; ?>" placeholder="e.g. 12" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-green-deep mb-1.5">Format</label>
                    <input type="text" name="format" value="<?php echo $ec ? htmlspecialchars($ec['format']) : ''; ?>" placeholder="e.g. Video / Audio" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-green-deep mb-1.5">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition">
                        <?php
                        $statuses = ['Ongoing','Completed','New'];
                        $curStatus = $ec ? $ec['status'] : '';
                        foreach($statuses as $st) {
                            echo '<option '.($curStatus === $st ? 'selected' : '').'>'.$st.'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-green-deep mb-1.5">Description</label>
                    <textarea name="description" rows="3" placeholder="Short description of the course..." class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition resize-none"><?php echo $ec ? htmlspecialchars($ec['description']) : ''; ?></textarea>
                </div>
                <div class="md:col-span-2 flex gap-3">
                    <button type="submit" class="flex-1 bg-gold hover:bg-gold-light text-white px-4 py-3 rounded-lg font-bold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo $ec ? 'M5 13l4 4L19 7' : 'M12 4v16m8-8H4'; ?>"/></svg>
                        <?php echo $ec ? 'Update Course' : 'Save Course'; ?>
                    </button>
                    <?php if($ec): ?>
                    <a href="courses.php" class="px-6 py-3 rounded-lg font-bold border border-gray-300 text-gray-600 hover:bg-gray-50 transition flex items-center">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-bold text-green-deep flex items-center gap-2"><span class="w-1.5 h-6 bg-gold rounded-sm"></span> All Courses <span class="text-sm font-normal text-gray-400">(<?php echo count($courses); ?>)</span></h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                            <th class="p-4 font-semibold">Title</th>
                            <th class="p-4 font-semibold">Urdu</th>
                            <th class="p-4 font-semibold">Category</th>
                            <th class="p-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($courses)): ?>
                            <tr><td colspan="4" class="p-6 text-center text-gray-400">No courses added yet.</td></tr>
                        <?php else: ?>
                            <?php foreach($courses as $c): ?>
                            <tr class="border-b border-gray-100 last:border-b-0 hover:bg-gold/5 transition">
                                <td class="p-4 text-sm font-semibold text-gray-800"><?php echo htmlspecialchars($c['title_en']); ?></td>
                                <td class="p-4 text-lg text-green-deep" dir="rtl" style="font-family:'Noto Nastaliq Urdu',serif;"><?php echo htmlspecialchars($c['title_ur']); ?></td>
                                <td class="p-4"><span class="bg-gold/10 text-green-deep border border-gold/20 px-2.5 py-1 rounded-full text-xs font-medium"><?php echo htmlspecialchars($c['category']); ?></span></td>
                                <td class="p-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="?edit=<?php echo $c['id']; ?>" class="inline-flex items-center gap-1.5 bg-green-mid/10 hover:bg-green-mid text-green-mid hover:text-white border border-green-mid/30 hover:border-green-mid px-3 py-1.5 rounded-lg transition-all text-xs font-semibold">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </a>
                                        <a href="?delete=<?php echo $c['id']; ?>" onclick="return confirm('Delete this course?');" class="inline-flex items-center gap-1.5 bg-red-50 hover:bg-red-500 text-red-600 hover:text-white border border-red-200 hover:border-red-500 px-3 py-1.5 rounded-lg transition-all text-xs font-semibold">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Delete
                                        </a>
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
</body>
</html>
