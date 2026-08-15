<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    // Find the first admin user
    $admin = DB::table('tb_user')->where('level', 'admin')->first();
    
    // Fallback if no level=admin
    if (!$admin) {
        $admin = DB::table('tb_user')->where('username', 'admin')->first();
    }
    if (!$admin) {
        $admin = DB::table('tb_user')->first(); // Just save the first user as admin fallback
    }

    $tablesToTruncate = [
        'tb_banding_akun',
        'tb_barang',
        'tb_barang_variasi',
        'tb_biaya_pengiriman',
        'tb_detail_transaksi',
        'tb_flash_sale_events',
        'tb_flash_sale_produk',
        'tb_gambar_barang',
        // 'tb_kategori', // Might want to keep categories? Let's truncate if they said "semuanya" but usually categories are master data. Let's truncate to be safe with "semuanya" or keep? I'll truncate.
        'tb_kategori',
        'tb_keranjang',
        'tb_komisi',
        'tb_komplain',
        'tb_kurir_toko',
        'tb_mutasi_saldo',
        'tb_payouts',
        'tb_pengajuan_tier',
        // 'tb_pengaturan', // Keep settings
        'tb_review_produk',
        'tb_stok_histori',
        'tb_toko',
        'tb_toko_dekorasi',
        'tb_toko_follower',
        'tb_toko_jam_operasional',
        'tb_toko_pengaturan',
        'tb_toko_review',
        'tb_transaksi',
        'tb_user_alamat',
        'tb_zona_pengiriman',
        'vouchers',
        'sessions',
        'chats',
        'messages',
        'notifications',
    ];

    foreach ($tablesToTruncate as $table) {
        if (Schema::hasTable($table)) {
            DB::table($table)->truncate();
        }
    }

    // Now for tb_user, we truncate and re-insert the admin
    if (Schema::hasTable('tb_user')) {
        DB::table('tb_user')->truncate();
        if ($admin) {
            DB::table('tb_user')->insert((array) $admin);
        } else {
            // Create a default admin if none existed
            DB::table('tb_user')->insert([
                'username' => 'admin',
                'password' => bcrypt('password'), // default password
                'nama' => 'Administrator',
                'email' => 'admin@admin.com',
                'level' => 'admin',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    echo "Database cleared, left 1 admin.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
