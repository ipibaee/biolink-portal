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

// Ambil inisial huruf dari masing-masing aplikasi untuk badge (maksimal 5)
$appInitials = [];
foreach ($apps as $app) {
    if (!empty($app['name'])) {
        $appInitials[] = strtoupper(mb_substr(trim($app['name']), 0, 1));
    }
    if (count($appInitials) >= 5) break;
}

// Ambil kategori unik untuk filter tabs di modal
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
    <meta name="description" content="<?= $name ?> | <?= $title ?> — Portal resmi, portofolio sistem, dan kontak langsung.">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        darkbg: '#0c0d11',
                        darkcard: '#14151b',
                        darkcardhover: '#1b1c24',
                        darkborder: '#21222c',
                    }
                }
            }
        }
    </script>
    <style>
        /* Ambient subtle radial glow */
        .ambient-glow {
            position: fixed;
            inset: 0;
            pointer-events: none;
            background: 
                radial-gradient(ellipse at 50% -10%, rgba(16, 185, 129, 0.08) 0%, transparent 60%),
                radial-gradient(ellipse at 50% 110%, rgba(99, 102, 241, 0.05) 0%, transparent 60%);
            z-index: 0;
        }
    </style>
</head>
<body class="bg-slate-100 dark:bg-darkbg text-slate-800 dark:text-zinc-100 font-sans transition-colors duration-300 min-h-screen flex flex-col justify-between py-6 px-4 sm:px-6 relative selection:bg-emerald-500/20 selection:text-emerald-400">

    <div class="ambient-glow"></div>

    <!-- MAIN CONTAINER (Centered App Layout for Desktop, Full Width for Mobile) -->
    <div class="w-full max-w-[460px] mx-auto relative z-10 flex flex-col items-center flex-grow">
        
        <!-- TOP NAVIGATION BAR -->
        <header class="w-full flex items-center justify-between mb-7">
            <!-- Status Badge (Available for Projects) -->
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium bg-white/80 dark:bg-darkcard border border-slate-200 dark:border-darkborder shadow-sm backdrop-blur-md">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-slate-700 dark:text-zinc-300 text-[11px] font-semibold"><?= $statusText ?></span>
            </div>

            <!-- Action Controls (Share & Theme Toggle) -->
            <div class="flex items-center gap-2">
                <button onclick="handleShare()" aria-label="Share Link" class="w-8 h-8 rounded-full bg-white/80 dark:bg-darkcard border border-slate-200 dark:border-darkborder text-slate-500 dark:text-zinc-400 hover:text-emerald-500 dark:hover:text-emerald-400 transition-all flex items-center justify-center shadow-sm">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" x2="15.42" y1="13.51" y2="17.49"/><line x1="15.41" x2="8.59" y1="6.51" y2="10.49"/></svg>
                </button>

                <button id="themeToggleBtn" aria-label="Toggle Theme" class="w-8 h-8 rounded-full bg-white/80 dark:bg-darkcard border border-slate-200 dark:border-darkborder text-slate-500 dark:text-zinc-400 hover:text-emerald-500 dark:hover:text-emerald-400 transition-all flex items-center justify-center shadow-sm">
                    <svg id="sunIcon" class="w-3.5 h-3.5 hidden dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
                    <svg id="moonIcon" class="w-3.5 h-3.5 block dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                </button>
            </div>
        </header>

        <!-- PROFILE HEADER SECTION -->
        <section class="w-full flex flex-col items-center text-center mb-7">
            <!-- Avatar with Green Checkmark Badge -->
            <div class="relative mb-4">
                <div class="w-24 h-24 rounded-full p-1 bg-gradient-to-b from-slate-200 to-slate-300 dark:from-zinc-700 dark:to-zinc-800 shadow-xl">
                    <img src="<?= $avatarUrl ?>" alt="<?= $name ?>" class="w-full h-full object-cover rounded-full bg-zinc-800">
                </div>
                <!-- Checkmark verified badge -->
                <div class="absolute bottom-1 right-1 w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center shadow-md border-2 border-white dark:border-darkbg">
                    <svg class="w-3.5 h-3.5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
            </div>

            <!-- Name, Title, & Location -->
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-white mb-1"><?= $name ?></h1>
            <p class="text-xs sm:text-sm font-medium text-slate-500 dark:text-zinc-400 mb-2"><?= $title ?></p>

            <div class="inline-flex items-center gap-1 text-[11px] text-slate-400 dark:text-zinc-500 mb-3">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                <span><?= $location ?></span>
            </div>

            <!-- Bio -->
            <?php if (!empty($bio)): ?>
                <p class="text-xs text-slate-600 dark:text-zinc-400 max-w-sm leading-relaxed px-2 font-normal">
                    <?= $bio ?>
                </p>
            <?php endif; ?>
        </section>

        <!-- LINKS & INTERACTIVE CARDS -->
        <main class="w-full flex flex-col gap-3 mb-8">

            <!-- SPECIAL FEATURED CARD: SISTEM & APLIKASI (Opens Modal) -->
            <button onclick="openAppsModal()" class="w-full group text-left relative overflow-hidden rounded-2xl p-4 bg-white dark:bg-darkcard border border-emerald-500/40 dark:border-emerald-500/30 hover:border-emerald-500/70 dark:hover:border-emerald-500/60 transition-all duration-300 shadow-sm hover:shadow-md">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/15 dark:bg-emerald-500/20 text-emerald-500 flex items-center justify-center shrink-0">
                            <?= render_icon('layout-grid', 'w-5 h-5 text-emerald-500') ?>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-emerald-500 transition-colors">
                                    Aplikasi & Sistem Saya
                                </h3>
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-0.5">
                                Kumpulan web app & sistem yang sudah live
                            </p>
                        </div>
                    </div>
                    <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-zinc-800/80 flex items-center justify-center text-slate-400 dark:text-zinc-400 group-hover:text-emerald-500 group-hover:translate-x-0.5 transition-all shrink-0">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </div>
                </div>

                <!-- Bottom Row inside card: Initials Pills + Live Count Badge -->
                <div class="flex items-center justify-between pt-2.5 border-t border-slate-100 dark:border-darkborder/70 text-[11px]">
                    <div class="flex items-center gap-1.5">
                        <?php foreach ($appInitials as $initial): ?>
                            <span class="w-5 h-5 rounded-md bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400 text-[10px] font-bold flex items-center justify-center border border-slate-200 dark:border-zinc-700/60">
                                <?= $initial ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                    <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-500 border border-emerald-500/30">
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
                <a href="<?= htmlspecialchars($link['url']) ?>" <?= $target ?> class="w-full group block rounded-2xl p-3.5 bg-white dark:bg-darkcard border border-slate-200 dark:border-darkborder hover:border-slate-300 dark:hover:border-zinc-700 hover:bg-slate-50/80 dark:hover:bg-darkcardhover transition-all duration-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-zinc-800/80 text-slate-600 dark:text-zinc-300 flex items-center justify-center shrink-0 group-hover:text-emerald-500 group-hover:bg-emerald-50 dark:group-hover:bg-zinc-700/60 transition-colors">
                                <?= render_icon($icon, 'w-5 h-5') ?>
                            </div>
                            <div class="overflow-hidden">
                                <h3 class="text-xs font-semibold text-slate-800 dark:text-zinc-200 group-hover:text-emerald-500 transition-colors truncate">
                                    <?= htmlspecialchars($link['title']) ?>
                                </h3>
                                <?php if (!empty($link['description'])): ?>
                                    <p class="text-[11px] text-slate-400 dark:text-zinc-500 mt-0.5 truncate font-normal">
                                        <?= htmlspecialchars($link['description']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="text-slate-400 dark:text-zinc-500 group-hover:text-emerald-500 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all shrink-0 ml-2">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="7" x2="17" y1="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>

        </main>

        <!-- CIRCULAR SOCIAL BUTTONS ROW (Bulat-bulat di bagian bawah) -->
        <?php if (!empty($socials)): ?>
            <div class="w-full flex items-center justify-center gap-3 mb-7">
                <?php foreach ($socials as $soc): ?>
                    <a href="<?= htmlspecialchars($soc['url']) ?>" target="_blank" rel="noopener noreferrer" title="<?= htmlspecialchars($soc['title']) ?>" class="w-10 h-10 rounded-full bg-white dark:bg-darkcard border border-slate-200 dark:border-darkborder text-slate-600 dark:text-zinc-400 hover:text-emerald-500 dark:hover:text-emerald-400 hover:border-emerald-500/40 hover:scale-110 transition-all duration-200 flex items-center justify-center shadow-sm">
                        <?= render_icon($soc['icon'], 'w-4 h-4') ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- FOOTER -->
        <footer class="w-full text-center py-4 border-t border-slate-200/60 dark:border-darkborder/60 mt-auto">
            <p class="text-[11px] text-slate-400 dark:text-zinc-500">
                &copy; <?= date('Y') ?> <span class="font-medium text-slate-600 dark:text-zinc-400"><?= $name ?></span>. Dirancang & dibangun sendiri.
            </p>
        </footer>

    </div>

    <!-- MODAL: DAFTAR APLIKASI & SISTEM -->
    <div id="appsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 dark:bg-black/80 backdrop-blur-md opacity-0 pointer-events-none transition-opacity duration-200">
        <div id="appsModalCard" class="w-full max-w-[460px] max-h-[85vh] flex flex-col bg-white dark:bg-[#161720] border border-slate-200 dark:border-darkborder rounded-3xl shadow-2xl overflow-hidden scale-95 transition-transform duration-200">
            
            <!-- Modal Header -->
            <div class="p-5 border-b border-slate-100 dark:border-darkborder flex items-start justify-between shrink-0">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Aplikasi & Sistem Saya</h2>
                    <p class="text-[11px] text-slate-400 dark:text-zinc-400 mt-0.5"><?= $appCount ?> web app live yang saya bangun & kelola sendiri.</p>
                </div>
                <button onclick="closeAppsModal()" aria-label="Tutup Modal" class="p-1.5 rounded-full text-slate-400 hover:text-slate-600 dark:hover:text-white bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-700 transition-colors">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
                </button>
            </div>

            <!-- Filter Tabs Pills -->
            <div class="px-5 py-3 border-b border-slate-100 dark:border-darkborder/60 shrink-0 flex items-center gap-2 overflow-x-auto scrollbar-none">
                <button onclick="filterCategory('all', this)" class="cat-pill active px-3 py-1 rounded-full text-[11px] font-semibold bg-emerald-500/15 text-emerald-500 border border-emerald-500/30 transition-all shrink-0">
                    Semua · <?= $appCount ?>
                </button>
                <?php foreach ($categories as $cat): ?>
                    <button onclick="filterCategory('<?= strtolower(htmlspecialchars($cat)) ?>', this)" class="cat-pill px-3 py-1 rounded-full text-[11px] font-medium bg-slate-100 dark:bg-zinc-800/80 text-slate-600 dark:text-zinc-400 border border-transparent hover:border-slate-300 dark:hover:border-zinc-700 transition-all shrink-0">
                        <?= htmlspecialchars($cat) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- App List Scrollable Area -->
            <div id="appsListContainer" class="p-4 overflow-y-auto space-y-2.5 flex-grow">
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
                        <a href="<?= htmlspecialchars($app['url']) ?>" target="_blank" rel="noopener noreferrer" class="app-card group block p-3.5 rounded-2xl bg-slate-50/80 dark:bg-darkcard border border-slate-200/80 dark:border-darkborder hover:border-emerald-500/40 dark:hover:border-emerald-500/40 hover:bg-white dark:hover:bg-darkcardhover transition-all" data-category="<?= strtolower($cat) ?>">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 overflow-hidden">
                                    <div class="w-9 h-9 rounded-xl bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700/60 text-emerald-500 flex items-center justify-center shrink-0">
                                        <?= render_icon($appIcon, 'w-4 h-4 text-emerald-500') ?>
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="flex items-center gap-2">
                                            <h4 class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-emerald-500 transition-colors"><?= htmlspecialchars($app['name']) ?></h4>
                                            <?php if (!empty($cat)): ?>
                                                <span class="px-2 py-0.5 rounded text-[10px] font-mono text-slate-400 dark:text-zinc-400 bg-slate-200/50 dark:bg-zinc-800">
                                                    <?= $cat ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-1 truncate">
                                            <?= htmlspecialchars($app['description']) ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="text-slate-400 dark:text-zinc-500 group-hover:text-emerald-500 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all shrink-0">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="7" x2="17" y1="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Modal Footer -->
            <div class="px-5 py-3 border-t border-slate-100 dark:border-darkborder/60 flex items-center justify-between text-[10px] text-slate-400 dark:text-zinc-500 shrink-0">
                <span>Terakhir diperbarui <?= date('F Y') ?></span>
                <span class="font-mono">↵ ESC untuk menutup</span>
            </div>

        </div>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div id="toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 flex items-center gap-2 px-4 py-2.5 rounded-full bg-slate-900/95 dark:bg-zinc-800/95 text-white text-xs font-medium shadow-2xl border border-white/10 opacity-0 pointer-events-none transition-all duration-300">
        <svg class="w-4 h-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
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

        // Filter modal apps by category
        function filterCategory(cat, btn) {
            document.querySelectorAll('.cat-pill').forEach(b => {
                b.classList.remove('bg-emerald-500/15', 'text-emerald-500', 'border-emerald-500/30', 'font-semibold');
                b.classList.add('bg-slate-100', 'dark:bg-zinc-800/80', 'text-slate-600', 'dark:text-zinc-400');
            });
            btn.classList.add('bg-emerald-500/15', 'text-emerald-500', 'border-emerald-500/30', 'font-semibold');
            btn.classList.remove('bg-slate-100', 'dark:bg-zinc-800/80', 'text-slate-600', 'dark:text-zinc-400');

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
                text: '<?= addslashes($title) ?> | Official Bio Link & Systems',
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
