<?php
/**
 * Database & Data Storage Engine
 * Menggunakan SQLite (PDO) jika tersedia, dengan auto-fallback ke JSON file
 * sehingga 100% kompatibel di berbagai konfigurasi aaPanel / Apache / Nginx.
 */

define('DATA_DIR', __DIR__ . '/data');
define('SQLITE_FILE', DATA_DIR . '/database.sqlite');
define('JSON_FILE', DATA_DIR . '/data.json');

if (!is_dir(DATA_DIR)) {
    mkdir(DATA_DIR, 0755, true);
}

// Inisialisasi Storage Engine
class Storage {
    private static $useSqlite = false;
    private static $pdo = null;

    public static function init() {
        if (extension_loaded('pdo_sqlite')) {
            try {
                self::$pdo = new PDO('sqlite:' . SQLITE_FILE);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                self::$useSqlite = true;
                self::createTables();
                return;
            } catch (Exception $e) {
                self::$useSqlite = false;
            }
        }
        self::initJson();
    }

    private static function createTables() {
        // Tabel Settings (Profile & Admin)
        self::$pdo->exec("CREATE TABLE IF NOT EXISTS settings (
            key TEXT PRIMARY KEY,
            value TEXT
        )");

        // Tabel Links Utama
        self::$pdo->exec("CREATE TABLE IF NOT EXISTS links (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT,
            url TEXT NOT NULL,
            icon TEXT DEFAULT 'link',
            is_active INTEGER DEFAULT 1,
            sort_order INTEGER DEFAULT 0
        )");

        // Tabel Aplikasi & Sistem (Modal Showcase)
        self::$pdo->exec("CREATE TABLE IF NOT EXISTS apps (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            category TEXT,
            description TEXT,
            url TEXT NOT NULL,
            badge TEXT DEFAULT 'Live',
            icon TEXT DEFAULT 'layers',
            is_active INTEGER DEFAULT 1,
            sort_order INTEGER DEFAULT 0
        )");

        // Cek data awal
        $stmt = self::$pdo->query("SELECT COUNT(*) as cnt FROM settings");
        if ($stmt->fetch()['cnt'] == 0) {
            self::seedInitialData();
        }
    }

    private static function seedInitialData() {
        $defaultPassword = password_hash('admin123', PASSWORD_BCRYPT);
        $settings = [
            'admin_password' => $defaultPassword,
            'name' => 'Rifki Figianto, S.Kom.',
            'title' => 'Full-Stack Developer & Tech Enthusiast',
            'bio' => 'Membangun aplikasi web interaktif, arsitektur cloud, dan solusi otomatisasi yang elegan serta berkinerja tinggi.',
            'location' => 'Indonesia',
            'status_text' => 'Available for Projects',
            'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&auto=format&fit=crop&q=80',
            'domain_badge' => 'PORTAL RESMI'
        ];

        foreach ($settings as $k => $v) {
            $stmt = self::$pdo->prepare("INSERT INTO settings (key, value) VALUES (:k, :v)");
            $stmt->execute([':k' => $k, ':v' => $v]);
        }

        // Default Links
        $links = [
            ['title' => 'LinkedIn Profile', 'description' => 'Koneksi profesional, riwayat karier & portfolio kerja', 'url' => 'https://linkedin.com', 'icon' => 'linkedin', 'sort_order' => 1],
            ['title' => 'GitHub Repositories', 'description' => 'Source code, eksperimen open-source & kontribusi', 'url' => 'https://github.com', 'icon' => 'github', 'sort_order' => 2],
            ['title' => 'Hubungi via WhatsApp', 'description' => '+62 812-3456-7890 (Tanya kerja sama & respon cepat)', 'url' => 'https://wa.me/6281234567890?text=Halo,%20saya%20menghubungi%20lewat%20website%20portal', 'icon' => 'whatsapp', 'sort_order' => 3],
            ['title' => 'Kirim Email Langsung', 'description' => 'Kontak formal untuk penawaran proyek & konsultasi', 'url' => 'mailto:yourname@example.com', 'icon' => 'mail', 'sort_order' => 4],
        ];

        foreach ($links as $l) {
            $stmt = self::$pdo->prepare("INSERT INTO links (title, description, url, icon, sort_order) VALUES (:title, :description, :url, :icon, :sort_order)");
            $stmt->execute($l);
        }

        // Default Apps Showcase
        $apps = [
            ['name' => 'CV AI Generator', 'category' => 'Artificial Intelligence', 'description' => 'Aplikasi pembuat resume profesional berbasis AI dengan analisis skor lolos ATS otomatis.', 'url' => 'https://example.com/cv-ai', 'badge' => 'Live', 'icon' => 'sparkles', 'sort_order' => 1],
            ['name' => 'CuacaAI Monitoring', 'category' => 'Data & Forecasting', 'description' => 'Dashboard prakiraan cuaca satelit presisi tinggi dengan integrasi open data.', 'url' => 'https://example.com/cuaca-ai', 'badge' => 'Live', 'icon' => 'cloud', 'sort_order' => 2],
            ['name' => 'Sistem Pendukung Keputusan Desa', 'category' => 'Web App', 'description' => 'Platform transparansi evaluasi program desa menggunakan metode multi-kriteria.', 'url' => 'https://example.com/spk-desa', 'badge' => 'Live', 'icon' => 'layout-grid', 'sort_order' => 3],
            ['name' => 'DSS RK Business Analytics', 'category' => 'Enterprise System', 'description' => 'Sistem pendukung keputusan analitik penjualan dan peramalan inventori stok.', 'url' => 'https://example.com/dss-rk', 'badge' => 'Dev', 'icon' => 'bar-chart', 'sort_order' => 4],
        ];

        foreach ($apps as $a) {
            $stmt = self::$pdo->prepare("INSERT INTO apps (name, category, description, url, badge, icon, sort_order) VALUES (:name, :category, :description, :url, :badge, :icon, :sort_order)");
            $stmt->execute($a);
        }
    }

