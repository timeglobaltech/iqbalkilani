<?php
require_once 'includes/header.php';

// Fetch Courses
$courses_stmt = $pdo->query("SELECT * FROM courses ORDER BY id DESC LIMIT 4");
$courses = $courses_stmt->fetchAll();

// Fetch Books
$books_stmt = $pdo->query("SELECT * FROM books ORDER BY id DESC");
$books = $books_stmt->fetchAll();

// Fetch Articles
$articles_stmt = $pdo->query("SELECT * FROM articles ORDER BY date_published DESC LIMIT 3");
$articles = $articles_stmt->fetchAll();

// Fetch Published Fatwas
$fatwas_stmt = $pdo->query("SELECT * FROM fatwas WHERE status = 'Published' ORDER BY created_at DESC LIMIT 5");
$fatwas = $fatwas_stmt->fetchAll();

// Fetch Audios
$audios_stmt = $pdo->query("SELECT * FROM audios ORDER BY created_at DESC LIMIT 3");
$audios = $audios_stmt->fetchAll();

// Ensure session is started for CSRF
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message_text = $_POST['message'];

    if (!empty($name) && !empty($email) && !empty($message_text)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $message_text]);
            
            echo "<script>alert('Thank you! Your message has been successfully received.');</script>";
        } catch (PDOException $e) {
            echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
        }
    } else {
        echo "<script>alert('Tamam fields bharna zaroori hain.');</script>";
    }
}
?>

<!-- Hero Section -->
<section class="relative bg-green-deep overflow-hidden">
    <!-- Calligraphy Background Image -->
    <div class="absolute inset-0" style="background-image: url('image/hero-bg.png'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
    <!-- Dark overlay taake text clearly parha jaye -->
    <div class="absolute inset-0 bg-green-deep/60"></div>


    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            <!-- Left: Gold Medallion -->
            <div class="flex justify-center lg:justify-start order-1">
                <img src="<?php echo SITE_URL; ?>/image/medallion.webp" alt="Islamic Calligraphy Medallion" class="w-full max-w-sm drop-shadow-2xl">
            </div>

            <!-- Right: Text content -->
            <div class="text-center lg:text-left order-2">
                <h2 class="arabic-text text-gold text-3xl md:text-4xl mb-3">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</h2>
                <div class="w-32 h-px bg-gold mx-auto lg:mx-0 mb-6"></div>

                <h1 class="arabic-text text-white text-5xl md:text-6xl mb-3">محمد اقبال کیلانی</h1>
                <h2 class="font-english text-gold-light text-3xl md:text-4xl italic mb-5">Muhammad Iqbal Kilani</h2>

                <p class="font-english text-gold-pale text-lg italic mb-2">Reviving Qur'an & Sunnah through authentic knowledge</p>
                <p class="font-urdu text-gray-300 text-lg mb-8">احیائِ قرآن و سنت — بذریعہ علمِ صحیح</p>

                <div class="flex flex-col sm:flex-row justify-center lg:justify-start gap-4">
                    <a href="#courses" class="bg-gold hover:bg-gold-light text-white font-semibold py-3 px-8 rounded transition duration-300">Explore Courses</a>
                    <a href="#about" class="border border-gold text-gold hover:bg-gold hover:text-white font-semibold py-3 px-8 rounded transition duration-300">View Biography</a>
                </div>
            </div>
        </div>

        <!-- Scroll-down arrow -->
        <div class="mt-16 animate-bounce text-center">
            <a href="#about" class="text-gold opacity-70 hover:opacity-100 inline-block">
                <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
            </a>
        </div>
    </div>
</section>
<!-- About Section -->
<section id="about" class="py-20 bg-cream">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-12 items-start">

            <div class="md:col-span-4">
                <div class="aspect-[3/4] bg-green-deep rounded-t-full rounded-b-md border-4 border-gold-light/20 relative overflow-hidden shadow-xl">
                    <img src="<?php echo SITE_URL; ?>/image/about-sheikh.jpg" alt="About the Sheikh" class="absolute inset-0 w-full h-full object-cover">
                </div>
                <div class="mt-6 flex flex-wrap gap-2 justify-center">
                    <span class="bg-beige text-green-deep text-xs px-3 py-1 rounded-full border border-gold/20">Tafsir al-Qur'an</span>
                    <span class="bg-beige text-green-deep text-xs px-3 py-1 rounded-full border border-gold/20">Hadith Sciences</span>
                    <span class="bg-beige text-green-deep text-xs px-3 py-1 rounded-full border border-gold/20">Hanafi Fiqh</span>
                </div>
            </div>

            <div class="md:col-span-8">
                <span class="text-green-mid uppercase tracking-widest text-sm font-semibold">The Scholar</span>
                <h2 class="font-english text-4xl text-green-deep mt-2 mb-1">About the Sheikh</h2>
                <h3 class="arabic-text text-gold text-3xl mb-4">تعارف</h3>
                <div class="w-10 h-px bg-gold mb-8"></div>

                <div class="prose prose-lg text-body-text font-english">
                    <p class="mb-4">Sheikh Muhammad Iqbal Kilani is a distinguished Islamic scholar dedicated to teaching the classical Islamic sciences. With decades of rigorous study under prominent ulema, he specializes in Tafsir, Hadith, and Usul al-Fiqh.</p>
                    <p class="mb-6">His approach bridges the rich intellectual heritage of traditional Islamic scholarship with contemporary challenges, providing clarity and guidance to the modern Muslim.</p>
                </div>

                <div class="quran-quote my-8">
                    <p class="arabic-text text-2xl text-green-deep mb-2">طلب العلم فريضة على كل مسلم</p>
                    <p class="font-english text-muted italic">"Seeking knowledge is an obligation upon every Muslim."</p>
                </div>

                <p class="font-english text-body-text mt-6">Our mission is to make authentic Islamic scholarship accessible through structured courses, insightful writings, and dedicated dawah.</p>
            </div>
        </div>
    </div>
