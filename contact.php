<?php
require_once 'includes/header.php';
?>

<div class="bg-green-deep py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="font-english text-white text-5xl mb-2">Contact & Maktaba</h1>
        <h2 class="arabic-text text-gold text-4xl">رابطہ</h2>
    </div>
</div>

<section class="py-20 bg-cream min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            
            <!-- Contact Info & Form -->
            <div class="bg-white p-8 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-2xl font-english text-green-deep font-bold mb-6">Get in Touch</h3>
                
                <div class="space-y-4 mb-8 bg-beige p-6 rounded">
                    <p class="text-body-text flex items-center">
                        <span class="w-24 font-bold">Institution:</span> Maktaba Quddusia
                    </p>
                    <p class="text-body-text flex items-center">
                        <span class="w-24 font-bold">Location:</span> Pakistan
                    </p>
                    <p class="text-body-text flex items-center">
                        <span class="w-24 font-bold">Email:</span> info@maktabaquddusia.com
                    </p>
                </div>
                
                <form action="#" method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" placeholder="First Name" required class="w-full border border-gray-300 rounded px-3 py-2 focus:border-green-mid outline-none">
                        <input type="text" placeholder="Last Name" class="w-full border border-gray-300 rounded px-3 py-2 focus:border-green-mid outline-none">
                    </div>
                    <div>
                        <input type="email" placeholder="Email Address" required class="w-full border border-gray-300 rounded px-3 py-2 focus:border-green-mid outline-none">
                    </div>
                    <div>
                        <input type="text" placeholder="Subject" required class="w-full border border-gray-300 rounded px-3 py-2 focus:border-green-mid outline-none">
                    </div>
                    <div>
                        <textarea placeholder="Your Message" rows="5" required class="w-full border border-gray-300 rounded px-3 py-2 focus:border-green-mid outline-none resize-none"></textarea>
                    </div>
                    <button type="button" onclick="alert('Message Sent!')" class="bg-green-deep hover:bg-green-mid text-white font-semibold px-8 py-3 rounded transition w-full shadow">Send Message</button>
                </form>
            </div>
            
            <!-- Donations -->
            <div class="bg-green-deep rounded-lg p-10 shadow-2xl text-white relative overflow-hidden">
                <div class="absolute -right-10 -top-10 text-gold opacity-10 text-9xl">
                    &hearts;
                </div>
                
                <div class="relative z-10">
                    <div class="mb-8">
                        <h2 class="arabic-text text-gold text-4xl mb-1">تعاون</h2>
                        <h3 class="font-english text-3xl font-bold">Support the Dawah</h3>
                    </div>
                    
                    <p class="text-gray-300 text-lg mb-8 leading-relaxed font-english">
                        "Those who spend their wealth in the way of Allah is like a seed which grows seven spikes; in each spike is a hundred grains..." (Al-Baqarah: 261)
                    </p>
                    
                    <p class="text-sm text-gray-400 mb-8">
                        Your support enables us to provide free Islamic education, translate classical texts, and maintain the scholarship platform.
                    </p>
                    
                    <div class="flex space-x-2 mb-4">
                        <button class="bg-gold text-white text-sm px-4 py-1 rounded font-semibold">PKR</button>
                        <button class="bg-transparent border border-green-mid text-gray-300 hover:bg-green-mid text-sm px-4 py-1 rounded transition">USD</button>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-8">
                        <button class="border border-gold hover:bg-gold hover:text-white rounded py-3 text-md font-semibold transition">500</button>
                        <button class="border border-gold hover:bg-gold hover:text-white rounded py-3 text-md font-semibold transition">1000</button>
                        <button class="bg-gold text-white rounded py-3 text-md font-semibold transition shadow-md">2500</button>
                        <button class="border border-gray-500 text-gray-300 hover:border-gold hover:text-white rounded py-3 text-md transition">Custom</button>
                    </div>
                    
                    <button class="w-full bg-gold hover:bg-gold-light text-white font-bold py-4 rounded transition shadow-lg text-xl mb-8">Donate Now &mdash; Sadaqah Jariyah</button>
                    
                    <div class="text-center text-sm text-gray-400 bg-black/20 p-4 rounded">
                        <p class="mb-2">Supported Payment Methods</p>
                        <div class="flex justify-center space-x-6">
                            <span class="font-semibold text-white">JazzCash</span>
                            <span class="font-semibold text-white">EasyPaisa</span>
                            <span class="font-semibold text-white">Bank Transfer</span>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
