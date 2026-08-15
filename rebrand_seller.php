<?php

$dirs = [
    'c:\laragon\www\helix\resources\views\seller',
    'c:\laragon\www\helix\resources\views\auth'
];

$replacements = [
    'Pondasikita' => 'Helix',
    'pondasikita' => 'helix',
    'Toko Bangunan' => 'Toko UMKM',
    'toko bangunan' => 'toko UMKM',
    'Material' => 'Produk',
    'material' => 'produk',
    'B2B' => 'UMKM',
    'Proyek' => 'Pesanan',
    'proyek' => 'pesanan',
    'logopondasikita.png' => 'logohelix.png',
    
    // Gradients & Colors
    'bg-blue-600' => 'bg-gradient-to-r from-emerald-600 via-blue-600 to-orange-500',
    'bg-indigo-600' => 'bg-gradient-to-r from-emerald-600 via-blue-600 to-orange-500',
    'hover:bg-blue-700' => 'hover:from-emerald-700 hover:via-blue-700 hover:to-orange-600',
    'hover:bg-indigo-700' => 'hover:from-emerald-700 hover:via-blue-700 hover:to-orange-600',
    'text-blue-600' => 'text-emerald-600',
    'text-indigo-600' => 'text-emerald-600',
    'dark:text-blue-400' => 'dark:text-emerald-400',
    'dark:text-indigo-400' => 'dark:text-emerald-400',
    'border-blue-500' => 'border-emerald-500',
    'border-indigo-500' => 'border-emerald-500',
    'ring-blue-500' => 'ring-emerald-500',
    'focus:border-blue-500' => 'focus:border-emerald-500',
    'focus:ring-blue-500' => 'focus:ring-emerald-500',
    'bg-blue-50' => 'bg-emerald-50',
    'text-blue-500' => 'text-emerald-500',
];

foreach ($dirs as $directory) {
    $dir = new RecursiveDirectoryIterator($directory);
    $iterator = new RecursiveIteratorIterator($dir);
    $files = new RegexIterator($iterator, '/^.+\.blade\.php$/i', RecursiveRegexIterator::GET_MATCH);

    foreach ($files as $file) {
        $filePath = $file[0];
        // skip if it's customer login/register we already painstakingly fixed
        if (strpos($filePath, 'login_customer') !== false || strpos($filePath, 'register_customer') !== false) {
            continue;
        }

        $content = file_get_contents($filePath);
        $original = $content;
        
        foreach ($replacements as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }
        
        if ($content !== $original) {
            file_put_contents($filePath, $content);
            echo "Updated: $filePath\n";
        }
    }
}

echo "Seller rebranding completed.\n";
