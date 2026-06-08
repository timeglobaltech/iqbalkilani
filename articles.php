<?php
require_once 'includes/header.php';

if (isset($_GET['id'])) {
    // Show single article
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $article = $stmt->fetch();

    if (!$article) {
        die("Article not found.");
    }
    ?>
    <section class="py-20 bg-beige min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 bg-white p-8 md:p-12 rounded shadow-sm">
            <span class="inline-block bg-cream text-green-deep text-sm px-3 py-1 rounded mb-4 font-semibold"><?php echo htmlspecialchars($article['category']); ?></span>
            <h1 class="text-4xl md:text-5xl font-english text-green-deep mb-4 font-bold leading-tight"><?php echo htmlspecialchars($article['title']); ?></h1>
            <p class="text-gray-500 mb-8 border-b pb-4 text-sm">Published on <?php echo date('F j, Y', strtotime($article['date_published'])); ?></p>
            
            <div class="prose prose-lg text-body-text max-w-none font-english">
                <?php echo nl2br(htmlspecialchars($article['content'])); ?>
            </div>

            <div class="mt-12 pt-8 border-t">
                <a href="articles.php" class="text-gold font-semibold hover:text-gold-light">&larr; Back to all articles</a>
            </div>
        </div>
    </section>
    <?php
} else {
    // Show article list
    $stmt = $pdo->query("SELECT * FROM articles ORDER BY date_published DESC");
    $all_articles = $stmt->fetchAll();
    ?>
    <div class="bg-green-deep py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="font-english text-white text-5xl mb-2">Articles & Blog</h1>
            <h2 class="arabic-text text-gold text-4xl">مضامین و درس</h2>
        </div>
    </div>

    <section class="py-20 bg-beige min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="space-y-8">
                <?php foreach($all_articles as $a): ?>
                <div class="flex flex-col md:flex-row gap-8 items-start bg-white p-8 rounded shadow-sm hover:shadow-md transition">
                    <div class="w-full md:w-32 flex-none bg-green-deep text-gold rounded text-center py-6 px-2 shadow-inner">
                        <span class="block text-3xl font-bold"><?php echo date('M', strtotime($a['date_published'])); ?></span>
                        <span class="block text-md mt-1"><?php echo date('Y', strtotime($a['date_published'])); ?></span>
                    </div>
                    <div class="flex-1">
                        <span class="inline-block bg-beige text-green-deep text-xs px-2 py-1 rounded mb-3 font-semibold tracking-wider uppercase"><?php echo htmlspecialchars($a['category']); ?></span>
                        <h4 class="text-2xl text-green-deep font-semibold mb-3 font-english leading-snug"><?php echo htmlspecialchars($a['title']); ?></h4>
                        <p class="text-muted text-md mb-4 line-clamp-3 leading-relaxed"><?php echo htmlspecialchars($a['excerpt']); ?></p>
                        <a href="articles.php?id=<?php echo $a['id']; ?>" class="inline-block text-gold border border-gold hover:bg-gold hover:text-white px-4 py-2 rounded text-sm font-semibold transition">Read Full Article &rarr;</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php
}
require_once 'includes/footer.php'; 
?>
