<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB; 

class ChatAiController extends Controller
{
    public function handleChat(Request $request)
    {
        $userMessage = $request->input('message');
        $chatHistory = $request->input('history', []); 
        $imageBase64 = $request->input('image');
        $userId = auth()->id();
        \Log::info("POTA Chat Request. UserID: " . ($userId ?: 'NULL'));

        if (!$userMessage && !$imageBase64) {
            return response()->json(['reply' => 'Pesan atau gambar tidak boleh kosong, Bos!'], 400);
        }

        if ($imageBase64 && strpos($imageBase64, ',') !== false) {
            $imageBase64 = explode(',', $imageBase64)[1];
        }

        // Ambil string keys dari config, lalu pisahkan berdasarkan koma
        $apiKeysString = config('services.gemini.api_keys');
        $apiKeys = array_filter(array_map('trim', explode(',', $apiKeysString)));

        if (empty($apiKeys)) {
            return response()->json(['reply' => 'Sistem POTA belum dikonfigurasi (API Key kosong).'], 500);
        }

        // ====================================================================
        // 1. CEK TOKO POPULER
        // ====================================================================
        $tokoPopuler = DB::table('tb_toko')
            ->where('status', 'active')
            ->whereIn('tier_toko', ['power_merchant', 'official_store'])
            ->limit(3)
            ->get(['nama_toko', 'tier_toko']);

        $infoToko = "INFO TOKO TERBAIK: ";
        if ($tokoPopuler->count() > 0) {
            foreach($tokoPopuler as $t) {
                $infoToko .= "Toko {$t->nama_toko} ({$t->tier_toko}), ";
            }
        } else {
            $infoToko .= "Belum ada toko berstatus Power Merchant.";
        }

        // ====================================================================
        // 2. MESIN PENCARI BARANG PINTAR
        // ====================================================================
        $keywords = explode(' ', strtolower($userMessage));
        $stopWords = ['yang', 'buat', 'apa', 'tolong', 'carikan', 'halo', 'pota', 'di', 'dari', 'ke', 'ini', 'itu', 'adalah', 'ada', 'jual', 'toko', 'paling', 'mahal', 'murah'];
        $cleanWords = array_diff($keywords, $stopWords);

        $infoPencarian = "";
        
        // Buat Query Dasar Barang
        $query = DB::table('tb_barang')
            ->join('tb_toko', 'tb_barang.toko_id', '=', 'tb_toko.id')
            ->where('tb_barang.is_active', 1)
            ->select('tb_barang.id', 'tb_barang.nama_barang', 'tb_barang.harga', 'tb_barang.stok', 'tb_toko.nama_toko');

        // Tambahkan kriteria populer/rating
        if (str_contains(strtolower($userMessage), 'rekomendasi') || str_contains(strtolower($userMessage), 'terlaris') || str_contains(strtolower($userMessage), 'bagus')) {
            // Jika ada kolom terjual atau rating, kita urutkan. Jika tidak, pakai ID / Harga
            // Karena belum tau skema pasti, kita asumsikan yang stoknya paling banyak (paling sering restock)
            $query->orderBy('tb_barang.stok', 'desc');
        }

        // Jika ada kata kunci spesifik
        if (count($cleanWords) > 0) {
            $query->where(function($q) use ($cleanWords) {
                foreach ($cleanWords as $word) {
                    if (strlen($word) > 2) {
                        $q->orWhere('tb_barang.nama_barang', 'like', '%' . $word . '%')
                          ->orWhere('tb_barang.deskripsi', 'like', '%' . $word . '%');
                    }
                }
            });
        }
        
        // Jika user nanya "paling mahal", kita bantu urutkan dari yang termahal
        if (str_contains(strtolower($userMessage), 'mahal')) {
            $query->orderBy('tb_barang.harga', 'desc');
        } elseif (str_contains(strtolower($userMessage), 'murah')) {
            $query->orderBy('tb_barang.harga', 'asc');
        }

        // Ambil 4 produk teratas
        $hasilCari = $query->limit(4)->get();

        if ($hasilCari->count() > 0) {
            $infoPencarian = "\n\nHASIL PENCARIAN DATABASE:\n";
            foreach($hasilCari as $item) {
                $hargaRupiah = number_format($item->harga, 0, ',', '.');
                // KITA BUAT URL ASLI MENUJU PRODUK
                $urlProduk = route('produk.detail', $item->id); 
                
                // Masukkan URL ke dalam bisikan untuk POTA
                $infoPencarian .= "- Nama: {$item->nama_barang} | Harga: Rp{$hargaRupiah} | Stok: {$item->stok} | Penjual: {$item->nama_toko} | LinkAsli: {$urlProduk}\n";
            }
        } else {
            $infoPencarian = "\n\nINFO: Maaf, barang yang dicari user saat ini sedang kosong di database.";
        }

        // Cek Status Pesanan
        $infoPesanan = "";
        if ($userId) {
            try {
                $pesanan = DB::table('tb_transaksi')
                    ->where('user_id', $userId)
                    ->orderBy('tanggal_transaksi', 'desc')
                    ->limit(3)
                    ->get();
                if ($pesanan->count() > 0) {
                    $infoPesanan = "\n\nINFO PESANAN USER SAAT INI (JIKA USER BERTANYA PESANANNYA, WAJIB TAMPILKAN KOTAK HTML INI DI BAWAH JAWABANMU. PASTIKAN TIDAK ADA BACKTICKS/CODE BLOCK): \n";
                    foreach($pesanan as $p) {
                        $link = route('pesanan.lacak', $p->kode_invoice);
                        $statusColor = $p->status_pesanan_global == 'selesai' ? 'bg-emerald-100 text-emerald-700' : 'bg-orange-100 text-orange-700';
                        $card = "<a href=\"{$link}\" class=\"block mt-3 mb-2 p-4 border border-indigo-100 rounded-xl bg-white hover:border-indigo-300 hover:shadow-md transition-all no-underline group\"><div class=\"flex justify-between items-center mb-1\"><div class=\"text-xs text-indigo-500 font-bold uppercase tracking-wider\">Invoice</div><div class=\"px-2 py-1 {$statusColor} text-[10px] font-bold rounded-md uppercase\">{$p->status_pesanan_global}</div></div><div class=\"font-black text-slate-800 text-base mb-2 group-hover:text-indigo-600 transition-colors\">{$p->kode_invoice}</div><div class=\"flex justify-between items-center pt-2 border-t border-slate-100\"><div class=\"text-xs text-slate-500 font-medium\">Total Belanja</div><div class=\"text-sm font-bold text-emerald-600\">Rp" . number_format($p->total_final, 0, ',', '.') . "</div></div></a>";
                        $infoPesanan .= "- Pesanan {$p->kode_invoice}. Kode HTML: {$card}\n";
                    }
                } else {
                    $infoPesanan = "\n\nINFO PESANAN: Saat ini user belum memiliki pesanan aktif.";
                }
            } catch(\Exception $e) {}
        }

        // ====================================================================
        // GABUNGKAN PERSONA DENGAN ATURAN HTML LINK
        // ====================================================================
        
        $systemInstruction = "Kamu adalah POTA (Pondasikita Assistant) alias 'Mandor', pakar material bangunan dan arsitektur di marketplace Pondasikita. Panggil user 'Bos' atau 'Juragan'.

ATURAN SANGAT PENTING:
1. JAWABAN HARUS SANGAT SINGKAT, PADAT, DAN TO-THE-POINT! Jangan bertele-tele. Maksimal 3 kalimat.
2. JANGAN PERNAH MENGGUNAKAN FORMAT MATEMATIKA / LATEX (seperti $2 \\times 3$, \text{}, dll). Tulis angka biasa saja (contoh: 2 x 3 meter = 6 meter persegi).
3. Jika ditanya soal pesanan/order, JIKA ADA DATA di CONTEKAN DATA, KAMU WAJIB mencetak Kode HTML yang disediakan! Jangan suruh user ngecek menu sendiri! CETAK KODE HTML TERSEBUT APA ADANYA TANPA DIBUNGKUS MARKDOWN/BACKTICKS!
4. JANGAN MENGARANG DATA. Gunakan CONTEKAN DATA di bawah ini jika relevan.
5. JIKA merekomendasikan produk dari data contekan, ubah namanya jadi link HTML: <a href=\"[LinkAsli]\" class=\"text-blue-600 font-black hover:underline\" target=\"_blank\">[Nama Barang]</a>

Contoh jawaban baik: 'Bos, untuk dinding 20m2 butuh sekitar 4 kg cat. Ane saranin ambil <a href=\"link\" class=\"text-blue-600 font-black hover:underline\" target=\"_blank\">Cat Avian 5kg</a> seharga Rp145.000. Untuk pesanan Bos dengan Invoice INV-123 saat ini statusnya selesai.'
";

        if ($imageBase64) {
            $systemInstruction .= "\n\nATURAN KHUSUS GAMBAR: User melampirkan sebuah gambar. TUGAS UTAMAMU SAAT INI ADALAH SEBAGAI AI INTERIOR DESIGNER. JIKA user bertanya tentang desain, warna, renovasi, atau apapun yang berkaitan dengan pengubahan gambar, KAMU WAJIB MENGHASILKAN GAMBAR BARU HASIL EDITAN. PENTING: Gunakan fitur Image Generation-mu dan usahakan mempertahankan struktur asli ruangan (letak perabotan, jendela, dll). WAJIB sertakan gambar dalam balasanmu (jangan hanya teks)!";
        }

        $systemInstruction .= "\n\nCONTEKAN DATA:\n" . $infoToko . $infoPencarian . $infoPesanan;

        // Format History
        $formattedContents = [];
        if (is_array($chatHistory) && count($chatHistory) > 0) {
            // Hilangkan elemen terakhir jika itu adalah pesan yang sama dengan userMessage (karena JS mengirim history yang sudah di-push userMessage)
            $lastIndex = count($chatHistory) - 1;
            if (isset($chatHistory[$lastIndex]['sender']) && $chatHistory[$lastIndex]['sender'] === 'user' && $chatHistory[$lastIndex]['text'] === $userMessage) {
                array_pop($chatHistory);
            }
            
            // Masukkan ke format Gemini
            foreach ($chatHistory as $chat) {
                if (!empty(trim($chat['text']))) {
                    $role = ($chat['sender'] === 'bot') ? 'model' : 'user';
                    // Gemini tidak suka ada 2 role yang sama berurutan. Pastikan bergantian!
                    if (count($formattedContents) > 0 && $formattedContents[count($formattedContents) - 1]['role'] === $role) {
                        continue; // Skip jika rolenya sama dengan sebelumnya
                    }
                    $formattedContents[] = [
                        'role' => $role,
                        'parts' => [['text' => $chat['text']]]
                    ];
                }
            }
        }

        // Pesan Baru
        $parts = [];
        if ($imageBase64) {
            $strictImgPrompt = "User melampirkan gambar untuk direnovasi/didesain ulang. Berikan saran ramah sebagai Mandor sesuai permintaan user. Beritahu user bahwa POTA sedang merender visualisasinya di bawah pesan ini. (TAPI JANGAN PERNAH MENGHASILKAN JSON/GAMBAR SENDIRI, TUGASMU HANYA NGOBROL).\nPermintaan user: " . ($userMessage ?: 'Tolong desain ruangan ini.');
            $parts[] = ['text' => $strictImgPrompt];
        } else {
            $parts[] = ['text' => $userMessage ?: 'Halo'];
        }

        if ($imageBase64) {
            $parts[] = [
                'inlineData' => [
                    'mimeType' => 'image/jpeg',
                    'data' => $imageBase64
                ]
            ];
        }

        // Pastikan isi pesan baru masuk ke role user
        // Cegah error consecutive roles (Jika history terakhir adalah user, pop dulu)
        if (count($formattedContents) > 0 && $formattedContents[count($formattedContents) - 1]['role'] === 'user') {
            array_pop($formattedContents);
        }

        $formattedContents[] = [
            'role' => 'user',
            'parts' => $parts
        ];

        // Model Rotation (hanya text models untuk chat)
        $models = ['gemini-3.5-flash', 'gemini-3.1-flash-lite', 'gemini-2.5-flash', 'gemini-2.0-flash'];
        
        $response = null;
        $berhasil = false;
        $pesanError = '';
        
        $replyText = '';
        $replyImage = null;

        // 1. Ambil Teks Chat dari Gemini Flash
        foreach ($apiKeys as $key) {
            foreach ($models as $model) {
                try {
                    $response = Http::withoutVerifying()
                        ->timeout(30)
                        ->withHeaders([
                            'Content-Type' => 'application/json',
                        ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$key}", [
                        'system_instruction' => [
                            'parts' => [['text' => $systemInstruction]]
                        ],
                        'contents' => $formattedContents
                    ]);

                    if ($response->successful()) {
                        $berhasil = true;
                        $data = $response->json();
                        
                        $parts = $data['candidates'][0]['content']['parts'] ?? [];
                        foreach ($parts as $part) {
                            if (isset($part['text'])) {
                                $replyText .= $part['text'];
                            }
                        }
                        
                        // Fix jika text model hallucinate JSON object
                        if (is_string($replyText) && str_starts_with(trim($replyText), '{')) {
                            $decoded = json_decode(trim($replyText), true);
                            if (json_last_error() === JSON_ERROR_NONE && isset($decoded['thought'])) {
                                $replyText = $decoded['thought'];
                            }
                        }
                        
                        break 2;
                    } else {
                        $pesanError = $response->body();
                        continue;
                    }
                } catch (\Exception $e) {
                    $pesanError = $e->getMessage();
                    continue;
                }
            }
        }

