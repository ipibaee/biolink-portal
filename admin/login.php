<?php
session_start();
require_once __DIR__ . '/../db.php';

$error = '';
$settings = Storage::getSettings();
$hash = $settings['admin_password'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if (password_verify($password, $hash)) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Password salah, silakan coba lagi.';
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Portal Security</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        graybg: { base: '#121216', card: '#1a1a22', border: '#2b2b36' }
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-slate-100 dark:bg-graybg-base text-slate-800 dark:text-zinc-100 font-sans min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-sm bg-white dark:bg-graybg-card border border-slate-200 dark:border-graybg-border rounded-3xl p-6 sm:p-8 shadow-2xl">
        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/15 text-emerald-500 flex items-center justify-center mx-auto mb-3">
                <i data-lucide="shield-check" class="w-6 h-6"></i>
            </div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Admin Portal</h1>
            <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">Masukkan password admin untuk melanjutkan</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="mb-4 p-3 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-500 text-xs flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-zinc-300 mb-1.5">Password</label>
                <div class="relative">
                    <input type="password" name="password" required autofocus placeholder="••••••••" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-zinc-900/80 border border-slate-200 dark:border-zinc-700 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                </div>
            </div>

            <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-sm shadow-md transition-all flex items-center justify-center gap-2">
                <span>Masuk ke Panel</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </button>
        </form>

        <div class="mt-6 pt-4 border-t border-slate-100 dark:border-zinc-800 text-center">
            <a href="../index.php" class="text-xs text-slate-400 dark:text-zinc-500 hover:text-emerald-500 transition-colors inline-flex items-center gap-1">
                <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>
                <span>Kembali ke Halaman Depan</span>
            </a>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>
