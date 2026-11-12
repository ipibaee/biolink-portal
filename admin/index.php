<?php
require_once __DIR__ . '/auth.php';
checkAdminAuth();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../icons.php';

$message = '';
$msgType = 'success';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // 1. UPDATE PROFILE & AVATAR UPLOAD
    if ($action === 'update_profile') {
        $avatarUrl = trim($_POST['avatar_url'] ?? '');

        // Cek jika ada file gambar yang diupload
        if (!empty($_FILES['avatar_file']['name']) && $_FILES['avatar_file']['error'] === UPLOAD_ERR_OK) {
            $fileTmp = $_FILES['avatar_file']['tmp_name'];
            $fileExt = strtolower(pathinfo($_FILES['avatar_file']['name'], PATHINFO_EXTENSION));
            $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (in_array($fileExt, $allowedExt)) {
                $newFileName = 'avatar_' . time() . '.' . $fileExt;
                $targetPath = UPLOADS_DIR . '/' . $newFileName;
                if (move_uploaded_file($fileTmp, $targetPath)) {
                    $avatarUrl = 'uploads/' . $newFileName;
                } else {
                    $message = 'Gagal memindahkan file upload.';
                    $msgType = 'error';
                }
            } else {
                $message = 'Format file tidak diizinkan. Gunakan JPG, PNG, WEBP, atau GIF.';
                $msgType = 'error';
            }
        }

        if ($msgType !== 'error') {
            $settingsToUpdate = [
                'name' => trim($_POST['name'] ?? ''),
                'title' => trim($_POST['title'] ?? ''),
                'bio' => trim($_POST['bio'] ?? ''),
                'location' => trim($_POST['location'] ?? ''),
                'status_text' => trim($_POST['status_text'] ?? ''),
                'avatar_url' => $avatarUrl,
                'domain_badge' => trim($_POST['domain_badge'] ?? '')
            ];
            Storage::updateSettings($settingsToUpdate);
            $message = 'Profil & Foto Avatar berhasil disimpan!';
        }
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
            'icon' => trim($_POST['icon'] ?? 'layout-grid'),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 1
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

    // 6. SAVE SOCIAL (ADD / EDIT)
    if ($action === 'save_social') {
        $id = !empty($_POST['social_id']) ? (int)$_POST['social_id'] : null;
        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'url' => trim($_POST['url'] ?? ''),
            'icon' => trim($_POST['icon'] ?? 'instagram'),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 1
        ];
        Storage::saveSocial($data, $id);
        $message = $id ? 'Tombol sosmed berhasil diperbarui!' : 'Tombol sosmed berhasil ditambahkan!';
    }

    // 7. DELETE SOCIAL
    if ($action === 'delete_social') {
        $id = (int)($_POST['social_id'] ?? 0);
        if ($id) {
            Storage::deleteSocial($id);
            $message = 'Tombol sosmed berhasil dihapus!';
        }
    }

    // 8. CHANGE PASSWORD
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
$socials = Storage::getSocials(false);
?>
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — Portal Manager</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        darkbg: '#0c0d11',
                        darkcard: '#14151b',
                        darkcardalt: '#1a1b24',
                        darkborder: '#21222c'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-100 dark:bg-darkbg text-slate-800 dark:text-zinc-100 font-sans min-h-screen py-8 px-4 sm:px-6">

    <div class="max-w-4xl mx-auto">
        <!-- TOP NAVBAR -->
        <header class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 mb-6 border-b border-slate-200 dark:border-darkborder gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/15 text-emerald-500 flex items-center justify-center font-bold">
                    <?= render_icon('layout-grid', 'w-5 h-5') ?>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>Portal Dashboard</span>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-emerald-500/15 text-emerald-500 border border-emerald-500/30">Admin</span>
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-zinc-400">Kelola foto avatar, link utama, aplikasi, & tombol sosmed</p>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="../index.php" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-white dark:bg-darkcard border border-slate-200 dark:border-darkborder hover:border-emerald-500 text-slate-700 dark:text-zinc-200 shadow-sm transition-all">
                    <span>Lihat Website</span>
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="7" x2="17" y1="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
                </a>
                <a href="logout.php" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-rose-500/10 hover:bg-rose-500/20 text-rose-500 border border-rose-500/20 transition-all">
                    <span>Keluar</span>
                </a>
            </div>
        </header>

        <!-- ALERT MESSAGE -->
        <?php if (!empty($message)): ?>
            <div class="mb-6 p-4 rounded-2xl flex items-center gap-3 text-xs font-medium border <?= $msgType === 'success' ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400' : 'bg-rose-500/10 border-rose-500/30 text-rose-400' ?>">
                <span><?= htmlspecialchars($message) ?></span>
            </div>
        <?php endif; ?>

        <!-- NAVIGATION TABS -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 mb-6 scrollbar-none border-b border-slate-200 dark:border-darkborder">
            <button onclick="switchTab('profile')" id="tab-btn-profile" class="tab-btn active px-4 py-2.5 rounded-xl text-xs font-semibold transition-all">
                Profil & Foto
            </button>
            <button onclick="switchTab('links')" id="tab-btn-links" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-semibold transition-all">
                Link Utama (<?= count($links) ?>)
            </button>
            <button onclick="switchTab('apps')" id="tab-btn-apps" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-semibold transition-all">
                Katalog Aplikasi (<?= count($apps) ?>)
            </button>
            <button onclick="switchTab('socials')" id="tab-btn-socials" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-semibold transition-all">
                Tombol Bulat Sosmed (<?= count($socials) ?>)
            </button>
            <button onclick="switchTab('security')" id="tab-btn-security" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-semibold transition-all">
                Keamanan
            </button>
        </div>

        <!-- ==================== TAB 1: PROFIL & UPLOAD FOTO ==================== -->
        <div id="tab-profile" class="tab-content">
            <form method="POST" action="" enctype="multipart/form-data" class="bg-white dark:bg-darkcard border border-slate-200 dark:border-darkborder rounded-3xl p-6 sm:p-8 space-y-5 shadow-sm">
                <input type="hidden" name="action" value="update_profile">

                <!-- UPLOAD FOTO SECTION -->
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-darkcardalt border border-slate-200 dark:border-darkborder/70 flex flex-col sm:flex-row items-center gap-5">
                    <div class="relative w-20 h-20 rounded-full overflow-hidden border-2 border-emerald-500 shrink-0">
                        <img src="../<?= htmlspecialchars($settings['avatar_url'] ?? '') ?>" onerror="this.src='https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400'" alt="Avatar" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-grow text-center sm:text-left">
                        <label class="block text-xs font-bold text-slate-800 dark:text-white mb-1">Ganti Foto Profil (Upload Langsung)</label>
                        <p class="text-[11px] text-slate-500 dark:text-zinc-400 mb-2.5">Pilih foto dari galeri/komputer kamu (Format JPG, PNG, atau WEBP).</p>
                        <input type="file" name="avatar_file" accept="image/*" class="text-xs text-slate-500 dark:text-zinc-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-500/15 file:text-emerald-500 hover:file:bg-emerald-500/25">
                        <input type="hidden" name="avatar_url" value="<?= htmlspecialchars($settings['avatar_url'] ?? '') ?>">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold mb-1.5">Nama Lengkap & Gelar</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($settings['name'] ?? '') ?>" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-darkcardalt border border-slate-200 dark:border-darkborder text-xs focus:ring-2 focus:ring-emerald-500/40 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1.5">Profesi / Role Singkat</label>
                        <input type="text" name="title" value="<?= htmlspecialchars($settings['title'] ?? '') ?>" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-darkcardalt border border-slate-200 dark:border-darkborder text-xs focus:ring-2 focus:ring-emerald-500/40 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold mb-1.5">Lokasi</label>
                        <input type="text" name="location" value="<?= htmlspecialchars($settings['location'] ?? '') ?>" placeholder="Jakarta, Indonesia • GMT+7" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-darkcardalt border border-slate-200 dark:border-darkborder text-xs focus:ring-2 focus:ring-emerald-500/40 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1.5">Status Indikator Header</label>
                        <input type="text" name="status_text" value="<?= htmlspecialchars($settings['status_text'] ?? '') ?>" placeholder="Available for Projects" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-darkcardalt border border-slate-200 dark:border-darkborder text-xs focus:ring-2 focus:ring-emerald-500/40 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1.5">Bio Singkat</label>
                    <textarea name="bio" rows="3" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-darkcardalt border border-slate-200 dark:border-darkborder text-xs focus:ring-2 focus:ring-emerald-500/40 focus:outline-none"><?= htmlspecialchars($settings['bio'] ?? '') ?></textarea>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-md transition-all">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- ==================== TAB 2: LINK UTAMA ==================== -->
        <div id="tab-links" class="tab-content hidden space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Daftar Link Utama</h2>
                    <p class="text-xs text-slate-400 dark:text-zinc-500">Kartu tautan vertikal di halaman depan</p>
                </div>
                <button onclick="openLinkModal()" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-sm transition-all">
                    + Tambah Link
                </button>
            </div>

            <div class="bg-white dark:bg-darkcard border border-slate-200 dark:border-darkborder rounded-3xl overflow-hidden divide-y divide-slate-100 dark:divide-darkborder">
                <?php foreach ($links as $link): ?>
                    <div class="p-4 flex items-center justify-between gap-4 hover:bg-slate-50/50 dark:hover:bg-darkcardalt/50 transition-colors">
                        <div class="flex items-center gap-3.5 overflow-hidden">
                            <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-darkcardalt text-slate-700 dark:text-zinc-300 flex items-center justify-center shrink-0">
                                <?= render_icon($link['icon'] ?? 'link', 'w-4 h-4') ?>
                            </div>
                            <div class="overflow-hidden">
                                <h3 class="text-xs font-semibold text-slate-900 dark:text-white truncate"><?= htmlspecialchars($link['title']) ?></h3>
                                <p class="text-[11px] text-slate-400 dark:text-zinc-500 truncate"><?= htmlspecialchars($link['description'] ?? $link['url']) ?></p>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0">
                            <button onclick='editLink(<?= json_encode($link) ?>)' class="px-2.5 py-1.5 rounded-lg text-xs text-slate-500 hover:text-emerald-500 hover:bg-slate-100 dark:hover:bg-darkcardalt transition-colors">
                                Edit
                            </button>
                            <form method="POST" action="" onsubmit="return confirm('Hapus link ini?')">
                                <input type="hidden" name="action" value="delete_link">
                                <input type="hidden" name="link_id" value="<?= $link['id'] ?>">
                                <button type="submit" class="px-2.5 py-1.5 rounded-lg text-xs text-rose-500 hover:bg-rose-500/10 transition-colors">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ==================== TAB 3: KATALOG APLIKASI ==================== -->
        <div id="tab-apps" class="tab-content hidden space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Katalog Aplikasi (Showcase Modal)</h2>
                    <p class="text-xs text-slate-400 dark:text-zinc-500">Muncul saat kartu showcase aplikasi diklik</p>
                </div>
                <button onclick="openAppModal()" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-sm transition-all">
                    + Tambah Aplikasi
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <?php foreach ($apps as $app): ?>
                    <div class="p-4 rounded-3xl bg-white dark:bg-darkcard border border-slate-200 dark:border-darkborder flex flex-col justify-between gap-3 shadow-sm">
                        <div>
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-emerald-500/15 text-emerald-500 flex items-center justify-center shrink-0">
                                        <?= render_icon($app['icon'] ?? 'layout-grid', 'w-4 h-4') ?>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($app['name']) ?></h4>
                                        <p class="text-[10px] text-slate-400 dark:text-zinc-500"><?= htmlspecialchars($app['category'] ?? 'System') ?></p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-medium bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                    <?= htmlspecialchars($app['badge'] ?? 'Live') ?>
                                </span>
                            </div>
                            <p class="text-xs text-slate-600 dark:text-zinc-400 mt-2 line-clamp-2">
                                <?= htmlspecialchars($app['description'] ?? '') ?>
                            </p>
                        </div>

                        <div class="pt-3 border-t border-slate-100 dark:border-darkborder/60 flex items-center justify-between text-xs">
                            <a href="<?= htmlspecialchars($app['url']) ?>" target="_blank" class="text-[11px] text-emerald-500 hover:underline">
                                Buka URL ↗
                            </a>
                            <div class="flex items-center gap-1">
                                <button onclick='editApp(<?= json_encode($app) ?>)' class="px-2 py-1 rounded text-xs text-slate-400 hover:text-emerald-500 hover:bg-slate-100 dark:hover:bg-darkcardalt">
                                    Edit
                                </button>
                                <form method="POST" action="" onsubmit="return confirm('Hapus aplikasi ini?')">
                                    <input type="hidden" name="action" value="delete_app">
                                    <input type="hidden" name="app_id" value="<?= $app['id'] ?>">
                                    <button type="submit" class="px-2 py-1 rounded text-xs text-rose-500 hover:bg-rose-500/10">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ==================== TAB 4: TOMBOL BULAT SOSMED ==================== -->
        <div id="tab-socials" class="tab-content hidden space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Tombol Bulat Sosmed (Bawah)</h2>
                    <p class="text-xs text-slate-400 dark:text-zinc-500">Ikon bulat di atas footer (Instagram, TikTok, dll)</p>
                </div>
                <button onclick="openSocialModal()" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-sm transition-all">
                    + Tambah Sosmed
                </button>
            </div>

            <div class="bg-white dark:bg-darkcard border border-slate-200 dark:border-darkborder rounded-3xl overflow-hidden divide-y divide-slate-100 dark:divide-darkborder">
                <?php if (empty($socials)): ?>
                    <div class="p-8 text-center text-xs text-slate-400 dark:text-zinc-500">
                        Belum ada tombol sosmed. Klik tombol + Tambah Sosmed di atas.
                    </div>
                <?php else: ?>
                    <?php foreach ($socials as $soc): ?>
                        <div class="p-4 flex items-center justify-between gap-4 hover:bg-slate-50/50 dark:hover:bg-darkcardalt/50 transition-colors">
                            <div class="flex items-center gap-3.5 overflow-hidden">
                                <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-darkcardalt text-slate-700 dark:text-zinc-300 flex items-center justify-center shrink-0 border border-slate-200 dark:border-darkborder">
                                    <?= render_icon($soc['icon'] ?? 'instagram', 'w-4 h-4') ?>
                                </div>
                                <div class="overflow-hidden">
                                    <h3 class="text-xs font-semibold text-slate-900 dark:text-white truncate"><?= htmlspecialchars($soc['title']) ?></h3>
                                    <p class="text-[11px] text-slate-400 dark:text-zinc-500 truncate"><?= htmlspecialchars($soc['url']) ?></p>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5 shrink-0">
                                <button onclick='editSocial(<?= json_encode($soc) ?>)' class="px-2.5 py-1.5 rounded-lg text-xs text-slate-500 hover:text-emerald-500 hover:bg-slate-100 dark:hover:bg-darkcardalt transition-colors">
                                    Edit
                                </button>
                                <form method="POST" action="" onsubmit="return confirm('Hapus tombol sosmed ini?')">
                                    <input type="hidden" name="action" value="delete_social">
                                    <input type="hidden" name="social_id" value="<?= $soc['id'] ?>">
                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg text-xs text-rose-500 hover:bg-rose-500/10 transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- ==================== TAB 5: KEAMANAN ==================== -->
        <div id="tab-security" class="tab-content hidden">
            <form method="POST" action="" class="max-w-md bg-white dark:bg-darkcard border border-slate-200 dark:border-darkborder rounded-3xl p-6 sm:p-8 space-y-4 shadow-sm">
                <input type="hidden" name="action" value="change_password">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-2">Ganti Password Admin</h3>

                <div>
                    <label class="block text-xs font-semibold mb-1.5">Password Saat Ini</label>
                    <input type="password" name="old_password" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-darkcardalt border border-slate-200 dark:border-darkborder text-xs focus:ring-2 focus:ring-emerald-500/40 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5">Password Baru (min 6 karakter)</label>
                    <input type="password" name="new_password" required minlength="6" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-darkcardalt border border-slate-200 dark:border-darkborder text-xs focus:ring-2 focus:ring-emerald-500/40 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5">Konfirmasi Password Baru</label>
                    <input type="password" name="confirm_password" required minlength="6" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-darkcardalt border border-slate-200 dark:border-darkborder text-xs focus:ring-2 focus:ring-emerald-500/40 focus:outline-none">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-md transition-all">
                        Perbarui Password
                    </button>
                </div>
            </form>
        </div>

    </div>

    <!-- MODAL LINK -->
    <div id="linkModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-200">
        <div class="w-full max-w-md bg-white dark:bg-darkcard border border-slate-200 dark:border-darkborder rounded-3xl p-6 shadow-2xl">
            <h3 id="linkModalTitle" class="text-sm font-bold text-slate-900 dark:text-white mb-4">Tambah Link Baru</h3>
            <form method="POST" action="" class="space-y-3.5">
                <input type="hidden" name="action" value="save_link">
                <input type="hidden" name="link_id" id="link_id" value="">

                <div>
                    <label class="block text-xs font-semibold mb-1">Judul Link</label>
                    <input type="text" name="title" id="link_title" required placeholder="Contoh: WhatsApp" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-darkcardalt border border-slate-200 dark:border-darkborder text-xs focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Deskripsi Singkat</label>
                    <input type="text" name="description" id="link_desc" placeholder="Contoh: Chat langsung — respon cepat" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-darkcardalt border border-slate-200 dark:border-darkborder text-xs focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">URL Tujuan</label>
                    <input type="text" name="url" id="link_url" required placeholder="https://wa.me/..." class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-darkcardalt border border-slate-200 dark:border-darkborder text-xs focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Pilih Logo / Ikon</label>
                    <select name="icon" id="link_icon" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-darkcardalt border border-slate-200 dark:border-darkborder text-xs focus:outline-none">
                        <option value="whatsapp">WhatsApp</option>
                        <option value="linkedin">LinkedIn</option>
                        <option value="github">GitHub</option>
                        <option value="mail">Email</option>
                        <option value="award">Credly / Sertifikat</option>
                        <option value="globe">Website / Portofolio</option>
                        <option value="instagram">Instagram</option>
                        <option value="twitter">X (Twitter)</option>
                        <option value="youtube">YouTube</option>
                        <option value="tiktok">TikTok</option>
                        <option value="telegram">Telegram</option>
                        <option value="discord">Discord</option>
                        <option value="link">Link Biasa</option>
                    </select>
                </div>

                <div class="pt-3 flex justify-end gap-2">
                    <button type="button" onclick="closeLinkModal()" class="px-4 py-2 rounded-xl text-xs bg-slate-200 dark:bg-zinc-800">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-emerald-600 text-white">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL APP -->
    <div id="appModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-200">
        <div class="w-full max-w-md bg-white dark:bg-darkcard border border-slate-200 dark:border-darkborder rounded-3xl p-6 shadow-2xl">
            <h3 id="appModalTitle" class="text-sm font-bold text-slate-900 dark:text-white mb-4">Tambah Aplikasi</h3>
            <form method="POST" action="" class="space-y-3.5">
                <input type="hidden" name="action" value="save_app">
                <input type="hidden" name="app_id" id="app_id" value="">

                <div>
                    <label class="block text-xs font-semibold mb-1">Nama Aplikasi</label>
                    <input type="text" name="name" id="app_name" required placeholder="Contoh: KasirKu" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-darkcardalt border border-slate-200 dark:border-darkborder text-xs focus:outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold mb-1">Kategori Badge</label>
                        <input type="text" name="category" id="app_category" placeholder="Contoh: POS & Kasir" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-darkcardalt border border-slate-200 dark:border-darkborder text-xs focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1">Ikon Aplikasi</label>
                        <select name="icon" id="app_icon" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-darkcardalt border border-slate-200 dark:border-darkborder text-xs focus:outline-none">
                            <option value="store">Store (Kasir / Toko)</option>
                            <option value="qr-code">QR Code (Absensi)</option>
                            <option value="kanban">Kanban (SaaS / Project)</option>
                            <option value="shopping-bag">Shopping Bag (E-commerce)</option>
                            <option value="layout-grid">Grid (Web App)</option>
                            <option value="sparkles">Sparkles (AI)</option>
                            <option value="cloud">Cloud (Data / Server)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Deskripsi Singkat</label>
                    <input type="text" name="description" id="app_desc" placeholder="Contoh: Sistem kasir multi-outlet dengan laporan real-time." class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-darkcardalt border border-slate-200 dark:border-darkborder text-xs focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">URL Aplikasi</label>
                    <input type="text" name="url" id="app_url" required placeholder="https://..." class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-darkcardalt border border-slate-200 dark:border-darkborder text-xs focus:outline-none">
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="is_active" id="app_active" value="1" checked class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <label for="app_active" class="text-xs text-slate-600 dark:text-zinc-300">Tampilkan aplikasi ini di halaman utama</label>
                </div>

                <div class="pt-3 flex justify-end gap-2">
                    <button type="button" onclick="closeAppModal()" class="px-4 py-2 rounded-xl text-xs bg-slate-200 dark:bg-zinc-800">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-emerald-600 text-white">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL SOCIALS (TOMBOL BULAT) -->
    <div id="socialModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-200">
        <div class="w-full max-w-md bg-white dark:bg-darkcard border border-slate-200 dark:border-darkborder rounded-3xl p-6 shadow-2xl">
            <h3 id="socialModalTitle" class="text-sm font-bold text-slate-900 dark:text-white mb-4">Tambah Tombol Sosmed</h3>
            <form method="POST" action="" class="space-y-3.5">
                <input type="hidden" name="action" value="save_social">
                <input type="hidden" name="social_id" id="social_id" value="">

                <div>
                    <label class="block text-xs font-semibold mb-1">Platform / Nama Sosmed</label>
                    <input type="text" name="title" id="social_title" required placeholder="Contoh: Instagram" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-darkcardalt border border-slate-200 dark:border-darkborder text-xs focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Pilih Ikon</label>
                    <select name="icon" id="social_icon" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-darkcardalt border border-slate-200 dark:border-darkborder text-xs focus:outline-none">
                        <option value="instagram">Instagram</option>
                        <option value="github">GitHub</option>
                        <option value="linkedin">LinkedIn</option>
                        <option value="youtube">YouTube</option>
                        <option value="tiktok">TikTok</option>
                        <option value="twitter">X (Twitter)</option>
                        <option value="whatsapp">WhatsApp</option>
                        <option value="telegram">Telegram</option>
                        <option value="discord">Discord</option>
                        <option value="mail">Email</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">URL Profil Sosmed</label>
                    <input type="text" name="url" id="social_url" required placeholder="https://instagram.com/username" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-darkcardalt border border-slate-200 dark:border-darkborder text-xs focus:outline-none">
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="is_active" id="social_active" value="1" checked class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <label for="social_active" class="text-xs text-slate-600 dark:text-zinc-300">Tampilkan tombol sosmed ini di halaman utama</label>
                </div>

                <div class="pt-3 flex justify-end gap-2">
                    <button type="button" onclick="closeSocialModal()" class="px-4 py-2 rounded-xl text-xs bg-slate-200 dark:bg-zinc-800">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-emerald-600 text-white">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script>
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
        switchTab('profile');

        const linkModal = document.getElementById('linkModal');
        function openLinkModal() {
            document.getElementById('linkModalTitle').innerText = 'Tambah Link Baru';
            document.getElementById('link_id').value = '';
            document.getElementById('link_title').value = '';
            document.getElementById('link_desc').value = '';
            document.getElementById('link_url').value = '';
            document.getElementById('link_icon').value = 'whatsapp';
            linkModal.classList.remove('opacity-0', 'pointer-events-none');
        }
        function editLink(link) {
            document.getElementById('linkModalTitle').innerText = 'Edit Link';
            document.getElementById('link_id').value = link.id;
            document.getElementById('link_title').value = link.title;
            document.getElementById('link_desc').value = link.description || '';
            document.getElementById('link_url').value = link.url;
            document.getElementById('link_icon').value = link.icon || 'whatsapp';
            linkModal.classList.remove('opacity-0', 'pointer-events-none');
        }
        function closeLinkModal() { linkModal.classList.add('opacity-0', 'pointer-events-none'); }

        const appModal = document.getElementById('appModal');
        function openAppModal() {
            document.getElementById('appModalTitle').innerText = 'Tambah Aplikasi';
            document.getElementById('app_id').value = '';
            document.getElementById('app_name').value = '';
            document.getElementById('app_category').value = '';
            document.getElementById('app_desc').value = '';
            document.getElementById('app_url').value = '';
            document.getElementById('app_icon').value = 'store';
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
            document.getElementById('app_icon').value = app.icon || 'store';
            document.getElementById('app_active').checked = (app.is_active == 1 || app.is_active === undefined);
            appModal.classList.remove('opacity-0', 'pointer-events-none');
        }
        function closeAppModal() { appModal.classList.add('opacity-0', 'pointer-events-none'); }

        const socialModal = document.getElementById('socialModal');
        function openSocialModal() {
            document.getElementById('socialModalTitle').innerText = 'Tambah Tombol Sosmed';
            document.getElementById('social_id').value = '';
            document.getElementById('social_title').value = '';
            document.getElementById('social_url').value = '';
            document.getElementById('social_icon').value = 'instagram';
            document.getElementById('social_active').checked = true;
            socialModal.classList.remove('opacity-0', 'pointer-events-none');
        }
        function editSocial(soc) {
            document.getElementById('socialModalTitle').innerText = 'Edit Tombol Sosmed';
            document.getElementById('social_id').value = soc.id;
            document.getElementById('social_title').value = soc.title;
            document.getElementById('social_url').value = soc.url;
            document.getElementById('social_icon').value = soc.icon || 'instagram';
            document.getElementById('social_active').checked = (soc.is_active == 1 || soc.is_active === undefined);
            socialModal.classList.remove('opacity-0', 'pointer-events-none');
        }
        function closeSocialModal() { socialModal.classList.add('opacity-0', 'pointer-events-none'); }
    </script>
</body>
</html>