</section>
<!-- Courses Section -->
<section id="courses" class="py-20 bg-beige">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="font-english text-4xl text-green-deep">Lectures & Courses</h2>
            <h3 class="arabic-text text-gold text-3xl mt-2">دروس و تعلیم</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($courses as $c): ?>
                <div class="bg-white rounded border border-gray-200 overflow-hidden group hover:shadow-lg transition">
                    <div class="bg-green-deep h-32 flex items-center justify-center relative">
                        <span class="absolute top-2 right-2 bg-gold text-white text-xs px-2 py-1 rounded"><?php echo htmlspecialchars($c['status']); ?></span>
                        <h4 class="arabic-text text-gold-light text-2xl"><?php echo htmlspecialchars($c['title_ur']); ?></h4>
                    </div>
                    <div class="p-6">
                        <h3 class="font-english font-semibold text-lg text-green-deep mb-2 h-14 line-clamp-2"><?php echo htmlspecialchars($c['title_en']); ?></h3>
                        <div class="text-xs text-muted mb-4 flex justify-between border-b border-gray-100 pb-2">
                            <span><?php echo $c['total_lessons']; ?> Lessons</span>
                            <span><?php echo htmlspecialchars($c['format']); ?></span>
                        </div>

                        <div class="flex gap-2">
                            <button
                                class="view-btn flex-1 border border-gold text-gold py-2 rounded text-xs hover:bg-gold hover:text-white transition"
                                data-course='<?php echo json_encode($c, JSON_HEX_APOS | JSON_HEX_QUOT); ?>'
                                data-mode="details">
                                View Details
                            </button>

                            <button
                                class="view-btn flex-1 bg-green-mid text-white py-2 rounded text-xs hover:bg-green-deep transition"
                                data-course='<?php echo json_encode($c, JSON_HEX_APOS | JSON_HEX_QUOT); ?>'
                                data-mode="enroll">
                                Enroll Free
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- COURSE MODAL -->
<div id="courseModal" class="fixed inset-0 z-[100] hidden items-center justify-center" style="backdrop-filter: blur(8px);">
    <div class="absolute inset-0 bg-slate-900/80" onclick="closeCourseModal()"></div>

    <div class="relative bg-white rounded-2xl shadow-2xl w-[92%] max-w-xl max-h-[85vh] overflow-y-auto border border-white/20">
        <div class="relative bg-green-deep p-8 text-white">
            <button onclick="closeCourseModal()"
                class="absolute top-4 right-4 text-white/60 hover:text-gold bg-white/10 hover:bg-white/20 rounded-full w-10 h-10 flex items-center justify-center text-2xl z-10">
                &times;
            </button>

            <div class="relative z-10">
                <span class="inline-block bg-gold/20 text-gold-light text-[10px] uppercase tracking-widest font-bold px-3 py-1 rounded-full mb-3">
                    Course Preview
                </span>

                <h3 id="modalTitleUr" class="arabic-text text-gold text-4xl mb-2"></h3>
                <h2 id="modalTitleEn" class="font-english text-2xl font-bold"></h2>
            </div>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-3 gap-4 mb-6 pb-6 border-b border-gray-100">
                <div class="text-center">
                    <span class="block text-gray-400 text-[10px] uppercase">Duration</span>
                    <span id="statLessons" class="text-green-deep font-semibold"></span>
                </div>

                <div class="text-center border-x border-gray-100">
                    <span class="block text-gray-400 text-[10px] uppercase">Language</span>
                    <span class="text-green-deep font-semibold">Urdu/Eng</span>
                </div>

                <div class="text-center">
                    <span class="block text-gray-400 text-[10px] uppercase">Format</span>
                    <span id="statFormat" class="text-green-deep font-semibold"></span>
                </div>
            </div>

            <div class="mb-6">
                <label class="text-sm font-semibold text-green-deep">Select Lesson</label>
                <select id="lessonSelect"
                    class="w-full mt-2 border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-deep">
                    <option value="">-- Choose Lesson --</option>
                </select>
            </div>

            <div id="modalDescription" class="text-gray-600 leading-relaxed mb-8"></div>

            <div class="flex gap-4">
                <button onclick="closeCourseModal()" class="flex-1 px-6 py-3 text-gray-500 hover:text-gray-700 font-semibold">
                    Dismiss
                </button>

                <button id="modalActionButton"
                    class="flex-[2] px-6 py-3 bg-green-deep hover:bg-green-mid text-gold font-bold rounded-xl shadow-lg">
                    Confirm Enrollment
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Books Section -->
<section id="books" class="py-20 bg-cream">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12">
            <h2 class="font-english text-4xl text-green-deep">Books & Writings</h2>
            <h3 class="arabic-text text-gold text-3xl mt-2">کتب و تصنیفات</h3>
        </div>

        <div class="flex overflow-x-auto space-x-6 pb-4 scrollbar-hide">
            <?php foreach (array_slice($books, 0, 6) as $b): ?>
                <div class="flex-none w-40 text-center group">
                    <div class="h-56 w-full rounded shadow-md relative flex items-center justify-center text-white p-4 transition-transform duration-300 group-hover:-translate-y-2 overflow-hidden" style="<?php echo empty($b['cover_image']) ? 'background-color: #' . substr(md5($b['id']), 0, 6) . ';' : ''; ?>">

                        <?php if (!empty($b['cover_image'])): ?>
                            <img src="<?php echo htmlspecialchars($b['cover_image']); ?>"
                                class="absolute inset-0 w-full h-full object-cover">
                        <?php else: ?>
                            <div class="absolute inset-0 flex items-center justify-center bg-green-deep">
                                <span class="text-white text-lg font-bold tracking-wide">
                                    Coming Soon
                                </span>
                            </div>
                        <?php endif; ?>

                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-70 transition flex flex-col items-center justify-center gap-3 opacity-0 group-hover:opacity-100 z-20">
                            <?php if (!empty($b['file_path'])): ?>
                                <a href="<?php echo isset($_SESSION['user_id']) ? htmlspecialchars($b['file_path']) : 'login.php?msg=Please login to read books'; ?>" <?php echo isset($_SESSION['user_id']) ? 'target="_blank"' : ''; ?> class="bg-white text-green-deep text-[10px] px-3 py-1 rounded transition font-semibold shadow hover:bg-gray-100">Read / Download</a>
                            <?php endif; ?>
                            <a href="view_book.php?id=<?php echo $b['id']; ?>" class="bg-gold text-white text-[10px] px-3 py-1 rounded font-semibold shadow hover:bg-gold-light transition">View Details</a>
                        </div>
                    </div>
                    <h4 class="mt-3 font-english font-medium text-body-text text-sm leading-tight line-clamp-2"><?php echo htmlspecialchars($b['title']); ?></h4>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mt-8">
            <a href="books.php" class="text-green-mid hover:text-green-deep font-semibold text-sm transition">Visit Full Library &rarr;</a>
        </div>
    </div>
