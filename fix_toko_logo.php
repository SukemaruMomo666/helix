<?php
$files = [
    'c:\laragon\www\helix\resources\views\landing.blade.php',
    'c:\laragon\www\helix\resources\views\landing_new.blade.php'
];

$oldBlock = <<<HTML
                            @if(!empty(\$toko->logo_toko))
                                <img src="{{ asset('assets/uploads/logos/' . \$toko->logo_toko) }}" class="w-full h-full rounded-full object-cover" onerror="this.onerror=null; this.src='{{ asset('logohelix.png') }}'">
                            @else
HTML;

$newBlock = <<<HTML
                            @php
                                \$logoPath = asset('logohelix.png');
                                if (!empty(\$toko->logo_toko)) {
                                    if (file_exists(public_path('assets/uploads/logos/' . \$toko->logo_toko))) {
                                        \$logoPath = asset('assets/uploads/logos/' . \$toko->logo_toko);
                                    } elseif (file_exists(public_path('uploads/toko/' . \$toko->logo_toko))) {
                                        \$logoPath = asset('uploads/toko/' . \$toko->logo_toko);
                                    }
                                }
                            @endphp
                            @if(!empty(\$toko->logo_toko))
                                <img src="{{ \$logoPath }}" class="w-full h-full rounded-full object-cover" onerror="this.onerror=null; this.src='{{ asset('logohelix.png') }}'">
                            @else
HTML;

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        // Replace CRLF to LF for consistent replace
        $content = str_replace("\r\n", "\n", $content);
        $oldBlockLf = str_replace("\r\n", "\n", $oldBlock);
        
        $content = str_replace($oldBlockLf, $newBlock, $content);
        file_put_contents($file, $content);
        echo "Updated logo logic in: $file\n";
    }
}
?>
