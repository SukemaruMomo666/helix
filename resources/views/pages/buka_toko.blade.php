<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="{{ asset('logohelix.png') }}" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Buka Toko Mitra - Helix</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        zinc: { 50: '#fafafa', 100: '#f4f4f5', 200: '#e4e4e7', 300: '#d4d4d8', 400: '#a1a1aa', 500: '#71717a', 600: '#52525b', 700: '#3f3f46', 800: '#27272a', 900: '#18181b', 950: '#09090b', },
                        blue: { 50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', },
                        indigo: { 500: '#6366f1', 600: '#4f46e5', }
                    },
                    boxShadow: {
                        'soft': '0 10px 40px -10px rgba(0,0,0,0.08)',
                        'glow': '0 0 20px rgba(37,99,235,0.4)',
                        'map-overlay': '0 8px 32px rgba(0,0,0,0.12)'
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                        'blob': 'blob 10s infinite alternate cubic-bezier(0.4, 0, 0.2, 1)',
                        'blob-reverse': 'blob 12s infinite alternate-reverse cubic-bezier(0.4, 0, 0.2, 1)',
                        'spin': 'spin 1s linear infinite'
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(40px, -60px) scale(1.1)' },
                            '66%': { transform: 'translate(-30px, 40px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        }
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    {{-- LEAFLET CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        html { scroll-behavior: smooth; }

        /* Custom Scrollbar untuk area form */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        /* Glassmorphism Panel */
        .glass-panel {
            background: linear-gradient(145deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.01) 100%);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.05);
        }

        /* Hilangkan panah di input number */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }

        /* Leaflet Map Custom Styling */
        .leaflet-control-zoom { border: none !important; box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important; border-radius: 12px !important; overflow: hidden; margin-top: 20px !important; margin-left: 20px !important; }
        .leaflet-control-zoom a { background: rgba(255,255,255,0.9) !important; color: #3b82f6 !important; border: none !important; width: 40px !important; height: 40px !important; line-height: 40px !important; font-size: 18px !important; backdrop-filter: blur(10px); transition: all 0.3s; }
        .leaflet-control-zoom a:hover { background: #3b82f6 !important; color: white !important; }
        .glass-map-panel { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.5); }
        
        .loader-spin { animation: spin 1s linear infinite; }
    </style>
</head>
<body class="bg-white font-sans text-zinc-900 antialiased flex flex-col lg:flex-row min-h-screen overflow-hidden">

    {{-- ======================================================= --}}
    {{-- KIRI: SISI VISUAL (STICKY BRANDING) --}}
    {{-- ======================================================= --}}
    <div class="hidden lg:flex w-5/12 bg-zinc-950 relative flex-col justify-between p-12 z-0">

        {{-- Tombol Back Desktop --}}
        <a href="{{ route('profil.index') }}" class="absolute top-8 left-12 z-50 flex items-center justify-center w-12 h-12 bg-white/5 border border-white/10 text-white rounded-2xl hover:bg-white/10 active:scale-95 transition-all shadow-lg backdrop-blur-md group">
            <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
        </a>

        {{-- Ambient Light FX --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none z-0 mix-blend-screen">
            <div class="absolute top-0 left-0 w-[600px] h-[600px] bg-blue-600/20 rounded-full blur-[120px] -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-indigo-600/20 rounded-full blur-[100px] translate-x-1/3 translate-y-1/3"></div>
        </div>

        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-[0.05] pointer-events-none z-10"></div>

        <div class="relative z-20 animate-fade-in-up pt-16">
            <a href="{{ url('/') }}" class="inline-flex items-center justify-center p-3 glass-panel rounded-2xl shadow-2xl hover:scale-105 transition-transform duration-500 mb-12">
                <img src="{{ asset('assets/image/Helix.com.png') }}" alt="Logo" class="h-8 w-auto object-contain drop-shadow-md" onerror="this.outerHTML='<div class=\'text-white font-black text-xl px-2\'>P<span class=\'text-blue-600\'>.</span></div>'">
            </a>

            <h1 class="text-4xl xl:text-5xl font-black text-white leading-[1.1] tracking-tight mb-6">
                Langkah Terakhir<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">Upgrade Bisnis Anda.</span>
            </h1>
            
            <p class="text-zinc-400 text-lg font-medium leading-relaxed mb-10 max-w-sm">
                Lengkapi profil UMKM Anda. Sistem kami akan menghubungkan produk Anda dengan pembeli secara otomatis.
            </p>

            <div class="space-y-4">
                <div class="flex items-center gap-4 text-zinc-300">
                    <div class="w-10 h-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center shrink-0">
                        <i class="fas fa-truck-fast text-blue-400 text-sm"></i>
                    </div>
                    <span class="font-medium text-sm">Integrasi logistik otomatis seluruh Indonesia</span>
                </div>
                <div class="flex items-center gap-4 text-zinc-300">
                    <div class="w-10 h-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center shrink-0">
                        <i class="fas fa-money-bill-transfer text-blue-400 text-sm"></i>
                    </div>
                    <span class="font-medium text-sm">Pembayaran aman dengan sistem Escrow</span>
                </div>
            </div>
        </div>
        
        <div class="relative z-20 text-[10px] text-zinc-600 font-bold tracking-widest uppercase pb-4">
            &copy; {{ date('Y') }} Helix Enterprise.
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- KANAN: SISI FORM REGISTER (SCROLLABLE) --}}
    {{-- ======================================================= --}}
    <div class="w-full lg:w-7/12 overflow-y-auto h-screen custom-scrollbar relative bg-white z-10 shadow-[-20px_0_40px_rgba(0,0,0,0.05)]">
        <div class="p-6 sm:p-12 lg:p-16 max-w-2xl mx-auto animate-fade-in-up">

            {{-- Header Mobile (Back + Logo) --}}
            <div class="lg:hidden flex items-center justify-between mb-8 pt-2">
                <a href="{{ route('profil.index') }}" class="flex items-center justify-center w-10 h-10 bg-white border border-zinc-200 text-zinc-900 rounded-2xl hover:bg-zinc-50 active:scale-95 transition-all shadow-sm">
                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
                
                <img src="{{ asset('assets/image/Helix.com.png') }}" alt="Logo" class="h-8 w-auto object-contain" onerror="this.outerHTML='<div class=\'text-black font-black text-2xl tracking-tight\'>Helix<span class=\'text-blue-600\'>.</span></div>'">
            </div>

            <div class="mb-10">
                <h2 class="text-3xl font-black text-black tracking-tight mb-2">Buka Toko Mitra</h2>
                <p class="text-zinc-500 font-medium text-sm">
                    Satu langkah lagi untuk meroketkan penjualan produk UMKM Anda.
                </p>
            </div>

            <form action="{{ route('profil.buka_toko.process') }}" method="POST" enctype="multipart/form-data" id="bukaTokoForm" class="space-y-10">
                @csrf
                
                {{-- Hidden Inputs Lokasi Toko GPS --}}
                <input type="hidden" name="latitude" id="input_latitude" value="{{ old('latitude', '-6.571589') }}">
                <input type="hidden" name="longitude" id="input_longitude" value="{{ old('longitude', '107.758736') }}">

                {{-- INFO TOKO & LOKASI --}}
                <div>
                    <div class="flex items-center gap-3 mb-6 border-b border-zinc-100 pb-3">
                        <div class="w-8 h-8 rounded-full bg-zinc-900 text-white font-black flex items-center justify-center text-sm">
                            <i class="fas fa-store"></i>
                        </div>
                        <h3 class="text-lg font-black text-black">Profil Bisnis / Toko</h3>
                    </div>

                    <div class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="relative group">
                                <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1">Nama UMKM / Toko <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_toko" required value="{{ old('nama_toko') }}" placeholder="UMKM Makmur Jaya" class="w-full bg-zinc-50 border-2 border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 px-5 py-4 transition-all outline-none">
                            </div>
                            <div class="relative group">
                                <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1">Telepon / WA Toko <span class="text-red-500">*</span></label>
                                <input type="number" name="telepon_toko" required value="{{ old('telepon_toko', Auth::user()->no_telepon ?? '') }}" placeholder="0821..." class="w-full bg-zinc-50 border-2 border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 px-5 py-4 transition-all outline-none">
                            </div>
                        </div>

                        {{-- MAPS TITIK LOKASI TOKO --}}
                        <div class="bg-white rounded-[2rem] border border-zinc-200 p-2 relative overflow-hidden mt-6 mb-2">
                            <div class="p-3 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 z-10 relative">
                                <div>
                                    <h2 class="text-base font-black text-black flex items-center gap-2">
                                        <i class="fas fa-map-marked-alt text-blue-600"></i> Titik Lokasi GPS Toko
                                    </h2>
                                    <p class="text-[11px] text-zinc-500 mt-1 font-medium">Geser pin merah ke lokasi persis UMKM Anda untuk kurir sistem.</p>
                                </div>
                                <button type="button" onclick="getLocation()" class="shrink-0 bg-zinc-900 hover:bg-blue-600 text-white font-bold py-2.5 px-4 rounded-xl transition-all text-xs flex items-center justify-center gap-2">
                                    <i class="fas fa-crosshairs"></i> Gunakan GPS HP
                                </button>
                            </div>

                            <div class="relative w-full h-[280px] rounded-[1.5rem] overflow-hidden border-2 border-white shadow-inner bg-zinc-100 z-10">
                                <div id="map" class="w-full h-full"></div>
                                <div class="absolute bottom-4 left-4 z-[400] glass-map-panel p-3 rounded-xl shadow-map-overlay">
                                    <div class="text-[10px] font-bold text-zinc-600 flex flex-col gap-0.5">
                                        <div class="flex gap-2"><span>Lat:</span> <span id="lat-display" class="text-blue-600 font-black">-</span></div>
                                        <div class="flex gap-2"><span>Lng:</span> <span id="lng-display" class="text-blue-600 font-black">-</span></div>
                                    </div>
                                </div>
                                <div id="map-loading" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-[500] flex flex-col items-center justify-center opacity-0 pointer-events-none transition-opacity">
                                    <i class="fas fa-circle-notch fa-spin text-2xl text-blue-600 mb-2"></i>
                                    <span class="text-[10px] font-bold text-zinc-600 uppercase tracking-wider" id="loading-text">Menyelaraskan Lokasi...</span>
                                </div>
                            </div>
                        </div>

                        <div class="relative group">
                            <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1">Alamat Toko / Gudang <span class="text-red-500">*</span></label>
                            <textarea name="alamat_toko" id="alamat_toko" rows="3" required placeholder="Nama Jalan, Nomor Bangunan, RT/RW, Patokan..." class="custom-scrollbar w-full bg-zinc-50 border-2 border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 px-5 py-4 transition-all outline-none resize-none">{{ old('alamat_toko') }}</textarea>
                        </div>

                        {{-- BITESHIP AUTOCOMPLETE SEARCH --}}
                        <div class="relative group mt-2">
                            <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1">Wilayah Ekspedisi<span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none" id="search-icon">
                                    <i class="fas fa-search text-zinc-400"></i>
                                </div>
                                <input type="text" id="search_area" placeholder="Ketik HANYA nama Kecamatan atau Kelurahan lalu pilih..." class="w-full bg-zinc-50 border-2 border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 pl-11 pr-5 py-4 transition-all outline-none" autocomplete="off">
                                <input type="hidden" name="area_id" id="area_id" value="{{ old('area_id') }}">
                                
                                <ul id="area_results" class="absolute z-[1000] w-full bg-white border border-zinc-200 rounded-xl shadow-lg mt-1 hidden max-h-60 overflow-y-auto custom-scrollbar">
                                    </ul>
                            </div>
                            <p class="text-[10px] text-zinc-500 mt-2 ml-1 font-medium"><i class="fas fa-info-circle text-blue-500"></i> Sangat Krusial: Klik nama daerah yang muncul di list dropdown agar sistem ongkos kirim toko Anda berfungsi.</p>
                        </div>

                        {{-- Custom File Upload --}}
                        <div class="relative group mt-4">
                            <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1">Logo Toko (Opsional)</label>
                            <div class="w-full bg-zinc-50 border-2 border-dashed border-zinc-300 rounded-2xl p-4 flex items-center gap-4 transition-colors hover:border-blue-500 relative">
                                <input type="file" name="logo_toko" id="logo_toko" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewImage(this)">
                                <div class="w-12 h-12 rounded-xl bg-white border border-zinc-200 flex items-center justify-center shrink-0 overflow-hidden" id="logo-preview-container">
                                    <i class="fas fa-image text-zinc-300 text-lg" id="logo-icon"></i>
                                    <img id="logo-preview" class="w-full h-full object-cover hidden">
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-zinc-700" id="file-name">Unggah Logo Anda</h4>
                                    <p class="text-[10px] font-medium text-zinc-400 mt-0.5">Klik area ini. Maksimal 2MB (JPG/PNG).</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Area --}}
                <div class="pt-4 pb-10">
                    <button type="submit" id="btnSubmit" class="w-full bg-black hover:bg-blue-600 text-white font-black py-4 rounded-2xl transition-all duration-300 shadow-[0_8px_20px_rgba(0,0,0,0.15)] hover:shadow-glow hover:-translate-y-1 text-base flex items-center justify-center gap-2">
                        Buka Toko Sekarang <i class="fas fa-rocket"></i>
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // 1. Custom Image Preview
            window.previewImage = function(input) {
                const preview = document.getElementById('logo-preview');
                const icon = document.getElementById('logo-icon');
                const fileName = document.getElementById('file-name');

                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                        icon.classList.add('hidden');
                        fileName.innerText = input.files[0].name;
                        fileName.classList.add('text-blue-600');
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            // 2. Loading State pada Tombol Submit
            document.getElementById('bukaTokoForm').addEventListener('submit', function(e) {
                const areaInput = document.getElementById('area_id').value;
                if(!areaInput) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning', title: 'Data Belum Lengkap', text: "Mohon ketik dan klik nama Kecamatan/Kelurahan di kolom Wilayah Ekspedisi.",
                        confirmButtonColor: '#000000', customClass: { popup: 'rounded-[2rem]', confirmButton: 'rounded-xl px-8 py-3 font-bold' }
                    });
                    return;
                }

                const btn = document.getElementById('btnSubmit');
                setTimeout(() => {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Sedang Mendaftarkan Toko...';
                    btn.classList.add('opacity-80', 'cursor-not-allowed');
                    btn.classList.remove('hover:-translate-y-1', 'hover:bg-blue-600', 'hover:shadow-glow');
                }, 10);
            });

            // 3. MAPS LEAFLET (Lokasi Gudang)
            let currentLat = parseFloat(document.getElementById('input_latitude').value) || -6.571589;
            let currentLng = parseFloat(document.getElementById('input_longitude').value) || 107.758736;

            const map = L.map('map', { zoomControl: false, scrollWheelZoom: false }).setView([currentLat, currentLng], 14);
            L.control.zoom({ position: 'topleft' }).addTo(map);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap contributors', maxZoom: 19
            }).addTo(map);

            const customIcon = L.divIcon({
                className: 'custom-pin',
                html: `<div style="background: #e11d48; width: 24px; height: 24px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); border: 4px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.3);"></div>`,
                iconSize: [24, 24], iconAnchor: [12, 24]
            });
            const marker = L.marker([currentLat, currentLng], { draggable: true, icon: customIcon }).addTo(map);

            document.getElementById('lat-display').innerText = currentLat.toFixed(6);
            document.getElementById('lng-display').innerText = currentLng.toFixed(6);

            const loadingOverlay = document.getElementById('map-loading');
            const loadingText = document.getElementById('loading-text');

            async function performGeocoding(lat, lng) {
                loadingOverlay.style.opacity = '1';
                loadingText.innerText = "Membaca Peta...";

                try {
                    const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}&accept-language=id`);
                    const data = await res.json();

                    if(data.display_name) {
                        document.getElementById('alamat_toko').value = data.display_name;

                        let dName = data.address.suburb || data.address.village || data.address.town || data.address.district || '';
                        if(dName) {
                            document.getElementById('search_area').value = dName;
                            searchBiteshipAreas(dName);
                        }
                    }
                } catch (error) {
                    console.error("Geocoding Error:", error);
                } finally {
                    loadingOverlay.style.opacity = '0';
                }
            }

            marker.on('dragend', function(e) {
                const position = marker.getLatLng();
                document.getElementById('input_latitude').value = position.lat;
                document.getElementById('input_longitude').value = position.lng;
                document.getElementById('lat-display').innerText = position.lat.toFixed(6);
                document.getElementById('lng-display').innerText = position.lng.toFixed(6);

                map.panTo(position);
                performGeocoding(position.lat, position.lng);
            });

            window.getLocation = function() {
                if (navigator.geolocation) {
                    loadingOverlay.style.opacity = '1';
                    loadingText.innerText = "Mencari Sinyal GPS...";
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;

                            map.setView([lat, lng], 16);
                            marker.setLatLng([lat, lng]);

                            document.getElementById('input_latitude').value = lat;
                            document.getElementById('input_longitude').value = lng;
                            document.getElementById('lat-display').innerText = lat.toFixed(6);
                            document.getElementById('lng-display').innerText = lng.toFixed(6);

                            performGeocoding(lat, lng);
                        },
                        (error) => {
                            loadingOverlay.style.opacity = '0';
                            alert('Gagal mendeteksi GPS HP/Browser. Pastikan izin lokasi (location) menyala.');
                        },
                        { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
                    );
                }
            };
            setTimeout(() => { map.invalidateSize(); }, 500);

            // 4. BITESHIP AUTOCOMPLETE SEARCH
            const searchInput = document.getElementById('search_area');
            const areaIdInput = document.getElementById('area_id');
            const resultsList = document.getElementById('area_results');
            const searchIcon = document.getElementById('search-icon');
            let debounceTimer;

            async function searchBiteshipAreas(keyword) {
                searchIcon.innerHTML = '<i class="fas fa-spinner loader-spin text-blue-500"></i>';
                try {
                    const response = await fetch(`/api/biteship/search?q=${encodeURIComponent(keyword)}`);
                    const data = await response.json();
                    
                    resultsList.innerHTML = '';
                    if(data.areas && data.areas.length > 0) {
                        data.areas.forEach(area => {
                            const li = document.createElement('li');
                            li.className = 'px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-zinc-100 last:border-0 transition-colors';
                            li.innerHTML = `
                                <div class="font-bold text-sm text-zinc-800">${area.name}</div>
                                <div class="text-[11px] text-zinc-500">${area.administrative_division_level_2_name}, ${area.administrative_division_level_1_name}</div>
                            `;
                            li.addEventListener('click', () => {
                                searchInput.value = `${area.name}, ${area.administrative_division_level_2_name}`;
                                areaIdInput.value = area.id; 
                                resultsList.classList.add('hidden');
                            });
                            resultsList.appendChild(li);
                        });
                        resultsList.classList.remove('hidden');
                    } else {
                        resultsList.innerHTML = '<li class="px-4 py-3 text-sm text-zinc-500 text-center">Area tidak ditemukan</li>';
                        resultsList.classList.remove('hidden');
                    }
                } catch (error) {
                    console.error("Gagal mengambil data Biteship", error);
                } finally {
                    searchIcon.innerHTML = '<i class="fas fa-search text-zinc-400"></i>';
                }
            }

            searchInput.addEventListener('input', function() {
                const keyword = this.value.trim();
                clearTimeout(debounceTimer);
                
                if (keyword.length < 3) {
                    resultsList.classList.add('hidden');
                    return;
                }
                debounceTimer = setTimeout(() => { searchBiteshipAreas(keyword); }, 800);
            });

            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !resultsList.contains(e.target)) {
                    resultsList.classList.add('hidden');
                }
            });

            // 5. SweetAlert Flash Messages System
            @if(session('success'))
                Swal.fire({
                    icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}",
                    confirmButtonColor: '#000000', allowOutsideClick: false,
                    customClass: { popup: 'rounded-[2rem]', confirmButton: 'rounded-xl px-8 py-3 font-bold' }
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error', title: 'Gagal!', text: "{{ session('error') }}", confirmButtonColor: '#000000',
                    customClass: { popup: 'rounded-[2rem]', confirmButton: 'rounded-xl px-8 py-3 font-bold' }
                });
            @endif

            @if($errors->any())
                let errorMessages = {!! json_encode($errors->all()) !!};
                Swal.fire({
                    icon: 'warning', title: 'Periksa Data Anda',
                    html: `<div style="text-align: left; font-size: 0.85rem; color: #dc2626; background: #fef2f2; padding: 15px; border-radius: 12px; border: 1px solid #fecaca; margin-top: 10px;">
                            <ul style="padding-left: 20px; margin: 0; font-weight: 500; line-height: 1.5;">
                                ${errorMessages.map(err => `<li>${err}</li>`).join('')}
                            </ul></div>`,
                    confirmButtonColor: '#000000',
                    customClass: { popup: 'rounded-[2rem]', confirmButton: 'rounded-xl px-8 py-3 font-bold' }
                });
            @endif

        });
    </script>
</body>
</html>