</section>

<!-- Articles Section -->
<section id="articles" class="py-20 bg-beige">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12">
            <h2 class="font-english text-4xl text-green-deep">Articles & Blog</h2>
            <h3 class="arabic-text text-gold text-3xl mt-2">مضامین و درس</h3>
        </div>

        <div class="space-y-6 max-w-4xl">
            <?php foreach ($articles as $a): ?>
                <div class="flex flex-col md:flex-row gap-6 items-start bg-white p-6 rounded shadow-sm hover:shadow transition">
                    <div class="w-full md:w-32 flex-none bg-green-deep text-gold rounded text-center py-4 px-2">
                        <span class="block text-2xl font-bold"><?php echo date('M', strtotime($a['date_published'])); ?></span>
                        <span class="block text-sm"><?php echo date('Y', strtotime($a['date_published'])); ?></span>
                    </div>
                    <div class="flex-1">
                        <span class="inline-block bg-beige text-green-deep text-xs px-2 py-1 rounded mb-2"><?php echo htmlspecialchars($a['category']); ?></span>
                        <h4 class="text-xl text-green-deep font-semibold mb-2"><?php echo htmlspecialchars($a['title']); ?></h4>
                        <p class="text-muted text-sm mb-4 line-clamp-2"><?php echo htmlspecialchars($a['excerpt']); ?></p>
                        <a href="#" class="text-gold hover:text-gold-light text-sm font-semibold">Read More &rarr;</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-8">
            <a href="articles.php" class="text-green-mid hover:text-green-deep font-semibold text-sm transition">View All Articles &rarr;</a>
        </div>
    </div>
</section>

