<!-- ===== FOOTER ===== -->
<footer class="relative bg-green-deep overflow-hidden mt-16">

    <!-- Calligraphy Background Image -->
    <div class="absolute inset-0" style="background-image: url('<?php echo SITE_URL; ?>/image/hero-bg.png'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
    <!-- Green overlay for readability -->
    <div class="absolute inset-0 bg-green-deep/85"></div>

    <!-- Content wrapper (above background) -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- ===== Top CTA Band ===== -->
        <div class="flex flex-col md:flex-row items-center justify-between gap-6 py-10">
            <div class="flex items-center gap-5">
                <!-- Crescent + Lantern Icon -->
                <div class="flex-shrink-0 text-gold">
                    <svg class="w-14 h-14" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.3 2c.4 0 .8 0 1.2.1A8 8 0 0 0 14 18a8 8 0 0 0 7.9-6.7c.1.4.1.8.1 1.2A10 10 0 1 1 12.3 2z"/>
                        <circle cx="17.5" cy="6.5" r="1.5"/>
                    </svg>
                </div>
                <div>
                    <p class="text-gold-pale text-sm md:text-base tracking-wide">Become a Part of Our Community</p>
                    <h2 class="font-english text-white text-3xl md:text-4xl font-bold uppercase tracking-wide">Inspired? Join Us Right Now!</h2>
                </div>
            </div>
            <a href="<?php echo SITE_URL; ?>/register.php" class="flex-shrink-0 bg-gold hover:bg-gold-light text-white font-semibold px-10 py-4 rounded-full shadow-lg transition duration-300 text-lg">
                Join Community
            </a>
        </div>

        <!-- Divider -->
        <div class="border-t border-gold/20"></div>

        <!-- ===== Main Footer Columns ===== -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 py-16">

            <!-- Col 1: Decorative Gold Badge (image) -->
            <div class="flex justify-center md:justify-start">
                <img src="<?php echo SITE_URL; ?>/image/footer-logo.png" alt="محمد اقبال کیلانی" class="w-52 h-auto md:-ml-4">
            </div>

            <!-- Col 2: Information -->
            <div>
                <h3 class="font-english text-gold text-xl font-bold uppercase tracking-wider mb-5">Information</h3>
                <p class="text-gray-300 text-sm leading-relaxed">
                    Muhammad Iqbal Kilani reviving the Qur'an &amp; Sunnah through authentic Islamic scholarship — offering structured courses, classical books, and guidance for the modern Muslim.
                </p>
            </div>

            <!-- Col 3: Contact Info -->
            <div>
                <h3 class="font-english text-gold text-xl font-bold uppercase tracking-wider mb-5">Contact Info</h3>
                <ul class="space-y-5">
                    <li class="flex items-center gap-4">
                        <span class="flex-shrink-0 w-11 h-11 rounded-full bg-gold flex items-center justify-center text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.95.68l1.5 4.5a1 1 0 01-.5 1.2l-2.26 1.13a11 11 0 005.52 5.52l1.13-2.26a1 1 0 011.2-.5l4.5 1.5a1 1 0 01.68.95V19a2 2 0 01-2 2h-1C9.7 21 3 14.3 3 6V5z"/></svg>
                        </span>
                        <div class="text-sm text-gray-300">
                            <p>Hotline: 1800-123-456-7</p>
                            <p>Mon – Sat: 9.00 am – 6.00 pm</p>
                        </div>
                    </li>
                    <li class="flex items-center gap-4">
                        <span class="flex-shrink-0 w-11 h-11 rounded-full bg-gold flex items-center justify-center text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </span>
                        <p class="text-sm text-gray-300">info@muhammadiqbalkilani.com</p>
                    </li>
                    <li class="flex items-center gap-4">
                        <span class="flex-shrink-0 w-11 h-11 rounded-full bg-gold flex items-center justify-center text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.66 6.34A8 8 0 0117.66 17.66L12 23l-5.66-5.34A8 8 0 1117.66 6.34z"/><circle cx="12" cy="11" r="2.5" stroke-width="2"/></svg>
                        </span>
                        <p class="text-sm text-gray-300">Muhammad Iqbal Kilani, Pakistan</p>
                    </li>
                </ul>
            </div>

            <!-- Col 4: Quick Links -->
            <div>
                <h3 class="font-english text-gold text-xl font-bold uppercase tracking-wider mb-5">Quick Links</h3>
                <ul class="space-y-3 text-sm">
                    <li><a href="<?php echo SITE_URL; ?>/#about" class="flex items-center gap-2 text-gray-300 hover:text-gold transition"><span class="text-gold">&#9656;</span> About Sheikh</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/#courses" class="flex items-center gap-2 text-gray-300 hover:text-gold transition"><span class="text-gold">&#9656;</span> Lectures &amp; Courses</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/#books" class="flex items-center gap-2 text-gray-300 hover:text-gold transition"><span class="text-gold">&#9656;</span> Books &amp; Writings</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/#qa" class="flex items-center gap-2 text-gray-300 hover:text-gold transition"><span class="text-gold">&#9656;</span> Fatwa &amp; Q&amp;A</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/#contact" class="flex items-center gap-2 text-gray-300 hover:text-gold transition"><span class="text-gold">&#9656;</span> Contact Us</a></li>
                </ul>
            </div>
        </div>

        <!-- Divider -->
        <div class="border-t border-gold/20"></div>

        <!-- ===== Bottom Bar ===== -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 py-6">
            <p class="text-sm text-gray-400">
                Muhammad Iqbal Kilani &copy; Copyright <?php echo date('Y'); ?>, <span class="text-gold">All Rights Reserved</span>
            </p>
            <div class="flex items-center gap-5">
                <a href="#" class="text-gray-300 hover:text-gold transition" aria-label="Facebook">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12a10 10 0 10-11.56 9.87v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.78-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.88h-2.34v6.99A10 10 0 0022 12z"/></svg>
                </a>
                <a href="#" class="text-gray-300 hover:text-gold transition" aria-label="Twitter">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23 5a8.5 8.5 0 01-2.36.65A4.13 4.13 0 0022.45 3a8.22 8.22 0 01-2.6 1 4.1 4.1 0 00-7 3.74A11.64 11.64 0 013 4.05a4.1 4.1 0 001.27 5.48A4.07 4.07 0 012.4 9v.05a4.1 4.1 0 003.3 4.02 4.1 4.1 0 01-1.85.07 4.11 4.11 0 003.83 2.85A8.23 8.23 0 012 17.6a11.61 11.61 0 006.29 1.84c7.55 0 11.67-6.25 11.67-11.67 0-.18 0-.36-.01-.53A8.34 8.34 0 0023 5z"/></svg>
                </a>
                <a href="#" class="text-gray-300 hover:text-gold transition" aria-label="LinkedIn">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h14zM8.34 18.34v-7.2H6.05v7.2h2.29zM7.2 9.9a1.33 1.33 0 100-2.66 1.33 1.33 0 000 2.66zm11.14 8.44v-3.94c0-2.1-.45-3.72-2.91-3.72-1.18 0-1.97.65-2.3 1.27h-.03v-1.07h-2.2v7.46h2.29v-3.69c0-.97.18-1.92 1.39-1.92 1.18 0 1.2 1.12 1.2 1.98v3.63h2.29z"/></svg>
                </a>
                <a href="#" class="text-gray-300 hover:text-gold transition" aria-label="Instagram">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.16c3.2 0 3.58.01 4.85.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.42.36 1.06.41 2.23.06 1.27.07 1.65.07 4.85s-.01 3.58-.07 4.85c-.05 1.17-.25 1.8-.41 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.42.16-1.06.36-2.23.41-1.27.06-1.65.07-4.85.07s-3.58-.01-4.85-.07c-1.17-.05-1.8-.25-2.23-.41a3.72 3.72 0 01-1.38-.9 3.72 3.72 0 01-.9-1.38c-.16-.42-.36-1.06-.41-2.23-.06-1.27-.07-1.65-.07-4.85s.01-3.58.07-4.85c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.42-.16 1.06-.36 2.23-.41C8.42 2.17 8.8 2.16 12 2.16zM12 0C8.74 0 8.33.01 7.05.07 5.78.13 4.9.33 4.14.63c-.79.3-1.46.72-2.12 1.38C1.36 2.67.94 3.34.63 4.14.33 4.9.13 5.78.07 7.05.01 8.33 0 8.74 0 12s.01 3.67.07 4.95c.06 1.27.26 2.15.56 2.91.3.79.72 1.46 1.38 2.12.66.66 1.33 1.08 2.12 1.38.76.3 1.64.5 2.91.56C8.33 23.99 8.74 24 12 24s3.67-.01 4.95-.07c1.27-.06 2.15-.26 2.91-.56a5.88 5.88 0 002.12-1.38c.66-.66 1.08-1.33 1.38-2.12.3-.76.5-1.64.56-2.91.06-1.28.07-1.69.07-4.95s-.01-3.67-.07-4.95c-.06-1.27-.26-2.15-.56-2.91a5.88 5.88 0 00-1.38-2.12A5.88 5.88 0 0019.86.63c-.76-.3-1.64-.5-2.91-.56C15.67.01 15.26 0 12 0zm0 5.84a6.16 6.16 0 100 12.32 6.16 6.16 0 000-12.32zM12 16a4 4 0 110-8 4 4 0 010 8zm6.41-10.85a1.44 1.44 0 100 2.88 1.44 1.44 0 000-2.88z"/></svg>
                </a>
            </div>
        </div>
    </div>
