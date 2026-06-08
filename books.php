<?php
require_once 'includes/header.php';

// Fetch all books
$books = $pdo->query("SELECT * FROM books ORDER BY id DESC")->fetchAll();
?>

<div class="bg-green-deep py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="font-english text-white text-5xl mb-2">Books & Writings</h1>
        <h2 class="arabic-text text-gold text-4xl">کتب و تصنیفات</h2>
    </div>
</div>

<section class="py-20 bg-cream min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <?php if(!empty($_GET['order']) && $_GET['order'] === 'success'): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-8 text-center font-semibold">
                Your order has been placed successfully! We will contact you soon.
            </div>
        <?php endif; ?>

        <?php if(empty($books)): ?>
            <div class="text-center py-12 text-gray-500">No books available yet.</div>
        <?php else: ?>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                <?php foreach($books as $b):
                    $cover = !empty($b['cover_image']) ? '/islamic_scholar/' . $b['cover_image'] : '';
                ?>
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition group">
                    <!-- Cover -->
                    <div class="h-64 rounded-t-lg relative flex items-center justify-center text-white overflow-hidden"
                         style="<?php echo $cover ? 'background:#1a3a2a;' : 'background-color:#' . substr(md5($b['id']), 0, 6) . ';'; ?>">
                        <?php if($cover): ?>
                            <img src="<?php echo htmlspecialchars($cover); ?>"
                                 alt="<?php echo htmlspecialchars($b['title']); ?>"
                                 class="absolute inset-0 w-full h-full object-cover"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <span class="text-sm font-bold text-center p-4 z-10" style="display:none;">Coming Soon</span>
                        <?php else: ?>
                            <span class="text-sm font-bold text-center p-4 z-10">Coming Soon</span>
                        <?php endif; ?>

                        <!-- Hover overlay -->
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-60 transition flex items-center justify-center opacity-0 group-hover:opacity-100 z-20">
                            <a href="view_book.php?id=<?php echo $b['id']; ?>" class="bg-gold text-white px-5 py-2 rounded font-semibold text-sm shadow hover:bg-yellow-600 transition">View Details</a>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="p-4">
                        <h3 class="font-english font-bold text-green-deep text-lg leading-tight line-clamp-2 mb-2"><?php echo htmlspecialchars($b['title']); ?></h3>
                        <div class="flex items-center justify-between">
                            <?php if(!empty($b['language'])): ?>
                                <span class="text-xs text-gray-500"><?php echo htmlspecialchars($b['language']); ?></span>
                            <?php endif; ?>
                            <?php if(isset($b['price']) && $b['price'] > 0): ?>
                                <span class="text-sm font-bold text-gold">PKR <?php echo $b['price']; ?></span>
                            <?php else: ?>
                                <span class="text-xs text-green-600 font-semibold">Free</span>
                            <?php endif; ?>
                        </div>
                        <a href="view_book.php?id=<?php echo $b['id']; ?>" class="block mt-3 text-center bg-green-deep text-white py-2 rounded text-sm font-semibold hover:bg-green-mid transition">View Details</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>