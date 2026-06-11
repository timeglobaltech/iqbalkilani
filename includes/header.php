<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Muhammad Iqbal Khalani - Official Website</title>
    <meta name="description" content="Official website for Islamic scholar Muhammad Iqbal Khalani. Reviving Qur'an & Sunnah through authentic knowledge.">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,400&family=Lateef:wght@400;500;600&family=Noto+Nastaliq+Urdu:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (CDN for rapid prototyping) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        cream: '#F7F3EC',
                        beige: '#EDE5D5',
                        'green-deep': '#1B3C2E',
                        'green-mid': '#2E6B4F',
                        gold: '#C9960A',
                        'gold-light': '#E8B840',
                        'gold-pale': '#F5E4A8',
                        'body-text': '#3A2E22',
                        muted: '#7A6B56',
                    },
                    fontFamily: {
                        arabic: ['Lateef', 'serif'],
                        english: ['Lato', 'sans-serif'],
                        urdu: ['"Noto Nastaliq Urdu"', 'serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            background-color: #F7F3EC;
            color: #3A2E22;
            font-family: <?php echo ($lang == 'ur') ? '"Noto Nastaliq Urdu", serif' : "'Lato', sans-serif"; ?>;
        }
        .arabic-text { font-family: 'Lateef', serif; }
        /* Navbar solid state — hero calligraphy texture ke saath */
        #mainNav.nav-solid {
            background-color: #1B3C2E;
            background-image: linear-gradient(rgba(27,60,46,0.85), rgba(27,60,46,0.85)), url('<?php echo SITE_URL; ?>/image/hero-bg.png');
            background-size: cover;
            background-position: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.25);
        }
        .gold-divider {
            height: 2px;
            background: #C9960A;
            margin: 2rem auto;
            width: 100%;
        }
        .quran-quote {
            border-left: 3px solid #C9960A;
            background-color: #F7F3EC;
            padding: 1rem 1.5rem;
        }
        [dir="rtl"] .quran-quote {
            border-left: none;
            border-right: 3px solid #C9960A;
        }
        
        /* Audio Visualizer Animation */
        .equalizer {
            display: none;
            align-items: flex-end;
            height: 24px;
            gap: 3px;
        }
        .equalizer.playing {
            display: flex;
        }
        .equalizer span {
            display: inline-block;
            width: 4px;
            background-color: #C9960A;
            animation: bounce 1.2s infinite ease-in-out;
            border-radius: 2px;
        }
        .equalizer span:nth-child(1) { animation-delay: -1.2s; height: 10px; }
        .equalizer span:nth-child(2) { animation-delay: -1.1s; height: 16px; }
        .equalizer span:nth-child(3) { animation-delay: -1.0s; height: 24px; }
        .equalizer span:nth-child(4) { animation-delay: -0.9s; height: 12px; }
        .equalizer span:nth-child(5) { animation-delay: -0.8s; height: 18px; }

        @keyframes bounce {
            0%, 100% { transform: scaleY(0.5); }
            50% { transform: scaleY(1.0); }
        }

        /* ===== Google Translate — banner & engine hide ===== */
        .goog-te-banner-frame,
        .goog-te-banner-frame.skiptranslate,
        iframe.goog-te-banner-frame,
        .VIpgJd-ZVi9od-aZ2wEe-wOHMyf,
        .VIpgJd-ZVi9od-ORHb-OEVmcd {
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
        }
        body { top: 0 !important; position: static !important; }
        #goog-gt-tt, .goog-te-balloon-frame, .goog-tooltip { display: none !important; }
        .goog-text-highlight { background: none !important; box-shadow: none !important; }
        /* Google ka apna widget chupa do — sirf translation engine ke liye chahiye */
        #google_translate_element { position: fixed !important; left: -9999px !important; top: -9999px !important; visibility: hidden; }

        /* ===== Custom Floating Language Switcher ===== */
        .lang-float {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
        }
        .lang-current {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #1B3C2E;
            color: #fff;
            border: 1px solid rgba(201,150,10,0.5);
            border-radius: 10px;
            padding: 8px 14px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(0,0,0,0.25);
            transition: all 0.2s ease;
        }
        .lang-current:hover { border-color: #C9960A; }
        .lang-current img { width: 24px; height: 16px; object-fit: cover; border-radius: 2px; }
        .lang-current .caret { font-size: 11px; transition: transform 0.2s ease; }
        .lang-float.open .lang-current .caret { transform: rotate(180deg); }
        .lang-menu {
            position: absolute;
            bottom: calc(100% + 10px);
            right: 0;
            background: #fff;
            border-radius: 12px;
            padding: 8px;
            min-width: 190px;
            box-shadow: 0 12px 35px rgba(0,0,0,0.22);
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.2s ease;
            max-height: 320px;
            overflow-y: auto;
        }
        .lang-float.open .lang-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .lang-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 8px;
            color: #1B3C2E;
            font-size: 16px;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.15s ease;
        }
        .lang-menu a:hover { background: #F7F3EC; }
        .lang-menu a.active { background: rgba(201,150,10,0.12); font-weight: 600; }
        .lang-menu img { width: 26px; height: 18px; object-fit: cover; border-radius: 3px; box-shadow: 0 1px 3px rgba(0,0,0,0.15); }
        /* Dropdown select ko theme ke mutabiq style karo */
        .goog-te-gadget .goog-te-combo {
            margin: 0 !important;
            color: #C9960A;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(201,150,10,0.45);
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            outline: none;
            transition: all 0.2s ease;
        }
        .goog-te-gadget .goog-te-combo:hover {
            background: rgba(201,150,10,0.15);
            border-color: #C9960A;
        }
        .goog-te-gadget .goog-te-combo option { color: #1B3C2E; background: #fff; font-weight: 500; }
        /* Translated text ka yellow highlight hata do */
        .goog-text-highlight { background: none !important; box-shadow: none !important; }
    </style>
</head>
<body class="antialiased">

<!-- Navigation -->
<nav id="mainNav" class="sticky top-0 z-50 nav-solid transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-32">
            <!-- Logo (hanging banner) -->
            <div class="flex-shrink-0 self-start">
                <a href="<?php echo SITE_URL; ?>" class="block">
                    <img src="<?php echo SITE_URL; ?>/image/logo-t.png" alt="Muhammad Iqbal Kilani" class="h-48 w-auto drop-shadow-lg">
                </a>
            </div>
            
            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="<?php echo SITE_URL; ?>/#about" class="text-white hover:text-gold-light transition">About</a>
                <a href="<?php echo SITE_URL; ?>/#courses" class="text-white hover:text-gold-light transition">Courses</a>
                <a href="<?php echo SITE_URL; ?>/#books" class="text-white hover:text-gold-light transition">Books</a>
                <a href="<?php echo SITE_URL; ?>/#articles" class="text-white hover:text-gold-light transition">Articles</a>
                <a href="<?php echo SITE_URL; ?>/setting.php" class="text-white hover:text-gold-light transition">Directory</a>
                <a href="<?php echo SITE_URL; ?>/#audios" class="text-white hover:text-gold-light transition">Audio</a>
                <a href="<?php echo SITE_URL; ?>/#qa" class="text-white hover:text-gold-light transition">Q&A</a>
                <a href="<?php echo SITE_URL; ?>/#contact" class="text-white hover:text-gold-light transition">Contact</a>

                <!-- Auth Links -->
                <div class="flex items-center space-x-4 border-l border-gold-light pl-4 ml-4">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <span class="text-gold-pale text-sm">Salam, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
                        <a href="<?php echo SITE_URL; ?>/user_dashboard.php" class="text-sm text-gold hover:text-white transition font-semibold">Dashboard</a>
                        <form method="POST" action="<?php echo SITE_URL; ?>/user_dashboard.php" class="inline">
                            <input type="hidden" name="action" value="logout">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <button type="submit" class="text-sm text-gray-300 hover:text-white transition">Logout</button>
                        </form>
                    <?php else: ?>
                        <a href="<?php echo SITE_URL; ?>/login.php" class="text-sm text-white hover:text-gold transition">Login</a>
                        <a href="<?php echo SITE_URL; ?>/register.php" class="text-sm bg-gold hover:bg-gold-light text-white px-3 py-1 rounded transition">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Mobile menu button -->
            <div class="md:hidden flex items-center">
                <button type="button" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="text-gold hover:text-white focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-green-deep border-t border-green-mid">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
            <a href="<?php echo SITE_URL; ?>/#about" class="block px-3 py-2 text-white hover:text-gold">About</a>
            <a href="<?php echo SITE_URL; ?>/#courses" class="block px-3 py-2 text-white hover:text-gold">Courses</a>
            <a href="<?php echo SITE_URL; ?>/#books" class="block px-3 py-2 text-white hover:text-gold">Books</a>
            <a href="<?php echo SITE_URL; ?>/#articles" class="block px-3 py-2 text-white hover:text-gold">Articles</a>
            <a href="<?php echo SITE_URL; ?>/setting.php" class="block px-3 py-2 text-white hover:text-gold">Directory</a>
            <a href="<?php echo SITE_URL; ?>/#audios" class="block px-3 py-2 text-white hover:text-gold">Audio</a>
            <a href="<?php echo SITE_URL; ?>/#qa" class="block px-3 py-2 text-white hover:text-gold">Q&A</a>
            <a href="<?php echo SITE_URL; ?>/#contact" class="block px-3 py-2 text-white hover:text-gold">Contact</a>
            <div class="flex flex-col space-y-2 px-3 py-2 mt-4 border-t border-green-mid">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <span class="text-gold-pale text-sm">Salam, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
                    <a href="<?php echo SITE_URL; ?>/user_dashboard.php" class="text-gold text-sm font-bold">Dashboard</a>
                    <form method="POST" action="<?php echo SITE_URL; ?>/user_dashboard.php">
                        <input type="hidden" name="action" value="logout">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <button type="submit" class="text-white text-sm">Logout</button>
                    </form>
                <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>/login.php" class="text-white text-sm">Login</a>
                    <a href="<?php echo SITE_URL; ?>/register.php" class="text-gold text-sm font-bold">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Navbar transparent-on-top (sirf homepage), scroll par solid green -->
<script>
(function(){
    var nav = document.getElementById('mainNav');
    if (!nav) return;
    var p = window.location.pathname;
    var isHome = (p === '/' || /\/index\.php$/.test(p) || /\/islamic_scholar\/?$/.test(p));
    function updNav(){
        if (isHome && window.scrollY < 60) {
            nav.classList.remove('nav-solid');
        } else {
            nav.classList.add('nav-solid');
        }
    }
    window.addEventListener('scroll', updNav, {passive:true});
    updNav();
})();
</script>

<!-- Hidden Google Translate engine (sirf translation chalata hai) -->
<div id="google_translate_element"></div>

<!-- ===== Custom Floating Language Switcher (flags ke sath) ===== -->
<div class="lang-float" id="langSwitcher">
    <div class="lang-menu">
        <a data-lang="en" onclick="setLanguage('en'); return false;" class="active"><img src="https://flagcdn.com/w40/gb.png" alt="EN"> English</a>
        <a data-lang="ur" onclick="setLanguage('ur'); return false;"><img src="https://flagcdn.com/w40/pk.png" alt="UR"> Urdu</a>
        <a data-lang="ar" onclick="setLanguage('ar'); return false;"><img src="https://flagcdn.com/w40/sa.png" alt="AR"> Arabic</a>
        <a data-lang="tr" onclick="setLanguage('tr'); return false;"><img src="https://flagcdn.com/w40/tr.png" alt="TR"> Turkish</a>
        <a data-lang="es" onclick="setLanguage('es'); return false;"><img src="https://flagcdn.com/w40/es.png" alt="ES"> Spanish</a>
    </div>
    <button class="lang-current" type="button" onclick="event.stopPropagation(); toggleLangMenu();">
        <img id="langFlag" src="https://flagcdn.com/w40/gb.png" alt="flag">
        <span id="langCode">EN</span>
        <span class="caret">&#9662;</span>
    </button>
</div>

<!-- Google Translate Engine -->
<script type="text/javascript">
function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: 'en',
        includedLanguages: 'en,ur,ar,tr,es',
        autoDisplay: false
    }, 'google_translate_element');
}
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