</footer>

<script>
// Simple smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const target = document.querySelector(this.getAttribute('href'));
        if(target) {
            e.preventDefault();
            target.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});
</script>

<!-- ===== Back to Top Button ===== -->
<style>
    .back-to-top {
        position: fixed;
        bottom: 24px;
        left: 24px;
        z-index: 9998;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #C9960A;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 6px 18px rgba(0,0,0,0.25);
        opacity: 0;
        visibility: hidden;
        transform: translateY(16px);
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }
    .back-to-top:hover { background: #E8B840; transform: translateY(0) scale(1.08); }
    .back-to-top.show { opacity: 1; visibility: visible; transform: translateY(0); }
</style>
<button id="backToTop" onclick="scrollToTop()" aria-label="Back to top" class="back-to-top">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
</button>
<script>
(function(){
    var btn = document.getElementById('backToTop');
    if (!btn) return;
    function onScroll(){
        if (window.scrollY > 300) { btn.classList.add('show'); }
        else { btn.classList.remove('show'); }
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
})();
function scrollToTop(){
    var start = window.scrollY || window.pageYOffset;
    var startTime = null;
    var duration = 700;
    function easeInOutQuad(t){ return t < 0.5 ? 2*t*t : -1 + (4 - 2*t)*t; }
    function step(now){
        if (!startTime) startTime = now;
        var elapsed = now - startTime;
        var progress = Math.min(elapsed / duration, 1);
        window.scrollTo(0, Math.round(start * (1 - easeInOutQuad(progress))));
        if (elapsed < duration) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
}
</script>
</body>
</html>
