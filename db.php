<?php
/**
 * Database & Data Storage Engine
 * Mendukung SQLite (PDO) dengan auto-fallback ke JSON file.
 */

define('DATA_DIR', __DIR__ . '/data');
define('UPLOADS_DIR', __DIR__ . '/uploads');
define('SQLITE_FILE', DATA_DIR . '/database.sqlite');
define('JSON_FILE', DATA_DIR . '/data.json');

if (!is_dir(DATA_DIR)) {
    @mkdir(DATA_DIR, 0777, true);
}
if (!is_dir(UPLOADS_DIR)) {
    @mkdir(UPLOADS_DIR, 0777, true);
}

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
        self::$pdo->exec("CREATE TABLE IF NOT EXISTS settings (
            key TEXT PRIMARY KEY,
            value TEXT
        )");

        self::$pdo->exec("CREATE TABLE IF NOT EXISTS links (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT,
            url TEXT NOT NULL,
            icon TEXT DEFAULT 'link',
            is_active INTEGER DEFAULT 1,
            sort_order INTEGER DEFAULT 0
        )");

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

        self::$pdo->exec("CREATE TABLE IF NOT EXISTS socials (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            url TEXT NOT NULL,
            icon TEXT DEFAULT 'globe',
            is_active INTEGER DEFAULT 1,
            sort_order INTEGER DEFAULT 0
        )");

        // Auto-aktifkan sosmed yang sebelumnya tersimpan nonaktif
        self::$pdo->exec("UPDATE socials SET is_active = 1 WHERE is_active = 0");

        $stmt = self::$pdo->query("SELECT COUNT(*) as cnt FROM settings");
        if ($stmt->fetch()['cnt'] == 0) {
            self::seedInitialData();
        }
    }

    private static function seedInitialData() {
        $defaultPassword = password_hash('admin123', PASSWORD_BCRYPT);
        $settings = [
            'admin_password' => $defaultPassword,
            'name' => 'Raka Adiputra, S.Kom.',
            'title' => 'Full-Stack Developer',
            'bio' => 'Saya merancang dan membangun produk digital end-to-end — dari arsitektur backend sampai detail antarmuka. Terbuka untuk kolaborasi jangka panjang.',
            'location' => 'Jakarta, Indonesia • GMT+7',
            'status_text' => 'Available for Projects',
            'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&auto=format&fit=crop&q=80',
            'domain_badge' => 'Available for Projects'
        ];

        foreach ($settings as $k => $v) {
            $stmt = self::$pdo->prepare("INSERT INTO settings (key, value) VALUES (:k, :v)");
            $stmt->execute([':k' => $k, ':v' => $v]);
        }

        $links = [
            ['title' => 'WhatsApp', 'description' => 'Chat langsung — respon cepat', 'url' => 'https://wa.me/6281234567890?text=Halo%20Raka,%20saya%20tertarik%20bekerja%20sama', 'icon' => 'whatsapp', 'sort_order' => 1],
            ['title' => 'LinkedIn', 'description' => 'Profil profesional & pengalaman', 'url' => 'https://linkedin.com', 'icon' => 'linkedin', 'sort_order' => 2],
            ['title' => 'GitHub Repositories', 'description' => 'Kode sumber & eksperimen terbuka', 'url' => 'https://github.com', 'icon' => 'github', 'sort_order' => 3],
            ['title' => 'Credly Certificates', 'description' => 'Sertifikat & badge terverifikasi', 'url' => 'https://credly.com', 'icon' => 'award', 'sort_order' => 4],
            ['title' => 'Email', 'description' => 'raka@adiputra.dev', 'url' => 'mailto:raka@adiputra.dev', 'icon' => 'mail', 'sort_order' => 5],
        ];

        foreach ($links as $l) {
            $stmt = self::$pdo->prepare("INSERT INTO links (title, description, url, icon, sort_order) VALUES (:title, :description, :url, :icon, :sort_order)");
            $stmt->execute($l);
        }

        $apps = [
            ['name' => 'KasirKu', 'category' => 'POS & Kasir', 'description' => 'Sistem kasir multi-outlet dengan laporan penjualan real-time.', 'url' => 'https://example.com/kasirku', 'badge' => 'Live', 'icon' => 'store', 'sort_order' => 1],
            ['name' => 'SiHadir', 'category' => 'HR & Absensi', 'description' => 'Absensi QR + geofencing untuk 200+ karyawan lapangan.', 'url' => 'https://example.com/sihadir', 'badge' => 'Live', 'icon' => 'qr-code', 'sort_order' => 2],
            ['name' => 'JurnalTim', 'category' => 'SaaS & Kolaborasi', 'description' => 'Manajemen proyek ringan dengan kanban dan timeline.', 'url' => 'https://example.com/jurnaltim', 'badge' => 'Live', 'icon' => 'kanban', 'sort_order' => 3],
            ['name' => 'TokoKita', 'category' => 'E-commerce', 'description' => 'Storefront headless dengan checkout satu halaman.', 'url' => 'https://example.com/tokokita', 'badge' => 'Live', 'icon' => 'shopping-bag', 'sort_order' => 4],
        ];

        foreach ($apps as $a) {
            $stmt = self::$pdo->prepare("INSERT INTO apps (name, category, description, url, badge, icon, sort_order) VALUES (:name, :category, :description, :url, :badge, :icon, :sort_order)");
            $stmt->execute($a);
        }

        $socials = [
            ['title' => 'Instagram', 'url' => 'https://instagram.com', 'icon' => 'instagram', 'sort_order' => 1],
            ['title' => 'GitHub', 'url' => 'https://github.com', 'icon' => 'github', 'sort_order' => 2],
            ['title' => 'LinkedIn', 'url' => 'https://linkedin.com', 'icon' => 'linkedin', 'sort_order' => 3],
            ['title' => 'YouTube', 'url' => 'https://youtube.com', 'icon' => 'youtube', 'sort_order' => 4],
            ['title' => 'TikTok', 'url' => 'https://tiktok.com', 'icon' => 'tiktok', 'sort_order' => 5],
        ];

        foreach ($socials as $s) {
            $stmt = self::$pdo->prepare("INSERT INTO socials (title, url, icon, sort_order) VALUES (:title, :url, :icon, :sort_order)");
            $stmt->execute($s);
        }
    }

    private static function initJson() {
        if (!file_exists(JSON_FILE)) {
            $defaultPassword = password_hash('admin123', PASSWORD_BCRYPT);
            $initial = [
                'settings' => [
                    'admin_password' => $defaultPassword,
                    'name' => 'Raka Adiputra, S.Kom.',
                    'title' => 'Full-Stack Developer',
                    'bio' => 'Saya merancang dan membangun produk digital end-to-end — dari arsitektur backend sampai detail antarmuka. Terbuka untuk kolaborasi jangka panjang.',
                    'location' => 'Jakarta, Indonesia • GMT+7',
                    'status_text' => 'Available for Projects',
                    'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&auto=format&fit=crop&q=80',
                    'domain_badge' => 'Available for Projects'
                ],
                'links' => [
                    ['id' => 1, 'title' => 'WhatsApp', 'description' => 'Chat langsung — respon cepat', 'url' => 'https://wa.me/6281234567890?text=Halo%20Raka,%20saya%20tertarik%20bekerja%20sama', 'icon' => 'whatsapp', 'is_active' => 1, 'sort_order' => 1],
                    ['id' => 2, 'title' => 'LinkedIn', 'description' => 'Profil profesional & pengalaman', 'url' => 'https://linkedin.com', 'icon' => 'linkedin', 'is_active' => 1, 'sort_order' => 2],
                    ['id' => 3, 'title' => 'GitHub Repositories', 'description' => 'Kode sumber & eksperimen terbuka', 'url' => 'https://github.com', 'icon' => 'github', 'is_active' => 1, 'sort_order' => 3],
                    ['id' => 4, 'title' => 'Credly Certificates', 'description' => 'Sertifikat & badge terverifikasi', 'url' => 'https://credly.com', 'icon' => 'award', 'is_active' => 1, 'sort_order' => 4],
                    ['id' => 5, 'title' => 'Email', 'description' => 'raka@adiputra.dev', 'url' => 'mailto:raka@adiputra.dev', 'icon' => 'mail', 'is_active' => 1, 'sort_order' => 5],
                ],
                'apps' => [
                    ['id' => 1, 'name' => 'KasirKu', 'category' => 'POS & Kasir', 'description' => 'Sistem kasir multi-outlet dengan laporan penjualan real-time.', 'url' => 'https://example.com/kasirku', 'badge' => 'Live', 'icon' => 'store', 'is_active' => 1, 'sort_order' => 1],
                    ['id' => 2, 'name' => 'SiHadir', 'category' => 'HR & Absensi', 'description' => 'Absensi QR + geofencing untuk 200+ karyawan lapangan.', 'url' => 'https://example.com/sihadir', 'badge' => 'Live', 'icon' => 'qr-code', 'is_active' => 1, 'sort_order' => 2],
                    ['id' => 3, 'name' => 'JurnalTim', 'category' => 'SaaS & Kolaborasi', 'description' => 'Manajemen proyek ringan dengan kanban dan timeline.', 'url' => 'https://example.com/jurnaltim', 'badge' => 'Live', 'icon' => 'kanban', 'is_active' => 1, 'sort_order' => 3],
                    ['id' => 4, 'name' => 'TokoKita', 'category' => 'E-commerce', 'description' => 'Storefront headless dengan checkout satu halaman.', 'url' => 'https://example.com/tokokita', 'badge' => 'Live', 'icon' => 'shopping-bag', 'is_active' => 1, 'sort_order' => 4],
                ],
                'socials' => [
                    ['id' => 1, 'title' => 'Instagram', 'url' => 'https://instagram.com', 'icon' => 'instagram', 'is_active' => 1, 'sort_order' => 1],
                    ['id' => 2, 'title' => 'GitHub', 'url' => 'https://github.com', 'icon' => 'github', 'is_active' => 1, 'sort_order' => 2],
                    ['id' => 3, 'title' => 'LinkedIn', 'url' => 'https://linkedin.com', 'icon' => 'linkedin', 'is_active' => 1, 'sort_order' => 3],
                    ['id' => 4, 'title' => 'YouTube', 'url' => 'https://youtube.com', 'icon' => 'youtube', 'is_active' => 1, 'sort_order' => 4],
                    ['id' => 5, 'title' => 'TikTok', 'url' => 'https://tiktok.com', 'icon' => 'tiktok', 'is_active' => 1, 'sort_order' => 5],
                ]
            ];
            @file_put_contents(JSON_FILE, json_encode($initial, JSON_PRETTY_PRINT));
        }
    }

    public static function getSettings() {
        if (self::$useSqlite) {
            $stmt = self::$pdo->query("SELECT key, value FROM settings");
            $res = [];
            while ($row = $stmt->fetch()) {
                $res[$row['key']] = $row['value'];
            }
            return $res;
        } else {
            $data = json_decode(@file_get_contents(JSON_FILE), true);
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
            $data = json_decode(@file_get_contents(JSON_FILE), true);
            foreach ($settings as $k => $v) {
                $data['settings'][$k] = $v;
            }
            @file_put_contents(JSON_FILE, json_encode($data, JSON_PRETTY_PRINT));
        }
    }

    public static function getLinks($onlyActive = true) {
        if (self::$useSqlite) {
            $sql = "SELECT * FROM links " . ($onlyActive ? "WHERE is_active = 1 " : "") . "ORDER BY sort_order ASC, id ASC";
            return self::$pdo->query($sql)->fetchAll();
        } else {
            $data = json_decode(@file_get_contents(JSON_FILE), true);
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
            $json = json_decode(@file_get_contents(JSON_FILE), true);
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
            @file_put_contents(JSON_FILE, json_encode($json, JSON_PRETTY_PRINT));
        }
    }

    public static function deleteLink($id) {
        if (self::$useSqlite) {
            $stmt = self::$pdo->prepare("DELETE FROM links WHERE id = :id");
            $stmt->execute([':id' => $id]);
        } else {
            $json = json_decode(@file_get_contents(JSON_FILE), true);
            $json['links'] = array_values(array_filter($json['links'], fn($l) => $l['id'] != $id));
            @file_put_contents(JSON_FILE, json_encode($json, JSON_PRETTY_PRINT));
        }
    }

    public static function getApps($onlyActive = true) {
        if (self::$useSqlite) {
            $sql = "SELECT * FROM apps " . ($onlyActive ? "WHERE is_active = 1 " : "") . "ORDER BY sort_order ASC, id ASC";
            return self::$pdo->query($sql)->fetchAll();
        } else {
            $data = json_decode(@file_get_contents(JSON_FILE), true);
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
            $json = json_decode(@file_get_contents(JSON_FILE), true);
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
            @file_put_contents(JSON_FILE, json_encode($json, JSON_PRETTY_PRINT));
        }
    }

    public static function deleteApp($id) {
        if (self::$useSqlite) {
            $stmt = self::$pdo->prepare("DELETE FROM apps WHERE id = :id");
            $stmt->execute([':id' => $id]);
        } else {
            $json = json_decode(@file_get_contents(JSON_FILE), true);
            $json['apps'] = array_values(array_filter($json['apps'], fn($a) => $a['id'] != $id));
            @file_put_contents(JSON_FILE, json_encode($json, JSON_PRETTY_PRINT));
        }
    }

    public static function getSocials($onlyActive = true) {
        if (self::$useSqlite) {
            $sql = "SELECT * FROM socials " . ($onlyActive ? "WHERE is_active = 1 " : "") . "ORDER BY sort_order ASC, id ASC";
            return self::$pdo->query($sql)->fetchAll();
        } else {
            $data = json_decode(@file_get_contents(JSON_FILE), true);
            $socials = $data['socials'] ?? [];
            foreach ($socials as &$soc) {
                if (!isset($soc['is_active']) || $soc['is_active'] === 0) {
                    $soc['is_active'] = 1;
                }
            }
            if ($onlyActive) {
                $socials = array_filter($socials, fn($item) => !empty($item['is_active']));
            }
            usort($socials, fn($a, $b) => ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0));
            return array_values($socials);
        }
    }

    public static function saveSocial($data, $id = null) {
        if (self::$useSqlite) {
            if ($id) {
                $stmt = self::$pdo->prepare("UPDATE socials SET title = :title, url = :url, icon = :icon, is_active = :is_active, sort_order = :sort_order WHERE id = :id");
                $data['id'] = $id;
                $stmt->execute($data);
            } else {
                $stmt = self::$pdo->prepare("INSERT INTO socials (title, url, icon, is_active, sort_order) VALUES (:title, :url, :icon, :is_active, :sort_order)");
                $stmt->execute($data);
            }
        } else {
            $json = json_decode(@file_get_contents(JSON_FILE), true);
            if ($id) {
                foreach ($json['socials'] as &$soc) {
                    if ($soc['id'] == $id) {
                        $soc = array_merge($soc, $data, ['id' => (int)$id]);
                        break;
                    }
                }
            } else {
                $nextId = empty($json['socials']) ? 1 : max(array_column($json['socials'], 'id')) + 1;
                $data['id'] = $nextId;
                $json['socials'][] = $data;
            }
            @file_put_contents(JSON_FILE, json_encode($json, JSON_PRETTY_PRINT));
        }
    }

    public static function deleteSocial($id) {
        if (self::$useSqlite) {
            $stmt = self::$pdo->prepare("DELETE FROM socials WHERE id = :id");
            $stmt->execute([':id' => $id]);
        } else {
            $json = json_decode(@file_get_contents(JSON_FILE), true);
            $json['socials'] = array_values(array_filter($json['socials'], fn($s) => $s['id'] != $id));
            @file_put_contents(JSON_FILE, json_encode($json, JSON_PRETTY_PRINT));
        }
    }
}

Storage::init();
