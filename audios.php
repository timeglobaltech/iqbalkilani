<?php
require_once 'includes/header.php';

// Fetch all audios
$stmt = $pdo->query("SELECT * FROM audios ORDER BY created_at DESC");
$all_audios = $stmt->fetchAll();
?>

<div class="bg-green-deep py-16">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="font-english text-white text-5xl mb-2">Audio Bayanaat</h1>
        <h2 class="arabic-text text-gold text-4xl">آڈیو بیانات</h2>
    </div>
</div>

<section class="py-20 bg-cream min-h-screen">
    <div class="max-w-5xl mx-auto px-4">

        <?php if (empty($all_audios)): ?>
            <div class="text-center py-12 text-gray-500">No audio tracks found.</div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($all_audios as $a): ?>
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex flex-col md:flex-row items-center gap-6">

                        <!-- Play/Pause Button -->
                        <button type="button"
                            class="w-16 h-16 bg-beige rounded-full flex items-center justify-center text-green-deep flex-shrink-0 hover:bg-gold hover:text-white transition"
                            onclick="controlAudio('<?php echo htmlspecialchars($a['id']); ?>')">
                            <svg id="svg-<?php echo htmlspecialchars($a['id']); ?>"
                                class="w-6 h-6"
                                fill="currentColor"
                                viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z" />
                            </svg>
                        </button>

                        <div class="flex-1 w-full">
                            <!-- Title + Equalizer -->
                            <div class="flex items-center justify-between mb-1">
                                <h3 class="text-xl font-bold text-green-deep">
                                    <?php echo htmlspecialchars($a['title']); ?>
                                </h3>
                                <div class="equalizer" id="eq-<?php echo htmlspecialchars($a['id']); ?>">
                                    <span></span><span></span><span></span><span></span>
                                </div>
                            </div>

                            <!-- Audio Player -->
                            <div class="w-full mt-3">
                                <audio
                                    id="audio-<?php echo htmlspecialchars($a['id']); ?>"
                                    preload="metadata"
                                    controls
                                    class="w-full h-10"
                                    onplay="toggleVisuals('<?php echo htmlspecialchars($a['id']); ?>', true)"
                                    onpause="toggleVisuals('<?php echo htmlspecialchars($a['id']); ?>', false)"
                                    onended="onAudioEnded('<?php echo htmlspecialchars($a['id']); ?>')">
                                    <source
                                        src="<?php echo htmlspecialchars($a['audio_url']); ?>"
                                        type="audio/mpeg"
                                        onerror="showPathError('<?php echo htmlspecialchars($a['id']); ?>', '<?php echo htmlspecialchars($a['audio_url']); ?>')">
                                </audio>
                                <div id="error-<?php echo htmlspecialchars($a['id']); ?>"
                                    class="hidden text-red-600 text-xs mt-2 font-mono bg-red-50 p-2 rounded">
                                </div>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
/* Equalizer animation */
.equalizer {
    display: flex;
    align-items: flex-end;
    gap: 3px;
    height: 20px;
    opacity: 0;
    transition: opacity 0.3s;
}
.equalizer.playing {
    opacity: 1;
}
.equalizer span {
    display: block;
    width: 4px;
    background-color: #c8a96e;
    border-radius: 2px;
    height: 5px;
    animation: none;
}
.equalizer.playing span:nth-child(1) { animation: eq 0.8s ease-in-out infinite alternate; }
.equalizer.playing span:nth-child(2) { animation: eq 0.6s ease-in-out 0.1s infinite alternate; }
.equalizer.playing span:nth-child(3) { animation: eq 0.9s ease-in-out 0.2s infinite alternate; }
.equalizer.playing span:nth-child(4) { animation: eq 0.7s ease-in-out 0.3s infinite alternate; }

@keyframes eq {
    0%  { height: 4px; }
    100% { height: 18px; }
}
</style>

<script>
    let currentlyPlaying = null;

    function controlAudio(id) {
        const player = document.getElementById('audio-' + id);
        if (!player) return;

        // Same track — toggle play/pause
        if (currentlyPlaying === id) {
            if (player.paused) {
                player.play().catch(err => handleError(id, err));
            } else {
                player.pause();
            }
            return;
        }

        // Stop previous audio completely
        if (currentlyPlaying !== null) {
            const prev = document.getElementById('audio-' + currentlyPlaying);
            if (prev) {
                prev.pause();
                prev.currentTime = 0;
            }
            toggleVisuals(currentlyPlaying, false);
        }

        // Load and play new audio fresh
        currentlyPlaying = id;
        player.load(); // Forces browser to reload the source
        player.play().catch(err => handleError(id, err));
    }

    function toggleVisuals(id, isPlaying) {
        const svg = document.getElementById('svg-' + id);
        const eq  = document.getElementById('eq-' + id);
        if (!svg) return;

        if (isPlaying) {
            svg.innerHTML = '<path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>'; // Pause icon
            if (eq) eq.classList.add('playing');
        } else {
            svg.innerHTML = '<path d="M8 5v14l11-7z"/>'; // Play icon
            if (eq) eq.classList.remove('playing');
        }
    }

    function onAudioEnded(id) {
        toggleVisuals(id, false);
        currentlyPlaying = null;
    }

    function handleError(id, err) {
        console.error("Playback Error:", err);
        // player.load() is async — play() needs to wait for canplay event
        const player = document.getElementById('audio-' + id);
        if (player) {
            player.addEventListener('canplay', function onCanPlay() {
                player.removeEventListener('canplay', onCanPlay);
                player.play().catch(e => {
                    const errorBox = document.getElementById('error-' + id);
                    if (errorBox) {
                        errorBox.classList.remove('hidden');
                        errorBox.textContent = "Error: Could not play audio. Check the file path or format.";
                    }
                    toggleVisuals(id, false);
                    currentlyPlaying = null;
                });
            });
        }
    }

    function showPathError(id, url) {
        const errorBox = document.getElementById('error-' + id);
        if (errorBox) {
            errorBox.classList.remove('hidden');
            errorBox.textContent = "File not found: " + url;
        }
    }
</script>

<?php require_once 'includes/footer.php'; ?>