<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$categories = [
    ['nama_kategori' => 'Makanan & Minuman', 'deskripsi' => 'Kuliner dan jajanan lokal', 'icon_class' => 'fas fa-utensils'],
    ['nama_kategori' => 'Pakaian & Fashion', 'deskripsi' => 'Busana dan aksesoris', 'icon_class' => 'fas fa-tshirt'],
    ['nama_kategori' => 'Kerajinan Tangan', 'deskripsi' => 'Karya seni dan kriya', 'icon_class' => 'fas fa-paint-brush'],
    ['nama_kategori' => 'Oleh-Oleh Khas', 'deskripsi' => 'Buah tangan khas daerah', 'icon_class' => 'fas fa-gift'],
    ['nama_kategori' => 'Perlengkapan Rumah', 'deskripsi' => 'Dekorasi dan perabotan', 'icon_class' => 'fas fa-home'],
    ['nama_kategori' => 'Kesehatan & Kecantikan', 'deskripsi' => 'Perawatan dan herbal', 'icon_class' => 'fas fa-heart']
];

foreach ($categories as $cat) {
    Illuminate\Support\Facades\DB::table('tb_kategori')->insert($cat);
}

echo "Kategori berhasil ditambahkan.\n";
