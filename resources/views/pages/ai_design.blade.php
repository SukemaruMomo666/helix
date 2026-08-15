@extends('layouts.app')

@section('title', 'AI Interior Design - Helix')

@section('content')
<div class="bg-slate-50 min-h-screen py-10">
    <div class="container mx-auto px-4 max-w-7xl">
        
        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-4xl md:text-5xl font-black text-slate-800 mb-4 tracking-tight">AI <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Interior Design</span> ✨</h1>
            <p class="text-slate-500 text-lg max-w-2xl mx-auto">Upload foto ruanganmu, tandai bagian yang ingin diubah, dan biarkan AI menyulapnya menjadi desain yang estetik.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Kiri: Editor -->
            <div class="lg:col-span-7 bg-white p-6 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100">
                <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-magic text-blue-500"></i> 1. Upload & Tandai Area
                </h2>

                <!-- Upload Area (Hidden when image loaded) -->
                <div id="uploadArea" class="border-2 border-dashed border-slate-300 rounded-2xl p-10 text-center hover:bg-slate-50 hover:border-blue-400 transition-colors cursor-pointer flex flex-col items-center justify-center min-h-[400px]">
                    <i class="fas fa-cloud-upload-alt text-5xl text-slate-300 mb-4"></i>
                    <p class="text-slate-600 font-medium mb-1">Klik atau seret foto ruangan ke sini</p>
                    <p class="text-sm text-slate-400">Format: JPG, PNG (Max 5MB)</p>
                    <input type="file" id="imageInput" accept="image/png, image/jpeg" class="hidden">
                </div>

                <!-- Editor Area (Visible when image loaded) -->
                <div id="editorArea" class="hidden">
                    <div class="relative rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 flex justify-center items-center group cursor-crosshair">
                        <!-- Wrapper for Canvas & Image -->
                        <div class="relative inline-block" id="canvasWrapper">
                            <img id="sourceImage" class="max-w-full h-auto block select-none rounded-lg shadow-sm" draggable="false">
                            <canvas id="maskCanvas" class="absolute top-0 left-0 w-full h-full touch-none rounded-lg opacity-60"></canvas>
                        </div>
                    </div>

                    <!-- Toolbar -->
                    <div class="mt-4 flex flex-wrap items-center justify-between gap-4 bg-slate-50 p-3 rounded-xl border border-slate-200">
                        <div class="flex items-center gap-3">
                            <label class="text-xs font-bold text-slate-600 uppercase tracking-wider">Ukuran Kuas:</label>
                            <input type="range" id="brushSize" min="10" max="100" value="30" class="w-32 accent-blue-600">
                        </div>
                        <div class="flex gap-2">
                            <button onclick="clearMask()" class="px-4 py-2 bg-white text-slate-600 text-sm font-semibold rounded-lg shadow-sm border border-slate-200 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-all">
                                <i class="fas fa-trash-alt mr-1"></i> Hapus Kuas
                            </button>
                            <button onclick="resetImage()" class="px-4 py-2 bg-white text-slate-600 text-sm font-semibold rounded-lg shadow-sm border border-slate-200 hover:bg-slate-100 transition-all">
                                <i class="fas fa-times mr-1"></i> Ganti Foto
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Prompt Input -->
                <div class="mt-8">
                    <h2 class="text-xl font-bold text-slate-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-keyboard text-indigo-500"></i> 2. Masukkan Perintah
                    </h2>
                    <textarea id="promptInput" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all resize-none shadow-inner" placeholder="Contoh: Ubah warna cat tembok menjadi sage green estetik ala scandinavian..."></textarea>
                    
                    <button id="generateBtn" onclick="generateDesign()" class="mt-4 w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-4 px-6 rounded-2xl shadow-lg shadow-blue-500/30 transform hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-paint-roller"></i> Generate Desain Sekarang
                    </button>
                </div>
            </div>

            <!-- Kanan: Result -->
            <div class="lg:col-span-5 relative">
                <div class="sticky top-24 bg-white p-6 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 flex flex-col min-h-[600px]">
                    <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-image text-emerald-500"></i> Hasil Desain AI
                    </h2>

                    <div id="resultArea" class="flex-1 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center p-6 text-center overflow-hidden relative">
                        
                        <!-- Empty State -->
                        <div id="resultEmpty" class="text-slate-400">
                            <i class="fas fa-box-open text-6xl mb-4 opacity-50"></i>
                            <p class="font-medium">Hasil desain akan muncul di sini</p>
                        </div>

                        <!-- Loading State -->
                        <div id="resultLoading" class="hidden flex-col items-center justify-center w-full h-full absolute top-0 left-0 bg-white/80 backdrop-blur-sm z-10">
                            <div class="w-16 h-16 border-4 border-slate-200 border-t-blue-600 rounded-full animate-spin mb-4 shadow-lg"></div>
                            <p class="text-blue-700 font-bold text-lg animate-pulse">AI Sedang Merenovasi...</p>
                            <p class="text-slate-500 text-sm mt-1">Ini mungkin memakan waktu 10-30 detik.</p>
                        </div>

                        <!-- Result Image -->
                        <img id="resultImage" class="hidden max-w-full max-h-full object-contain rounded-xl shadow-md cursor-pointer hover:scale-[1.02] transition-transform" onclick="window.open(this.src, '_blank')">
                        
                    </div>

                    <div id="downloadAction" class="mt-4 hidden">
                        <a id="downloadLink" href="#" download="helix-ai-design.jpg" class="w-full block text-center bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 px-6 rounded-xl shadow-md shadow-emerald-500/20 transition-all">
                            <i class="fas fa-download mr-1"></i> Download Gambar
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    // Elements
    const uploadArea = document.getElementById('uploadArea');
    const imageInput = document.getElementById('imageInput');
    const editorArea = document.getElementById('editorArea');
    const sourceImage = document.getElementById('sourceImage');
    const maskCanvas = document.getElementById('maskCanvas');
    const brushSizeInput = document.getElementById('brushSize');
    const generateBtn = document.getElementById('generateBtn');
    
    let ctx = maskCanvas.getContext('2d');
    let isDrawing = false;
    let originalBase64 = null;

    // Trigger file input
    uploadArea.addEventListener('click', () => imageInput.click());

    // Handle Image Upload
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(event) {
            originalBase64 = event.target.result;
            sourceImage.src = originalBase64;
            
            sourceImage.onload = () => {
                uploadArea.classList.add('hidden');
                editorArea.classList.remove('hidden');
                
                // Set canvas size match image rendered size
                maskCanvas.width = sourceImage.width;
                maskCanvas.height = sourceImage.height;
                
                // Clear canvas with black (transparent mask)
                ctx.fillStyle = 'black';
                ctx.fillRect(0, 0, maskCanvas.width, maskCanvas.height);
            };
        };
        reader.readAsDataURL(file);
    });

    // Drawing Logic (Masking - White brush on Black bg)
    function startPosition(e) {
        isDrawing = true;
        draw(e);
    }
    
    function endPosition() {
        isDrawing = false;
        ctx.beginPath();
    }
    
    function draw(e) {
        if (!isDrawing) return;
        
        // Dapatkan posisi relatif mouse terhadap canvas
        const rect = maskCanvas.getBoundingClientRect();
        
        // Hitung faktor skala (karena ukuran asli image/canvas mungkin beda dengan yg di-render browser)
        const scaleX = maskCanvas.width / rect.width;
        const scaleY = maskCanvas.height / rect.height;

        let clientX = e.clientX;
        let clientY = e.clientY;

        // Support Touch
        if(e.type.includes('touch')) {
            clientX = e.touches[0].clientX;
            clientY = e.touches[0].clientY;
        }
        
        const x = (clientX - rect.left) * scaleX;
        const y = (clientY - rect.top) * scaleY;
        
        ctx.lineWidth = brushSizeInput.value;
        ctx.lineCap = 'round';
        ctx.strokeStyle = 'white'; // Mask area
        
        ctx.lineTo(x, y);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(x, y);
    }

    // Event Listeners for drawing
    maskCanvas.addEventListener('mousedown', startPosition);
    maskCanvas.addEventListener('mouseup', endPosition);
    maskCanvas.addEventListener('mousemove', draw);
    maskCanvas.addEventListener('mouseleave', endPosition);

    maskCanvas.addEventListener('touchstart', startPosition, {passive: true});
    maskCanvas.addEventListener('touchend', endPosition);
    maskCanvas.addEventListener('touchmove', draw, {passive: true});

    // Toolbar Actions
    function clearMask() {
        ctx.fillStyle = 'black';
        ctx.fillRect(0, 0, maskCanvas.width, maskCanvas.height);
    }

    function resetImage() {
        originalBase64 = null;
        imageInput.value = '';
        editorArea.classList.add('hidden');
        uploadArea.classList.remove('hidden');
        document.getElementById('resultImage').classList.add('hidden');
        document.getElementById('resultEmpty').classList.remove('hidden');
        document.getElementById('downloadAction').classList.add('hidden');
    }

    // Generate Action
    async function generateDesign() {
        const prompt = document.getElementById('promptInput').value.trim();
        
        if (!originalBase64) {
            alert('Silakan upload foto ruangan terlebih dahulu!');
            return;
        }
        if (!prompt) {
            alert('Prompt (perintah) tidak boleh kosong!');
            return;
        }

        // Ambil base64 dari mask canvas
        const maskBase64 = maskCanvas.toDataURL('image/jpeg', 0.9);

        // UI Loading
        generateBtn.disabled = true;
        generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        
        document.getElementById('resultEmpty').classList.add('hidden');
        document.getElementById('resultImage').classList.add('hidden');
        document.getElementById('downloadAction').classList.add('hidden');
        document.getElementById('resultLoading').classList.remove('hidden');
        document.getElementById('resultLoading').classList.add('flex');

        try {
            const response = await fetch('{{ route('ai_design.generate') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    image: originalBase64,
                    mask: maskBase64,
                    prompt: prompt
                })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Terjadi kesalahan pada server.');
            }

            // Tampilkan hasil
            const resImg = document.getElementById('resultImage');
            resImg.src = data.image;
            resImg.classList.remove('hidden');
            
            // Set link download
            const dL = document.getElementById('downloadLink');
            dL.href = data.image;
            document.getElementById('downloadAction').classList.remove('hidden');

        } catch (error) {
            alert('Gagal: ' + error.message);
            document.getElementById('resultEmpty').classList.remove('hidden');
        } finally {
            document.getElementById('resultLoading').classList.add('hidden');
            document.getElementById('resultLoading').classList.remove('flex');
            generateBtn.disabled = false;
            generateBtn.innerHTML = '<i class="fas fa-paint-roller"></i> Generate Desain Sekarang';
        }
    }
</script>
@endsection
