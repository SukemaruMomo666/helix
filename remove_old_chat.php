<?php
$dir = new RecursiveDirectoryIterator('c:\laragon\www\helix\resources\views');
$iterator = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($iterator, '/^.+\.blade\.php$/i', RecursiveRegexIterator::GET_MATCH);

foreach ($files as $file) {
    $filePath = $file[0];
    $content = file_get_contents($filePath);
    
    if (strpos($content, "@include('partials.chat')") !== false) {
        $content = str_replace("@include('partials.chat')", "", $content);
        file_put_contents($filePath, $content);
        echo "Removed chat from: " . $filePath . "\n";
    }
}
?>
