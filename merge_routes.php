<?php

$helixWeb = file_get_contents('c:\laragon\www\helix\routes\web.php');
$sellerWeb = file_get_contents('c:\laragon\www\helix\sellerpondasi-main\routes\web.php');
$adminWeb = file_get_contents('c:\laragon\www\helix\adminpondasi-main\routes\web.php');

// 1. Extract imports from seller
preg_match_all('/use App\\\Http\\\Controllers\\\Seller\\\[A-Za-z]+( as [A-Za-z]+)?;/i', $sellerWeb, $sellerImports);
$sImports = implode("\n", $sellerImports[0]);

// 2. Extract imports from admin
preg_match_all('/use App\\\Http\\\Controllers\\\Admin\\\[A-Za-z]+( as [A-Za-z]+)?;/i', $adminWeb, $adminImports);
$aImports = implode("\n", $adminImports[0]);

// 3. Extract Seller routes
// The seller routes are inside Route::middleware(['auth', 'role:seller'])->prefix('seller')->name('seller.')->group(...)
$startSeller = strpos($sellerWeb, "Route::middleware(['auth', 'role:seller'])->prefix('seller')->name('seller.')->group(function () {");
$endSeller = strpos($sellerWeb, "});\n\n// ROUTE PENJAGA PINTU MEDIA PRIVATE", $startSeller);
if($endSeller === false) $endSeller = strpos($sellerWeb, "});\n\n// ========================================================\n// RUTE SAPU JAGAT", $startSeller);
if($endSeller === false) $endSeller = strlen($sellerWeb);

$sellerRoutes = substr($sellerWeb, $startSeller, $endSeller - $startSeller + 3);

// 4. Extract Admin routes
$startAdminLogin = strpos($adminWeb, "Route::get('/kunci-brankas-pks'");
$endAdmin = strpos($adminWeb, "});\n\n// ========================================================\n// RUTE SAPU JAGAT", $startAdminLogin);
if($endAdmin === false) $endAdmin = strlen($adminWeb);

$adminRoutes = substr($adminWeb, $startAdminLogin, $endAdmin - $startAdminLogin + 3);

// Now construct the new helix routes
// We need to inject the imports at the top
$helixWeb = str_replace(
    "use App\Http\Controllers\AiDesignController;",
    "use App\Http\Controllers\AiDesignController;\n\n// --- IMPORT CONTROLLER SELLER (MERGED) ---\n" . $sImports . "\n\n// --- IMPORT CONTROLLER ADMIN (MERGED) ---\n" . $aImports,
    $helixWeb
);

// We need to inject the routes at the bottom, just before the DANA routes or SAPU JAGAT
$injectPos = strpos($helixWeb, "// ========================================================\n// RUTE SAPU JAGAT");

$newHelixWeb = substr($helixWeb, 0, $injectPos) .
"// ========================================================\n" .
"// RUTE SELLER CENTER (MERGED)\n" .
"// ========================================================\n" .
$sellerRoutes . "\n\n" .
"// ========================================================\n" .
"// RUTE ADMIN PANEL (MERGED)\n" .
"// ========================================================\n" .
$adminRoutes . "\n\n" .
substr($helixWeb, $injectPos);

file_put_contents('c:\laragon\www\helix\routes\web.php', $newHelixWeb);
echo "Routes merged successfully!";