    private static function initJson() {
        if (!file_exists(JSON_FILE)) {
            $defaultPassword = password_hash('admin123', PASSWORD_BCRYPT);
            $initial = [
                'settings' => [
                    'admin_password' => $defaultPassword,
                    'name' => 'Rifki Figianto, S.Kom.',
                    'title' => 'Full-Stack Developer & Tech Enthusiast',
                    'bio' => 'Membangun aplikasi web interaktif, arsitektur cloud, dan solusi otomatisasi yang elegan serta berkinerja tinggi.',
                    'location' => 'Indonesia',
                    'status_text' => 'Available for Projects',
                    'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&auto=format&fit=crop&q=80',
                    'domain_badge' => 'PORTAL RESMI'
                ],
                'links' => [
                    ['id' => 1, 'title' => 'LinkedIn Profile', 'description' => 'Koneksi profesional, riwayat karier & portfolio kerja', 'url' => 'https://linkedin.com', 'icon' => 'linkedin', 'is_active' => 1, 'sort_order' => 1],
                    ['id' => 2, 'title' => 'GitHub Repositories', 'description' => 'Source code, eksperimen open-source & kontribusi', 'url' => 'https://github.com', 'icon' => 'github', 'is_active' => 1, 'sort_order' => 2],
                    ['id' => 3, 'title' => 'Hubungi via WhatsApp', 'description' => '+62 812-3456-7890 (Tanya kerja sama & respon cepat)', 'url' => 'https://wa.me/6281234567890?text=Halo,%20saya%20menghubungi%20lewat%20website%20portal', 'icon' => 'whatsapp', 'is_active' => 1, 'sort_order' => 3],
                    ['id' => 4, 'title' => 'Kirim Email Langsung', 'description' => 'Kontak formal untuk penawaran proyek & konsultasi', 'url' => 'mailto:yourname@example.com', 'icon' => 'mail', 'is_active' => 1, 'sort_order' => 4],
                ],
                'apps' => [
                    ['id' => 1, 'name' => 'CV AI Generator', 'category' => 'Artificial Intelligence', 'description' => 'Aplikasi pembuat resume profesional berbasis AI dengan analisis skor lolos ATS otomatis.', 'url' => 'https://example.com/cv-ai', 'badge' => 'Live', 'icon' => 'sparkles', 'is_active' => 1, 'sort_order' => 1],
                    ['id' => 2, 'name' => 'CuacaAI Monitoring', 'category' => 'Data & Forecasting', 'description' => 'Dashboard prakiraan cuaca satelit presisi tinggi dengan integrasi open data.', 'url' => 'https://example.com/cuaca-ai', 'badge' => 'Live', 'icon' => 'cloud', 'is_active' => 1, 'sort_order' => 2],
                    ['id' => 3, 'name' => 'Sistem Pendukung Keputusan Desa', 'category' => 'Web App', 'description' => 'Platform transparansi evaluasi program desa menggunakan metode multi-kriteria.', 'url' => 'https://example.com/spk-desa', 'badge' => 'Live', 'icon' => 'layout-grid', 'is_active' => 1, 'sort_order' => 3],
                    ['id' => 4, 'name' => 'DSS RK Business Analytics', 'category' => 'Enterprise System', 'description' => 'Sistem pendukung keputusan analitik penjualan dan peramalan inventori stok.', 'url' => 'https://example.com/dss-rk', 'badge' => 'Dev', 'icon' => 'bar-chart', 'is_active' => 1, 'sort_order' => 4],
                ]
            ];
            file_put_contents(JSON_FILE, json_encode($initial, JSON_PRETTY_PRINT));
        }
    }

    // --- API HELPER METHODS ---

    public static function getSettings() {
        if (self::$useSqlite) {
            $stmt = self::$pdo->query("SELECT key, value FROM settings");
            $res = [];
            while ($row = $stmt->fetch()) {
                $res[$row['key']] = $row['value'];
            }
            return $res;
        } else {
            $data = json_decode(file_get_contents(JSON_FILE), true);
            return $data['settings'] ?? [];
        }
    }

