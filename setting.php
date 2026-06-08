<?php
require_once 'includes/header.php';

// ============================================================
// 1. DATA GROUPING ENGINE (Safe Structured Query Match)
// ============================================================
try {
    $queryStr = "
        SELECT 
            s.id AS scholar_id, s.name AS scholar_name, s.specialization AS scholar_spec,
            sub.id AS subject_id, sub.name AS subject_name, sub.book_id AS sub_book_id,
            t.id AS topic_id, t.name AS topic_name, t.book_id AS top_book_id,
            c.id AS concept_id, c.name AS concept_name, c.book_id AS con_book_id,
            b.title AS b_title, b.title_urdu AS b_urdu, b.file_path AS b_file
        FROM islamic_scholars s
        LEFT JOIN subjects sub ON s.id = sub.islamic_scholar_id
        LEFT JOIN topics t ON sub.id = t.subject_id
        LEFT JOIN concepts c ON t.id = c.topic_id
        LEFT JOIN books b ON (sub.book_id = b.id OR t.book_id = b.id OR c.book_id = b.id)
        ORDER BY s.name ASC, sub.name ASC, t.name ASC, c.name ASC
    ";
    
    $raw_rows = $pdo->query($queryStr)->fetchAll();
    
    $scholars = [];
    foreach ($raw_rows as $row) {
        $s_id = $row['scholar_id'];
        if (!$s_id) continue;
        
        if (!isset($scholars[$s_id])) {
            $scholars[$s_id] = [
                'id' => $s_id,
                'name' => $row['scholar_name'] ?? 'Unknown Scholar',
                'specialization' => $row['scholar_spec'] ?? 'Muhammad Iqbal Kalani Library',
                'subjects' => []
            ];
        }
        
        if (!empty($row['subject_id'])) {
            $sub_id = $row['subject_id'];
            if (!isset($scholars[$s_id]['subjects'][$sub_id])) {
                $scholars[$s_id]['subjects'][$sub_id] = [
                    'id' => $sub_id,
                    'name' => $row['subject_name'] ?? '',
                    'book_id' => $row['sub_book_id'] ?? null,
                    'b_title' => $row['b_title'] ?? '',
                    'topics' => []
                ];
            }
            
            if (!empty($row['topic_id'])) {
                $top_id = $row['topic_id'];
                if (!isset($scholars[$s_id]['subjects'][$sub_id]['topics'][$top_id])) {
                    $scholars[$s_id]['subjects'][$sub_id]['topics'][$top_id] = [
                        'id' => $top_id,
                        'name' => $row['topic_name'] ?? '',
                        'book_id' => $row['top_book_id'] ?? null,
                        'b_title' => $row['b_title'] ?? '',
                        'concepts' => []
                    ];
                }
                
                if (!empty($row['concept_id'])) {
                    $con_id = $row['concept_id'];
                    if (!isset($scholars[$s_id]['subjects'][$sub_id]['topics'][$top_id]['concepts'][$con_id])) {
                        $scholars[$s_id]['subjects'][$sub_id]['topics'][$top_id]['concepts'][$con_id] = [
                            'id' => $con_id,
                            'name' => $row['concept_name'] ?? '',
                            'book_id' => $row['con_book_id'] ?? null,
                            'b_title' => $row['b_title'] ?? ''
                        ];
                    }
                }
            }
        }
    }
} catch (PDOException $e) {
    die("<div style='background:#fef3c7; color:#92400e; padding:20px; border:1px solid #fde68a; border-radius:8px;'>
            <h3>Database Processing Fail!</h3>
            <p>" . htmlspecialchars($e->getMessage()) . "</p>
         </div>");
}
?>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .accordion-content { max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out; }
    .accordion-content.open { max-height: 3000px; transition: max-height 0.5s ease-in; }
    
    .search-tab {
        background: rgba(255,255,255,0.15);
        color: rgba(255,255,255,0.9);
        border: 1px solid rgba(255,255,255,0.25);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .search-tab.active {
        background: #f5c242 !important;
        color: #1a3a2a !important;
        border-color: #f5c242 !important;
        box-shadow: 0 4px 6px rgba(0,0,0,0.15);
    }
    .highlight { background: #fef08a !important; padding: 1px 4px; border-radius: 4px; font-weight: 750; color: #000; }
</style>

<section class="bg-gradient-to-br from-[#1a3a2a] to-[#2d5c3e] text-white py-12 text-center shadow-inner">
    <div class="max-w-4xl mx-auto px-4">
        <h1 class="text-3xl md:text-4xl font-extrabold mb-3">Knowledge Classification Directory</h1>
        <p class="text-gray-200 text-sm md:text-base mb-6">Explore authentic Islamic research structured by Scholars, Subjects, Topics, and Core Concepts.</p>
        
        <div class="flex justify-center gap-2 mb-4 flex-wrap">
            <button onclick="switchSearchType('general')" id="tab-general" class="search-tab active px-4 py-2 rounded-lg font-semibold text-sm">🔍 General</button>
            <button onclick="switchSearchType('book')" id="tab-book" class="search-tab px-4 py-2 rounded-lg font-semibold text-sm">📚 By Book</button>
            <button onclick="switchSearchType('subject')" id="tab-subject" class="search-tab px-4 py-2 rounded-lg font-semibold text-sm">📖 By Subject</button>
            <button onclick="switchSearchType('topic')" id="tab-topic" class="search-tab px-4 py-2 rounded-lg font-semibold text-sm">📋 By Topic</button>
            <button onclick="switchSearchType('concept')" id="tab-concept" class="search-tab px-4 py-2 rounded-lg font-semibold text-sm">💡 By Concept</button>
        </div>

        <div class="relative max-w-xl mx-auto">
            <input type="text" id="searchInput" onkeyup="performSearch()" placeholder="Search scholars, subjects, topics..." class="w-full pl-12 pr-4 py-3 rounded-xl border-none text-gray-800 focus:ring-2 focus:ring-[#f5c242] shadow-lg outline-none text-sm">
            <div class="absolute left-4 top-3.5 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <div id="activeFilter" class="text-xs text-yellow-200 mt-2 hidden">
                <span id="filterText"></span> | <button onclick="clearSearch()" class="underline font-bold hover:text-white">Reset Matrix</button>
            </div>
        </div>
    </div>
</section>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <?php if (empty($scholars)): ?>
        <div class="bg-blue-50 border border-blue-200 text-blue-800 p-8 rounded-xl text-center max-w-2xl mx-auto shadow-sm">
            <h3 class="font-bold text-lg mb-2">Directory Empty</h3>
            <p class="text-sm">Database mein processing records nahi mile.</p>
        </div>
    <?php else: ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-4 space-y-4">
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2 mb-2">
                <span class="w-2 h-5 bg-[#1a3a2a] rounded-sm"></span> Islamic Scholars
            </h2>
            <div class="space-y-3 custom-scrollbar overflow-y-auto max-h-[70vh] pr-1" id="scholarCardsContainer">
                <?php $first = true; foreach ($scholars as $scholar): ?>
                    <div class="scholar-card bg-white border border-gray-200 hover:border-[#5DCAA5] rounded-xl p-5 shadow-sm transition cursor-pointer <?php echo $first ? 'ring-2 ring-[#1a3a2a]' : ''; ?>" 
                         data-scholar-id="<?= $scholar['id'] ?>"
                         onclick="viewScholarTree(this, 'scholar-tree-<?= $scholar['id'] ?>')">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-bold text-gray-900 text-base scholar-name"><?= htmlspecialchars($scholar['name']) ?></h3>
                                <p class="text-xs text-emerald-700 font-medium mt-0.5"><?= htmlspecialchars($scholar['specialization']) ?></p>
                            </div>
                            <span class="bg-emerald-50 text-emerald-700 text-xs font-semibold px-2.5 py-1 rounded-full border border-emerald-200">
                                <?= count($scholar['subjects']) ?> Subjects
                            </span>
                        </div>
                    </div>
                <?php $first = false; endforeach; ?>
            </div>
        </div>

        <div class="lg:col-span-8">
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2 mb-4">
                <span class="w-2 h-5 bg-[#f5c242] rounded-sm"></span> Mapping Directory Tree
            </h2>
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm min-h-[50vh]">
                <?php $first = true; foreach ($scholars as $scholar): ?>
                    <div id="scholar-tree-<?= $scholar['id'] ?>" class="scholar-tree-content <?php echo $first ? '' : 'hidden'; ?>">
                        <div class="mb-6 pb-4 border-b border-gray-100">
                            <h4 class="text-xl font-extrabold text-[#1a3a2a]"><?= htmlspecialchars($scholar['name']) ?></h4>
                        </div>

                        <div class="space-y-3">
                            <?php foreach ($scholar['subjects'] as $subject): ?>
                                <div class="border border-purple-100 rounded-xl bg-purple-50/10 overflow-hidden subject-wrapper" data-subject-name="<?= strtolower(htmlspecialchars($subject['name'])) ?>">
                                    <div class="flex items-center justify-between p-4 bg-white hover:bg-purple-50/40 cursor-pointer transition border-b border-purple-100" onclick="toggleAccordion(this)">
                                        <div class="flex items-center gap-3">
                                            <div class="w-2 h-2 rounded-full bg-purple-500"></div>
                                            <span class="font-bold text-purple-950 text-sm subject-title"><?= htmlspecialchars($subject['name']) ?></span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <?php if(!empty($subject['book_id'])): ?>
                                                <a href="view_book.php?id=<?= $subject['book_id'] ?>" onclick="event.stopPropagation()" class="text-xs bg-[#f5c242] text-white font-bold px-2 py-0.5 rounded shadow book-link-tag" data-book-search="<?= strtolower(htmlspecialchars($subject['b_title'])) ?>">📖 <?= htmlspecialchars($subject['b_title']) ?></a>
                                            <?php endif; ?>
                                            <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded"><?= count($subject['topics']) ?> Topics</span>
                                            <svg class="w-4 h-4 text-purple-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </div>
                                    </div>

                                    <div class="accordion-content bg-white/40 pl-6 pr-4 border-l-2 border-purple-300 ml-4">
                                        <div class="py-3 space-y-3">
                                            <?php foreach ($subject['topics'] as $topic): ?>
                                                <div class="border border-amber-100 rounded-lg bg-white overflow-hidden topic-wrapper" data-topic-name="<?= strtolower(htmlspecialchars($topic['name'])) ?>">
                                                    <div class="flex items-center justify-between p-3 hover:bg-amber-50/40 cursor-pointer transition" onclick="toggleAccordion(this)">
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-1.5 h-1.5 rounded-full bg-amber-500"></div>
                                                            <span class="font-semibold text-amber-950 text-xs topic-title"><?= htmlspecialchars($topic['name']) ?></span>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <?php if(!empty($topic['book_id'])): ?>
                                                                <a href="view_book.php?id=<?= $topic['book_id'] ?>" onclick="event.stopPropagation()" class="text-[11px] bg-[#1a3a2a] text-white px-2 py-0.5 rounded book-link-tag" data-book-search="<?= strtolower(htmlspecialchars($topic['b_title'])) ?>">📖 <?= htmlspecialchars($topic['b_title']) ?></a>
                                                            <?php endif; ?>
                                                            <span class="text-[11px] bg-amber-100 text-amber-800 px-2 py-0.5 rounded"><?= count($topic['concepts']) ?> Concepts</span>
                                                            <svg class="w-3.5 h-3.5 text-amber-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                        </div>
                                                    </div>

                                                    <div class="accordion-content bg-gray-50/50 pl-4 pr-3 border-l border-amber-300 ml-3">
                                                        <div class="py-3">
                                                            <?php if (!empty($topic['concepts'])): ?>
                                                                <div class="flex flex-wrap gap-2">
                                                                    <?php foreach ($topic['concepts'] as $concept): ?>
                                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-50 border border-orange-200 rounded-lg text-orange-900 text-xs concept-badge" data-concept-name="<?= strtolower(htmlspecialchars($concept['name'])) ?>">
                                                                            <span class="concept-name-text"><?= htmlspecialchars($concept['name']) ?></span>
                                                                            <?php if(!empty($concept['book_id'])): ?>
                                                                                <a href="view_book.php?id=<?= $concept['book_id'] ?>" class="text-[10px] bg-[#f5c242] text-white px-1 py-0.5 rounded book-link-tag" data-book-search="<?= strtolower(htmlspecialchars($concept['b_title'])) ?>">📖</a>
                                                                            <?php endif; ?>
                                                                        </span>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            <?php else: ?>
                                                                <p class="text-xs text-gray-400 italic">No core concepts linked.</p>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php $first = false; endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</main>

<script>
let currentSearchType = 'general';

function switchSearchType(type) {
    currentSearchType = type;
    document.querySelectorAll('.search-tab').forEach(tab => tab.classList.remove('active'));
    document.getElementById('tab-' + type).classList.add('active');
    const placeholders = {
        general:  '🔍 Search anything globally...',
        book:     '📚 Search by exact Book Title...',
        subject:  '📖 Search by Subject Framework (e.g., Hadith)...',
        topic:    '📋 Search by specific Topic Field...',
        concept:  '💡 Search by distinct Concept Node...'
    };
    document.getElementById('searchInput').placeholder = placeholders[type];
    clearSearch();
}

// ── Helper: open all .accordion-content inside a wrapper ──────────────────
function openAllAccordions(wrapper) {
    wrapper.querySelectorAll('.accordion-content').forEach(a => a.classList.add('open'));
    // also rotate the chevron icons
    wrapper.querySelectorAll('svg.transform').forEach(icon => icon.classList.add('rotate-180'));
}

// ── Helper: show subject + open its accordion ─────────────────────────────
function showSubject(subEl) {
    subEl.style.display = '';
    let subAcc = subEl.querySelector(':scope > .accordion-content, :scope > div > .accordion-content');
    // fallback — just grab first accordion-content child
    if (!subAcc) subAcc = subEl.querySelector('.accordion-content');
    if (subAcc) {
        subAcc.classList.add('open');
        let icon = subEl.querySelector(':scope > div > svg.transform, :scope > div:first-child svg.transform');
        if (icon) icon.classList.add('rotate-180');
    }
}

// ── Helper: show topic + open its accordion ───────────────────────────────
function showTopic(topEl) {
    topEl.style.display = '';
    let topAcc = topEl.querySelector(':scope > .accordion-content, :scope > div > .accordion-content');
    if (!topAcc) topAcc = topEl.querySelector('.accordion-content');
    if (topAcc) {
        topAcc.classList.add('open');
        let icon = topEl.querySelector(':scope > div > svg.transform, :scope > div:first-child svg.transform');
        if (icon) icon.classList.add('rotate-180');
    }
}

// ── Main search function ──────────────────────────────────────────────────
function performSearch() {
    const query = document.getElementById('searchInput').value.toLowerCase().trim();
    if (!query) { clearSearch(); return; }

    document.getElementById('activeFilter').classList.remove('hidden');
    document.getElementById('filterText').textContent = `Scope: ${currentSearchType.toUpperCase()}`;

    // Reset visibility
    document.querySelectorAll('.subject-wrapper, .topic-wrapper, .concept-badge').forEach(el => {
        el.style.display = 'none';
    });
    document.querySelectorAll('.accordion-content').forEach(a => a.classList.remove('open'));
    document.querySelectorAll('.concept-name-text').forEach(tx => tx.classList.remove('highlight'));
    document.querySelectorAll('svg.transform').forEach(icon => icon.classList.remove('rotate-180'));

    let visibleSchTree = new Set();

    // ── BY BOOK ───────────────────────────────────────────────────────────
    if (currentSearchType === 'book') {
        document.querySelectorAll('.book-link-tag').forEach(bk => {
            if ((bk.getAttribute('data-book-search') || '').includes(query)) {
                let sub = bk.closest('.subject-wrapper');
                let top = bk.closest('.topic-wrapper');
                let bge = bk.closest('.concept-badge');
                let tree = bk.closest('.scholar-tree-content');
                if (tree) visibleSchTree.add(tree.id);

                if (sub && !top && !bge) {
                    // Book is on a subject
                    showSubject(sub);
                    sub.querySelectorAll('.topic-wrapper').forEach(t => t.style.display = '');
                } else if (top && !bge) {
                    // Book is on a topic
                    showTopic(top);
                    let parentSub = top.closest('.subject-wrapper');
                    if (parentSub) showSubject(parentSub);
                } else if (bge) {
                    // Book is on a concept badge
                    bge.style.display = '';
                    let parentTop = bge.closest('.topic-wrapper');
                    if (parentTop) {
                        showTopic(parentTop);
                        let parentSub = parentTop.closest('.subject-wrapper');
                        if (parentSub) showSubject(parentSub);
                    }
                }
            }
        });
    }

    // ── BY SUBJECT ────────────────────────────────────────────────────────
    else if (currentSearchType === 'subject') {
        document.querySelectorAll('.subject-wrapper').forEach(sb => {
            if ((sb.getAttribute('data-subject-name') || '').includes(query)) {
                showSubject(sb);
                sb.querySelectorAll('.topic-wrapper').forEach(t => t.style.display = '');
                let tree = sb.closest('.scholar-tree-content');
                if (tree) visibleSchTree.add(tree.id);
            }
        });
    }

    // ── BY TOPIC ──────────────────────────────────────────────────────────
    else if (currentSearchType === 'topic') {
        document.querySelectorAll('.topic-wrapper').forEach(tp => {
            if ((tp.getAttribute('data-topic-name') || '').includes(query)) {
                showTopic(tp);
                let parentSub = tp.closest('.subject-wrapper');
                if (parentSub) showSubject(parentSub);
                let tree = tp.closest('.scholar-tree-content');
                if (tree) visibleSchTree.add(tree.id);
            }
        });
    }

    // ── BY CONCEPT ────────────────────────────────────────────────────────
    else if (currentSearchType === 'concept') {
        document.querySelectorAll('.concept-badge').forEach(cp => {
            if ((cp.getAttribute('data-concept-name') || '').includes(query)) {
                cp.style.display = '';
                let label = cp.querySelector('.concept-name-text');
                if (label) label.classList.add('highlight');

                let parentTop = cp.closest('.topic-wrapper');
                if (parentTop) {
                    showTopic(parentTop);
                    let parentSub = parentTop.closest('.subject-wrapper');
                    if (parentSub) showSubject(parentSub);
                }
                let tree = cp.closest('.scholar-tree-content');
                if (tree) visibleSchTree.add(tree.id);
            }
        });
    }

    // ── GENERAL (scholar name + subject + topic + concept) ────────────────
    else if (currentSearchType === 'general') {

        // 1. Scholar name match → show ALL their content
        document.querySelectorAll('.scholar-card').forEach(card => {
            let sName = (card.querySelector('.scholar-name') || {}).textContent || '';
            if (sName.toLowerCase().includes(query)) {
                let treeId = 'scholar-tree-' + card.getAttribute('data-scholar-id');
                visibleSchTree.add(treeId);
                let tree = document.getElementById(treeId);
                if (tree) {
                    tree.querySelectorAll('.subject-wrapper').forEach(sb => {
                        showSubject(sb);
                        sb.querySelectorAll('.topic-wrapper').forEach(t => t.style.display = '');
                    });
                }
            }
        });

        // 2. Subject name match
        document.querySelectorAll('.subject-wrapper').forEach(sb => {
            if ((sb.getAttribute('data-subject-name') || '').includes(query)) {
                showSubject(sb);
                sb.querySelectorAll('.topic-wrapper').forEach(t => t.style.display = '');
                let tree = sb.closest('.scholar-tree-content');
                if (tree) visibleSchTree.add(tree.id);
            }
        });

        // 3. Topic name match
        document.querySelectorAll('.topic-wrapper').forEach(tp => {
            if ((tp.getAttribute('data-topic-name') || '').includes(query)) {
                showTopic(tp);
                let parentSub = tp.closest('.subject-wrapper');
                if (parentSub) showSubject(parentSub);
                let tree = tp.closest('.scholar-tree-content');
                if (tree) visibleSchTree.add(tree.id);
            }
        });

        // 4. Concept name match
        document.querySelectorAll('.concept-badge').forEach(cp => {
            if ((cp.getAttribute('data-concept-name') || '').includes(query)) {
                cp.style.display = '';
                let label = cp.querySelector('.concept-name-text');
                if (label) label.classList.add('highlight');

                let parentTop = cp.closest('.topic-wrapper');
                if (parentTop) {
                    showTopic(parentTop);
                    let parentSub = parentTop.closest('.subject-wrapper');
                    if (parentSub) showSubject(parentSub);
                }
                let tree = cp.closest('.scholar-tree-content');
                if (tree) visibleSchTree.add(tree.id);
            }
        });
    }

    // ── Sync left sidebar ─────────────────────────────────────────────────
    let firstVisibleCard = null;
    document.querySelectorAll('.scholar-card').forEach(card => {
        let matchingId = 'scholar-tree-' + card.getAttribute('data-scholar-id');
        if (visibleSchTree.has(matchingId)) {
            card.style.display = '';
            if (!firstVisibleCard) firstVisibleCard = card;
        } else {
            card.style.display = 'none';
        }
    });

    // ── FIX: Auto-switch right panel to first matched scholar ─────────────
    if (firstVisibleCard) {
        let tId = 'scholar-tree-' + firstVisibleCard.getAttribute('data-scholar-id');
        viewScholarTree(firstVisibleCard, tId);
    } else {
        // No results at all — show a message but don't break the UI
        document.querySelectorAll('.scholar-card').forEach(c => c.style.display = '');
        document.querySelectorAll('.scholar-tree-content').forEach(t => t.classList.add('hidden'));
    }
}

// ── Book search chain helper (legacy, kept for safety) ────────────────────
function forceDisplayTreeChain(element, resultSet) {
    let sub = element.closest('.subject-wrapper');
    let top = element.closest('.topic-wrapper');
    let bge = element.closest('.concept-badge');
    let tree = element.closest('.scholar-tree-content');
    if (tree) resultSet.add(tree.id);

    if (bge) bge.style.display = '';
    if (top) showTopic(top);
    if (sub) {
        showSubject(sub);
        if (!top) sub.querySelectorAll('.topic-wrapper').forEach(t => t.style.display = '');
    }
}

// ── Clear / Reset ─────────────────────────────────────────────────────────
function clearSearch() {
    document.getElementById('searchInput').value = '';
    document.getElementById('activeFilter').classList.add('hidden');

    document.querySelectorAll('.scholar-card, .subject-wrapper, .topic-wrapper, .concept-badge').forEach(el => {
        el.style.display = '';
    });
    document.querySelectorAll('.accordion-content').forEach(a => a.classList.remove('open'));
    document.querySelectorAll('.concept-name-text').forEach(tx => tx.classList.remove('highlight'));
    document.querySelectorAll('svg.transform').forEach(icon => icon.classList.remove('rotate-180'));

    // Show first scholar tree by default
    let baseCard = document.querySelector('.scholar-card');
    if (baseCard) {
        let tId = 'scholar-tree-' + baseCard.getAttribute('data-scholar-id');
        viewScholarTree(baseCard, tId);
    }
}

// ── Scholar tree switcher ─────────────────────────────────────────────────
function viewScholarTree(card, treeId) {
    document.querySelectorAll('.scholar-card').forEach(c => c.classList.remove('ring-2', 'ring-[#1a3a2a]'));
    card.classList.add('ring-2', 'ring-[#1a3a2a]');
    document.querySelectorAll('.scholar-tree-content').forEach(t => t.classList.add('hidden'));
    let activeTree = document.getElementById(treeId);
    if (activeTree) activeTree.classList.remove('hidden');
}

// ── Accordion toggle ──────────────────────────────────────────────────────
function toggleAccordion(header) {
    const content = header.nextElementSibling;
    const icon = header.querySelector('svg');
    if (content.classList.contains('open')) {
        content.classList.remove('open');
        if (icon) icon.classList.remove('rotate-180');
    } else {
        content.classList.add('open');
        if (icon) icon.classList.add('rotate-180');
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>