<!-- Audios Section -->
<section id="audios" class="py-20 bg-cream">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12 text-center">
            <h2 class="font-english text-4xl text-green-deep">Audio Bayanaat</h2>
            <h3 class="arabic-text text-gold text-3xl mt-2">آڈیو بیانات</h3>
        </div>
        <div class="space-y-6">
            <?php foreach ($audios as $a): ?>
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex flex-col md:flex-row items-center gap-6">
                    <div class="flex-1 text-center md:text-left w-full">
                        <div class="flex items-center justify-between mb-1">
                            <h3 class="text-xl font-bold font-english text-green-deep"><?php echo htmlspecialchars($a['title']); ?></h3>
                            <div class="equalizer" id="eq-home-<?php echo $a['id']; ?>">
                                <span></span><span></span><span></span><span></span><span></span>
                            </div>
                        </div>
                        <div class="w-full mt-3">
                            <audio id="home-audio-<?php echo $a['id']; ?>" onplay="document.getElementById('eq-home-<?php echo $a['id']; ?>').classList.add('playing')" onpause="document.getElementById('eq-home-<?php echo $a['id']; ?>').classList.remove('playing')" onended="document.getElementById('eq-home-<?php echo $a['id']; ?>').classList.remove('playing')" controls class="w-full h-10 rounded outline-none" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));">
                                <source src="<?php echo htmlspecialchars($a['audio_url']); ?>" type="audio/mpeg">
                            </audio>
                        </div>
                    </div>
                    <div class="hidden md:block flex-shrink-0 text-gold font-mono font-semibold">
                        <?php echo htmlspecialchars($a['duration']); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mt-8 text-center">
            <a href="audios.php" class="text-green-mid hover:text-green-deep font-semibold text-sm transition">Listen to All Audios &rarr;</a>
        </div>
    </div>
</section>

<!-- Fatwa Q&A Section -->
<!-- <section id="qa" class="py-20 bg-beige">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="font-english text-4xl text-green-deep">Fatwa & Q&A</h2>
            <h3 class="arabic-text text-gold text-3xl mt-2">فتویٰ و استفتاء</h3>
            <p class="text-muted mt-4 max-w-2xl mx-auto">Submit your question for scholarly guidance. Responses are provided within 3–5 working days, in sha Allah.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <div class="bg-white p-8 rounded shadow-sm border border-gray-100 border-t-4 border-t-green-mid">
                <form action="fatwa_submit.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" name="user_name" required class="w-full border border-gray-300 rounded px-3 py-2 focus:border-green-mid focus:ring-1 focus:ring-green-mid outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" name="user_email" required class="w-full border border-gray-300 rounded px-3 py-2 focus:border-green-mid focus:ring-1 focus:ring-green-mid outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select name="category" class="w-full border border-gray-300 rounded px-3 py-2 focus:border-green-mid outline-none">
                                <option>Salah & Worship</option>
                                <option>Fasting & Zakat</option>
                                <option>Marriage & Family</option>
                                <option>Business & Finance</option>
                                <option>Aqeedah & Belief</option>
                                <option>Halal & Haram</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Question</label>
                            <textarea name="question_text" rows="5" required class="w-full border border-gray-300 rounded px-3 py-2 focus:border-green-mid outline-none" placeholder="Type your question here..."></textarea>
                        </div>
                        <p class="text-xs text-muted italic">Personal/sensitive matters kept confidential.</p>
                        <button type="submit" class="w-full bg-gold hover:bg-gold-light text-white font-semibold py-3 rounded transition">Submit Question — بِسْمِ اللَّه</button>
                    </div>
                </form>
            </div>

            <div>
                <h4 class="text-xl text-green-deep mb-6 font-english border-b border-gray-200 pb-2">Recently Answered</h4>
                <div class="space-y-4">
                    <?php foreach ($fatwas as $fatwa): ?>
                        <div class="bg-white border border-gray-200 rounded p-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-semibold bg-beige text-green-deep px-2 py-1 rounded"><?php echo htmlspecialchars($fatwa['category']); ?></span>
                                <span class="text-xs text-muted"><?php echo htmlspecialchars($fatwa['reference_no']); ?></span>
                            </div>
                            <h5 class="font-medium text-body-text mb-2"><?php echo htmlspecialchars($fatwa['question_text']); ?></h5>
                            <p class="text-sm text-gray-600 border-l-2 border-gold pl-3 py-1 bg-gray-50"><?php echo htmlspecialchars(substr($fatwa['answer_text'], 0, 100)) . '...'; ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section> -->

