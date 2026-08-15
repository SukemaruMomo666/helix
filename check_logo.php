<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$toko = Illuminate\Support\Facades\DB::table('tb_toko')->where('nama_toko', 'like', '%QISA%')->first();
echo "Nama Toko: " . $toko->nama_toko . "\n";
echo "Logo Toko: " . $toko->logo_toko . "\n";