<!-- Floating Switcher Logic + Banner Hide -->
<script type="text/javascript">
var langData = {
    en: { name: 'English', flag: 'gb' },
    ur: { name: 'Urdu',    flag: 'pk' },
    ar: { name: 'Arabic',  flag: 'sa' },
    tr: { name: 'Turkish', flag: 'tr' },
    es: { name: 'Spanish', flag: 'es' }
};

function toggleLangMenu() {
    document.getElementById('langSwitcher').classList.toggle('open');
}

function setLanguage(code) {
    var combo = document.querySelector('.goog-te-combo');
    if (combo) {
        combo.value = code;
        combo.dispatchEvent(new Event('change'));
    }
    updateLangButton(code);
    document.getElementById('langSwitcher').classList.remove('open');
}

function updateLangButton(code) {
    var d = langData[code];
    if (!d) return;
    document.getElementById('langFlag').src = 'https://flagcdn.com/w40/' + d.flag + '.png';
    document.getElementById('langCode').innerText = code.toUpperCase();
    document.querySelectorAll('.lang-menu a').forEach(function(a) {
        a.classList.toggle('active', a.getAttribute('data-lang') === code);
    });
}

// Page reload ke baad current language cookie se button sync karo
function syncCurrentLang() {
    var m = document.cookie.match(/googtrans=\/[a-z]+\/([a-z]+)/);
    if (m && langData[m[1]]) updateLangButton(m[1]);
}

// Bahar click karne par menu band ho jaye
document.addEventListener('click', function(e) {
    var sw = document.getElementById('langSwitcher');
    if (sw && !sw.contains(e.target)) sw.classList.remove('open');
});

// Google ka "Translated into" banner + body shift force hata do
(function() {
    function killGoogleBanner() {
        var banner = document.querySelector('.goog-te-banner-frame') ||
                     document.querySelector('iframe.skiptranslate');
        if (banner) { banner.style.display = 'none'; }
        if (document.body && document.body.style.top !== '0px') {
            document.body.style.top = '0px';
            document.body.style.position = 'static';
        }
    }
    setInterval(killGoogleBanner, 400);
    window.addEventListener('load', function() { killGoogleBanner(); syncCurrentLang(); });
})();
</script>