<?php
$file = 'c:\laragon\www\helix\resources\views\partials\navbar.blade.php';
$content = file_get_contents($file);

$replacements = [
    'blue-800' => 'emerald-800',
    'blue-700' => 'emerald-700',
    'blue-600' => 'emerald-600',
    'blue-500' => 'emerald-500',
    'blue-400' => 'emerald-400',
    'rgba(37,99,235,' => 'rgba(5,150,105,', // blue-600 rgb to emerald-600 rgb
    'from-blue-600 to-emerald-500' => 'from-emerald-600 to-teal-500', // Logo gradient
    'from-blue-600 to-indigo-600' => 'from-emerald-600 to-teal-600', // Admin button
    'hover:from-blue-500 hover:to-indigo-500' => 'hover:from-emerald-500 hover:to-teal-500'
];

foreach ($replacements as $search => $replace) {
    $content = str_replace($search, $replace, $content);
}

file_put_contents($file, $content);
echo "Colors updated in navbar.blade.php";
?>
