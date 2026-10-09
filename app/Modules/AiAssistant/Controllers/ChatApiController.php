<?php

namespace App\Modules\AiAssistant\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AiAssistant\Models\AiSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatApiController extends Controller
{
    private $relevantTables = [
        'siswas',
        'absensi_harians',
        'absensi_mapels',
        'nilai_akhirs',
        'nilai_formatifs',
        'nilai_sumatifs',
        'rombels',
        'rayons',
        'jurusans',
        'mapels',
        'pegawais',
    ];

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $userMessage = $request->input('message');
        $apiKey = env('GEMINI_API_KEY');

        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'message' => 'GEMINI_API_KEY belum dikonfigurasi di file .env.'
            ], 500);
        }

        $setting = AiSetting::first();
        $personaStyle = $setting ? $setting->style : 'Sopan dan informatif.';
        $basePrompt = $setting ? $setting->base_prompt : 'Kamu adalah AI Assistant SIRAGA.';

        try {
            // 1. Dapatkan skema database
            $schema = $this->getDatabaseSchema();

            // 2. Step 1: Tanya Gemini untuk generate SQL
            $sqlPrompt = "Berikut adalah skema tabel database SIRAGA:\n" . $schema . "\n\n";
            $sqlPrompt .= "Pertanyaan pengguna: \"{$userMessage}\"\n\n";
            $sqlPrompt .= "Buatkan SATU query SQL (hanya perintah SELECT) untuk menjawab pertanyaan tersebut berdasarkan skema di atas. ";
            $sqlPrompt .= "Perhatikan relasi antar tabel. Kembalikan HANYA teks query SQL-nya saja, tanpa markdown block (```sql) dan tanpa penjelasan tambahan.";

            $sqlResponse = $this->callGemini($apiKey, $sqlPrompt);
            
            // Bersihkan hasil response dari markdown jika ada
            $sqlQuery = trim(str_replace(['```sql', '```'], '', $sqlResponse));

            // Validasi keamanan: Pastikan hanya SELECT
            if (empty($sqlQuery) || stripos($sqlQuery, 'SELECT') !== 0) {
                // Jika bukan query SQL yang valid atau AI memutuskan tidak butuh query
                return $this->generateFinalAnswer($apiKey, $userMessage, [], $personaStyle, $basePrompt);
            }

            // Mencegah query destruktif
            $forbiddenKeywords = ['INSERT ', 'UPDATE ', 'DELETE ', 'DROP ', 'TRUNCATE ', 'ALTER ', 'CREATE '];
            foreach ($forbiddenKeywords as $keyword) {
                if (stripos($sqlQuery, $keyword) !== false) {
                    throw new \Exception("Query tidak diizinkan.");
                }
            }

            // 3. Eksekusi SQL
            try {
                $queryResult = DB::select($sqlQuery);
            } catch (\Exception $e) {
                Log::error("Error mengeksekusi AI SQL: " . $e->getMessage() . " | Query: " . $sqlQuery);
                $queryResult = []; // Anggap kosong jika error
            }

            // 4. Step 2: Hasilkan Jawaban Akhir
            return $this->generateFinalAnswer($apiKey, $userMessage, $queryResult, $personaStyle, $basePrompt);

        } catch (\Exception $e) {
            Log::error("AI Chat Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Maaf, terjadi kesalahan saat memproses permintaan Anda: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateFinalAnswer($apiKey, $userMessage, $queryResult, $personaStyle, $basePrompt)
    {
        $finalPrompt = "Instruksi Utama (Base Prompt): {$basePrompt}\n";
        $finalPrompt .= "Gaya Bahasa (Persona): {$personaStyle}\n\n";
        $finalPrompt .= "Pertanyaan pengguna: \"{$userMessage}\"\n\n";
        
        if (empty($queryResult)) {
            $finalPrompt .= "Hasil Query Database: Kosong.\n";
            $finalPrompt .= "PENTING: Karena data tidak ditemukan, kamu WAJIB menjawab dengan menyertakan kalimat: \"Maaf, saya tidak menemukan data yang anda cari di sistem.\" Kamu boleh menggabungkannya dengan gaya bahasamu sendiri.";
        } else {
            // Batasi hasil query agar tidak melebihi batas token (ambil maksimal 50 baris pertama)
            $limitedResult = array_slice($queryResult, 0, 50);
            $finalPrompt .= "Hasil Query Database (JSON):\n" . json_encode($limitedResult) . "\n\n";
            $finalPrompt .= "Tugasmu: Berikan jawaban yang alami dan mudah dibaca (dalam format teks biasa atau markdown) berdasarkan data JSON di atas untuk menjawab pertanyaan pengguna. Jika data terlalu banyak, buatlah kesimpulan atau rekapitulasinya.";
        }

        $answer = $this->callGemini($apiKey, $finalPrompt);

        return response()->json([
            'success' => true,
            'message' => $answer
        ]);
    }

    private function getDatabaseSchema()
    {
        $schema = "";
        foreach ($this->relevantTables as $table) {
            try {
                $columns = DB::select('SHOW COLUMNS FROM ' . $table);
                $schema .= "Tabel: $table\nKolom: ";
                $colNames = [];
                foreach ($columns as $column) {
                    $colNames[] = $column->Field . " (" . $column->Type . ")";
                }
                $schema .= implode(", ", $colNames) . "\n\n";
            } catch (\Exception $e) {
                // Abaikan jika tabel tidak ada
                continue;
            }
        }
        return $schema;
    }

    private function callGemini($apiKey, $prompt)
    {
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent';

        $response = Http::withHeaders([
            'X-goog-api-key' => $apiKey
        ])->post($url, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ]);

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                return $data['candidates'][0]['content']['parts'][0]['text'];
            }
        }
        
        throw new \Exception("Gagal menghubungi API Gemini: " . $response->body());
    }
}