<section id="qa" class="py-20 bg-beige">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="font-english text-4xl text-green-deep">Fatwa & Q&A</h2>
            <h3 class="arabic-text text-gold text-3xl mt-2">فتویٰ و استفتاء</h3>
            <p class="text-muted mt-4 max-w-2xl mx-auto">Submit your question for scholarly guidance. Responses are provided within 3–5 working days, in sha Allah.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Form Column -->
            <div class="bg-white p-8 rounded shadow-sm border border-gray-100 border-t-4 border-t-green-mid">
                
                <?php 
                // Check kar rahe hain ke user logged in hai ya nahi
                $is_logged_in = isset($_SESSION['user_id']) ? 'true' : 'false';
                ?>

                <!-- JS Verification: Agar login false hoga to form submit hone se pehle hi alert dikhaega -->
                <form action="fatwa_submit.php" method="POST" onsubmit="return checkUserLogin(<?php echo $is_logged_in; ?>);">
                    <!-- Aapki config file wala asli CSRF token integration -->
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" name="user_name" required class="w-full border border-gray-300 rounded px-3 py-2 focus:border-green-mid focus:ring-1 focus:ring-green-mid outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" name="user_email" required class="w-full border border-gray-300 rounded px-3 py-2 focus:border-green-mid focus:ring-1 focus:ring-green-mid outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select name="category" class="w-full border border-gray-300 rounded px-3 py-2 focus:border-green-mid outline-none">
                                <option>Salah & Worship</option>
                                <option>Fasting & Zakat</option>
                                <option>Marriage & Family</option>
                                <option>Business & Finance</option>
                                <option>Aqeedah & Belief</option>
                                <option>Halal & Haram</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Question</label>
                            <textarea name="question_text" rows="5" required class="w-full border border-gray-300 rounded px-3 py-2 focus:border-green-mid outline-none" placeholder="Type your question here..."></textarea>
                        </div>
                        <p class="text-xs text-muted italic">Personal/sensitive matters kept confidential.</p>
                        <button type="submit" class="w-full bg-gold hover:bg-gold-light text-white font-semibold py-3 rounded transition">Submit Question — بِسْمِ اللَّه</button>
                    </div>
                </form>
            </div>

            <!-- Recently Answered Column -->
            <div>
                <h4 class="text-xl text-green-deep mb-6 font-english border-b border-gray-200 pb-2">Recently Answered</h4>
                <div class="space-y-4">
                    <?php if (!empty($fatwas)): ?>
                        <?php foreach ($fatwas as $fatwa): ?>
                            <div class="bg-white border border-gray-200 rounded p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-semibold bg-beige text-green-deep px-2 py-1 rounded"><?php echo htmlspecialchars($fatwa['category']); ?></span>
                                    <span class="text-xs text-muted"><?php echo htmlspecialchars($fatwa['reference_no'] ?? ''); ?></span>
                                </div>
                                <h5 class="font-medium text-body-text mb-2"><?php echo htmlspecialchars($fatwa['question_text']); ?></h5>
                                <p class="text-sm text-gray-600 border-l-2 border-gold pl-3 py-1 bg-gray-50"><?php echo htmlspecialchars(substr($fatwa['answer_text'] ?? '', 0, 100)) . '...'; ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-sm text-gray-500 italic">No fatwas answered recently.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Auth Warning Modal -->
<div id="loginAuthModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <!-- Modal Backdrop / Overlay -->
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeAuthModal()"></div>
    
    <!-- Modal Content Box -->
    <div class="bg-white rounded-lg shadow-xl border-t-4 border-t-gold p-6 max-w-sm w-full mx-4 relative z-10 transform transition-all duration-300">
        <div class="text-center">
            <!-- Icon -->
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-amber-50 mb-4">
                <svg class="h-6 w-6 text-gold" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                </svg>
            </div>
            
            <!-- Heading -->
            <h3 class="text-lg font-semibold text-green-deep font-english mb-2">Login Required</h3>
            
            <!-- Message -->
            <p class="text-sm text-gray-600 mb-6">
                Please login or create an account first to submit your question for scholarly guidance.
            </p>
            
            <!-- Action Buttons -->
            <div class="space-y-2">
                <a href="login.php" class="block w-full text-center bg-green-mid hover:bg-green-deep text-white font-semibold py-2 px-4 rounded transition text-sm">
                    Login Now
                </a>
                <button type="button" onclick="closeAuthModal()" class="block w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded transition text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

</section>

