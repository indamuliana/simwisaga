<!-- ── Babak 3: Direktori 4 Program Keahlian Vokasi (Desain Destinasi Editorial Sesuai Gambar Referensi) ── -->
@php
    $departments = [
        'PPLG' => [
            'code' => 'PPLG',
            'name' => 'Pengembangan Perangkat Lunak & Gim',
            'bg_color' => '#0E1B2E',
            'glow_color' => 'rgba(14, 27, 46, 0.65)',
            'card_class' => 'jurusan-card-pplg',
            'image' => asset('images/landing/lab-komputer-pplg.jpg'),
            'subtitle' => 'Web & Mobile App • Cloud & AI Studio',
            'url' => url('/jurusan/pplg'),
        ],
        'TJKT' => [
            'code' => 'TJKT',
            'name' => 'Teknik Jaringan Komputer & Telekomunikasi',
            'bg_color' => '#064E3B',
            'glow_color' => 'rgba(6, 78, 59, 0.65)',
            'card_class' => 'jurusan-card-tjkt',
            'image' => asset('images/landing/lab-tjkt.jpeg'),
            'subtitle' => 'Cyber Security • Cloud Infrastructure • Fiber Optic',
            'url' => url('/jurusan/tjkt'),
        ],
        'PMN' => [
            'code' => 'PMN',
            'name' => 'Pemasaran (Bisnis Digital)',
            'bg_color' => '#4C1D95',
            'glow_color' => 'rgba(76, 29, 149, 0.65)',
            'card_class' => 'jurusan-card-pmn',
            'image' => asset('images/web/hero-pmn.jpg'),
            'subtitle' => 'Live Commerce Host • Digital Marketing & SEO',
            'url' => url('/jurusan/pmn'),
        ],
        'HTL' => [
            'code' => 'HTL',
            'name' => 'Perhotelan Internasional',
            'bg_color' => '#451A03',
            'glow_color' => 'rgba(69, 26, 3, 0.65)',
            'card_class' => 'jurusan-card-htl',
            'image' => asset('images/landing/jurusan-hotel.png'),
            'subtitle' => '5★ Hospitality Service • Barista & Banquet',
            'url' => url('/jurusan/htl'),
        ],
    ];
@endphp

<style>
    #jurusan .jurusan-card {
        border-radius: 28px;
        overflow: hidden;
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.1);
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        text-decoration: none;
    }
    #jurusan .jurusan-card:hover {
        transform: translateY(-18px) !important;
    }
    #jurusan .jurusan-card-pplg:hover {
        box-shadow: 0 28px 45px -10px rgba(14, 27, 46, 0.7);
    }
    #jurusan .jurusan-card-tjkt:hover {
        box-shadow: 0 28px 45px -10px rgba(6, 78, 59, 0.7);
    }
    #jurusan .jurusan-card-pmn:hover {
        box-shadow: 0 28px 45px -10px rgba(76, 29, 149, 0.7);
    }
    #jurusan .jurusan-card-htl:hover {
        box-shadow: 0 28px 45px -10px rgba(69, 26, 3, 0.7);
    }
    #jurusan .jurusan-btn-pill {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        transition: all 0.3s ease;
    }
    #jurusan .jurusan-card:hover .jurusan-btn-pill {
        background: rgba(255, 255, 255, 0.22);
        border-color: rgba(255, 255, 255, 0.32);
    }
</style>

<section class="py-16 sm:py-24 bg-[#FAF9F6] text-slate-800 border-b border-slate-200/80 relative overflow-hidden scroll-mt-20" 
         id="jurusan">
    <span id="fasilitas" class="absolute -top-20"></span>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <!-- Section Header (Editorial, Tenang & Berwibawa) -->
        <div class="max-w-3xl mb-12 sm:mb-16 text-left" data-animate="fade-up">
            <h2 class="text-2xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 leading-tight">
                Kompetensi Keahlian
            </h2>
            <p class="mt-3 text-base sm:text-lg text-slate-600 leading-relaxed font-normal">
                Seluruh program keahlian memadukan kurikulum vokasi industri mutakhir, fasilitas laboratorium modern berstandar kerja, dan sertifikasi profesi resmi LSP-P1 BNSP.
            </p>
        </div>

        <!-- ── Grid 4 Kolom: 4 Kartu Destinasi Vertikal Sesuai Referensi Gambar ── -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 items-stretch pt-4 pb-2" data-animate="fade-up">
            @foreach($departments as $code => $dept)
            <a href="{{ $dept['url'] }}"
               class="group jurusan-card {{ $dept['card_class'] }}"
               style="background-color: {{ $dept['bg_color'] }}; min-height: 500px;">
                
                <!-- Lapisan Visual: Foto Lab & Gradasi Dissolve Halus ke Latar Gelap Kartu -->
                <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                    <img src="{{ $dept['image'] }}" 
                         alt="{{ $dept['name'] }}" 
                         class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-out"
                         loading="lazy">
                    
                    <!-- Gradasi Mulus: Foto di atas membaur halus ke warna solid kartu di bagian bawah -->
                    <div class="absolute inset-0"
                         style="background: linear-gradient(180deg, rgba(0,0,0,0.05) 0%, rgba(0,0,0,0.2) 25%, {{ $dept['bg_color'] }} 60%, {{ $dept['bg_color'] }} 100%);">
                    </div>
                </div>

                <!-- Konten Kartu di Bagian Bawah (Sesuai Hirarki Referensi Gambar) -->
                <div class="relative z-10 flex flex-col justify-end p-5 sm:p-6 text-left w-full mt-auto">
                    
                    <!-- Judul Jurusan -->
                    <h3 class="text-xl sm:text-2xl font-bold text-white leading-snug tracking-tight">
                        {{ $dept['name'] }}
                    </h3>

                    <!-- Subtitle / Spesialisasi Ringkas -->
                    <p class="text-xs sm:text-[13px] text-white/80 font-normal mt-1.5 mb-5 tracking-wide">
                        {{ $dept['subtitle'] }}
                    </p>

                    <!-- Tombol Frosted Pill "Jelajahi Jurusan ›" -->
                    <div class="jurusan-btn-pill w-full py-3.5 px-4 rounded-2xl text-white text-xs sm:text-sm font-medium flex items-center justify-between">
                        <span class="tracking-wide">Jelajahi Jurusan</span>
                        <svg class="w-4 h-4 text-white/80 group-hover:translate-x-1.5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>

                </div>
            </a>
            @endforeach
        </div>

        <!-- ── Strip Jaminan Mutu Vokasi di Bawah Grid ── -->
        <div class="mt-10 sm:mt-12 p-6 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4 text-left" data-animate="fade-up">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-[#EBF4FC] border border-[#002752]/20 flex items-center justify-center text-[#002752] font-bold text-sm shrink-0">
                    ✓
                </div>
                <div>
                    <span class="text-xs sm:text-sm font-bold text-slate-900 block">
                        Seluruh Program Keahlian Didukung Teaching Factory &amp; Uji Kompetensi LSP-P1 BNSP
                    </span>
                    <span class="text-xs text-slate-500 block mt-0.5">
                        Kurikulum diselaraskan berkala bersama mitra industri multinasional dan perguruan tinggi vokasi terkemuka.
                    </span>
                </div>
            </div>
            <a href="#kontak" class="text-xs sm:text-sm font-bold text-[#002752] hover:text-[#014694] whitespace-nowrap inline-flex items-center gap-1 group shrink-0">
                <span>Konsultasi Pemilihan Jurusan</span>
                <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
            </a>
        </div>

    </div>
</section>
