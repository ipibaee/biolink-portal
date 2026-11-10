<?php
require_once __DIR__ . '/db.php';

$settings = Storage::getSettings();
$links = Storage::getLinks(true);
$apps = Storage::getApps(true);

$name = htmlspecialchars($settings['name'] ?? 'Rifki Figianto, S.Kom.');
$title = htmlspecialchars($settings['title'] ?? 'Full-Stack Developer');
$bio = htmlspecialchars($settings['bio'] ?? '');
$location = htmlspecialchars($settings['location'] ?? 'Indonesia');
$statusText = htmlspecialchars($settings['status_text'] ?? 'Available for Projects');
$avatarUrl = htmlspecialchars($settings['avatar_url'] ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&auto=format&fit=crop&q=80');
$domainBadge = htmlspecialchars($settings['domain_badge'] ?? 'PORTAL RESMI');
$appCount = count($apps);
?>
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $name ?> — Official Portal</title>
    <meta name="description" content="<?= $name ?> | <?= $title ?> — Portal resmi, portofolio sistem, dan kontak langsung.">

    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            500: '#10b981',
                            600: '#059669',
                        },
                        graybg: {
                            base: '#111116',
                            card: '#18181f',
                            cardhover: '#22222b',
                            border: '#2e2e38'
                        }
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        /* Ambient subtle mesh background */
        .ambient-mesh {
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(16, 185, 129, 0.06) 0px, transparent 50%),
                radial-gradient(at 50% 100%, rgba(168, 85, 247, 0.05) 0px, transparent 50%);
        }
        .light-mesh {
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.04) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(16, 185, 129, 0.04) 0px, transparent 50%);
        }
    </style>
