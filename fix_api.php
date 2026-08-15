<?php
$content = file_get_contents('c:\laragon\www\helix\routes\api.php');
$content = str_replace("use App\Http\Controllers\ProjectController;", "// use App\Http\Controllers\ProjectController;", $content);
$content = str_replace("Route::get('/projects', [ProjectController::class, 'index']);", "// Route::get('/projects', [ProjectController::class, 'index']);", $content);
$content = str_replace("Route::post('/projects', [ProjectController::class, 'store']);", "// Route::post('/projects', [ProjectController::class, 'store']);", $content);
$content = str_replace("Route::get('/projects/{id}', [ProjectController::class, 'show']);", "// Route::get('/projects/{id}', [ProjectController::class, 'show']);", $content);
file_put_contents('c:\laragon\www\helix\routes\api.php', $content);
echo "Fixed api.php";
