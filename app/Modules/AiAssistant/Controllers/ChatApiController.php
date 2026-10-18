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
        // $apiKey = env('GEMINI_API_KEY');
        $apiKey = config('services.gemini.api_key', env('GEMINI_API_KEY'));


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

            $sqlResponse = $this->callGemini($apiKey, $sqlPrompt, [
                'maxOutputTokens' => 300,
                'temperature' => 0.1,
            ]);
            
            // Bersihkan hasil response dari markdown jika ada
            $sqlQuery = trim(str_replace(['```sql', '```'], '', $sqlResponse));

            // Validasi keamanan: Pastikan hanya SELECT
            if (empty($sqlQuery) || !preg_match('/^SELECT\s+/i', $sqlQuery)) {
                // Jika bukan query SQL yang valid atau AI memutuskan tidak butuh query
                return $this->generateFinalAnswer($apiKey, $userMessage, [], $personaStyle, $basePrompt);
            }

            // Bersihkan semicolon di akhir, tolak multi-query
            $cleanedSql = rtrim($sqlQuery, "; \t\n\r");
            if (str_contains($cleanedSql, ';')) {
                throw new \Exception("Query multi-statement tidak diizinkan.");
            }

            // Mencegah query destruktif, akses ke tabel sensitif, dan ekstraksi kredensial
            $forbiddenPatterns = [
                '/\b(INSERT|UPDATE|DELETE|DROP|TRUNCATE|ALTER|CREATE|REPLACE|GRANT|REVOKE)\b/i',
                '/\b(users|audits|sessions|password_resets|cache|failed_jobs|personal_access_tokens)\b/i',
                '/\b(password|remember_token|secret)\b/i',
                '/\b(INTO\s+OUTFILE|INTO\s+DUMPFILE|LOAD_FILE|BENCHMARK|SLEEP)\b/i',
            ];
            foreach ($forbiddenPatterns as $pattern) {
                if (preg_match($pattern, $cleanedSql)) {
                    throw new \Exception("Query tidak diizinkan karena mengakses data sensitif atau operasi terlarang.");
                }
            }

            // 3. Eksekusi SQL
            try {
                $queryResult = DB::select($cleanedSql);
            } catch (\Exception $e) {
                Log::error("Error mengeksekusi AI SQL: " . $e->getMessage() . " | Query: " . $sqlQuery);
                $queryResult = []; // Anggap kosong jika error
            }

            // 4. Step 2: Hasilkan Jawaban Akhir
            return $this->generateFinalAnswer($apiKey, $userMessage, $queryResult, $personaStyle, $basePrompt);

        } catch (\Exception $e) {
            Log::error("AI Chat Error: " . $e->getMessage());

            $message = $e->getMessage();
            if (str_contains($message, 'quota') || str_contains($message, '429') || str_contains($message, 'RESOURCE_EXHAUSTED') || str_contains($message, 'Batas kuota')) {
                $friendlyMessage = 'Maaf, batas kuota gratis Gemini API (Free Tier) untuk hari ini telah tercapai. Silakan coba kembali dalam beberapa jam atau gunakan Google AI API Key lain di file .env.';
            } else {
                $friendlyMessage = 'Maaf, terjadi kesalahan saat memproses permintaan Anda: ' . $message;
            }

            return response()->json([
                'success' => false,
                'message' => $friendlyMessage
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

        $answer = $this->callGemini($apiKey, $finalPrompt, [
            'maxOutputTokens' => 1200,
            'temperature' => 0.7,
        ]);

        return response()->json([
            'success' => true,
            'message' => $answer
        ]);
    }

    private function getDatabaseSchema()
    {
        $sensitiveColumns = [
            'nik', 'no_kk', 'no_akta_lahir', 'nomor_rekening_bank', 'rekening_atas_nama',
            'hp', 'telepon', 'email', 'user_id', 'foto_path', 'skhun', 'no_peserta_un', 'no_seri_ijazah'
        ];

        $schema = "";
        foreach ($this->relevantTables as $table) {
            try {
                $columns = DB::select('SHOW COLUMNS FROM ' . $table);
                $schema .= "Tabel: $table\nKolom: ";
                $colNames = [];
                foreach ($columns as $column) {
                    if (in_array(strtolower($column->Field), $sensitiveColumns)) {
                        continue;
                    }
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

    private function callGemini($apiKey, $prompt, array $generationConfig = [])
    {
        $primaryModel = config('services.gemini.model', env('GEMINI_MODEL', 'gemini-flash-lite-latest'));
        $fallbackModels = [
            'gemini-flash-lite-latest',
            'gemini-3.5-flash-lite',
            'gemini-3.1-flash-lite',
            'gemini-3.8-flash',
        ];
        
        $modelsToTry = array_values(array_unique(array_merge([$primaryModel], $fallbackModels)));
        $lastError = null;

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ];

        if (!empty($generationConfig)) {
            $payload['generationConfig'] = $generationConfig;
        }

        foreach ($modelsToTry as $model) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

            try {
                $response = Http::timeout(45)->withHeaders([
                    'X-goog-api-key' => $apiKey
                ])->post($url, $payload);

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                        return $data['candidates'][0]['content']['parts'][0]['text'];
                    }
                }

                $status = $response->status();
                $body = $response->json();

                // Jika batas kuota habis (429) atau server sibuk (503), coba model cadangan
                if ($status === 429 || $status === 503 || (isset($body['error']['status']) && in_array($body['error']['status'], ['RESOURCE_EXHAUSTED', 'UNAVAILABLE']))) {
                    Log::warning("Gemini model {$model} mencapai batas kuota atau sibuk (HTTP {$status}). Mencoba model cadangan...");
                    $lastError = "Batas kuota harian atau beban tinggi pada model {$model}.";
                    continue;
                }

                // Error lain
                $errorMsg = $body['error']['message'] ?? $response->body();
                throw new \Exception("Gagal menghubungi API Gemini ({$model}): " . $errorMsg);

            } catch (\Exception $e) {
                if (str_contains($e->getMessage(), 'kuota') || str_contains($e->getMessage(), '429') || str_contains($e->getMessage(), '503') || str_contains($e->getMessage(), 'high demand')) {
                    $lastError = $e->getMessage();
                    continue;
                }
                throw $e;
            }
        }

        throw new \Exception($lastError ?? "Batas kuota Gemini API (Free Tier) telah habis untuk semua model.");
    }
}
