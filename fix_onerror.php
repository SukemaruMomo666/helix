<?php
$files = [
    'c:\laragon\www\helix\resources\views\landing.blade.php',
    'c:\laragon\www\helix\resources\views\landing_new.blade.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $content = str_replace(
            "onerror=\"this.src='{{ asset('logohelix.png') }}'\"",
            "onerror=\"this.onerror=null; this.src='{{ asset('logohelix.png') }}'\"",
            $content
        );
        file_put_contents($file, $content);
        echo "Fixed onerror loop in: $file\n";
    }
}
?>
