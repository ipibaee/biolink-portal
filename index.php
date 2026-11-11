<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/icons.php';

$settings = Storage::getSettings();
$links = Storage::getLinks(true);
$apps = Storage::getApps(true);
$socials = Storage::getSocials(true);

$name = htmlspecialchars($settings['name'] ?? 'Raka Adiputra, S.Kom.');
$title = htmlspecialchars($settings['title'] ?? 'Full-Stack Developer');
$bio = htmlspecialchars($settings['bio'] ?? '');
$location = htmlspecialchars($settings['location'] ?? 'Jakarta, Indonesia • GMT+7');
$statusText = htmlspecialchars($settings['status_text'] ?? 'Available for Projects');
$avatarUrl = htmlspecialchars($settings['avatar_url'] ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&auto=format&fit=crop&q=80');
$domainBadge = htmlspecialchars($settings['domain_badge'] ?? 'Available for Projects');
$appCount = count($apps);

// Inisial aplikasi untuk badge
$appInitials = [];
foreach ($apps as $app) {
    if (!empty($app['name'])) {
        $appInitials[] = strtoupper(mb_substr(trim($app['name']), 0, 1));
    }
    if (count($appInitials) >= 5) break;
}

// Kategori unik untuk modal
$categories = [];
foreach ($apps as $app) {
    if (!empty($app['category']) && !in_array($app['category'], $categories)) {
        $categories[] = $app['category'];
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $name ?> — Official Portal</title>
    <meta name="description" content="<?= $name ?> | <?= $title ?> — Portal resmi Neumorphic, portofolio sistem, dan kontak langsung.">

    <!-- Google Fonts: Lato (HKTI) & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        lato: ['"Lato"', 'sans-serif'],
                    },
                    colors: {
                        neu: {
                            light: '#ecf0f3',
                            dark: '#181920',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* ================= NEUMORPHISM (SOFT UI) STYLES ================= */
        :root {
            --neu-bg: #ecf0f3;
            --neu-card-shadow: 14px 14px 28px #c5cbd3, -14px -14px 28px #ffffff;
            --neu-flat: 6px 6px 14px #cbccd1, -6px -6px 14px #ffffff;
            --neu-flat-hover: 8px 8px 18px #b8b9be, -8px -8px 18px #ffffff;
            --neu-inset: inset 4px 4px 8px #cbccd1, inset -4px -4px 8px #ffffff;
            --neu-inset-sm: inset 2px 2px 5px #cbccd1, inset -2px -2px 5px #ffffff;
            --neu-avatar-shadow: 0px 0px 0px 5px #ecf0f3, 8px 8px 16px #a7aaaf, -8px -8px 16px #ffffff;
        }

        html.dark {
            --neu-bg: #181920;
            --neu-card-shadow: 14px 14px 30px #0e0f14, -14px -14px 30px #22232c;
            --neu-flat: 6px 6px 14px #101116, -6px -6px 14px #22232d;
            --neu-flat-hover: 8px 8px 18px #0d0e12, -8px -8px 18px #262734;
            --neu-inset: inset 4px 4px 8px #0f1015, inset -4px -4px 8px #23242e;
            --neu-inset-sm: inset 2px 2px 5px #0f1015, inset -2px -2px 5px #23242e;
            --neu-avatar-shadow: 0px 0px 0px 5px #181920, 8px 8px 16px #0e0f14, -8px -8px 16px #242530;
        }

        body {
            background-color: var(--neu-bg);
            transition: background-color 0.3s ease;
        }

        /* Neumorphic Container */
        .neu-container {
            background-color: var(--neu-bg);
            border-radius: 40px;
            box-shadow: var(--neu-card-shadow);
        }

        /* Neumorphic Extruded Cards & Buttons */
        .neu-card {
            background-color: var(--neu-bg);
            box-shadow: var(--neu-flat);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .neu-card:hover {
            box-shadow: var(--neu-flat-hover);
        }
        .neu-card:active {
            box-shadow: var(--neu-inset);
            transform: scale(0.985);
        }

        /* Neumorphic Inset / Depressed Elements */
        .neu-inset {
            background-color: var(--neu-bg);
            box-shadow: var(--neu-inset);
        }
        .neu-inset-sm {
            background-color: var(--neu-bg);
            box-shadow: var(--neu-inset-sm);
        }

        /* Neumorphic Round Buttons */
        .neu-btn-round {
            background-color: var(--neu-bg);
            box-shadow: var(--neu-flat);
            transition: all 0.2s ease;
        }
        .neu-btn-round:hover {
            box-shadow: var(--neu-flat-hover);
            transform: translateY(-1px);
        }
        .neu-btn-round:active {
            box-shadow: var(--neu-inset);
            transform: translateY(1px);
        }

        /* Concentric Avatar Ring */
        .neu-avatar {
            box-shadow: var(--neu-avatar-shadow);
        }
    </style>
</head>
<body class="text-slate-700 dark:text-zinc-200 font-sans min-h-screen flex flex-col justify-between py-8 px-4 sm:px-6 relative selection:bg-emerald-500/20 selection:text-emerald-500">

    <!-- MAIN CONTAINER (Neumorphic Card centered like HKTI login) -->
    <div class="neu-container w-full max-w-[430px] mx-auto p-6 sm:p-8 flex flex-col items-center flex-grow">
        
        <!-- TOP NAVIGATION BAR -->
        <header class="w-full flex items-center justify-between mb-7">
            <!-- Neumorphic Status Badge Inset -->
            <div class="neu-inset-sm inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-lato font-bold">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-slate-600 dark:text-zinc-300 text-[11px]"><?= $statusText ?></span>
            </div>

            <!-- Action Controls (Theme Toggle & Share) -->
            <div class="flex items-center gap-3">
                <button onclick="handleShare()" aria-label="Share Link" class="neu-btn-round w-9 h-9 rounded-full text-slate-500 dark:text-zinc-400 hover:text-emerald-500 dark:hover:text-emerald-400 flex items-center justify-center">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" x2="15.42" y1="13.51" y2="17.49"/><line x1="15.41" x2="8.59" y1="6.51" y2="10.49"/></svg>
                </button>

                <button id="themeToggleBtn" aria-label="Toggle Theme" class="neu-btn-round w-9 h-9 rounded-full text-slate-500 dark:text-zinc-400 hover:text-emerald-500 dark:hover:text-emerald-400 flex items-center justify-center">
                    <svg id="sunIcon" class="w-4 h-4 hidden dark:block text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
                    <svg id="moonIcon" class="w-4 h-4 block dark:hidden text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                </button>
            </div>
        </header>

        <!-- PROFILE HEADER (Neumorphic Logo/Avatar Ring) -->
        <section class="w-full flex flex-col items-center text-center mb-7">
            <!-- Concentric Neumorphic Avatar -->
            <div class="relative mb-5">
                <div class="neu-avatar w-24 h-24 rounded-full p-1 flex items-center justify-center">
                    <img src="<?= $avatarUrl ?>" alt="<?= $name ?>" class="w-full h-full object-cover rounded-full">
                </div>
                <!-- Status Verified Badge -->
                <div class="absolute bottom-1 right-1 w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center shadow-md border-2 border-[#ecf0f3] dark:border-[#181920]">
                    <svg class="w-3.5 h-3.5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
            </div>

            <!-- Name, Title, & Location (Font Lato & Jakarta) -->
            <h1 class="text-xl sm:text-2xl font-lato font-black tracking-wide text-slate-800 dark:text-white mb-1.5"><?= $name ?></h1>
            <p class="text-xs sm:text-sm font-semibold text-emerald-600 dark:text-emerald-400 mb-2"><?= $title ?></p>

            <div class="inline-flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-zinc-400 mb-3.5 font-medium">
                <svg class="w-3.5 h-3.5 text-slate-400 dark:text-zinc-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                <span><?= $location ?></span>
            </div>

            <!-- Bio -->
            <?php if (!empty($bio)): ?>
                <p class="text-xs text-slate-500 dark:text-zinc-400 max-w-sm leading-relaxed px-1 font-normal">
                    <?= $bio ?>
                </p>
            <?php endif; ?>
        </section>

        <!-- LINKS & INTERACTIVE CARDS -->
        <main class="w-full flex flex-col gap-3.5 mb-8">

            <!-- SPECIAL FEATURED CARD: SISTEM & APLIKASI (Opens Modal) -->
            <button onclick="openAppsModal()" class="neu-card w-full text-left rounded-3xl p-4 sm:p-4.5 border border-emerald-500/30">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3.5">
                        <div class="neu-inset-sm w-11 h-11 rounded-2xl text-emerald-500 flex items-center justify-center shrink-0">
                            <?= render_icon('layout-grid', 'w-5 h-5 text-emerald-500') ?>
                        </div>
                        <div>
                            <h3 class="text-sm font-lato font-bold text-slate-800 dark:text-white">
                                Aplikasi & Sistem Saya
                            </h3>
                            <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-0.5">
                                Kumpulan web app & sistem yang sudah live
                            </p>
                        </div>
                    </div>
                    <div class="neu-inset-sm w-8 h-8 rounded-full flex items-center justify-center text-slate-400 dark:text-zinc-400 shrink-0">
                        <svg class="w-4 h-4 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </div>
                </div>

                <!-- Bottom Row inside card: Initials Pills + Live Count Badge -->
                <div class="flex items-center justify-between pt-3 border-t border-slate-300/40 dark:border-zinc-800/80 text-[11px]">
                    <div class="flex items-center gap-1.5">
                        <?php foreach ($appInitials as $initial): ?>
                            <span class="neu-inset-sm w-6 h-6 rounded-lg text-slate-700 dark:text-zinc-300 text-[10px] font-lato font-bold flex items-center justify-center">
                                <?= $initial ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                    <div class="neu-inset-sm inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold text-emerald-600 dark:text-emerald-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span><?= $appCount ?> Web App Live</span>
                    </div>
                </div>
            </button>

            <!-- DYNAMIC LINKS FROM DATABASE -->
            <?php foreach ($links as $link): 
                $icon = htmlspecialchars($link['icon'] ?? 'link');
                $isMail = str_starts_with($link['url'], 'mailto:');
                $target = $isMail ? '' : 'target="_blank" rel="noopener noreferrer"';
            ?>
                <a href="<?= htmlspecialchars($link['url']) ?>" <?= $target ?> class="neu-card w-full block rounded-3xl p-3.5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3.5 overflow-hidden">
                            <div class="neu-inset-sm w-11 h-11 rounded-2xl text-slate-600 dark:text-zinc-300 flex items-center justify-center shrink-0">
                                <?= render_icon($icon, 'w-5 h-5') ?>
                            </div>
                            <div class="overflow-hidden">
                                <h3 class="text-xs font-lato font-bold text-slate-800 dark:text-zinc-100 truncate">
                                    <?= htmlspecialchars($link['title']) ?>
                                </h3>
                                <?php if (!empty($link['description'])): ?>
                                    <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-0.5 truncate font-normal">
                                        <?= htmlspecialchars($link['description']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="neu-inset-sm w-7 h-7 rounded-full flex items-center justify-center text-slate-400 dark:text-zinc-500 shrink-0 ml-2">
                            <svg class="w-3.5 h-3.5 text-slate-500 dark:text-zinc-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="7" x2="17" y1="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>

        </main>

        <!-- CIRCULAR SOCIAL BUTTONS ROW (Neumorphic Round Buttons) -->
        <?php if (!empty($socials)): ?>
            <div class="w-full flex items-center justify-center gap-3.5 mb-7 flex-wrap">
                <?php foreach ($socials as $soc): ?>
                    <a href="<?= htmlspecialchars($soc['url']) ?>" target="_blank" rel="noopener noreferrer" title="<?= htmlspecialchars($soc['title']) ?>" class="neu-btn-round w-11 h-11 rounded-full text-slate-600 dark:text-zinc-300 hover:text-emerald-500 dark:hover:text-emerald-400 flex items-center justify-center">
                        <?= render_icon($soc['icon'], 'w-4 h-4') ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- FOOTER -->
        <footer class="w-full text-center py-4 border-t border-slate-300/40 dark:border-zinc-800/80 mt-auto">
            <p class="text-[11px] text-slate-500 dark:text-zinc-400 font-lato font-bold">
                &copy; <?= date('Y') ?> <span class="text-slate-800 dark:text-white"><?= $name ?></span>. All rights reserved.
            </p>
        </footer>

    </div>

    <!-- MODAL: DAFTAR APLIKASI & SISTEM (Neumorphic Modal) -->
    <div id="appsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-200">
        <div id="appsModalCard" class="neu-container w-full max-w-[430px] max-h-[85vh] flex flex-col p-6 overflow-hidden scale-95 transition-transform duration-200 border border-slate-300/30 dark:border-zinc-800">
            
            <!-- Modal Header -->
            <div class="pb-4 border-b border-slate-300/40 dark:border-zinc-800 flex items-start justify-between shrink-0">
                <div>
                    <h2 class="text-base font-lato font-black text-slate-800 dark:text-white">Aplikasi & Sistem Saya</h2>
                    <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-0.5"><?= $appCount ?> web app live yang saya bangun & kelola.</p>
                </div>
                <button onclick="closeAppsModal()" aria-label="Tutup Modal" class="neu-btn-round w-8 h-8 rounded-full text-slate-400 hover:text-slate-700 dark:hover:text-white flex items-center justify-center">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
                </button>
            </div>

            <!-- Filter Tabs Pills -->
            <div class="py-3 border-b border-slate-300/40 dark:border-zinc-800 shrink-0 flex items-center gap-2 overflow-x-auto scrollbar-none">
                <button onclick="filterCategory('all', this)" class="cat-pill active neu-inset-sm px-3.5 py-1.5 rounded-full text-[11px] font-lato font-bold text-emerald-600 dark:text-emerald-400 shrink-0">
                    Semua · <?= $appCount ?>
                </button>
                <?php foreach ($categories as $cat): ?>
                    <button onclick="filterCategory('<?= strtolower(htmlspecialchars($cat)) ?>', this)" class="cat-pill neu-btn-round px-3.5 py-1.5 rounded-full text-[11px] font-lato font-bold text-slate-600 dark:text-zinc-400 shrink-0">
                        <?= htmlspecialchars($cat) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- App List Scrollable Area -->
            <div id="appsListContainer" class="py-4 overflow-y-auto space-y-3 flex-grow">
                <?php if (empty($apps)): ?>
                    <div class="text-center py-8 text-slate-400 dark:text-zinc-500 text-xs">
                        Belum ada aplikasi yang ditambahkan.
                    </div>
                <?php else: ?>
                    <?php foreach ($apps as $app): 
                        $badge = htmlspecialchars($app['badge'] ?? 'Live');
                        $cat = htmlspecialchars($app['category'] ?? '');
                        $appIcon = htmlspecialchars($app['icon'] ?? 'layout-grid');
                    ?>
                        <a href="<?= htmlspecialchars($app['url']) ?>" target="_blank" rel="noopener noreferrer" class="app-card neu-card block p-3.5 rounded-2xl" data-category="<?= strtolower($cat) ?>">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 overflow-hidden">
                                    <div class="neu-inset-sm w-10 h-10 rounded-xl text-emerald-500 flex items-center justify-center shrink-0">
                                        <?= render_icon($appIcon, 'w-5 h-5 text-emerald-500') ?>
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="flex items-center gap-2">
                                            <h4 class="text-xs font-lato font-bold text-slate-800 dark:text-white"><?= htmlspecialchars($app['name']) ?></h4>
                                            <?php if (!empty($cat)): ?>
                                                <span class="neu-inset-sm px-2 py-0.5 rounded text-[10px] font-mono font-medium text-slate-600 dark:text-zinc-300">
                                                    <?= $cat ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-1 truncate">
                                            <?= htmlspecialchars($app['description']) ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="neu-inset-sm w-7 h-7 rounded-full flex items-center justify-center text-slate-400 dark:text-zinc-500 shrink-0">
                                    <svg class="w-3.5 h-3.5 text-slate-500 dark:text-zinc-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="7" x2="17" y1="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Modal Footer -->
            <div class="pt-3 border-t border-slate-300/40 dark:border-zinc-800 flex items-center justify-between text-[10px] text-slate-400 dark:text-zinc-500 shrink-0 font-lato">
                <span>Terakhir diperbarui <?= date('F Y') ?></span>
                <span class="font-mono">↵ ESC untuk menutup</span>
            </div>

        </div>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div id="toast" class="neu-container fixed bottom-6 left-1/2 -translate-x-1/2 z-50 flex items-center gap-2 px-5 py-2.5 rounded-full text-slate-800 dark:text-white text-xs font-bold opacity-0 pointer-events-none transition-all duration-300">
        <svg class="w-4 h-4 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <span id="toastMsg">Tautan berhasil disalin!</span>
    </div>

    <!-- SCRIPTS -->
    <script>
        const themeToggleBtn = document.getElementById('themeToggleBtn');
        const htmlElement = document.documentElement;

        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            htmlElement.classList.toggle('dark', savedTheme === 'dark');
        } else {
            // Default dark neumorphic
            htmlElement.classList.add('dark');
        }

        themeToggleBtn.addEventListener('click', () => {
            const isDark = htmlElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });

        const appsModal = document.getElementById('appsModal');
        const appsModalCard = document.getElementById('appsModalCard');

        function openAppsModal() {
            appsModal.classList.remove('opacity-0', 'pointer-events-none');
            appsModalCard.classList.remove('scale-95');
            appsModalCard.classList.add('scale-100');
            document.body.style.overflow = 'hidden';
        }

        function closeAppsModal() {
            appsModal.classList.add('opacity-0', 'pointer-events-none');
            appsModalCard.classList.remove('scale-100');
            appsModalCard.classList.add('scale-95');
            document.body.style.overflow = '';
        }

        appsModal.addEventListener('click', (e) => {
            if (e.target === appsModal) closeAppsModal();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !appsModal.classList.contains('pointer-events-none')) {
                closeAppsModal();
            }
        });

        function filterCategory(cat, btn) {
            document.querySelectorAll('.cat-pill').forEach(b => {
                b.classList.remove('neu-inset-sm', 'text-emerald-600', 'dark:text-emerald-400');
                b.classList.add('neu-btn-round', 'text-slate-600', 'dark:text-zinc-400');
            });
            btn.classList.add('neu-inset-sm', 'text-emerald-600', 'dark:text-emerald-400');
            btn.classList.remove('neu-btn-round', 'text-slate-600', 'dark:text-zinc-400');

            const cards = document.querySelectorAll('.app-card');
            cards.forEach(card => {
                if (cat === 'all') {
                    card.style.display = 'block';
                } else {
                    const cardCat = card.getAttribute('data-category') || '';
                    card.style.display = cardCat === cat ? 'block' : 'none';
                }
            });
        }

        async function handleShare() {
            const shareData = {
                title: '<?= addslashes($name) ?> — Official Portal',
                text: '<?= addslashes($title) ?> | Official Neumorphic Bio Link',
                url: window.location.href
            };

            if (navigator.share) {
                try {
                    await navigator.share(shareData);
                    return;
                } catch (err) {}
            }

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(window.location.href).then(() => {
                    showToast('Tautan berhasil disalin ke clipboard!');
                });
            } else {
                const input = document.createElement('input');
                input.value = window.location.href;
                document.body.appendChild(input);
                input.select();
                document.execCommand('copy');
                document.body.removeChild(input);
                showToast('Tautan berhasil disalin!');
            }
        }

        function showToast(msg) {
            const toast = document.getElementById('toast');
            const toastMsg = document.getElementById('toastMsg');
            toastMsg.innerText = msg;
            toast.classList.remove('opacity-0', 'pointer-events-none');
            toast.classList.add('opacity-100');

            setTimeout(() => {
                toast.classList.add('opacity-0', 'pointer-events-none');
                toast.classList.remove('opacity-100');
            }, 2500);
        }
    </script>
</body>
</html>