<!-- Media & Dawah Section -->
<section id="media" class="py-20 bg-beige">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12 text-center">
            <h2 class="font-english text-4xl text-green-deep">Media & Dawah</h2>
            <h3 class="arabic-text text-gold text-3xl mt-2">میڈیا و دعوت</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-green-deep rounded p-6 flex flex-col items-center justify-center text-center aspect-square hover:shadow-lg transition group cursor-pointer">
                <div class="w-16 h-16 rounded-full border-2 border-gold flex items-center justify-center text-gold group-hover:bg-gold group-hover:text-green-deep transition mb-4">
                    <svg class="w-8 h-8 ml-1" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z" />
                    </svg>
                </div>
                <h4 class="text-white font-semibold text-lg">YouTube</h4>
                <p class="text-gold-pale text-sm mt-1">Weekly Tafsir</p>
            </div>
            <div class="bg-green-deep rounded p-6 flex flex-col items-center justify-center text-center aspect-square hover:shadow-lg transition group cursor-pointer">
                <div class="w-16 h-16 rounded-full border-2 border-gold flex items-center justify-center text-gold group-hover:bg-gold group-hover:text-green-deep transition mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                    </svg>
                </div>
                <h4 class="text-white font-semibold text-lg">Podcast</h4>
                <p class="text-gold-pale text-sm mt-1">Q&A Sessions</p>
            </div>
            <div class="bg-green-deep rounded p-6 flex flex-col items-center justify-center text-center aspect-square hover:shadow-lg transition group cursor-pointer">
                <div class="w-16 h-16 rounded-full border-2 border-gold flex items-center justify-center text-gold group-hover:bg-gold group-hover:text-green-deep transition mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <h4 class="text-white font-semibold text-lg">Short Clips</h4>
                <p class="text-gold-pale text-sm mt-1">Daily Reminders</p>
            </div>
            <div class="bg-green-deep rounded p-6 flex flex-col items-center justify-center text-center aspect-square hover:shadow-lg transition group cursor-pointer">
                <div class="w-16 h-16 rounded-full border-2 border-gold flex items-center justify-center text-gold group-hover:bg-gold group-hover:text-green-deep transition mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                    </svg>
                </div>
                <h4 class="text-white font-semibold text-lg">Live Q&A</h4>
                <p class="text-gold-pale text-sm mt-1">Monthly Streams</p>
            </div>
        </div>

        <div class="flex flex-wrap justify-center gap-4 mb-12">
            <span class="bg-green-mid text-white px-4 py-2 rounded text-sm hover:bg-green-deep transition cursor-pointer">YouTube</span>
            <span class="bg-green-mid text-white px-4 py-2 rounded text-sm hover:bg-green-deep transition cursor-pointer">Instagram</span>
            <span class="bg-green-mid text-white px-4 py-2 rounded text-sm hover:bg-green-deep transition cursor-pointer">Facebook</span>
            <span class="bg-green-mid text-white px-4 py-2 rounded text-sm hover:bg-green-deep transition cursor-pointer">Podcast</span>
            <span class="bg-green-mid text-white px-4 py-2 rounded text-sm hover:bg-green-deep transition cursor-pointer">Newsletter</span>
        </div>

        <div class="bg-green-deep p-8 rounded text-center flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="w-16 h-16 bg-gold rounded-full flex items-center justify-center text-white flex-shrink-0">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm-2 16l-4-4 1.414-1.414 2.586 2.586 6.586-6.586 1.414 1.414-8 8z" />
                </svg>
            </div>
            <div class="flex-1 text-left">
                <h4 class="text-white text-xl font-semibold mb-1">Subscribe for weekly Islamic reminders</h4>
                <p class="text-gold-pale text-sm">Join our newsletter to receive the latest updates, videos, and articles.</p>
            </div>
            <div class="flex w-full md:w-auto">
                <input type="email" placeholder="Email address" class="px-4 py-2 rounded-l w-full outline-none text-body-text">
                <button class="bg-gold hover:bg-gold-light text-white px-6 py-2 rounded-r transition font-semibold">Subscribe</button>
            </div>
        </div>
    </div>
</section>

<!-- Contact & Donations Section -->
<!-- <section id="contact" class="py-20 bg-cream">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <div>
                <h2 class="font-english text-4xl text-green-deep mb-2">Contact & Maktaba</h2>
                <h3 class="arabic-text text-gold text-3xl mb-8">رابطہ</h3>

                <div class="space-y-4 mb-8">
                    <p class="text-body-text"><strong>Institution:</strong> Maktaba Quddusia</p>
                    <p class="text-body-text"><strong>Location:</strong> Pakistan</p>
                    <p class="text-body-text"><strong>Email:</strong> info@maktabaquddusia.com</p>
                </div>

                <form action="" method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <div>
                        <input type="text" name="name" placeholder="Name" required 
                               class="w-full border-b border-gray-300 bg-transparent py-2 focus:border-green-mid outline-none">
                    </div>
                    <div>
                        <input type="email" name="email" placeholder="Email" required 
                               class="w-full border-b border-gray-300 bg-transparent py-2 focus:border-green-mid outline-none">
                    </div>
                    <div>
                        <input type="text" name="subject" placeholder="Subject" 
                               class="w-full border-b border-gray-300 bg-transparent py-2 focus:border-green-mid outline-none">
                    </div>
                    <div>
                        <textarea name="message" placeholder="Message" rows="3" required 
                                  class="w-full border-b border-gray-300 bg-transparent py-2 focus:border-green-mid outline-none resize-none"></textarea>
                    </div>
                    
                    <button type="submit" name="send_message" 
                            class="bg-green-deep hover:bg-green-mid text-white font-semibold px-8 py-2 rounded transition mt-4">
                        Send
                    </button>
                </form>
            </div>

            <div class="bg-green-deep rounded-lg p-8 shadow-xl text-white">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="arabic-text text-gold text-3xl mb-1">تعاون</h2>
                        <h3 class="font-english text-2xl font-semibold">Support the Dawah</h3>
                    </div>
                    <div class="text-gold opacity-50 text-5xl">
                        &hearts;
                    </div>
                </div>

                <p class="text-gray-300 text-sm mb-8 leading-relaxed">Your support enables us to provide free Islamic education, translate classical texts, and maintain the scholarship platform. All donations support Islamic education and dawah work.</p>

                <div class="flex space-x-2 mb-4">
                    <button class="bg-gold text-white text-xs px-3 py-1 rounded font-semibold">PKR</button>
                    <button class="bg-green-mid text-white hover:bg-green-600 text-xs px-3 py-1 rounded transition">USD</button>
                </div>

                <div class="grid grid-cols-4 gap-3 mb-6">
                    <button class="border border-gold hover:bg-gold hover:text-white rounded py-2 text-sm font-semibold transition">500</button>
                    <button class="border border-gold hover:bg-gold hover:text-white rounded py-2 text-sm font-semibold transition">1000</button>
                    <button class="bg-gold text-white rounded py-2 text-sm font-semibold transition shadow-md">2500</button>
                    <button class="border border-gray-500 text-gray-300 hover:border-gold hover:text-white rounded py-2 text-sm transition">Custom</button>
                </div>

                <button class="w-full bg-gold hover:bg-gold-light text-white font-semibold py-3 rounded transition shadow-lg text-lg mb-6">Donate Now &mdash; Sadaqah Jariyah</button>

                <div class="text-center text-xs text-gray-400">
                    <p>Supported Payment Methods:</p>
                    <div class="flex justify-center space-x-4 mt-2">
                        <span>JazzCash</span>
                        <span>&bull;</span>
                        <span>EasyPaisa</span>
                        <span>&bull;</span>
                        <span>Bank Transfer</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section> -->
