<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Reset data nilai lama sesuai instruksi
        try {
            DB::table('nilai_formatifs')->delete();
            DB::table('nilai_sumatifs')->delete();
            DB::table('nilai_akhirs')->delete();
            if (Schema::hasTable('nilai_asesmen_harians')) {
                DB::table('nilai_asesmen_harians')->delete();
            }
            if (Schema::hasTable('nilai_mapil_formatifs')) {
                DB::table('nilai_mapil_formatifs')->delete();
            }
            if (Schema::hasTable('nilai_mapil_sumatifs')) {
                DB::table('nilai_mapil_sumatifs')->delete();
            }
            if (Schema::hasTable('nilai_mapil_akhirs')) {
                DB::table('nilai_mapil_akhirs')->delete();
            }
        } catch (\Throwable $e) {
            // Abaikan jika tabel belum ada atau kosong
        }

        // 2. Modifikasi tabel tujuan_pembelajarans (hapus dependensi AH, tambah target_periode)
        Schema::table('tujuan_pembelajarans', function (Blueprint $table) {
            if (!Schema::hasColumn('tujuan_pembelajarans', 'target_periode')) {
                $table->string('target_periode', 10)->default('9M1')->after('kode');
            }
            if (!Schema::hasColumn('tujuan_pembelajarans', 'lingkup_materi')) {
                $table->string('lingkup_materi', 255)->nullable()->after('target_periode');
            }
            if (!Schema::hasColumn('tujuan_pembelajarans', 'urutan')) {
                $table->unsignedSmallInteger('urutan')->default(1)->after('deskripsi');
            }
            if (Schema::hasColumn('tujuan_pembelajarans', 'alokasi_ah')) {
                $table->string('alokasi_ah', 20)->nullable()->change();
            }
        });

        // 3. Buat tabel nilai_harians (Nilai Harian / NH subjektif guru)
        if (!Schema::hasTable('nilai_harians')) {
            Schema::create('nilai_harians', function (Blueprint $table) {
                $table->id();
                $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
                $table->foreignId('penugasan_mengajar_id')->constrained('penugasan_mengajars')->cascadeOnDelete();
                $table->string('target_periode', 10); // '9M1', '9M2', '9M3', '9M4'
                $table->decimal('nilai', 5, 2)->default(100.00);
                $table->timestamps();

                $table->unique(['siswa_id', 'penugasan_mengajar_id', 'target_periode'], 'uk_nh_siswa_pm_periode');
            });
        }

        // 4. Modifikasi tabel nilai_akhirs (tambah predikat dan metrik)
        Schema::table('nilai_akhirs', function (Blueprint $table) {
            if (!Schema::hasColumn('nilai_akhirs', 'predikat')) {
                $table->string('predikat', 50)->nullable()->after('nilai_akhir');
            }
            if (!Schema::hasColumn('nilai_akhirs', 'rata_tp')) {
                $table->decimal('rata_tp', 5, 2)->nullable()->after('rata_formatif');
            }
            if (!Schema::hasColumn('nilai_akhirs', 'nh')) {
                $table->decimal('nh', 5, 2)->nullable()->after('rata_tp');
            }
            if (!Schema::hasColumn('nilai_akhirs', 'sumatif')) {
                $table->decimal('sumatif', 5, 2)->nullable()->after('nh');
            }
            if (!Schema::hasColumn('nilai_akhirs', 'status')) {
                $table->string('status', 30)->default('belum_lengkap')->after('predikat');
            }
        });

        // 5. Buat tabel pengaturan_penilaians (Bobot dan rentang predikat capaian)
        if (!Schema::hasTable('pengaturan_penilaians')) {
            Schema::create('pengaturan_penilaians', function (Blueprint $table) {
                $table->id();
                $table->unsignedTinyInteger('bobot_tp')->default(50);
                $table->unsignedTinyInteger('bobot_sts')->default(30);
                $table->unsignedTinyInteger('bobot_nh')->default(20);
                $table->decimal('nh_default_value', 5, 2)->default(100.00);
                $table->json('predikat_intervals')->nullable();
                $table->timestamps();
            });

            // Seed default row
            DB::table('pengaturan_penilaians')->insert([
                'bobot_tp' => 50,
                'bobot_sts' => 30,
                'bobot_nh' => 20,
                'nh_default_value' => 100.00,
                'predikat_intervals' => json_encode([
                    'mulai_berkembang' => ['min' => 75.00, 'max' => 80.99, 'label' => 'Mulai Berkembang'],
                    'layak' => ['min' => 81.00, 'max' => 87.99, 'label' => 'Layak'],
                    'cakap' => ['min' => 88.00, 'max' => 93.99, 'label' => 'Cakap'],
                    'mahir' => ['min' => 94.00, 'max' => 100.00, 'label' => 'Mahir'],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 6. Buat tabel nilai_mapil_harians (Nilai Harian untuk Mapel Pilihan)
        if (!Schema::hasTable('nilai_mapil_harians')) {
            Schema::create('nilai_mapil_harians', function (Blueprint $table) {
                $table->id();
                $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
                $table->foreignId('mapel_pilihan_id')->constrained('mapel_pilihans')->cascadeOnDelete();
                $table->string('target_periode', 10); // '9M1', '9M2', '9M3', '9M4'
                $table->decimal('nilai', 5, 2)->default(100.00);
                $table->timestamps();

                $table->unique(['siswa_id', 'mapel_pilihan_id', 'target_periode'], 'uk_nmh_siswa_mapil_periode');
            });
        }

        // 7. Modifikasi tabel nilai_mapil_akhirs (tambah predikat)
        if (Schema::hasTable('nilai_mapil_akhirs')) {
            Schema::table('nilai_mapil_akhirs', function (Blueprint $table) {
                if (!Schema::hasColumn('nilai_mapil_akhirs', 'predikat')) {
                    $table->string('predikat', 50)->nullable()->after('nilai_akhir');
                }
            });
        }

        // 8. Modifikasi tabel nilai_mapil_formatifs agar mendukung mapel_pilihan_tp_id
        if (Schema::hasTable('nilai_mapil_formatifs')) {
            Schema::table('nilai_mapil_formatifs', function (Blueprint $table) {
                if (!Schema::hasColumn('nilai_mapil_formatifs', 'mapel_pilihan_tp_id')) {
                    $table->foreignId('mapel_pilihan_tp_id')->nullable()->after('siswa_id')->constrained('mapel_pilihan_tps')->cascadeOnDelete();
                }
                if (Schema::hasColumn('nilai_mapil_formatifs', 'kode_ah')) {
                    $table->string('kode_ah', 20)->nullable()->change();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_mapil_harians');
        Schema::dropIfExists('pengaturan_penilaians');
        Schema::dropIfExists('nilai_harians');

        if (Schema::hasColumn('tujuan_pembelajarans', 'target_periode')) {
            Schema::table('tujuan_pembelajarans', function (Blueprint $table) {
                $table->dropColumn(['target_periode', 'lingkup_materi', 'urutan']);
            });
        }

        if (Schema::hasColumn('nilai_akhirs', 'predikat')) {
            Schema::table('nilai_akhirs', function (Blueprint $table) {
                $table->dropColumn(['predikat', 'rata_tp', 'nh', 'sumatif', 'status']);
            });
        }
    }
};
