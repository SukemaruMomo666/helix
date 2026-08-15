<?php
$files = [
    'c:\laragon\www\helix\resources\views\auth\login_customer.blade.php',
    'c:\laragon\www\helix\resources\views\auth\register_customer.blade.php'
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    
    $content = file_get_contents($file);

    // Common gradient colors
    $content = str_replace(
        'from-blue-400 to-indigo-400',
        'from-emerald-400 via-blue-400 to-orange-400',
        $content
    );
    $content = str_replace(
        'from-blue-500 to-indigo-600',
        'from-emerald-500 via-blue-500 to-orange-500',
        $content
    );

    // Button colors (hover and bg)
    $content = str_replace(
        'bg-blue-600',
        'bg-gradient-to-r from-emerald-500 via-blue-500 to-orange-500',
        $content
    );
    $content = str_replace(
        'hover:bg-blue-700',
        'hover:brightness-110',
        $content
    );
    $content = str_replace(
        'text-blue-600',
        'text-emerald-600',
        $content
    );
    
    // Focus states
    $content = str_replace('focus:border-blue-600', 'focus:border-emerald-500', $content);
    $content = str_replace('focus:ring-blue-600/10', 'focus:ring-emerald-500/10', $content);
    $content = str_replace('focus:ring-blue-600/20', 'focus:ring-emerald-500/20', $content);
    $content = str_replace('group-focus-within:text-blue-600', 'group-focus-within:text-emerald-500', $content);

    // Text replacements - Login
    $content = str_replace('Suplai Proyek Anda.', 'Pusat Produk UMKM.', $content);
    $content = str_replace(
        'Akses ribuan material dari distributor terpercaya. Manajemen RAB, lacak pesanan, dan dapatkan harga khusus B2B dalam satu dashboard pintar.',
        'Akses ribuan produk dari UMKM terpercaya. Belanja aman, cepat, dan dukung usaha lokal dalam satu platform pintar.',
        $content
    );
    $content = str_replace('Belum punya akun B2B?', 'Belum punya akun?', $content);

    // Text replacements - Register
    $content = str_replace('Mulailah Bangun', 'Mulailah Jelajahi', $content);
    $content = str_replace('Kerajaan Anda.', 'Produk Lokal Terbaik.', $content);
    $content = str_replace(
        'Bergabung dengan ratusan kontraktor dan pemilik proyek yang telah mempercayakan suplai material mereka melalui ekosistem digital kami.',
        'Bergabung dengan ratusan UMKM dan pembeli yang telah mempercayakan transaksi mereka melalui ekosistem digital kami.',
        $content
    );
    $content = str_replace('Harga Spesial B2B', 'Harga Terbaik', $content);
    $content = str_replace('Sudah punya akun B2B?', 'Sudah punya akun?', $content);

    // Syarat dan ketentuan text in register
    $content = str_replace(
        'Pembeli wajib memastikan akses jalan menuju lokasi proyek',
        'Pembeli wajib memastikan akses jalan menuju lokasi pengiriman',
        $content
    );
    $content = str_replace(
        'sejak barang tiba di lokasi proyek',
        'sejak barang tiba di lokasi pengiriman',
        $content
    );
    
    file_put_contents($file, $content);
}

echo "Auth views updated.";