<section id="contact" class="py-20 bg-cream">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <div>
                <h2 class="font-english text-4xl text-green-deep mb-2">Contact & Maktaba</h2>
                <h3 class="arabic-text text-gold text-3xl mb-8">رابطہ</h3>

                <div class="space-y-4 mb-8">
                    <p class="text-body-text"><strong>Institution:</strong> Maktaba Quddusia</p>
                    <p class="text-body-text"><strong>Location:</strong> Pakistan</p>
                    <p class="text-body-text"><strong>Email:</strong> 
                </p>
                </div>

                <!-- 1. Form validation attached to the same checkUserLogin logic -->
                <form action="" method="POST" class="space-y-4" onsubmit="return checkUserLogin(<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>);">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <div>
                        <input type="text" name="name" placeholder="Name" required 
                               class="w-full border-b border-gray-300 bg-transparent py-2 focus:border-green-mid outline-none">
                    </div>
                    <div>
                        <input type="email" name="email" placeholder="Email" required 
                               class="w-full border-b border-gray-300 bg-transparent py-2 focus:border-green-mid outline-none">
                    </div>
                    <div>
                        <input type="text" name="subject" placeholder="Subject" 
                               class="w-full border-b border-gray-300 bg-transparent py-2 focus:border-green-mid outline-none">
                    </div>
                    <div>
                        <input type="textarea" name="message" placeholder="Message" rows="3" required 
                                  class="w-full border-b border-gray-300 bg-transparent py-2 focus:border-green-mid outline-none resize-none"></textarea>
                    </div>
                    
                    <button type="submit" name="send_message" 
                            class="bg-green-deep hover:bg-green-mid text-white font-semibold px-8 py-2 rounded transition mt-4">
                        Send
                    </button>
                </form>
            </div>

            <div class="bg-green-deep rounded-lg p-8 shadow-xl text-white">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="arabic-text text-gold text-3xl mb-1">تعاون</h2>
                        <h3 class="font-english text-2xl font-semibold">Support the Dawah</h3>
                    </div>
                    <div class="text-gold opacity-50 text-5xl">
                        &hearts;
                    </div>
                </div>

                <p class="text-gray-300 text-sm mb-8 leading-relaxed">Your support enables us to provide free Islamic education, translate classical texts, and maintain the scholarship platform. All donations support Islamic education and dawah work.</p>

                <div class="flex space-x-2 mb-4">
                    <button class="bg-gold text-white text-xs px-3 py-1 rounded font-semibold">PKR</button>
                    <button class="bg-green-mid text-white hover:bg-green-600 text-xs px-3 py-1 rounded transition">USD</button>
                </div>

                <div class="grid grid-cols-4 gap-3 mb-6">
                    <button class="border border-gold hover:bg-gold hover:text-white rounded py-2 text-sm font-semibold transition">500</button>
                    <button class="border border-gold hover:bg-gold hover:text-white rounded py-2 text-sm font-semibold transition">1000</button>
                    <button class="bg-gold text-white rounded py-2 text-sm font-semibold transition shadow-md">2500</button>
                    <button class="border border-gray-500 text-gray-300 hover:border-gold hover:text-white rounded py-2 text-sm transition">Custom</button>
                </div>

                <!-- 2. ID added here to trigger click event in JavaScript -->
                <button id="donateNowBtn" class="w-full bg-gold hover:bg-gold-light text-white font-semibold py-3 rounded transition shadow-lg text-lg mb-6">Donate Now &mdash; Sadaqah Jariyah</button>

                <div class="text-center text-xs text-gray-400">
                    <p>Supported Payment Methods:</p>
                    <div class="flex justify-center space-x-4 mt-2">
                        <span>JazzCash</span>
                        <span>&bull;</span>
                        <span>EasyPaisa</span>
                        <span>&bull;</span>
                        <span>Bank Transfer</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const course = JSON.parse(this.dataset.course);
        const mode = this.dataset.mode;
        
        const modal = document.getElementById('courseModal');
        
        document.getElementById('modalTitleUr').innerText = course.title_ur;
        document.getElementById('modalTitleEn').innerText = course.title_en;
        document.getElementById('statLessons').innerText = course.total_lessons + " Lessons";
        document.getElementById('statFormat').innerText = course.format;
        
        const lessonSelect = document.getElementById('lessonSelect');
        lessonSelect.innerHTML = `<option value="">-- Choose Lesson --</option>`;
        
        for (let i = 1; i <= course.total_lessons; i++) {
            lessonSelect.innerHTML += `<option value="Lesson ${i}">Lesson ${i}</option>`;
        }
        
        if (mode === 'details') {
            document.getElementById('modalDescription').innerHTML = course.description || 'No description available.';
        } else {
            document.getElementById('modalDescription').innerHTML = `<p>Enroll in <b>${course.title_en}</b> by selecting a lesson.</p>`;
        }
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        
        modal.dataset.courseId = course.id;
    });
});

