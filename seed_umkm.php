<?php
use Illuminate\Support\Facades\DB;

$categories = [
    ['nama' => 'Makanan Ringan', 'icon' => 'fas fa-cookie'],
    ['nama' => 'Oleh-oleh Khas', 'icon' => 'fas fa-gift'],
    ['nama' => 'Kerajinan Tangan', 'icon' => 'fas fa-hand-sparkles'],
    ['nama' => 'Pakaian & Batik', 'icon' => 'fas fa-tshirt'],
    ['nama' => 'Minuman Tradisional', 'icon' => 'fas fa-coffee'],
    ['nama' => 'Aksesoris Etnik', 'icon' => 'fas fa-gem']
];

$kategoriList = DB::table('tb_kategori')->get();
$idx = 0;
foreach($kategoriList as $kat) {
    $c = $categories[$idx % count($categories)];
    DB::table('tb_kategori')->where('id', $kat->id)->update([
        'nama_kategori' => $c['nama'] . ' ' . rand(1,99), 
        'deskripsi' => 'Kategori produk ' . $c['nama'],
        'icon_class' => $c['icon']
    ]);
    $idx++;
}

// Clean up first 6 categories specifically
DB::statement("UPDATE tb_kategori SET nama_kategori = 'Keripik & Kerupuk', icon_class='fas fa-cookie-bite' WHERE id=1");
DB::statement("UPDATE tb_kategori SET nama_kategori = 'Minuman Tradisional', icon_class='fas fa-coffee' WHERE id=2");
DB::statement("UPDATE tb_kategori SET nama_kategori = 'Kerajinan Bambu', icon_class='fas fa-tree' WHERE id=3");
DB::statement("UPDATE tb_kategori SET nama_kategori = 'Batik Sadawarna', icon_class='fas fa-tshirt' WHERE id=4");
DB::statement("UPDATE tb_kategori SET nama_kategori = 'Kue Kering', icon_class='fas fa-cookie' WHERE id=5");
DB::statement("UPDATE tb_kategori SET nama_kategori = 'Aksesoris Lokal', icon_class='fas fa-ring' WHERE id=6");

$products = [
    "Keripik Pisang Kepok Manis", "Kerupuk Kulit Sapi Asli", "Kue Bolu Susu Lembang", "Kopi Robusta Asli Pegunungan",
    "Jamu Kunyit Asam Segar", "Tampah Bambu Tradisional", "Patung Garuda Kayu Jati", "Batik Tulis Motif Mega Mendung",
    "Tas Selempang Etnik Baduy", "Kalung Manik-manik Cantik", "Dodol Nanas Subang", "Sale Pisang Ambon Asli",
    "Gula Aren Asli Organik", "Madu Hutan Liar Sumbawa", "Teh Rosella Segar", "Wedang Uwuh Siap Seduh",
    "Lumpia Basah Khas Bandung", "Kerupuk Udang Sidoarjo", "Sambal Roa Khas Manado", "Kue Nastar Nanas Subang",
    "Kaos Oleh-oleh Sadawarna", "Topi Anyaman Pandan", "Syal Tenun Ikat", "Dompet Kulit Pari Asli"
];

$barangList = DB::table('tb_barang')->get();
$idx = 0;
foreach($barangList as $brg) {
    $pName = $products[$idx % count($products)];
    $price = rand(10, 150) * 1000;
    DB::table('tb_barang')->where('id', $brg->id)->update([
        'nama_barang' => $pName . ' Premium',
        'harga' => $price,
        'satuan_unit' => 'Pcs',
        'kategori_id' => rand(1, 6)
    ]);
    $idx++;
}
echo "Database updated to UMKM!";
