<?php
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$message = '';
$edit_mode = false;
$edit_data = ['id' => '', 'title' => '', 'description' => '', 'duration' => '', 'audio_url' => ''];

// --- Handle Edit Mode Fetch ---
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM audios WHERE id = ?");
    $stmt->execute([$id]);
    $res = $stmt->fetch();
    if ($res) {
        $edit_data = $res;
        $edit_mode = true;
    }
}

// --- Handle Add or Update ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $duration = $_POST['duration'];
    $audio_url = $_POST['existing_url'] ?? '';

    // File Upload Logic
    if (!empty($_FILES['audio_file']['name'])) {
        $check = validate_upload($_FILES['audio_file'], 
            ['mp3', 'wav', 'ogg', 'm4a'], 
            ['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/mp4', 'audio/x-m4a'], 
            20 * 1024 * 1024
        );
        if ($check !== true) {
            $message = "Audio file error: $check";
        } else {
            $upload_dir = '../uploads/audios/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            $ext = strtolower(pathinfo($_FILES['audio_file']['name'], PATHINFO_EXTENSION));
            $file_name = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
            if (move_uploaded_file($_FILES['audio_file']['tmp_name'], $upload_dir . $file_name)) {
                // Purani file delete karein agar update ho raha hai
                if ($_POST['action'] === 'update' && !empty($_POST['existing_url'])) {
                    @unlink('../' . $_POST['existing_url']);
                }
                $audio_url = 'uploads/audios/' . $file_name;
            }
        }
    }

    if (empty($message)) {
        if ($_POST['action'] === 'add') {
            $stmt = $pdo->prepare("INSERT INTO audios (title, description, audio_url, duration) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $description, $audio_url, $duration]);
            $msg = "Audio track added successfully";
        } else {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("UPDATE audios SET title = ?, description = ?, audio_url = ?, duration = ? WHERE id = ?");
            $stmt->execute([$title, $description, $audio_url, $duration, $id]);
            $msg = "Audio track updated successfully";
        }
        header("Location: audios.php?msg=" . urlencode($msg));
        exit;
    }
}

// --- Handle Delete ---
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM audios WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: audios.php?msg=Audio+track+deleted+successfully");
        exit;
    }
}

if (isset($_GET['msg'])) {
    $message = htmlspecialchars($_GET['msg']);
}

$audios = $pdo->query("SELECT * FROM audios ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Audio - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex h-screen">
    <?php include 'sidebar.php'; ?>

    <main class="flex-1 p-8 overflow-y-auto">
        <h1 class="text-3xl font-bold mb-6"><?php echo $edit_mode ? 'Edit Audio Track' : 'Manage Audio Tracks'; ?></h1>
        
        <?php if ($message) echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'>$message</div>"; ?>

        <div class="bg-white p-6 rounded shadow mb-8 border-t-4 <?php echo $edit_mode ? 'border-blue-500' : 'border-[#C9960A]'; ?>">
            <h2 class="text-xl font-bold mb-4"><?php echo $edit_mode ? 'Update Record' : 'Add New Audio Track'; ?></h2>
            <form method="POST" enctype="multipart/form-data" class="grid grid-cols-2 gap-4">
                <input type="hidden" name="action" value="<?php echo $edit_mode ? 'update' : 'add'; ?>">
                <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                <input type="hidden" name="existing_url" value="<?php echo $edit_data['audio_url']; ?>">

                <div class="col-span-2">
                    <label class="block text-sm font-bold mb-1">Track Title</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($edit_data['title']); ?>" required class="border p-2 rounded w-full">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-bold mb-1">Audio File <?php echo $edit_mode ? '(Leave blank to keep current)' : ''; ?></label>
                    <input type="file" name="audio_file" accept=".mp3,.wav,.ogg" class="border p-2 rounded w-full">
                </div>

                <div>
                    <label class="block text-sm font-bold mb-1">Duration (e.g. 10:25)</label>
                    <input type="text" name="duration" value="<?php echo htmlspecialchars($edit_data['duration']); ?>" class="border p-2 rounded w-full">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-bold mb-1">Description</label>
                    <textarea name="description" class="border p-2 rounded w-full" rows="2"><?php echo htmlspecialchars($edit_data['description']); ?></textarea>
                </div>

                <div class="col-span-2 flex gap-2">
                    <button type="submit" class="bg-[#C9960A] text-white px-4 py-2 rounded font-bold hover:bg-[#E8B840]">
                        <?php echo $edit_mode ? 'Update Audio' : 'Add Audio'; ?>
                    </button>
                    <?php if($edit_mode): ?>
                        <a href="audios.php" class="bg-gray-500 text-white px-4 py-2 rounded font-bold hover:bg-gray-600">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded shadow">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50">
                    <tr class="border-b">
                        <th class="p-3">Title</th>
                        <th class="p-3">Duration</th>
                        <th class="p-3">Listen</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($audios as $a): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3 font-semibold"><?php echo htmlspecialchars($a['title']); ?></td>
                            <td class="p-3 text-sm text-gray-500"><?php echo htmlspecialchars($a['duration']); ?></td>
                            <td class="p-3">
                                <audio controls class="h-8 w-48">
                                    <source src="../<?php echo htmlspecialchars($a['audio_url']); ?>">
                                </audio>
                            </td>
                            <td class="p-3 text-right space-x-2">
                                <a href="?edit=<?php echo $a['id']; ?>" class="bg-green-800 hover:bg-green-deep text-white px-3 py-1 rounded transition text-xs">Edit</a>
                                <a href="?delete=<?php echo $a['id']; ?>" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded transition text-xs" onclick="return confirm('Delete?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>