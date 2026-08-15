<?php
$filepath = 'c:\laragon\www\helix\resources\views\pages\keranjang.blade.php';
$content = file_get_contents($filepath);

// 1. Text Gradients
$content = str_replace(
    '<span class="text-blue-600 italic">Belanja.</span>',
    '<span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 via-blue-500 to-orange-500 italic">Belanja.</span>',
    $content
);

$content = str_replace(
    'text-blue-600 tracking-tighter',
    'text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 via-blue-500 to-orange-500 tracking-tighter',
    $content
);

// 2. Buttons background and shadows
$content = str_replace(
    'hover:bg-blue-600',
    'hover:bg-gradient-to-r hover:from-emerald-500 hover:via-blue-500 hover:to-orange-500',
    $content
);

$content = str_replace(
    'bg-blue-600',
    'bg-gradient-to-r from-emerald-500 via-blue-500 to-orange-500',
    $content
);

// Checkbox selection
$content = str_replace(
    'peer-checked:bg-blue-600 peer-checked:border-blue-600',
    'peer-checked:bg-gradient-to-r peer-checked:from-emerald-500 peer-checked:via-blue-500 peer-checked:to-orange-500 peer-checked:border-emerald-500',
    $content
);

// Shadows
$content = str_replace('hover:shadow-blue-500', 'hover:shadow-emerald-500', $content);
$content = str_replace('shadow-blue-500', 'shadow-emerald-500', $content);

// Small icons
$content = str_replace('text-blue-500', 'text-emerald-500', $content);

file_put_contents($filepath, $content);
echo "Colors updated in keranjang.blade.php";
