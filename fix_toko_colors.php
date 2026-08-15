<?php
$file = 'c:\laragon\www\helix\resources\views\pages\detail_toko.blade.php';

if (file_exists($file)) {
    $content = file_get_contents($file);
    
    // Replace inline toko tailwind color from rose to emerald
    $content = str_replace(
        "toko: { 50: '#fff1f2', 100: '#ffe4e6', 500: '#f43f5e', 600: '#e11d48', 700: '#be123c' }",
        "toko: { 50: '#ecfdf5', 100: '#d1fae5', 500: '#10b981', 600: '#059669', 700: '#047857' }",
        $content
    );
    
    // Replace hardcoded pagination color
    $content = str_replace(
        "background: #e11d48; color: white; border-color: #e11d48;",
        "background: #059669; color: white; border-color: #059669;",
        $content
    );
    
    // Replace sweetalert confirm color
    $content = str_replace(
        "confirmButtonColor: '#e11d48'",
        "confirmButtonColor: '#059669'",
        $content
    );
    
    // Replace blue buttons with emerald
    $content = str_replace(
        "bg-blue-600 text-white font-bold px-8 py-2.5 rounded-[4px] hover:bg-blue-700",
        "bg-emerald-600 text-white font-bold px-8 py-2.5 rounded-[4px] hover:bg-emerald-700",
        $content
    );
    
    file_put_contents($file, $content);
    echo "Updated detail_toko colors to Helix Emerald\n";
}
?>
