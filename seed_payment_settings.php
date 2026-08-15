<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$settings = [
    ['setting_nama' => 'payment_gateway', 'setting_nilai' => 'qris_dinamis'],
    ['setting_nama' => 'qris_statis_string', 'setting_nilai' => '00020101021126570011ID.DANA.WWW011893600915382779171902098277917190303UMI51440014ID.CO.QRIS.WWW0215ID10253728215000303UMI5204490053033605802ID5910Toko Nurul6013Kab. Karawang61054137363044AC9'],
    ['setting_nama' => 'admin_wa_number', 'setting_nilai' => '6281234567890'],
    ['setting_nama' => 'midtrans_server_key', 'setting_nilai' => ''],
    ['setting_nama' => 'midtrans_client_key', 'setting_nilai' => ''],
    ['setting_nama' => 'midtrans_is_production', 'setting_nilai' => '0'],
];

foreach ($settings as $setting) {
    if (!Illuminate\Support\Facades\DB::table('tb_pengaturan')->where('setting_nama', $setting['setting_nama'])->exists()) {
        Illuminate\Support\Facades\DB::table('tb_pengaturan')->insert($setting);
    }
}

echo "Settings seeded.\n";
