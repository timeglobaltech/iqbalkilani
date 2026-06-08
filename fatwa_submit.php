<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid request. Please go back and try again.");
    }

    // Rate limiting: max 3 submissions per 10 minutes
    if (!rate_limit('fatwa_submit', 3, 600)) {
        die("You are submitting too frequently. Please wait a few minutes and try again.");
    }

    // Sanitize input
    $user_name = sanitize($_POST['user_name']);
    $user_email = sanitize($_POST['user_email']);
    $category = sanitize($_POST['category']);
    $question_text = sanitize($_POST['question_text']);
    
    // Validate email
    if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Insert first, then generate reference number from the auto-increment ID
    try {
        $sql = "INSERT INTO fatwas (reference_no, user_name, user_email, category, question_text, status)
                VALUES ('', :name, :email, :cat, :qtext, 'Pending')";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $user_name,
            ':email' => $user_email,
            ':cat' => $category,
            ':qtext' => $question_text
        ]);

        // Use the actual inserted ID — no race condition possible
        $fatwa_id = $pdo->lastInsertId();
        $reference_no = 'Q-' . ($fatwa_id + 1000);

        // Update the row with the final reference number
        $pdo->prepare("UPDATE fatwas SET reference_no = ? WHERE id = ?")->execute([$reference_no, $fatwa_id]);

        // In a real app, send email to user here
        // mail($user_email, "Question Received - $reference_no", "JazakAllah Khayran. Your question has been received.");

        // For demo, we just show a success message with tailwind
        ?>
        
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Submission Success</title>
            <script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body class="bg-[#F7F3EC] flex items-center justify-center min-h-screen">
            <div class="bg-white p-8 rounded shadow text-center max-w-md">
                <div class="w-16 h-16 bg-[#1B3C2E] text-[#C9960A] rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
                    ✓
                </div>
                <h1 class="text-2xl text-[#1B3C2E] font-serif mb-2">JazakAllah Khayran</h1>
                <p class="text-gray-600 mb-4">Your question has been submitted successfully.</p>
                <div class="bg-gray-100 p-4 rounded mb-6">
                    <p class="text-sm text-gray-500">Your Reference Number:</p>
                    <p class="text-xl font-bold text-[#C9960A] tracking-wider"><?php echo $reference_no; ?></p>
                </div>
                <a href="<?php echo SITE_URL; ?>" class="inline-block bg-[#1B3C2E] text-white px-6 py-2 rounded hover:bg-[#2E6B4F] transition">Return to Homepage</a>
            </div>
        </body>
        </html>
        <?php
        
    } catch(PDOException $e) {
        error_log("Fatwa submit error: " . $e->getMessage());
        die("Something went wrong. Please try again later.");
    }
} else {
    header("Location: index.php");
    exit;
}
?>
