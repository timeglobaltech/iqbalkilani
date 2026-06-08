<?php
require_once 'config.php';

// 3. Login not required for viewing details
// Read/Download buttons will check login in the template below

// 4. Book ID aur Data Fetching (Header se pehle taake error na aaye)
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: books.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
$stmt->execute([$id]);
$book = $stmt->fetch();

if (!$book) {
    header('Location: books.php');
    exit();
}

// Related books ka logic
$stmt2 = $pdo->prepare("SELECT * FROM books WHERE id != ? ORDER BY id DESC LIMIT 6");
$stmt2->execute([$id]);
$related_books = $stmt2->fetchAll();

$cover_src = !empty($book['cover_image']) ? '/islamic_scholar/' . $book['cover_image'] : '';
$file_src  = !empty($book['file_path'])   ? '/islamic_scholar/' . $book['file_path']   : '';

// 5. AB HEADER INCLUDE KAREIN (Jab saari redirection check ho chuki ho)
require_once 'includes/header.php';
?>

<!-- Yahan se UI shuru ho rahi hai -->
<div class="bg-green-deep py-3">
    <div class="max-w-7xl mx-auto px-4">
        <nav class="text-sm font-english text-green-200 flex items-center gap-2">
            <a href="index.php" class="hover:text-gold">Home</a>
            <span>›</span>
            <a href="books.php" class="hover:text-gold">Books</a>
            <span>›</span>
            <span class="text-gold truncate max-w-xs"><?php echo htmlspecialchars($book['title']); ?></span>
        </nav>
    </div>
</div>

<section class="bg-cream py-16">
    <div class="max-w-6xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 items-start">

            <!-- Book Cover -->
            <div class="md:col-span-1 flex flex-col items-center">
                <div class="w-52 rounded-lg shadow-2xl overflow-hidden border-2 border-gold">
                    <?php if ($cover_src): ?>
                        <img src="<?php echo htmlspecialchars($cover_src); ?>" class="w-full object-cover" style="min-height:280px;">
                    <?php else: ?>
                        <div class="bg-green-deep h-72 flex items-center justify-center p-4">
                            <p class="text-white text-center font-bold"><?php echo htmlspecialchars($book['title']); ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 w-full space-y-3 max-w-xs mx-auto">
                    <?php if ($file_src): ?>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="#pdf-viewer" class="flex items-center justify-center gap-2 w-full bg-green-deep text-white py-3 rounded-lg font-semibold shadow hover:bg-green-800 transition text-sm">📖 Read Online</a>
                            <a href="<?php echo htmlspecialchars($file_src); ?>" download class="flex items-center justify-center gap-2 w-full border-2 border-green-deep text-green-deep py-3 rounded-lg font-semibold hover:bg-green-deep hover:text-white transition text-sm">⬇ Download PDF</a>
                        <?php else: ?>
                            <a href="login.php?msg=Please login to read/download books" class="flex items-center justify-center gap-2 w-full bg-green-deep text-white py-3 rounded-lg font-semibold shadow hover:bg-green-800 transition text-sm">📖 Login to Read</a>
                            <a href="login.php?msg=Please login to read/download books" class="flex items-center justify-center gap-2 w-full border-2 border-green-deep text-green-deep py-3 rounded-lg font-semibold hover:bg-green-deep hover:text-white transition text-sm">⬇ Login to Download</a>
                        <?php endif; ?>
                    <?php endif; ?>
                    <a href="books.php" class="flex items-center justify-center w-full text-gray-500 py-2 text-sm hover:text-green-deep transition">← Back to Books</a>
                </div>
            </div>

            <!-- Book Info -->
            <div class="md:col-span-2">
                <h1 class="font-english text-4xl font-bold text-green-deep mb-2"><?php echo htmlspecialchars($book['title']); ?></h1>
                <?php if (!empty($book['title_urdu'])): ?>
                    <h2 class="arabic-text text-2xl text-gold mb-4 text-right"><?php echo htmlspecialchars($book['title_urdu']); ?></h2>
                <?php endif; ?>

                <div class="border-t-2 border-gold border-opacity-30 my-6"></div>
                <div class="text-gray-700 leading-relaxed text-sm">
                    <?php echo nl2br(htmlspecialchars($book['description'] ?? 'Is kitab ki maloomat jald faraham ki jayengi.')); ?>
                </div>
            </div>

        </div>
    </div>
</section>

<?php if ($file_src && isset($_SESSION['user_id'])): ?>
    <section id="pdf-viewer" class="bg-white py-10 border-t border-gray-200">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold text-green-deep">Read Online</h2>
                <a href="<?php echo htmlspecialchars($file_src); ?>" download class="bg-gold text-white px-4 py-2 rounded text-sm font-semibold hover:bg-yellow-600 transition">⬇ Download</a>
            </div>
            <div class="w-full border border-gray-200 rounded-lg overflow-hidden shadow">
                <iframe src="<?php echo htmlspecialchars($file_src); ?>"
                    width="100%"
                    height="800px"
                    style="border:none;"
                    loading="lazy">
                    <p>Your browser does not support PDFs. <a href="<?php echo htmlspecialchars($file_src); ?>">Download the PDF</a>.</p>
                </iframe>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>