</head>
<body class="bg-slate-100 dark:bg-graybg-base text-slate-800 dark:text-zinc-100 font-sans transition-colors duration-300 min-h-screen flex flex-col justify-between py-6 px-4 sm:px-6 relative selection:bg-emerald-500/20 selection:text-emerald-400">

    <!-- Background Ambient Lights -->
    <div class="fixed inset-0 pointer-events-none z-0 ambient-mesh dark:block hidden"></div>
    <div class="fixed inset-0 pointer-events-none z-0 light-mesh dark:hidden block"></div>

    <!-- MAIN CONTAINER (Centered App Layout for Desktop, Full Width for Mobile) -->
    <div class="w-full max-w-lg mx-auto relative z-10 flex flex-col items-center flex-grow">
        
        <!-- TOP NAVIGATION BAR -->
        <header class="w-full flex items-center justify-between mb-8 pb-3 border-b border-slate-200/80 dark:border-zinc-800/80">
            <!-- Domain / Official Badge -->
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-mono font-medium tracking-wider uppercase bg-white/80 dark:bg-zinc-800/80 border border-slate-200 dark:border-zinc-700/60 shadow-sm backdrop-blur-md">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-slate-600 dark:text-zinc-300"><?= $domainBadge ?></span>
            </div>

            <!-- Action Controls (Theme Toggle & Share) -->
            <div class="flex items-center gap-2">
                <!-- Theme Toggle -->
                <button id="themeToggleBtn" aria-label="Toggle Theme" class="p-2 rounded-full bg-white/80 dark:bg-zinc-800/80 border border-slate-200 dark:border-zinc-700/60 text-slate-600 dark:text-zinc-300 hover:text-emerald-500 dark:hover:text-emerald-400 hover:border-emerald-500/30 transition-all shadow-sm">
                    <i data-lucide="sun" class="w-4 h-4 hidden dark:block"></i>
                    <i data-lucide="moon" class="w-4 h-4 block dark:hidden"></i>
                </button>

                <!-- Share Button -->
                <button onclick="handleShare()" aria-label="Share Link" class="p-2 rounded-full bg-white/80 dark:bg-zinc-800/80 border border-slate-200 dark:border-zinc-700/60 text-slate-600 dark:text-zinc-300 hover:text-emerald-500 dark:hover:text-emerald-400 hover:border-emerald-500/30 transition-all shadow-sm">
                    <i data-lucide="share-2" class="w-4 h-4"></i>
                </button>
            </div>
        </header>

        <!-- PROFILE HEADER SECTION -->
        <section class="w-full flex flex-col items-center text-center mb-8">
            <!-- Avatar with Glow Ring -->
            <div class="relative mb-5 group">
                <div class="w-28 h-28 rounded-full p-1 bg-gradient-to-tr from-emerald-500/40 via-slate-400/20 to-indigo-500/30 dark:from-emerald-500/30 dark:via-zinc-700/50 dark:to-teal-500/30 shadow-xl">
                    <img src="<?= $avatarUrl ?>" alt="<?= $name ?>" class="w-full h-full object-cover rounded-full bg-zinc-800 border-2 border-white dark:border-zinc-900 shadow-inner group-hover:scale-[1.02] transition-transform duration-300">
                </div>
                <!-- Status Dot -->
                <div class="absolute bottom-1 right-2 w-5 h-5 bg-white dark:bg-zinc-900 rounded-full flex items-center justify-center shadow-md" title="<?= $statusText ?>">
                    <span class="w-3 h-3 bg-emerald-500 rounded-full ring-2 ring-emerald-400/40"></span>
                </div>
            </div>

            <!-- Name & Title -->
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white mb-1.5"><?= $name ?></h1>
            <p class="text-sm sm:text-base font-medium text-emerald-600 dark:text-emerald-400 mb-3"><?= $title ?></p>

            <!-- Location Badge -->
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs text-slate-500 dark:text-zinc-400 bg-slate-200/60 dark:bg-zinc-800/50 mb-3.5 border border-slate-200/50 dark:border-zinc-700/40">
                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400 dark:text-zinc-500"></i>
                <span><?= $location ?></span>
            </div>

            <!-- Short Bio Description -->
            <?php if (!empty($bio)): ?>
                <p class="text-sm text-slate-600 dark:text-zinc-400 max-w-md leading-relaxed px-2 font-normal">
                    <?= $bio ?>
                </p>
            <?php endif; ?>
        </section>

        <!-- LINKS & INTERACTIVE CARDS -->
        <main class="w-full flex flex-col gap-3.5 mb-10">

            <!-- SPECIAL FEATURED CARD: SISTEM & APLIKASI (Opens Modal) -->
            <button onclick="openAppsModal()" class="w-full group text-left relative overflow-hidden rounded-2xl p-4 sm:p-4.5 bg-gradient-to-r from-emerald-500/10 via-white to-white dark:from-emerald-950/30 dark:via-graybg-card dark:to-graybg-card border-2 border-emerald-500/30 dark:border-emerald-500/30 hover:border-emerald-500/60 dark:hover:border-emerald-400/60 transition-all duration-300 shadow-md hover:shadow-xl hover:-translate-y-0.5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/15 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                            <i data-lucide="layout-grid" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-semibold text-slate-900 dark:text-zinc-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                    Aplikasi & Sistem Saya
                                </h3>
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-mono font-semibold bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">
                                    <?= $appCount ?> Sistem
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5 line-clamp-1">
                                Klik untuk melihat katalog aplikasi & website live
                            </p>
                        </div>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-zinc-800 flex items-center justify-center text-slate-400 dark:text-zinc-400 group-hover:text-emerald-500 group-hover:bg-emerald-50 dark:group-hover:bg-zinc-700 transition-colors shrink-0 ml-2">
                        <i data-lucide="chevron-right" class="w-4 h-4 group-hover:translate-x-0.5 transition-transform"></i>
                    </div>
                </div>
            </button>

            <!-- DYNAMIC LINKS FROM DATABASE -->
            <?php foreach ($links as $link): 
                $icon = htmlspecialchars($link['icon'] ?? 'link');
                $isMail = str_starts_with($link['url'], 'mailto:');
                $target = $isMail ? '' : 'target="_blank" rel="noopener noreferrer"';
            ?>
                <a href="<?= htmlspecialchars($link['url']) ?>" <?= $target ?> class="w-full group block rounded-2xl p-4 bg-white dark:bg-graybg-card border border-slate-200 dark:border-graybg-border hover:border-slate-300 dark:hover:border-zinc-700 hover:bg-slate-50 dark:hover:bg-graybg-cardhover transition-all duration-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3.5 overflow-hidden">
                            <div class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-300 flex items-center justify-center shrink-0 group-hover:text-emerald-500 group-hover:bg-emerald-50 dark:group-hover:bg-zinc-700/80 transition-colors">
                                <i data-lucide="<?= $icon ?>" class="w-5 h-5"></i>
                            </div>
                            <div class="overflow-hidden">
                                <h3 class="text-sm font-semibold text-slate-800 dark:text-zinc-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors truncate">
                                    <?= htmlspecialchars($link['title']) ?>
                                </h3>
                                <?php if (!empty($link['description'])): ?>
                                    <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5 truncate">
                                        <?= htmlspecialchars($link['description']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="text-slate-400 dark:text-zinc-500 group-hover:text-emerald-500 transition-colors shrink-0 ml-2">
                            <i data-lucide="arrow-up-right" class="w-4 h-4 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform"></i>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>

        </main>

        <!-- FOOTER -->
        <footer class="w-full text-center py-4 border-t border-slate-200/60 dark:border-zinc-800/60 mt-auto">
            <p class="text-xs text-slate-400 dark:text-zinc-500">
                &copy; <?= date('Y') ?> <span class="font-medium text-slate-600 dark:text-zinc-400"><?= $name ?></span>. All rights reserved.
            </p>
        </footer>

    </div>

    <!-- MODAL: DAFTAR APLIKASI & SISTEM -->
    <div id="appsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300">
        <div id="appsModalCard" class="w-full max-w-lg max-h-[85vh] flex flex-col bg-white dark:bg-[#16161d] border border-slate-200 dark:border-zinc-800 rounded-3xl shadow-2xl overflow-hidden scale-95 transition-transform duration-300">
            
            <!-- Modal Header -->
            <div class="p-5 border-b border-slate-200 dark:border-zinc-800/80 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/15 text-emerald-500 flex items-center justify-center">
                        <i data-lucide="layers" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Katalog Aplikasi</h2>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">Pilih aplikasi untuk langsung membuka sistem</p>
                    </div>
                </div>
                <button onclick="closeAppsModal()" aria-label="Tutup Modal" class="p-2 rounded-full text-slate-400 hover:text-slate-600 dark:hover:text-white bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-700 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Search Filter Bar -->
            <div class="p-4 border-b border-slate-100 dark:border-zinc-800/60 shrink-0 bg-slate-50/50 dark:bg-zinc-900/40">
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3 top-3 text-slate-400 dark:text-zinc-500"></i>
                    <input type="text" id="appSearchInput" oninput="filterApps()" placeholder="Cari aplikasi atau kategori..." class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-white dark:bg-zinc-800/80 border border-slate-200 dark:border-zinc-700 text-slate-800 dark:text-zinc-200 placeholder-slate-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                </div>
            </div>

            <!-- App List Scrollable Area -->
            <div id="appsListContainer" class="p-4 overflow-y-auto space-y-3 flex-grow">
                <?php if (empty($apps)): ?>
                    <div class="text-center py-8 text-slate-400 dark:text-zinc-500 text-sm">
                        Belum ada aplikasi yang ditambahkan.
                    </div>
                <?php else: ?>
                    <?php foreach ($apps as $app): 
                        $badge = htmlspecialchars($app['badge'] ?? 'Live');
                        $badgeColor = strtolower($badge) === 'live' 
                            ? 'bg-emerald-500/10 text-emerald-500 border-emerald-500/30' 
                            : 'bg-amber-500/10 text-amber-500 border-amber-500/30';
                        $appIcon = htmlspecialchars($app['icon'] ?? 'sparkles');
                    ?>
                        <div class="app-item group p-3.5 rounded-2xl bg-slate-50 dark:bg-zinc-900/60 border border-slate-200/80 dark:border-zinc-800 hover:border-emerald-500/40 dark:hover:border-emerald-500/40 transition-all" data-title="<?= strtolower(htmlspecialchars($app['name'] . ' ' . $app['category'] . ' ' . $app['description'])) ?>">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700/60 text-slate-700 dark:text-zinc-300 flex items-center justify-center shrink-0 mt-0.5">
                                        <i data-lucide="<?= $appIcon ?>" class="w-5 h-5 text-emerald-500"></i>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="text-sm font-semibold text-slate-900 dark:text-white"><?= htmlspecialchars($app['name']) ?></h4>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-medium border <?= $badgeColor ?>">
                                                <?= $badge ?>
                                            </span>
                                            <?php if (!empty($app['category'])): ?>
                                                <span class="text-[11px] font-medium text-slate-400 dark:text-zinc-400">
                                                    • <?= htmlspecialchars($app['category']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-xs text-slate-600 dark:text-zinc-400 mt-1 leading-relaxed">
                                            <?= htmlspecialchars($app['description']) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-slate-200/60 dark:border-zinc-800 flex justify-end">
                                <a href="<?= htmlspecialchars($app['url']) ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-emerald-600 hover:bg-emerald-500 text-white shadow-sm transition-all hover:gap-2">
                                    <span>Buka Sistem</span>
                                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div id="toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 flex items-center gap-2 px-4 py-2.5 rounded-full bg-slate-900/90 dark:bg-zinc-800/95 text-white text-xs font-medium shadow-2xl border border-white/10 opacity-0 pointer-events-none transition-all duration-300">
        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i>
        <span id="toastMsg">Tautan berhasil disalin!</span>
    </div>

    <!-- SCRIPTS -->
    <script>
        // Init Lucide Icons
        lucide.createIcons();

        // Dark / Light Mode Handler
        const themeToggleBtn = document.getElementById('themeToggleBtn');
        const htmlElement = document.documentElement;

        // Cek saved theme atau preferensi sistem
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            htmlElement.classList.toggle('dark', savedTheme === 'dark');
        } else {
            // Default dark mode dominan abu-abu
            htmlElement.classList.add('dark');
        }

        themeToggleBtn.addEventListener('click', () => {
            const isDark = htmlElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            lucide.createIcons();
        });

        // Modal Handler
        const appsModal = document.getElementById('appsModal');
        const appsModalCard = document.getElementById('appsModalCard');

        function openAppsModal() {
            appsModal.classList.remove('opacity-0', 'pointer-events-none');
            appsModalCard.classList.remove('scale-95');
            appsModalCard.classList.add('scale-100');
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
                const searchInput = document.getElementById('appSearchInput');
                if (searchInput) searchInput.focus();
            }, 100);
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

        // Search Filter Apps
        function filterApps() {
            const query = document.getElementById('appSearchInput').value.toLowerCase().trim();
            const items = document.querySelectorAll('.app-item');
            items.forEach(item => {
                const text = item.getAttribute('data-title') || '';
                item.style.display = text.includes(query) ? 'block' : 'none';
            });
        }

        // Web Share & Clipboard Handler
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

            // Fallback: Copy to clipboard
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
            toast.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-2');
            toast.classList.add('opacity-100', 'translate-y-0');

            setTimeout(() => {
                toast.classList.add('opacity-0', 'pointer-events-none');
                toast.classList.remove('opacity-100');
            }, 2500);
        }
    </script>
</body>
</html>
