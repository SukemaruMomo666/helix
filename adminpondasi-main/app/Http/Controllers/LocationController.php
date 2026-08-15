<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    // Ambil Kota berdasarkan Provinsi
    public function getCities($provinceId)
    {
        $cities = DB::table('cities')->where('province_id', $provinceId)->orderBy('name')->get();
        return response()->json($cities);
    }

    // Ambil Kecamatan berdasarkan Kota
    public function getDistricts($cityId)
    {
        $districts = DB::table('districts')->where('city_id', $cityId)->orderBy('name')->get();
        return response()->json($districts);
    }
}