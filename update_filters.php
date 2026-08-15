<?php

$newFilterHtml = <<<HTML
                            {{-- 1. MAKANAN RINGAN --}}
                            <div class="accordion-item border border-zinc-100 rounded-2xl bg-zinc-50/50 overflow-hidden">
                                <button type="button" class="accordion-header w-full px-4 py-3 flex items-center justify-between text-sm font-bold text-zinc-700 hover:text-blue-600 transition-colors">
                                    <span class="flex items-center gap-2"><i class="fas fa-cookie-bite text-zinc-300 text-xs w-4"></i> Makanan Ringan</span>
                                    <i class="fas fa-chevron-down text-[10px] text-zinc-400 transition-transform duration-300 icon-arrow"></i>
                                </button>
                                <div class="accordion-content bg-white">
                                    <div class="px-4 pb-4 pt-1 space-y-3 border-t border-zinc-100 mt-1">
                                        @foreach(['Keripik & Kerupuk', 'Kue Kering', 'Camilan Manis'] as \$sub)
                                            <label class="flex items-start gap-3 cursor-pointer group">
                                                <input type="checkbox" name="kategori_text[]" value="{{ \$sub }}" class="custom-checkbox mt-0.5" {{ in_array(\$sub, request('kategori_text', [])) ? 'checked' : '' }} onchange="showApplyButton()">
                                                <span class="text-xs font-semibold text-zinc-600 group-hover:text-zinc-900 select-none">{{ \$sub }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- 2. MINUMAN TRADISIONAL --}}
                            <div class="accordion-item border border-zinc-100 rounded-2xl bg-zinc-50/50 overflow-hidden">
                                <button type="button" class="accordion-header w-full px-4 py-3 flex items-center justify-between text-sm font-bold text-zinc-700 hover:text-blue-600 transition-colors">
                                    <span class="flex items-center gap-2"><i class="fas fa-coffee text-zinc-300 text-xs w-4"></i> Minuman Tradisional</span>
                                    <i class="fas fa-chevron-down text-[10px] text-zinc-400 transition-transform duration-300 icon-arrow"></i>
                                </button>
                                <div class="accordion-content bg-white">
                                    <div class="px-4 pb-4 pt-1 space-y-3 border-t border-zinc-100 mt-1">
                                        @foreach(['Kopi & Teh Lokal', 'Wedang & Jamu', 'Sirup Buah'] as \$sub)
                                            <label class="flex items-start gap-3 cursor-pointer group">
                                                <input type="checkbox" name="kategori_text[]" value="{{ \$sub }}" class="custom-checkbox mt-0.5" {{ in_array(\$sub, request('kategori_text', [])) ? 'checked' : '' }} onchange="showApplyButton()">
                                                <span class="text-xs font-semibold text-zinc-600 group-hover:text-zinc-900 select-none">{{ \$sub }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- 3. KERAJINAN TANGAN --}}
                            <div class="accordion-item border border-zinc-100 rounded-2xl bg-zinc-50/50 overflow-hidden">
                                <button type="button" class="accordion-header w-full px-4 py-3 flex items-center justify-between text-sm font-bold text-zinc-700 hover:text-blue-600 transition-colors">
                                    <span class="flex items-center gap-2"><i class="fas fa-hand-sparkles text-zinc-300 text-xs w-4"></i> Kerajinan Tangan</span>
                                    <i class="fas fa-chevron-down text-[10px] text-zinc-400 transition-transform duration-300 icon-arrow"></i>
                                </button>
                                <div class="accordion-content bg-white">
                                    <div class="px-4 pb-4 pt-1 space-y-3 border-t border-zinc-100 mt-1">
                                        @foreach(['Anyaman Bambu', 'Ukiran Kayu', 'Keramik Lokal'] as \$sub)
                                            <label class="flex items-start gap-3 cursor-pointer group">
                                                <input type="checkbox" name="kategori_text[]" value="{{ \$sub }}" class="custom-checkbox mt-0.5" {{ in_array(\$sub, request('kategori_text', [])) ? 'checked' : '' }} onchange="showApplyButton()">
                                                <span class="text-xs font-semibold text-zinc-600 group-hover:text-zinc-900 select-none">{{ \$sub }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- 4. PAKAIAN & BATIK --}}
                            <div class="accordion-item border border-zinc-100 rounded-2xl bg-zinc-50/50 overflow-hidden">
                                <button type="button" class="accordion-header w-full px-4 py-3 flex items-center justify-between text-sm font-bold text-zinc-700 hover:text-blue-600 transition-colors">
                                    <span class="flex items-center gap-2"><i class="fas fa-tshirt text-zinc-300 text-xs w-4"></i> Pakaian & Batik</span>
                                    <i class="fas fa-chevron-down text-[10px] text-zinc-400 transition-transform duration-300 icon-arrow"></i>
                                </button>
                                <div class="accordion-content bg-white">
                                    <div class="px-4 pb-4 pt-1 space-y-3 border-t border-zinc-100 mt-1">
                                        @foreach(['Batik Tulis', 'Batik Cap', 'Kaos Oleh-oleh'] as \$sub)
                                            <label class="flex items-start gap-3 cursor-pointer group">
                                                <input type="checkbox" name="kategori_text[]" value="{{ \$sub }}" class="custom-checkbox mt-0.5" {{ in_array(\$sub, request('kategori_text', [])) ? 'checked' : '' }} onchange="showApplyButton()">
                                                <span class="text-xs font-semibold text-zinc-600 group-hover:text-zinc-900 select-none">{{ \$sub }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- 5. AKSESORIS --}}
                            <div class="accordion-item border border-zinc-100 rounded-2xl bg-zinc-50/50 overflow-hidden">
                                <button type="button" class="accordion-header w-full px-4 py-3 flex items-center justify-between text-sm font-bold text-zinc-700 hover:text-blue-600 transition-colors">
                                    <span class="flex items-center gap-2"><i class="fas fa-gem text-zinc-300 text-xs w-4"></i> Aksesoris Etnik</span>
                                    <i class="fas fa-chevron-down text-[10px] text-zinc-400 transition-transform duration-300 icon-arrow"></i>
                                </button>
                                <div class="accordion-content bg-white">
                                    <div class="px-4 pb-4 pt-1 space-y-3 border-t border-zinc-100 mt-1">
                                        @foreach(['Kalung & Gelang', 'Tas Etnik', 'Topi Pandan'] as \$sub)
                                            <label class="flex items-start gap-3 cursor-pointer group">
                                                <input type="checkbox" name="kategori_text[]" value="{{ \$sub }}" class="custom-checkbox mt-0.5" {{ in_array(\$sub, request('kategori_text', [])) ? 'checked' : '' }} onchange="showApplyButton()">
                                                <span class="text-xs font-semibold text-zinc-600 group-hover:text-zinc-900 select-none">{{ \$sub }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
HTML;

function updateFile($filepath) {
    global $newFilterHtml;
    $content = file_get_contents($filepath);
    
    // Change delimiter to ~ to avoid escaping slashes
    $pattern = '~\{\{\-\- 1\. BAHAN BANGUNAN DASAR \-\-\}\}.*?(?=\s*</div>\s*\{\{\-\- FILTER: JENIS TOKO \-\-\}\})~s';
    
    $newContent = preg_replace($pattern, $newFilterHtml, $content);
    if($newContent) {
        file_put_contents($filepath, $newContent);
        echo "Updated \$filepath\n";
    }
}

updateFile('C:/laragon/www/helix/resources/views/pages/produk.blade.php');
updateFile('C:/laragon/www/helix/resources/views/pages/search.blade.php');