// function closeCourseModal() {
//     const modal = document.getElementById('courseModal');
//     modal.classList.add('hidden');
//     modal.classList.remove('flex');
//     document.body.style.overflow = 'auto';
// }

// document.getElementById('modalActionButton').addEventListener('click', function() {
//     const lesson = document.getElementById('lessonSelect').value;
//     const modal = document.getElementById('courseModal');
//     const courseId = modal.dataset.courseId;
    
//     if (!lesson) {
//         alert('Please select a lesson');
//         return;
//     }
    
//     fetch('enroll.php', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/x-www-form-urlencoded'
//         },
//         body: `course_id=${courseId}&lesson=${lesson}`
//     })
//     .then(res => res.text())
//     .then(data => {
//         alert('Enrolled successfully!');
//         closeCourseModal();
//     });
// });
</script>

<script>
document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const course = JSON.parse(this.dataset.course);
        const mode = this.dataset.mode;
        
        const modal = document.getElementById('courseModal');
        
        document.getElementById('modalTitleUr').innerText = course.title_ur;
        document.getElementById('modalTitleEn').innerText = course.title_en;
        document.getElementById('statLessons').innerText = course.total_lessons + " Lessons";
        document.getElementById('statFormat').innerText = course.format;
        
        const lessonSelect = document.getElementById('lessonSelect');
        lessonSelect.innerHTML = `<option value="">-- Choose Lesson --</option>`;
        
        for (let i = 1; i <= course.total_lessons; i++) {
            lessonSelect.innerHTML += `<option value="Lesson ${i}">Lesson ${i}</option>`;
        }
        
        if (mode === 'details') {
            document.getElementById('modalDescription').innerHTML = course.description || 'No description available.';
        } else {
            document.getElementById('modalDescription').innerHTML = `<p>Enroll in <b>${course.title_en}</b> by selecting a lesson.</p>`;
        }
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        
        modal.dataset.courseId = course.id;
    });
});

function closeCourseModal() {
    const modal = document.getElementById('courseModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto';
}

document.getElementById('modalActionButton').addEventListener('click', function() {
    const lesson = document.getElementById('lessonSelect').value;
    const modal = document.getElementById('courseModal');
    const courseId = modal.dataset.courseId;
    
    if (!lesson) {
        alert('Please select a lesson');
        return;
    }
    
    // ✅ LOGIN CHECK - Pehle check karo user logged in hai ya nahi
    <?php if (!isset($_SESSION['user_id'])): ?>
        // User logged in nahi hai - Login page par redirect karo
        alert('Please login first to enroll in this course');
        window.location.href = 'login.php?redirect=index.php&course_id=' + courseId + '&lesson=' + lesson;
        return;
    <?php endif; ?>
    
    // User logged in hai - Enrollment process continue karo
    fetch('enroll.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `course_id=${courseId}&lesson=${lesson}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('✓ ' + data.message);
            closeCourseModal();
        } else if (data.message === 'already_enrolled') {
            alert('You are already enrolled in this course.');
            closeCourseModal();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Enrollment failed. Please try again.');
    });
});
</script>
<script>
function checkUserLogin(isLoggedIn) {
    if (!isLoggedIn) {
        // Pehle alert popup show hoga
        alert("Please login first to submit your Fatwa question.");
        // OK click karte hi login page par redirection
        window.location.href = "login.php";
        return false; // Form submit rok dega
    }
    return true; // Approved, form successfully submit ho jayega
}
</script>

<script>
function checkUserLogin(isLoggedIn) {
    // Agar user logged in nahi hai (isLoggedIn === false)
    if (!isLoggedIn) {
        // Modal se 'hidden' class hata kar usko display karwayen gy
        const modal = document.getElementById('loginAuthModal');
        if (modal) {
            modal.classList.remove('hidden');
        }
        
        // Form ko submit hone se rokne ke liye false return karenge
        return false; 
    }
    
    // Agar logged in hai to form normal submit ho jaye ga
    return true;
}

function closeAuthModal() {
    const modal = document.getElementById('loginAuthModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}
</script>
<script>
// Donation Button click listener
document.getElementById('donateNowBtn').addEventListener('click', function(e) {
    // PHP se login status check karenge
    const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    
    // Agar user logged in nahi hai to modal show hoga aur redirection ruk jayegi
    if (!checkUserLogin(isLoggedIn)) {
        e.preventDefault(); // Kisi bhi automatic action ya link navigation ko rokne ke liye
    } else {
        // Agar logged in hai toh yahan donation page ka redirect ya process logic dalen
        window.location.href = 'donation_process.php'; 
    }
});
</script>
<?php require_once 'includes/footer.php'; ?>