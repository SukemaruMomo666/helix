<?php
use Illuminate\Support\Facades\DB;
$shops = DB::table('tb_toko')->get(); 
foreach($shops as $shop) { 
    $newName = str_ireplace(['TB.', 'TB ', 'Toko Pusaka Baja'], ['UMKM ', 'UMKM ', 'Pusat Oleh-oleh'], $shop->nama_toko); 
    DB::table('tb_toko')->where('id', $shop->id)->update(['nama_toko' => $newName]); 
}
echo "Toko updated";
