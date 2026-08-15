<?php
/**
 * ============================================================
 * JEMBATAN SYMLINK HOSTINGER - PONDASIKITA
 * ============================================================
 * Taruh file ini di folder PUBLIC milik CUSTOMER (www.pondasikita.com/public_html)
 * Jalankan 1x saja via browser: https://www.pondasikita.com/setup_symlink.php
 * Setelah berhasil, HAPUS file ini dari server!
 * ============================================================
 */

echo "<pre style='font-family:monospace; background:#0f172a; color:#e2e8f0; padding:30px; border-radius:12px;'>";
echo "========================================\n";
echo "  PONDASIKITA - SETUP SYMLINK HOSTINGER\n";
echo "========================================\n\n";

// 1. Deteksi path
$public_customer = __DIR__;
$domains_folder = dirname(dirname(dirname($public_customer)));

// Cari folder public milik seller
$possible_seller_paths = [
    $domains_folder . '/seller.pondasikita.com/public_html/public',
    $domains_folder . '/seller.pondasikita.com/public_html',
];

$public_seller = null;
foreach ($possible_seller_paths as $path) {
    if (file_exists($path)) {
        $public_seller = $path;
        break;
    }
}

if (!$public_seller) {
    echo "GAGAL: Folder seller tidak ditemukan!\n";
    echo "Path yang dicoba:\n";
    foreach ($possible_seller_paths as $p) {
        echo "  - $p\n";
    }
    echo "</pre>";
    exit;
}

echo "Folder Customer : $public_customer\n";
echo "Folder Seller   : $public_seller\n\n";

// 2. Daftar semua folder yang perlu di-symlink
$folders = [
    'assets/uploads/products'   => 'Gambar Produk',
    'assets/uploads/logos'      => 'Logo Toko',
    'assets/uploads/banners'    => 'Banner Toko',
    'assets/uploads/legalitas'  => 'Dokumen Legalitas',
];

$success = 0;
$failed = 0;
$skipped = 0;

foreach ($folders as $relative_path => $label) {
    $sumber = $public_seller . '/' . $relative_path;
    $tujuan = $public_customer . '/' . $relative_path;

    echo "-------------------------------------------\n";
    echo "  $label ($relative_path)\n";

    // Cek apakah folder sumber (seller) ada
    if (!file_exists($sumber)) {
        echo "   Folder sumber belum ada, membuat...\n";
        @mkdir($sumber, 0777, true);
    }

    // Jika sudah berupa symlink, skip
    if (is_link($tujuan)) {
        $target = readlink($tujuan);
        echo "   Sudah symlink -> $target\n";
        $skipped++;
        continue;
    }

    // Jika folder asli sudah ada, rename sebagai backup + migrasi file
    if (file_exists($tujuan) && is_dir($tujuan)) {
        $backup = $tujuan . '_backup_' . date('Ymd_His');
        echo "   Backup folder lama -> $backup\n";

        // Pindahkan file yang ada di customer ke seller (merge)
        $files = glob($tujuan . '/*');
        foreach ($files as $file) {
            $filename = basename($file);
            $dest = $sumber . '/' . $filename;
            if (!file_exists($dest)) {
                copy($file, $dest);
                echo "   Migrasi: $filename\n";
            }
        }

        rename($tujuan, $backup);
    }

    // Pastikan parent directory ada
    $parentDir = dirname($tujuan);
    if (!file_exists($parentDir)) {
        @mkdir($parentDir, 0777, true);
    }

    // Buat symlink
    if (@symlink($sumber, $tujuan)) {
        echo "   BERHASIL! Symlink dibuat.\n";
        $success++;
    } else {
        echo "   GAGAL membuat symlink.\n";
        $failed++;
    }
}

echo "\n-------------------------------------------\n";
echo "HASIL: $success berhasil | $skipped sudah ada | $failed gagal\n";

if ($success > 0 || $skipped > 0) {
    echo "\nSELESAI! Sekarang semua file upload dari seller\n";
    echo "akan otomatis terlihat di sisi customer.\n";
    echo "\nPENTING: HAPUS FILE INI DARI SERVER SETELAH SELESAI!\n";
}

echo "</pre>";
