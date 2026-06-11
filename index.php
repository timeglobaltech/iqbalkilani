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
<section class="relative bg-green-deep overflow-hidden -mt-32 min-h-screen flex items-center">
    <!-- Calligraphy Background Image -->
    <div class="absolute inset-0" style="background-image: url('image/hero-bg.png'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
    <!-- Dark overlay taake text clearly parha jaye -->
    <div class="absolute inset-0 bg-green-deep/60"></div>

    <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-40 pb-24">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            <!-- Left: Gold Medallion -->
            <div class="flex justify-center lg:justify-start order-1">
                <img src="<?php echo SITE_URL; ?>/image/medallion.webp" alt="Islamic Calligraphy Medallion" class="w-full max-w-md drop-shadow-2xl">
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
                <div class="bg-white rounded-xl border border-gray-100 overflow-hidden group shadow-sm hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col">
                    <!-- Card Header -->
                    <div class="relative bg-gradient-to-br from-green-deep to-green-mid h-36 flex items-center justify-center overflow-hidden">
                        <!-- Subtle dotted pattern -->
                        <div class="absolute inset-0 opacity-[0.08]" style="background-image: radial-gradient(#C9960A 1px, transparent 1px); background-size: 14px 14px;"></div>
                        <!-- Status badge -->
                        <span class="absolute top-3 right-3 bg-gold text-white text-[10px] uppercase tracking-wider font-bold px-3 py-1 rounded-full shadow-md"><?php echo htmlspecialchars($c['status']); ?></span>
                        <h4 class="arabic-text text-gold-light text-3xl relative z-10 px-4 text-center group-hover:scale-105 transition-transform duration-300"><?php echo htmlspecialchars($c['title_ur']); ?></h4>
                        <!-- Gold accent line -->
                        <div class="absolute bottom-0 left-0 w-full h-1 bg-gold"></div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-6 flex flex-col flex-1">
                        <h3 class="font-english font-semibold text-lg text-green-deep mb-3 h-14 line-clamp-2 group-hover:text-green-mid transition"><?php echo htmlspecialchars($c['title_en']); ?></h3>
                        <div class="text-xs text-muted mb-5 flex justify-between items-center border-b border-gray-100 pb-3">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.25C10.5 5 8.5 4.5 5 4.5v13c3.5 0 5.5.5 7 1.75M12 6.25C13.5 5 15.5 4.5 19 4.5v13c-3.5 0-5.5.5-7 1.75M12 6.25v13"/></svg>
                                <?php echo $c['total_lessons']; ?> Lessons
                            </span>
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><circle cx="12" cy="12" r="9" stroke-width="2"/></svg>
                                <?php echo htmlspecialchars($c['format']); ?>
                            </span>
                        </div>

                        <div class="flex gap-2 mt-auto">
                            <button
                                class="view-btn flex-1 border border-gold text-gold py-2.5 rounded-lg text-xs font-semibold hover:bg-gold hover:text-white transition"
                                data-course='<?php echo json_encode($c, JSON_HEX_APOS | JSON_HEX_QUOT); ?>'
                                data-mode="details">
                                View Details
                            </button>

                            <button
                                class="view-btn flex-1 bg-green-mid text-white py-2.5 rounded-lg text-xs font-semibold hover:bg-green-deep transition shadow-sm"
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
        
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-x-6 gap-y-8">
            <?php foreach (array_slice($books, 0, 6) as $b): ?>
                <div class="group text-center">
                    <!-- Book Cover -->
                    <div class="relative aspect-[3/4] rounded-lg overflow-hidden shadow-lg group-hover:shadow-2xl group-hover:-translate-y-2 transition-all duration-300" style="<?php echo empty($b['cover_image']) ? 'background-color: #' . substr(md5($b['id']), 0, 6) . ';' : ''; ?>">

                        <?php if (!empty($b['cover_image'])): ?>
                            <img src="<?php echo htmlspecialchars($b['cover_image']); ?>" class="absolute inset-0 w-full h-full object-cover">
                        <?php else: ?>
                            <div class="absolute inset-0 flex flex-col items-center justify-center bg-gradient-to-br from-green-deep to-green-mid p-4">
                                <svg class="w-10 h-10 text-gold/60 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.25C10.5 5 8.5 4.5 5 4.5v13c3.5 0 5.5.5 7 1.75M12 6.25C13.5 5 15.5 4.5 19 4.5v13c-3.5 0-5.5.5-7 1.75M12 6.25v13"/></svg>
                                <span class="text-gold-pale text-xs font-semibold tracking-wide">Coming Soon</span>
                            </div>
                        <?php endif; ?>

                        <!-- Gold spine accent -->
                        <div class="absolute left-0 top-0 h-full w-1.5 bg-gradient-to-b from-gold-light to-gold z-10"></div>

                        <!-- Hover overlay -->
                        <div class="absolute inset-0 bg-green-deep/0 group-hover:bg-green-deep/80 transition-all duration-300 flex flex-col items-center justify-center gap-2 opacity-0 group-hover:opacity-100 z-20 p-3">
                            <?php if (!empty($b['file_path'])): ?>
                                <a href="<?php echo isset($_SESSION['user_id']) ? htmlspecialchars($b['file_path']) : 'login.php?msg=Please login to read books'; ?>" <?php echo isset($_SESSION['user_id']) ? 'target="_blank"' : ''; ?> class="w-full text-center bg-gold hover:bg-gold-light text-white text-[11px] px-3 py-1.5 rounded-full font-semibold shadow transition">Read / Download</a>
                            <?php endif; ?>
                            <a href="view_book.php?id=<?php echo $b['id']; ?>" class="w-full text-center bg-white/90 hover:bg-white text-green-deep text-[11px] px-3 py-1.5 rounded-full font-semibold shadow transition">View Details</a>
                        </div>
                    </div>
                    <!-- Title -->
                    <h4 class="mt-3 font-english font-semibold text-body-text text-sm leading-tight line-clamp-2 group-hover:text-green-mid transition px-1"><?php echo htmlspecialchars($b['title']); ?></h4>
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
<section id="audios" class="relative py-20 bg-cream overflow-hidden">
    <!-- Full-bleed gold panel (right side, page ke kinare tak) -->
    <svg class="hidden lg:block absolute right-0 top-1/2 -translate-y-1/2 w-1/2 h-[80%] pointer-events-none select-none z-0" viewBox="0 0 400 360" preserveAspectRatio="none">
        <path d="M62,0 L380,0 Q400,0 400,20 L400,340 Q400,360 380,360 L72,360 C34,320 92,272 52,232 C16,196 82,150 46,110 C22,76 70,38 62,0 Z" fill="#EAE3D3"/>
    </svg>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php
        $ap_data = array_map(function($a){
            return ['title' => $a['title'], 'url' => $a['audio_url']];
        }, $audios);
        ?>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            <!-- Left: Audio Player -->
            <div class="relative order-1">
            <!-- Lantern + Crescent decoration -->
            <div class="absolute -top-10 -left-2 md:-left-12 z-20 w-28 pointer-events-none select-none">
                <svg viewBox="0 0 120 150" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <mask id="crescentMask">
                            <rect width="120" height="150" fill="white"/>
                            <circle cx="56" cy="100" r="30" fill="black"/>
                        </mask>
                    </defs>
                    <line x1="74" y1="0" x2="74" y2="34" stroke="#C9960A" stroke-width="1.5"/>
                    <line x1="96" y1="0" x2="96" y2="24" stroke="#C9960A" stroke-width="1.5"/>
                    <g fill="#E8B840">
                        <rect x="90" y="24" width="12" height="16" rx="3"/>
                        <rect x="92" y="20" width="8" height="5" rx="1.5"/>
                        <rect x="93" y="40" width="6" height="4" rx="1"/>
                    </g>
                    <g fill="#C9960A">
                        <rect x="67" y="34" width="14" height="20" rx="3.5"/>
                        <rect x="69" y="29" width="10" height="6" rx="2"/>
                        <rect x="71" y="54" width="6" height="5" rx="1.5"/>
                    </g>
                    <circle cx="44" cy="105" r="36" fill="#C9960A" mask="url(#crescentMask)"/>
                </svg>
            </div>

            <!-- Player Card -->
            <div class="relative bg-white rounded-3xl shadow-xl border border-gold/15 overflow-hidden p-8 md:p-12">
                <!-- Pattern overlay -->
                <div class="absolute inset-0 opacity-[0.06]" style="background-image: radial-gradient(#C9960A 1.5px, transparent 1.5px); background-size: 22px 22px;"></div>

                <div class="relative z-10 text-center">
                    <h3 class="font-english text-2xl md:text-3xl text-green-deep font-bold mb-5">Listen To Audio Bayan</h3>
                    <p class="arabic-text text-gold text-3xl md:text-4xl mb-3">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</p>
                    <p id="apTitle" class="text-green-mid font-semibold mb-8 min-h-[1.5rem]">—</p>

                    <!-- Progress bar -->
                    <div class="flex items-center gap-3 mb-8">
                        <span id="apCurrent" class="text-xs text-muted font-mono w-10 text-right">0:00</span>
                        <div id="apBar" class="flex-1 h-2 bg-gray-200 rounded-full cursor-pointer relative">
                            <div id="apProgress" class="absolute top-0 left-0 h-full bg-gold rounded-full" style="width:0%"></div>
                        </div>
                        <span id="apDuration" class="text-xs text-muted font-mono w-10">0:00</span>
                    </div>

                    <!-- Controls -->
                    <div class="flex items-center justify-center gap-8">
                        <button onclick="apPrev()" class="text-green-deep hover:text-gold transition" aria-label="Previous">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button id="apPlayBtn" onclick="apToggle()" class="w-16 h-16 rounded-full bg-green-mid hover:bg-green-deep text-white flex items-center justify-center shadow-lg transition" aria-label="Play / Pause">
                            <svg class="w-7 h-7 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </button>
                        <button onclick="apNext()" class="text-green-deep hover:text-gold transition" aria-label="Next">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>

                    <audio id="apAudio" preload="metadata"></audio>
                </div>
            </div>
            </div>

            <!-- Right: Mosque Image -->
            <div class="order-2 relative flex items-center justify-center min-h-[440px]">
                <img src="<?php echo SITE_URL; ?>/image/mosque.jpg" alt="Mosque" class="relative z-10 w-full max-w-lg h-[460px] object-cover object-center">
            </div>

        </div>
    </div>

    <script>
    (function(){
        var apList = <?php echo json_encode($ap_data, JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        if(!apList || !apList.length){ return; }
        var apIndex = 0;
        var audio = document.getElementById('apAudio');
        var titleEl = document.getElementById('apTitle');
        var curEl = document.getElementById('apCurrent');
        var durEl = document.getElementById('apDuration');
        var progEl = document.getElementById('apProgress');
        var barEl = document.getElementById('apBar');
        var btn = document.getElementById('apPlayBtn');
        var playIcon = '<svg class="w-7 h-7 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>';
        var pauseIcon = '<svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="5" width="4" height="14" rx="1"/></svg>';

        function fmt(s){ if(isNaN(s)||!isFinite(s)) return '0:00'; var m=Math.floor(s/60), x=Math.floor(s%60); return m+':'+(x<10?'0':'')+x; }
        function load(i, play){
            apIndex = (i % apList.length + apList.length) % apList.length;
            audio.src = apList[apIndex].url;
            titleEl.textContent = apList[apIndex].title;
            progEl.style.width = '0%';
            curEl.textContent = '0:00';
            durEl.textContent = '0:00';
            if(play){ audio.play(); }
        }
        window.apToggle = function(){ if(audio.paused){ audio.play(); } else { audio.pause(); } };
        window.apPrev = function(){ load(apIndex-1, true); };
        window.apNext = function(){ load(apIndex+1, true); };
        audio.addEventListener('play', function(){ btn.innerHTML = pauseIcon; });
        audio.addEventListener('pause', function(){ btn.innerHTML = playIcon; });
        audio.addEventListener('timeupdate', function(){
            curEl.textContent = fmt(audio.currentTime);
            if(audio.duration){ progEl.style.width = (audio.currentTime/audio.duration*100)+'%'; }
        });
        audio.addEventListener('loadedmetadata', function(){ durEl.textContent = fmt(audio.duration); });
        audio.addEventListener('ended', function(){ load(apIndex+1, true); });
        barEl.addEventListener('click', function(e){
            if(!audio.duration) return;
            var r = barEl.getBoundingClientRect();
            audio.currentTime = (e.clientX - r.left) / r.width * audio.duration;
        });
        load(0, false);
    })();
    </script>
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
            <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100 border-t-4 border-t-gold">

                <!-- Form Header -->
                <div class="flex items-center gap-4 mb-6 pb-5 border-b border-gray-100">
                    <div class="w-14 h-14 rounded-full bg-gradient-to-br from-green-deep to-green-mid flex items-center justify-center text-gold flex-shrink-0 shadow-inner">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-english text-2xl font-bold text-green-deep leading-tight">Submit Your Question</h4>
                        <p class="text-xs text-muted mt-0.5">Confidential scholarly guidance, in sha Allah</p>
                    </div>
                </div>

                <?php $is_logged_in = isset($_SESSION['user_id']) ? 'true' : 'false'; ?>

                <form action="fatwa_submit.php" method="POST" onsubmit="return checkUserLogin(<?php echo $is_logged_in; ?>);">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-green-deep mb-1.5">Full Name</label>
                            <input type="text" name="user_name" required placeholder="Your name" class="w-full bg-beige/40 border border-transparent rounded-lg px-4 py-3 text-body-text focus:border-gold focus:bg-white outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-green-deep mb-1.5">Email Address</label>
                            <input type="email" name="user_email" required placeholder="you@example.com" class="w-full bg-beige/40 border border-transparent rounded-lg px-4 py-3 text-body-text focus:border-gold focus:bg-white outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-green-deep mb-1.5">Category</label>
                            <select name="category" class="w-full bg-beige/40 border border-transparent rounded-lg px-4 py-3 text-body-text focus:border-gold focus:bg-white outline-none transition">
                                <option>Salah & Worship</option>
                                <option>Fasting & Zakat</option>
                                <option>Marriage & Family</option>
                                <option>Business & Finance</option>
                                <option>Aqeedah & Belief</option>
                                <option>Halal & Haram</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-green-deep mb-1.5">Question</label>
                            <textarea name="question_text" rows="5" required placeholder="Type your question here..." class="w-full bg-beige/40 border border-transparent rounded-lg px-4 py-3 text-body-text focus:border-gold focus:bg-white outline-none transition resize-none"></textarea>
                        </div>
                        <p class="text-xs text-muted italic flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Personal/sensitive matters kept confidential.
                        </p>
                        <button type="submit" class="w-full bg-gold hover:bg-gold-light text-white font-semibold py-3.5 rounded-lg shadow-md hover:shadow-lg transition">Submit Question — بِسْمِ اللَّه</button>
                    </div>
                </form>
            </div>

            <!-- Recently Answered Column -->
            <div>
                <h4 class="font-english text-2xl text-green-deep mb-6 flex items-center gap-2"><span class="w-1.5 h-6 bg-gold rounded-sm"></span> Recently Answered</h4>
                <div class="space-y-4">
                    <?php if (!empty($fatwas)): ?>
                        <?php foreach ($fatwas as $fatwa): ?>
                            <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-gold/30 transition">
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-xs font-semibold bg-gold/10 text-green-deep px-3 py-1 rounded-full border border-gold/20"><?php echo htmlspecialchars($fatwa['category']); ?></span>
                                    <span class="text-xs text-gold font-mono font-semibold"><?php echo htmlspecialchars($fatwa['reference_no'] ?? ''); ?></span>
                                </div>
                                <h5 class="font-english font-semibold text-green-deep text-base mb-3 flex gap-2"><span class="text-gold font-bold">Q.</span> <span><?php echo htmlspecialchars($fatwa['question_text']); ?></span></h5>
                                <p class="text-sm text-gray-600 border-l-2 border-gold pl-3 py-1"><span class="text-green-mid font-bold">A. </span><?php echo htmlspecialchars(substr($fatwa['answer_text'] ?? '', 0, 100)) . '...'; ?></p>
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
            <div class="relative bg-gradient-to-br from-green-deep to-green-mid rounded-xl p-6 flex flex-col items-center justify-center text-center aspect-square shadow-sm hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-300 group cursor-pointer border border-gold/10 overflow-hidden">
                <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(#C9960A 1px, transparent 1px); background-size: 16px 16px;"></div>
                <div class="relative z-10 w-16 h-16 rounded-full border-2 border-gold flex items-center justify-center text-gold group-hover:bg-gold group-hover:text-green-deep group-hover:scale-110 transition-all duration-300 mb-4">
                    <svg class="w-8 h-8 ml-1" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z" />
                    </svg>
                </div>
                <h4 class="relative z-10 text-white font-semibold text-lg">YouTube</h4>
                <p class="relative z-10 text-gold-pale text-sm mt-1">Weekly Tafsir</p>
            </div>
            <div class="relative bg-gradient-to-br from-green-deep to-green-mid rounded-xl p-6 flex flex-col items-center justify-center text-center aspect-square shadow-sm hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-300 group cursor-pointer border border-gold/10 overflow-hidden">
                <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(#C9960A 1px, transparent 1px); background-size: 16px 16px;"></div>
                <div class="relative z-10 w-16 h-16 rounded-full border-2 border-gold flex items-center justify-center text-gold group-hover:bg-gold group-hover:text-green-deep group-hover:scale-110 transition-all duration-300 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                    </svg>
                </div>
                <h4 class="relative z-10 text-white font-semibold text-lg">Podcast</h4>
                <p class="relative z-10 text-gold-pale text-sm mt-1">Q&A Sessions</p>
            </div>
            <div class="relative bg-gradient-to-br from-green-deep to-green-mid rounded-xl p-6 flex flex-col items-center justify-center text-center aspect-square shadow-sm hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-300 group cursor-pointer border border-gold/10 overflow-hidden">
                <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(#C9960A 1px, transparent 1px); background-size: 16px 16px;"></div>
                <div class="relative z-10 w-16 h-16 rounded-full border-2 border-gold flex items-center justify-center text-gold group-hover:bg-gold group-hover:text-green-deep group-hover:scale-110 transition-all duration-300 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <h4 class="relative z-10 text-white font-semibold text-lg">Short Clips</h4>
                <p class="relative z-10 text-gold-pale text-sm mt-1">Daily Reminders</p>
            </div>
            <div class="relative bg-gradient-to-br from-green-deep to-green-mid rounded-xl p-6 flex flex-col items-center justify-center text-center aspect-square shadow-sm hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-300 group cursor-pointer border border-gold/10 overflow-hidden">
                <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(#C9960A 1px, transparent 1px); background-size: 16px 16px;"></div>
                <div class="relative z-10 w-16 h-16 rounded-full border-2 border-gold flex items-center justify-center text-gold group-hover:bg-gold group-hover:text-green-deep group-hover:scale-110 transition-all duration-300 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                    </svg>
                </div>
                <h4 class="relative z-10 text-white font-semibold text-lg">Live Q&A</h4>
                <p class="relative z-10 text-gold-pale text-sm mt-1">Monthly Streams</p>
            </div>
        </div>

        <div class="flex flex-wrap justify-center gap-4 mb-12">
            <span class="bg-green-mid text-white px-5 py-2 rounded-full text-sm font-medium hover:bg-green-deep hover:shadow-md hover:-translate-y-0.5 transition-all cursor-pointer">YouTube</span>
            <span class="bg-green-mid text-white px-5 py-2 rounded-full text-sm font-medium hover:bg-green-deep hover:shadow-md hover:-translate-y-0.5 transition-all cursor-pointer">Instagram</span>
            <span class="bg-green-mid text-white px-5 py-2 rounded-full text-sm font-medium hover:bg-green-deep hover:shadow-md hover:-translate-y-0.5 transition-all cursor-pointer">Facebook</span>
            <span class="bg-green-mid text-white px-5 py-2 rounded-full text-sm font-medium hover:bg-green-deep hover:shadow-md hover:-translate-y-0.5 transition-all cursor-pointer">Podcast</span>
            <span class="bg-green-mid text-white px-5 py-2 rounded-full text-sm font-medium hover:bg-green-deep hover:shadow-md hover:-translate-y-0.5 transition-all cursor-pointer">Newsletter</span>
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
<section id="contact" class="relative py-20 bg-cream overflow-hidden">

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

            <!-- Left: Heading + Image -->
            <div class="lg:col-span-5">
                <span class="inline-flex items-center gap-2 bg-white shadow-sm px-4 py-2 rounded-full text-sm font-semibold text-green-deep mb-6">
                    <span class="w-2.5 h-2.5 rounded-full bg-gold"></span> Help &amp; Donate
                </span>
                <h2 class="font-english text-4xl md:text-5xl text-green-deep font-bold leading-tight mb-2">Donate / Support<br>Our Center</h2>
                <h3 class="arabic-text text-gold text-3xl mb-6">تعاون</h3>
                <p class="text-muted text-base leading-relaxed mb-8 max-w-md">Your generous donations help us maintain the maktaba, provide community services, and educate future generations. Every contribution counts and is greatly appreciated.</p>

                <!-- Image (simple rounded) -->
                <div class="max-w-md mx-auto lg:mx-0">
                    <img src="<?php echo SITE_URL; ?>/image/donate-img.jpg?v=2" alt="Support Our Center" class="w-full h-80 object-cover rounded-2xl shadow-xl">
                </div>
            </div>

            <!-- Right: Make Donation Card -->
            <div class="lg:col-span-7 bg-white rounded-2xl shadow-2xl p-8 md:p-12 h-[665px] flex flex-col justify-center">
                <h2 class="font-english text-3xl text-green-deep font-bold mb-1">Make Donation</h2>
                <h3 class="arabic-text text-gold text-2xl mb-6">عطیہ دیں</h3>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <input type="text" placeholder="Enter Your Name" class="w-full bg-beige/40 border border-transparent focus:border-gold rounded-lg px-4 py-3 text-body-text outline-none transition">
                        <input type="email" placeholder="Your Email" class="w-full bg-beige/40 border border-transparent focus:border-gold rounded-lg px-4 py-3 text-body-text outline-none transition">
                    </div>
                    <input type="text" placeholder="Company Name (Optional)" class="w-full bg-beige/40 border border-transparent focus:border-gold rounded-lg px-4 py-3 text-body-text outline-none transition">
                    <input type="text" id="donationAmount" placeholder="Rs 1000" class="w-full bg-beige/40 border border-transparent focus:border-gold rounded-lg px-4 py-3 text-body-text outline-none transition">

                    <!-- Amount buttons -->
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="selectAmount(this,'500')" class="amt-btn border border-gold/40 text-green-deep hover:bg-gold hover:text-white rounded-lg px-4 py-2 text-sm font-semibold transition">500</button>
                        <button type="button" onclick="selectAmount(this,'1000')" class="amt-btn border border-gold/40 text-green-deep hover:bg-gold hover:text-white rounded-lg px-4 py-2 text-sm font-semibold transition">1000</button>
                        <button type="button" onclick="selectAmount(this,'2500')" class="amt-btn border border-gold/40 text-green-deep hover:bg-gold hover:text-white rounded-lg px-4 py-2 text-sm font-semibold transition">2500</button>
                        <button type="button" onclick="selectAmount(this,'5000')" class="amt-btn border border-gold/40 text-green-deep hover:bg-gold hover:text-white rounded-lg px-4 py-2 text-sm font-semibold transition">5000</button>
                        <button type="button" onclick="selectAmount(this,'10000')" class="amt-btn border border-gold/40 text-green-deep hover:bg-gold hover:text-white rounded-lg px-4 py-2 text-sm font-semibold transition">10000</button>
                        <button type="button" onclick="selectAmount(this,'custom')" class="amt-btn border border-gold/40 text-green-deep hover:bg-gold hover:text-white rounded-lg px-4 py-2 text-sm font-semibold transition">Custom</button>
                    </div>

                    <button id="donateNowBtn" type="button" class="w-full bg-gold hover:bg-gold-light text-white font-semibold py-4 rounded-xl shadow-lg transition text-lg">Submit Donation</button>

                    <div class="text-center text-xs text-muted pt-2">
                        <p>Supported Payment Methods:</p>
                        <div class="flex justify-center gap-3 mt-1 text-green-deep font-medium">
                            <span>JazzCash</span><span>&bull;</span><span>EasyPaisa</span><span>&bull;</span><span>Bank Transfer</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
// Donation amount buttons select logic
function selectAmount(btn, val) {
    document.querySelectorAll('.amt-btn').forEach(function(b) {
        b.classList.remove('bg-gold', 'text-white');
        b.classList.add('text-green-deep');
    });
    btn.classList.add('bg-gold', 'text-white');
    btn.classList.remove('text-green-deep');
    var f = document.getElementById('donationAmount');
    if (val !== 'custom') { f.value = 'Rs ' + val; } else { f.value = ''; f.focus(); }
}
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