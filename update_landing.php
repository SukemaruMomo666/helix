<?php
$filepath = 'c:\laragon\www\helix\resources\views\landing.blade.php';
$content = file_get_contents($filepath);

// 1. Remove AOS attributes
$content = preg_replace('/data-aos="[^"]*"/', '', $content);
$content = preg_replace('/data-aos-[a-z]+="[^"]*"/', '', $content);

// 2. Remove animation classes
$classesToRemove = [
    'animate-blob',
    'animate-float',
    'animate-bounce',
    'animate-shimmer',
    'animate-pulse-glow',
    'animate-blink'
];
foreach ($classesToRemove as $cls) {
    $content = str_replace($cls, '', $content);
}
// For animate-pulse, be careful not to remove it if it's the only class, but it usually isn't. Let's just str_replace.
$content = str_replace('animate-pulse', '', $content);

// 3. Remove AOS scripts and CSS
$content = str_replace('<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">', '', $content);
$content = str_replace('<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>', '', $content);
$content = preg_replace('/AOS\.init\([^)]+\);/s', '', $content);

// 4. Update colors
$content = str_replace('bg-primary', 'bg-gradient-to-r from-emerald-500 via-blue-500 to-orange-500', $content);
$content = str_replace('text-primary', 'text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 via-blue-500 to-orange-500', $content);

// Update some key blue-600 occurrences manually to avoid breaking UI
// Hero Title: text-slate-900 -> text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 via-blue-500 to-orange-500
$content = str_replace(
    'class="text-3xl md:text-5xl lg:text-6xl font-black text-slate-900',
    'class="text-3xl md:text-5xl lg:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 via-blue-500 to-orange-500',
    $content
);

// Buttons
$content = str_replace('bg-blue-600', 'bg-gradient-to-r from-emerald-500 via-blue-500 to-orange-500', $content);
$content = str_replace('hover:bg-blue-700', 'hover:brightness-110', $content);

file_put_contents($filepath, $content);
echo "Landing page animations reduced and colors updated.";