    public static function updateSettings($settings) {
        if (self::$useSqlite) {
            $stmt = self::$pdo->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES (:key, :value)");
            foreach ($settings as $k => $v) {
                $stmt->execute([':key' => $k, ':value' => $v]);
            }
        } else {
            $data = json_decode(file_get_contents(JSON_FILE), true);
            foreach ($settings as $k => $v) {
                $data['settings'][$k] = $v;
            }
            file_put_contents(JSON_FILE, json_encode($data, JSON_PRETTY_PRINT));
        }
    }

    public static function getLinks($onlyActive = true) {
        if (self::$useSqlite) {
            $sql = "SELECT * FROM links " . ($onlyActive ? "WHERE is_active = 1 " : "") . "ORDER BY sort_order ASC, id ASC";
            return self::$pdo->query($sql)->fetchAll();
        } else {
            $data = json_decode(file_get_contents(JSON_FILE), true);
            $links = $data['links'] ?? [];
            if ($onlyActive) {
                $links = array_filter($links, fn($item) => !empty($item['is_active']));
            }
            usort($links, fn($a, $b) => ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0));
            return array_values($links);
        }
    }

    public static function saveLink($data, $id = null) {
        if (self::$useSqlite) {
            if ($id) {
                $stmt = self::$pdo->prepare("UPDATE links SET title = :title, description = :description, url = :url, icon = :icon, is_active = :is_active, sort_order = :sort_order WHERE id = :id");
                $data['id'] = $id;
                $stmt->execute($data);
            } else {
                $stmt = self::$pdo->prepare("INSERT INTO links (title, description, url, icon, is_active, sort_order) VALUES (:title, :description, :url, :icon, :is_active, :sort_order)");
                $stmt->execute($data);
            }
        } else {
            $json = json_decode(file_get_contents(JSON_FILE), true);
            if ($id) {
                foreach ($json['links'] as &$link) {
                    if ($link['id'] == $id) {
                        $link = array_merge($link, $data, ['id' => (int)$id]);
                        break;
                    }
                }
            } else {
                $nextId = empty($json['links']) ? 1 : max(array_column($json['links'], 'id')) + 1;
                $data['id'] = $nextId;
                $json['links'][] = $data;
            }
            file_put_contents(JSON_FILE, json_encode($json, JSON_PRETTY_PRINT));
        }
    }

    public static function deleteLink($id) {
        if (self::$useSqlite) {
            $stmt = self::$pdo->prepare("DELETE FROM links WHERE id = :id");
            $stmt->execute([':id' => $id]);
        } else {
            $json = json_decode(file_get_contents(JSON_FILE), true);
            $json['links'] = array_values(array_filter($json['links'], fn($l) => $l['id'] != $id));
            file_put_contents(JSON_FILE, json_encode($json, JSON_PRETTY_PRINT));
        }
    }

    public static function getApps($onlyActive = true) {
        if (self::$useSqlite) {
            $sql = "SELECT * FROM apps " . ($onlyActive ? "WHERE is_active = 1 " : "") . "ORDER BY sort_order ASC, id ASC";
            return self::$pdo->query($sql)->fetchAll();
        } else {
            $data = json_decode(file_get_contents(JSON_FILE), true);
            $apps = $data['apps'] ?? [];
            if ($onlyActive) {
                $apps = array_filter($apps, fn($item) => !empty($item['is_active']));
            }
            usort($apps, fn($a, $b) => ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0));
            return array_values($apps);
        }
    }

    public static function saveApp($data, $id = null) {
        if (self::$useSqlite) {
            if ($id) {
                $stmt = self::$pdo->prepare("UPDATE apps SET name = :name, category = :category, description = :description, url = :url, badge = :badge, icon = :icon, is_active = :is_active, sort_order = :sort_order WHERE id = :id");
                $data['id'] = $id;
                $stmt->execute($data);
            } else {
                $stmt = self::$pdo->prepare("INSERT INTO apps (name, category, description, url, badge, icon, is_active, sort_order) VALUES (:name, :category, :description, :url, :badge, :icon, :sort_order)");
                $stmt->execute($data);
            }
        } else {
            $json = json_decode(file_get_contents(JSON_FILE), true);
            if ($id) {
                foreach ($json['apps'] as &$app) {
                    if ($app['id'] == $id) {
                        $app = array_merge($app, $data, ['id' => (int)$id]);
                        break;
                    }
                }
            } else {
                $nextId = empty($json['apps']) ? 1 : max(array_column($json['apps'], 'id')) + 1;
                $data['id'] = $nextId;
                $json['apps'][] = $data;
            }
            file_put_contents(JSON_FILE, json_encode($json, JSON_PRETTY_PRINT));
        }
    }

    public static function deleteApp($id) {
        if (self::$useSqlite) {
            $stmt = self::$pdo->prepare("DELETE FROM apps WHERE id = :id");
            $stmt->execute([':id' => $id]);
        } else {
            $json = json_decode(file_get_contents(JSON_FILE), true);
            $json['apps'] = array_values(array_filter($json['apps'], fn($a) => $a['id'] != $id));
            file_put_contents(JSON_FILE, json_encode($json, JSON_PRETTY_PRINT));
        }
    }
}

// Inisialisasi otomatis
Storage::init();
