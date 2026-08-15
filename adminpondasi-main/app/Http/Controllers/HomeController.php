<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Models\Produk; // Uncomment jika ingin ambil data produk

class HomeController extends Controller
{
    public function index()
    {
        // Logika untuk halaman Home (Dashboard Utama)
        // Contoh: Ambil data produk terbaru
        // $produkTerbaru = Produk::latest()->take(8)->get();

        // Return view utama. Data Navbar sudah diurus otomatis oleh AppServiceProvider
        return view('pages.home'); 
    }
}