<?php
$files = [
    'c:\laragon\www\helix\resources\views\landing.blade.php',
    'c:\laragon\www\helix\resources\views\landing_new.blade.php',
    'c:\laragon\www\helix\resources\views\layouts\app.blade.php',
    'c:\laragon\www\helix\resources\views\layouts\seller.blade.php',
    'c:\laragon\www\helix\resources\views\layouts\admin.blade.php',
    'c:\laragon\www\helix\resources\views\pages\checkout.blade.php'
];

$chatButtonHtml = <<<HTML

    {{-- FLOATING CHAT BUTTON (UPCOMING) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <button onclick="Swal.fire({icon: 'info', title: 'Segera Hadir!', text: 'Fitur Live Chat sedang dalam tahap pengembangan. Pantau terus update Helix UMKM!', confirmButtonColor: '#10b981'})" class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-tr from-emerald-500 to-teal-400 text-white rounded-full flex items-center justify-center shadow-lg shadow-emerald-500/30 hover:scale-110 hover:shadow-emerald-500/50 transition-all z-50 group">
        <i class="fas fa-comments text-2xl group-hover:animate-bounce"></i>
    </button>
</body>
HTML;

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Prevent duplicate insertion
        if (strpos($content, 'FLOATING CHAT BUTTON (UPCOMING)') === false) {
            $content = str_replace('</body>', $chatButtonHtml, $content);
            file_put_contents($file, $content);
            echo "Injected into $file\n";
        } else {
            echo "Already injected in $file\n";
        }
    }
}
?>
