<?php
require_once __DIR__ . '/auth.php';
checkAdminAuth();
require_once __DIR__ . '/../db.php';

$message = '';
$msgType = 'success';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // 1. UPDATE PROFILE
    if ($action === 'update_profile') {
        $settingsToUpdate = [
            'name' => trim($_POST['name'] ?? ''),
            'title' => trim($_POST['title'] ?? ''),
            'bio' => trim($_POST['bio'] ?? ''),
            'location' => trim($_POST['location'] ?? ''),
            'status_text' => trim($_POST['status_text'] ?? ''),
            'avatar_url' => trim($_POST['avatar_url'] ?? ''),
            'domain_badge' => trim($_POST['domain_badge'] ?? '')
        ];
        Storage::updateSettings($settingsToUpdate);
        $message = 'Profil berhasil diperbarui!';
    }

    // 2. SAVE LINK (ADD / EDIT)
    if ($action === 'save_link') {
        $id = !empty($_POST['link_id']) ? (int)$_POST['link_id'] : null;
        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'url' => trim($_POST['url'] ?? ''),
            'icon' => trim($_POST['icon'] ?? 'link'),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        Storage::saveLink($data, $id);
        $message = $id ? 'Link berhasil diperbarui!' : 'Link baru berhasil ditambahkan!';
    }

    // 3. DELETE LINK
    if ($action === 'delete_link') {
        $id = (int)($_POST['link_id'] ?? 0);
        if ($id) {
            Storage::deleteLink($id);
            $message = 'Link berhasil dihapus!';
        }
    }

    // 4. SAVE APP (ADD / EDIT)
    if ($action === 'save_app') {
        $id = !empty($_POST['app_id']) ? (int)$_POST['app_id'] : null;
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'category' => trim($_POST['category'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'url' => trim($_POST['url'] ?? ''),
            'badge' => trim($_POST['badge'] ?? 'Live'),
            'icon' => trim($_POST['icon'] ?? 'sparkles'),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        Storage::saveApp($data, $id);
        $message = $id ? 'Aplikasi berhasil diperbarui!' : 'Aplikasi baru berhasil ditambahkan!';
    }

    // 5. DELETE APP
    if ($action === 'delete_app') {
        $id = (int)($_POST['app_id'] ?? 0);
        if ($id) {
            Storage::deleteApp($id);
            $message = 'Aplikasi berhasil dihapus!';
        }
    }

    // 6. CHANGE PASSWORD
    if ($action === 'change_password') {
        $oldPass = $_POST['old_password'] ?? '';
        $newPass = $_POST['new_password'] ?? '';
        $confirmPass = $_POST['confirm_password'] ?? '';

        $currSettings = Storage::getSettings();
        if (!password_verify($oldPass, $currSettings['admin_password'] ?? '')) {
            $message = 'Password lama salah!';
            $msgType = 'error';
        } elseif (strlen($newPass) < 6) {
            $message = 'Password baru minimal 6 karakter!';
            $msgType = 'error';
        } elseif ($newPass !== $confirmPass) {
            $message = 'Konfirmasi password baru tidak cocok!';
            $msgType = 'error';
        } else {
            Storage::updateSettings(['admin_password' => password_hash($newPass, PASSWORD_BCRYPT)]);
            $message = 'Password admin berhasil diubah!';
        }
    }
}

$settings = Storage::getSettings();
$links = Storage::getLinks(false);
$apps = Storage::getApps(false);
?>
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — Portal Manager</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { 
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace']
                    },
                    colors: {
                        graybg: { base: '#0f0f13', card: '#17171f', cardalt: '#1e1e28', border: '#2a2a38' }
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-slate-100 dark:bg-graybg-base text-slate-800 dark:text-zinc-100 font-sans min-h-screen py-8 px-4 sm:px-6 selection:bg-emerald-500/20">

    <div class="max-w-4xl mx-auto">
        <!-- TOP NAVBAR -->
        <header class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 mb-6 border-b border-slate-200 dark:border-graybg-border gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/15 text-emerald-500 flex items-center justify-center font-bold">
                    <i data-lucide="command" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>Portal Dashboard</span>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-emerald-500/15 text-emerald-500 border border-emerald-500/30">Admin</span>
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-zinc-400">Kelola profil, tautan, & katalog aplikasi Anda</p>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="../index.php" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-white dark:bg-graybg-card border border-slate-200 dark:border-graybg-border hover:border-emerald-500 text-slate-700 dark:text-zinc-200 shadow-sm transition-all">
                    <span>Pratinjau Web</span>
                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                </a>
                <a href="logout.php" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-rose-500/10 hover:bg-rose-500/20 text-rose-500 border border-rose-500/20 transition-all">
                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                    <span>Keluar</span>
                </a>
            </div>
        </header>

        <!-- ALERT MESSAGE -->
        <?php if (!empty($message)): ?>
            <div class="mb-6 p-4 rounded-2xl flex items-center gap-3 text-xs font-medium border <?= $msgType === 'success' ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400' : 'bg-rose-500/10 border-rose-500/30 text-rose-400' ?>">
                <i data-lucide="<?= $msgType === 'success' ? 'check-circle-2' : 'alert-circle' ?>" class="w-4 h-4 shrink-0"></i>
                <span><?= htmlspecialchars($message) ?></span>
            </div>
        <?php endif; ?>

        <!-- NAVIGATION TABS -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 mb-6 scrollbar-none border-b border-slate-200 dark:border-graybg-border">
            <button onclick="switchTab('profile')" id="tab-btn-profile" class="tab-btn active px-4 py-2.5 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all">
                <i data-lucide="user" class="w-4 h-4"></i>
                <span>Profil & Identitas</span>
            </button>
            <button onclick="switchTab('links')" id="tab-btn-links" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all">
                <i data-lucide="link-2" class="w-4 h-4"></i>
                <span>Link Utama (<?= count($links) ?>)</span>
            </button>
            <button onclick="switchTab('apps')" id="tab-btn-apps" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all">
                <i data-lucide="layers" class="w-4 h-4"></i>
                <span>Katalog Aplikasi (<?= count($apps) ?>)</span>
            </button>
            <button onclick="switchTab('security')" id="tab-btn-security" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all">
                <i data-lucide="key" class="w-4 h-4"></i>
                <span>Keamanan</span>
            </button>
        </div>

        <!-- ==================== TAB 1: PROFIL & IDENTITAS ==================== -->
        <div id="tab-profile" class="tab-content">
            <form method="POST" action="" class="bg-white dark:bg-graybg-card border border-slate-200 dark:border-graybg-border rounded-3xl p-6 sm:p-8 space-y-5 shadow-sm">
                <input type="hidden" name="action" value="update_profile">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-zinc-300 mb-1.5">Nama Lengkap & Gelar</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($settings['name'] ?? '') ?>" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-graybg-cardalt border border-slate-200 dark:border-graybg-border text-xs focus:ring-2 focus:ring-emerald-500/40 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-zinc-300 mb-1.5">Profesi / Role Singkat</label>
                        <input type="text" name="title" value="<?= htmlspecialchars($settings['title'] ?? '') ?>" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-graybg-cardalt border border-slate-200 dark:border-graybg-border text-xs focus:ring-2 focus:ring-emerald-500/40 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-zinc-300 mb-1.5">Lokasi</label>
                        <input type="text" name="location" value="<?= htmlspecialchars($settings['location'] ?? '') ?>" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-graybg-cardalt border border-slate-200 dark:border-graybg-border text-xs focus:ring-2 focus:ring-emerald-500/40 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-zinc-300 mb-1.5">Status Indikator</label>
                        <input type="text" name="status_text" value="<?= htmlspecialchars($settings['status_text'] ?? '') ?>" placeholder="Available for Projects" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-graybg-cardalt border border-slate-200 dark:border-graybg-border text-xs focus:ring-2 focus:ring-emerald-500/40 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-zinc-300 mb-1.5">Badge Header</label>
                        <input type="text" name="domain_badge" value="<?= htmlspecialchars($settings['domain_badge'] ?? '') ?>" placeholder="PORTAL RESMI" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-graybg-cardalt border border-slate-200 dark:border-graybg-border text-xs focus:ring-2 focus:ring-emerald-500/40 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-zinc-300 mb-1.5">URL Foto Avatar</label>
                    <input type="text" name="avatar_url" value="<?= htmlspecialchars($settings['avatar_url'] ?? '') ?>" placeholder="https://..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-graybg-cardalt border border-slate-200 dark:border-graybg-border text-xs focus:ring-2 focus:ring-emerald-500/40 focus:outline-none">
                    <p class="text-[11px] text-slate-400 dark:text-zinc-500 mt-1">Bisa gunakan link gambar langsung (CDN/Imgur/Unsplash/Github avatar).</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-zinc-300 mb-1.5">Bio Singkat</label>
                    <textarea name="bio" rows="3" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-graybg-cardalt border border-slate-200 dark:border-graybg-border text-xs focus:ring-2 focus:ring-emerald-500/40 focus:outline-none"><?= htmlspecialchars($settings['bio'] ?? '') ?></textarea>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-md transition-all">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Simpan Perubahan Profil</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- ==================== TAB 2: KELOLA LINK UTAMA ==================== -->
        <div id="tab-links" class="tab-content hidden space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Daftar Link Utama</h2>
                    <p class="text-xs text-slate-400 dark:text-zinc-500">Tautan sosial media dan kontak di halaman utama</p>
                </div>
                <button onclick="openLinkModal()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-sm transition-all">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Tambah Link</span>
                </button>
            </div>

            <div class="bg-white dark:bg-graybg-card border border-slate-200 dark:border-graybg-border rounded-3xl overflow-hidden divide-y divide-slate-100 dark:divide-graybg-border">
                <?php if (empty($links)): ?>
                    <div class="p-8 text-center text-xs text-slate-400 dark:text-zinc-500">
                        Belum ada tautan. Klik tombol Tambah Link di atas.
                    </div>
                <?php else: ?>
                    <?php foreach ($links as $link): ?>
                        <div class="p-4 flex items-center justify-between gap-4 hover:bg-slate-50/50 dark:hover:bg-graybg-cardalt/50 transition-colors">
                            <div class="flex items-center gap-3.5 overflow-hidden">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-graybg-cardalt text-slate-700 dark:text-zinc-300 flex items-center justify-center shrink-0">
                                    <i data-lucide="<?= htmlspecialchars($link['icon'] ?? 'link') ?>" class="w-4 h-4"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-xs font-semibold text-slate-900 dark:text-white truncate"><?= htmlspecialchars($link['title']) ?></h3>
                                        <?php if (!$link['is_active']): ?>
                                            <span class="px-1.5 py-0.5 rounded text-[10px] bg-rose-500/10 text-rose-500 border border-rose-500/20">Nonaktif</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-[11px] text-slate-400 dark:text-zinc-500 truncate"><?= htmlspecialchars($link['url']) ?></p>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5 shrink-0">
                                <button onclick='editLink(<?= json_encode($link) ?>)' class="p-2 rounded-lg text-slate-500 hover:text-emerald-500 hover:bg-slate-100 dark:hover:bg-graybg-cardalt transition-colors">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </button>
                                <form method="POST" action="" onsubmit="return confirm('Yakin ingin menghapus link ini?')">
                                    <input type="hidden" name="action" value="delete_link">
                                    <input type="hidden" name="link_id" value="<?= $link['id'] ?>">
                                    <button type="submit" class="p-2 rounded-lg text-slate-500 hover:text-rose-500 hover:bg-rose-500/10 transition-colors">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- ==================== TAB 3: KATALOG APLIKASI ==================== -->
        <div id="tab-apps" class="tab-content hidden space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Katalog Aplikasi & Sistem</h2>
                    <p class="text-xs text-slate-400 dark:text-zinc-500">Muncul saat pengunjung mengklik kartu showcase</p>
                </div>
                <button onclick="openAppModal()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-sm transition-all">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Tambah Aplikasi</span>
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <?php if (empty($apps)): ?>
                    <div class="col-span-full p-8 text-center text-xs text-slate-400 dark:text-zinc-500 bg-white dark:bg-graybg-card rounded-3xl border border-slate-200 dark:border-graybg-border">
                        Belum ada aplikasi yang didaftarkan.
                    </div>
                <?php else: ?>
                    <?php foreach ($apps as $app): ?>
                        <div class="p-4 rounded-3xl bg-white dark:bg-graybg-card border border-slate-200 dark:border-graybg-border flex flex-col justify-between gap-3 shadow-sm">
                            <div>
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-emerald-500/15 text-emerald-500 flex items-center justify-center shrink-0">
                                            <i data-lucide="<?= htmlspecialchars($app['icon'] ?? 'sparkles') ?>" class="w-4 h-4"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($app['name']) ?></h4>
                                            <p class="text-[10px] text-slate-400 dark:text-zinc-500 font-mono"><?= htmlspecialchars($app['category'] ?? 'System') ?></p>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-medium <?= strtolower($app['badge'] ?? '') === 'live' ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-500 border border-amber-500/20' ?>">
                                        <?= htmlspecialchars($app['badge'] ?? 'Live') ?>
                                    </span>
                                </div>
                                <p class="text-xs text-slate-600 dark:text-zinc-400 mt-2 line-clamp-2">
                                    <?= htmlspecialchars($app['description'] ?? '') ?>
                                </p>
                            </div>

                            <div class="pt-3 border-t border-slate-100 dark:border-graybg-border/60 flex items-center justify-between text-xs">
                                <a href="<?= htmlspecialchars($app['url']) ?>" target="_blank" class="text-[11px] text-emerald-500 hover:underline inline-flex items-center gap-1 font-medium">
                                    <span>Buka URL</span>
                                    <i data-lucide="external-link" class="w-3 h-3"></i>
                                </a>
                                <div class="flex items-center gap-1">
                                    <button onclick='editApp(<?= json_encode($app) ?>)' class="p-1.5 rounded-lg text-slate-400 hover:text-emerald-500 hover:bg-slate-100 dark:hover:bg-graybg-cardalt transition-colors">
                                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                    </button>
                                    <form method="POST" action="" onsubmit="return confirm('Hapus aplikasi ini?')">
                                        <input type="hidden" name="action" value="delete_app">
                                        <input type="hidden" name="app_id" value="<?= $app['id'] ?>">
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-500/10 transition-colors">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- ==================== TAB 4: KEAMANAN & PASSWORD ==================== -->
        <div id="tab-security" class="tab-content hidden">
            <form method="POST" action="" class="max-w-md bg-white dark:bg-graybg-card border border-slate-200 dark:border-graybg-border rounded-3xl p-6 sm:p-8 space-y-4 shadow-sm">
                <input type="hidden" name="action" value="change_password">

                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-2">Ganti Password Admin</h3>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-zinc-300 mb-1.5">Password Saat Ini</label>
                    <input type="password" name="old_password" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-graybg-cardalt border border-slate-200 dark:border-graybg-border text-xs focus:ring-2 focus:ring-emerald-500/40 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-zinc-300 mb-1.5">Password Baru (min 6 karakter)</label>
                    <input type="password" name="new_password" required minlength="6" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-graybg-cardalt border border-slate-200 dark:border-graybg-border text-xs focus:ring-2 focus:ring-emerald-500/40 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-zinc-300 mb-1.5">Konfirmasi Password Baru</label>
                    <input type="password" name="confirm_password" required minlength="6" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-graybg-cardalt border border-slate-200 dark:border-graybg-border text-xs focus:ring-2 focus:ring-emerald-500/40 focus:outline-none">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-md transition-all">
                        Perbarui Password
                    </button>
                </div>
            </form>
        </div>

    </div>

    <!-- MODAL LINK (ADD / EDIT) -->
    <div id="linkModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-200">
        <div class="w-full max-w-md bg-white dark:bg-graybg-card border border-slate-200 dark:border-graybg-border rounded-3xl p-6 shadow-2xl">
            <div class="flex items-center justify-between mb-4">
                <h3 id="linkModalTitle" class="text-sm font-bold text-slate-900 dark:text-white">Tambah Link Baru</h3>
                <button onclick="closeLinkModal()" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>
            <form method="POST" action="" class="space-y-3.5">
                <input type="hidden" name="action" value="save_link">
                <input type="hidden" name="link_id" id="link_id" value="">

                <div>
                    <label class="block text-xs font-semibold mb-1">Judul Link</label>
                    <input type="text" name="title" id="link_title" required placeholder="Contoh: LinkedIn Profile" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-graybg-cardalt border border-slate-200 dark:border-graybg-border text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Deskripsi Singkat</label>
                    <input type="text" name="description" id="link_desc" placeholder="Contoh: Riwayat karier & portfolio" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-graybg-cardalt border border-slate-200 dark:border-graybg-border text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">URL Tujuan</label>
                    <input type="text" name="url" id="link_url" required placeholder="https://..." class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-graybg-cardalt border border-slate-200 dark:border-graybg-border text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold mb-1">Icon (Lucide)</label>
                        <select name="icon" id="link_icon" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-graybg-cardalt border border-slate-200 dark:border-graybg-border text-xs focus:outline-none">
                            <option value="link">Link (Default)</option>
                            <option value="globe">Globe (Web)</option>
                            <option value="github">GitHub</option>
                            <option value="linkedin">LinkedIn</option>
                            <option value="whatsapp">WhatsApp</option>
                            <option value="mail">Mail (Email)</option>
                            <option value="instagram">Instagram</option>
                            <option value="twitter">Twitter / X</option>
                            <option value="youtube">YouTube</option>
                            <option value="award">Award / Sertifikat</option>
                            <option value="file-text">File / Dokumen</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1">Urutan Sort</label>
                        <input type="number" name="sort_order" id="link_sort" value="0" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-graybg-cardalt border border-slate-200 dark:border-graybg-border text-xs focus:outline-none">
                    </div>
                </div>
                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="is_active" id="link_active" value="1" checked class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <label for="link_active" class="text-xs text-slate-600 dark:text-zinc-300">Tampilkan link ini di halaman utama</label>
                </div>

                <div class="pt-3 flex justify-end gap-2">
                    <button type="button" onclick="closeLinkModal()" class="px-4 py-2 rounded-xl text-xs bg-slate-200 dark:bg-zinc-800 text-slate-700 dark:text-zinc-300">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL APP (ADD / EDIT) -->
    <div id="appModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-200">
        <div class="w-full max-w-md bg-white dark:bg-graybg-card border border-slate-200 dark:border-graybg-border rounded-3xl p-6 shadow-2xl">
            <div class="flex items-center justify-between mb-4">
                <h3 id="appModalTitle" class="text-sm font-bold text-slate-900 dark:text-white">Tambah Aplikasi Baru</h3>
                <button onclick="closeAppModal()" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>
            <form method="POST" action="" class="space-y-3.5">
                <input type="hidden" name="action" value="save_app">
                <input type="hidden" name="app_id" id="app_id" value="">

                <div>
                    <label class="block text-xs font-semibold mb-1">Nama Aplikasi / Sistem</label>
                    <input type="text" name="name" id="app_name" required placeholder="Contoh: CV AI Generator" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-graybg-cardalt border border-slate-200 dark:border-graybg-border text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold mb-1">Kategori</label>
                        <input type="text" name="category" id="app_category" placeholder="Contoh: AI, Web App" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-graybg-cardalt border border-slate-200 dark:border-graybg-border text-xs focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1">Badge Status</label>
                        <select name="badge" id="app_badge" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-graybg-cardalt border border-slate-200 dark:border-graybg-border text-xs focus:outline-none">
                            <option value="Live">Live</option>
                            <option value="Dev">Dev</option>
                            <option value="Beta">Beta</option>
                            <option value="Demo">Demo</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Deskripsi Singkat</label>
                    <textarea name="description" id="app_desc" rows="2" placeholder="Fungsi dan keunggulan aplikasi..." class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-graybg-cardalt border border-slate-200 dark:border-graybg-border text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/40"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">URL Aplikasi</label>
                    <input type="text" name="url" id="app_url" required placeholder="https://..." class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-graybg-cardalt border border-slate-200 dark:border-graybg-border text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold mb-1">Icon (Lucide)</label>
                        <select name="icon" id="app_icon" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-graybg-cardalt border border-slate-200 dark:border-graybg-border text-xs focus:outline-none">
                            <option value="sparkles">Sparkles (AI)</option>
                            <option value="cloud">Cloud (Data/Weather)</option>
                            <option value="layout-grid">Grid (App)</option>
                            <option value="bar-chart">Bar Chart (Analytics)</option>
                            <option value="database">Database</option>
                            <option value="cpu">CPU (IoT / System)</option>
                            <option value="shopping-cart">Cart (E-commerce)</option>
                            <option value="code">Code</option>
                            <option value="shield">Shield (Security)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1">Urutan Sort</label>
                        <input type="number" name="sort_order" id="app_sort" value="0" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-graybg-cardalt border border-slate-200 dark:border-graybg-border text-xs focus:outline-none">
                    </div>
                </div>
                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="is_active" id="app_active" value="1" checked class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <label for="app_active" class="text-xs text-slate-600 dark:text-zinc-300">Tampilkan di katalog aplikasi</label>
                </div>

                <div class="pt-3 flex justify-end gap-2">
                    <button type="button" onclick="closeAppModal()" class="px-4 py-2 rounded-xl text-xs bg-slate-200 dark:bg-zinc-800 text-slate-700 dark:text-zinc-300">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        lucide.createIcons();

        // Tabs Handler
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('bg-emerald-500/15', 'text-emerald-500', 'border', 'border-emerald-500/30');
                el.classList.add('text-slate-600', 'dark:text-zinc-400');
            });

            const content = document.getElementById('tab-' + tabId);
            const btn = document.getElementById('tab-btn-' + tabId);
            if (content && btn) {
                content.classList.remove('hidden');
                btn.classList.add('bg-emerald-500/15', 'text-emerald-500', 'border', 'border-emerald-500/30');
                btn.classList.remove('text-slate-600', 'dark:text-zinc-400');
            }
        }
        // Init active tab style
        switchTab('profile');

        // Link Modal Handlers
        const linkModal = document.getElementById('linkModal');
        function openLinkModal() {
            document.getElementById('linkModalTitle').innerText = 'Tambah Link Baru';
            document.getElementById('link_id').value = '';
            document.getElementById('link_title').value = '';
            document.getElementById('link_desc').value = '';
            document.getElementById('link_url').value = '';
            document.getElementById('link_icon').value = 'link';
            document.getElementById('link_sort').value = '0';
            document.getElementById('link_active').checked = true;
            linkModal.classList.remove('opacity-0', 'pointer-events-none');
        }
        function editLink(link) {
            document.getElementById('linkModalTitle').innerText = 'Edit Link';
            document.getElementById('link_id').value = link.id;
            document.getElementById('link_title').value = link.title;
            document.getElementById('link_desc').value = link.description || '';
            document.getElementById('link_url').value = link.url;
            document.getElementById('link_icon').value = link.icon || 'link';
            document.getElementById('link_sort').value = link.sort_order || 0;
            document.getElementById('link_active').checked = link.is_active == 1;
            linkModal.classList.remove('opacity-0', 'pointer-events-none');
        }
        function closeLinkModal() {
            linkModal.classList.add('opacity-0', 'pointer-events-none');
        }

        // App Modal Handlers
        const appModal = document.getElementById('appModal');
        function openAppModal() {
            document.getElementById('appModalTitle').innerText = 'Tambah Aplikasi Baru';
            document.getElementById('app_id').value = '';
            document.getElementById('app_name').value = '';
            document.getElementById('app_category').value = '';
            document.getElementById('app_desc').value = '';
            document.getElementById('app_url').value = '';
            document.getElementById('app_badge').value = 'Live';
            document.getElementById('app_icon').value = 'sparkles';
            document.getElementById('app_sort').value = '0';
            document.getElementById('app_active').checked = true;
            appModal.classList.remove('opacity-0', 'pointer-events-none');
        }
        function editApp(app) {
            document.getElementById('appModalTitle').innerText = 'Edit Aplikasi';
            document.getElementById('app_id').value = app.id;
            document.getElementById('app_name').value = app.name;
            document.getElementById('app_category').value = app.category || '';
            document.getElementById('app_desc').value = app.description || '';
            document.getElementById('app_url').value = app.url;
            document.getElementById('app_badge').value = app.badge || 'Live';
            document.getElementById('app_icon').value = app.icon || 'sparkles';
            document.getElementById('app_sort').value = app.sort_order || 0;
            document.getElementById('app_active').checked = app.is_active == 1;
            appModal.classList.remove('opacity-0', 'pointer-events-none');
        }
        function closeAppModal() {
            appModal.classList.add('opacity-0', 'pointer-events-none');
        }
    </script>
</body>
</html>
