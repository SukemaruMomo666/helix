<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$barang = Illuminate\Support\Facades\DB::table('tb_barang')->whereNull('slug')->orWhere('slug', '')->get();
foreach ($barang as $b) {
    $slug = Illuminate\Support\Str::slug($b->nama_barang . '-' . uniqid());
    Illuminate\Support\Facades\DB::table('tb_barang')->where('id', $b->id)->update(['slug' => $slug]);
}
echo "Slugs updated.\n";
