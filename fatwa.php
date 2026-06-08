<?php
// 1. Sabse pehle aapki config file load hogi (Session auto-start handles ke sath)
require_once 'config.php';

// 2. Answered Fatwas fetch karne ka code (Recent 5)
$fatwas = [];
try {
    $fatwas = $pdo->query("SELECT * FROM fatwas WHERE status = 'Answered' ORDER BY id DESC LIMIT 5")->fetchAll();
} catch (PDOException $e) {
    $fatwas = []; // Agar koi data na mile to empty array
}
?>
<?php
require_once 'includes/header.php';

// Fetch Published Fatwas
$stmt = $pdo->query("SELECT * FROM fatwas WHERE status = 'Published' ORDER BY created_at DESC");
$all_fatwas = $stmt->fetchAll();
?>

<div class="bg-green-deep py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="font-english text-white text-5xl mb-2">Fatwa & Q&A Library</h1>
        <h2 class="arabic-text text-gold text-4xl">فتویٰ و استفتاء</h2>
    </div>
</div>

<section class="py-20 bg-cream min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            
            <!-- Left col: Published Fatwas -->
            <div class="lg:col-span-2">
                <div class="mb-6 flex justify-between items-center">
                    <h3 class="text-2xl font-english text-green-deep font-bold">Published Answers</h3>
                    <input type="text" placeholder="Search fatwas..." class="border rounded px-4 py-2 text-sm focus:outline-none focus:border-gold">
                </div>
                
                <div class="space-y-6">
                    <?php foreach($all_fatwas as $fatwa): ?>
                    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <span class="text-xs font-semibold bg-green-deep text-gold px-3 py-1 rounded-full"><?php echo htmlspecialchars($fatwa['category']); ?></span>
                            <span class="text-sm text-gray-500 font-mono">Ref: <?php echo htmlspecialchars($fatwa['reference_no']); ?></span>
                        </div>
                        <div class="mb-4">
                            <h4 class="font-semibold text-lg text-body-text mb-2 flex items-start">
                                <span class="text-gold font-bold mr-2">Q:</span>
                                <?php echo nl2br(htmlspecialchars($fatwa['question_text'])); ?>
                            </h4>
                        </div>
                        <div class="bg-gray-50 p-4 border-l-4 border-gold rounded-r">
                            <p class="text-body-text leading-relaxed flex items-start">
                                <span class="text-green-deep font-bold mr-2">A:</span>
                                <?php echo nl2br(htmlspecialchars($fatwa['answer_text'])); ?>
                            </p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right col: Form -->
            <div class="lg:col-span-1">
                <div class="bg-white p-8 rounded-lg shadow-md border-t-4 border-green-mid sticky top-24">
                    <h3 class="text-xl font-english text-green-deep font-bold mb-4">Submit a Question</h3>
                    <p class="text-sm text-gray-600 mb-6">Responses are provided within 3–5 working days, in sha Allah. Private matters will remain confidential.</p>
                    
                    <form action="fatwa_submit.php" method="POST" class="space-y-4">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <div>
                            <input type="text" name="user_name" required placeholder="Full Name" value="<?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : ''; ?>" class="w-full border border-gray-300 rounded px-3 py-2 focus:border-green-mid outline-none <?php echo isset($_SESSION['user_id']) ? 'bg-gray-100' : ''; ?>" <?php echo isset($_SESSION['user_id']) ? 'readonly' : ''; ?>>
                        </div>
                        <div>
                            <?php
                                $user_email = '';
                                if (isset($_SESSION['user_id'])) {
                                    $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
                                    $stmt->execute([$_SESSION['user_id']]);
                                    $user_email = $stmt->fetchColumn();
                                }
                            ?>
                            <input type="email" name="user_email" required placeholder="Email Address" value="<?php echo htmlspecialchars($user_email); ?>" class="w-full border border-gray-300 rounded px-3 py-2 focus:border-green-mid outline-none <?php echo isset($_SESSION['user_id']) ? 'bg-gray-100' : ''; ?>" <?php echo isset($_SESSION['user_id']) ? 'readonly' : ''; ?>>
                        </div>
                        <div>
                            <select name="category" class="w-full border border-gray-300 rounded px-3 py-2 focus:border-green-mid outline-none text-gray-600">
                                <option>Salah & Worship</option>
                                <option>Fasting & Zakat</option>
                                <option>Marriage & Family</option>
                                <option>Business & Finance</option>
                                <option>Aqeedah & Belief</option>
                                <option>Halal & Haram</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div>
                            <textarea name="question_text" rows="6" required class="w-full border border-gray-300 rounded px-3 py-2 focus:border-green-mid outline-none" placeholder="Type your question here..."></textarea>
                        </div>
                        <button type="submit" class="w-full bg-gold hover:bg-gold-light text-white font-semibold py-3 rounded transition shadow-md">Submit Question — بِسْمِ اللَّه</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