        // 2. Jika ada Gambar, panggil Imagen untuk generate visualisasi
        if ($imageBase64 && $berhasil) {
            $imagenModels = ['imagen-4.0-generate-001', 'gemini-3.1-flash-image']; // Fallback
            foreach ($apiKeys as $key) {
                foreach ($imagenModels as $model) {
                    try {
                        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:predict?key={$key}";
                        if (str_contains($model, 'gemini')) {
                            // Untuk gemini fallback
                            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$key}";
                            $payload = [
                                'contents' => [
                                    ['parts' => [
                                        ['text' => "TUGAS: Modifikasi gambar ini sesuai permintaan user: " . $userMessage],
                                        ['inlineData' => ['mimeType' => 'image/jpeg', 'data' => $imageBase64]]
                                    ]]
                                ]
                            ];
                        } else {
                            $payload = [
                                'instances' => [
                                    [
                                        'prompt' => $userMessage ?: 'Renovasi ruangan ini',
                                        'image' => [
                                            'bytesBase64Encoded' => $imageBase64
                                        ]
                                    ]
                                ],
                                'parameters' => [
                                    'sampleCount' => 1
                                ]
                            ];
                        }

                        $resImage = Http::withoutVerifying()
                            ->timeout(60)
                            ->withHeaders(['Content-Type' => 'application/json'])
                            ->post($url, $payload);

                        if ($resImage->successful()) {
                            $dataImage = $resImage->json();
                            if (str_contains($model, 'imagen')) {
                                $genImg = $dataImage['predictions'][0]['bytesBase64Encoded'] ?? null;
                            } else {
                                $genImg = $dataImage['candidates'][0]['content']['parts'][0]['inlineData']['data'] ?? null;
                                if (!$genImg) {
                                    $fallbackText = $dataImage['candidates'][0]['content']['parts'][0]['text'] ?? null;
                                    if ($fallbackText) {
                                        // Cek apakah dia output murni base64
                                        if (preg_match('/^[a-zA-Z0-9\+\/]+={0,2}$/', trim($fallbackText)) && strlen(trim($fallbackText)) > 1000) {
                                            $genImg = trim($fallbackText);
                                        } 
                                        // Cek apakah dia output markdown html <img src="data:image/jpeg;base64,...">
                                        elseif (preg_match('/data:image\/[a-zA-Z]+;base64,([a-zA-Z0-9\+\/]+={0,2})/', $fallbackText, $matches)) {
                                            $genImg = $matches[1];
                                        }
                                    }
                                }
                            }
                            
                            if ($genImg) {
                                $replyImage = $genImg;
                                break 2;
                            }
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        }
        
        if ($berhasil) {
            if ($replyImage) {
                $imgHtml = '<img src="data:image/jpeg;base64,' . $replyImage . '" class="w-full mt-2 rounded-lg shadow-md border border-zinc-200 cursor-pointer" onclick="window.open(this.src, \'_blank\')">';
                return response()->json(['reply' => ($replyText ? $replyText . '<br>' : 'Berikut gambarnya Bos!<br>') . $imgHtml]);
            }
            
            if ($replyText) {
                // Cek jika balasan berupa murni base64 string (fallback)
                if (preg_match('/^[a-zA-Z0-9\+\/]+={0,2}$/', trim($replyText)) && strlen(trim($replyText)) > 1000) {
                    $imgHtml = '<img src="data:image/jpeg;base64,' . trim($replyText) . '" class="w-full mt-2 rounded-lg shadow-md border border-zinc-200 cursor-pointer" onclick="window.open(this.src, \'_blank\')">';
                    return response()->json(['reply' => 'Ini hasil editan untuk ruanganmu, Bos!<br>' . $imgHtml]);
                }
                return response()->json(['reply' => $replyText]);
            }

            return response()->json(['reply' => 'AI berhasil memproses tapi tidak ada respon teks/gambar.']);
        } else {
            return response()->json([
                'reply' => "Waduh Bos, otak Mandor lagi pusing (semua kuota API habis atau gagal). Coba lagi nanti ya! Error detail: {$pesanError}"
            ], 500);
        }
    }
}