        <!-- ── Babak 3: Direktori 4 Program Keahlian Vokasi (Desain Destinasi Editorial Sesuai Gambar Referensi) ── -->
        @php
            $departments = [
                'PPLG' => [
                    'code' => 'PPLG',
                    'name' => 'Pengembangan Perangkat Lunak',
                    'bg_color' => '#0E1B2E',
                    'image' => asset('images/landing/lab-komputer-pplg.jpg'),
                    'desc' => 'Mencetak software engineer berkeahlian web fullstack, aplikasi mobile Flutter, machine learning & AI, serta database cloud berstandar industri modern.',
                    'tag_rating' => '4.9 Unggul',
                    'tag_focus' => 'Web & AI Studio',
                    'tag_cert' => 'LSP-P1 BNSP',
                    'url' => url('/jurusan/pplg'),
                    'icon_svg' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>',
                ],
                'TJKT' => [
                    'code' => 'TJKT',
                    'name' => 'Teknik Jaringan Komputer',
                    'bg_color' => '#0B2028',
                    'image' => asset('images/landing/lab-tjkt.jpeg'),
                    'desc' => 'Penguasaan arsitektur jaringan fiber optik enterprise, administrasi server Linux cloud, keamanan siber (cybersecurity), dan integrasi router MikroTik.',
                    'tag_rating' => '4.9 Unggul',
                    'tag_focus' => 'Cyber & Cloud',
                    'tag_cert' => 'MikroTik MTCNA',
                    'url' => url('/jurusan/tjkt'),
                    'icon_svg' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/></svg>',
                ],
                'PMN' => [
                    'code' => 'PMN',
                    'name' => 'Pemasaran (Bisnis Digital)',
                    'bg_color' => '#191A2A',
                    'image' => asset('images/web/hero-pmn.jpg'),
                    'desc' => 'Keahlian digital marketing komprehensif: live streamer host, optimasi kampanye iklan berbayar (Meta & Google Ads), SEO specialist, dan analitik ritel WikraMart.',
                    'tag_rating' => '4.8 Unggul',
                    'tag_focus' => 'Live Commerce',
                    'tag_cert' => 'Meta & Google Ads',
                    'url' => url('/jurusan/pmn'),
                    'icon_svg' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>',
                ],
                'HTL' => [
                    'code' => 'HTL',
                    'name' => 'Perhotelan Internasional',
                    'bg_color' => '#241820',
                    'image' => asset('images/landing/jurusan-hotel.png'),
                    'desc' => 'Keterampilan pelayanan hotel bintang 5 bertaraf internasional: reservasi front office, mock-up suite room housekeeping, tata hidang banquet, dan barista bersertifikat.',
                    'tag_rating' => '4.9 Unggul',
                    'tag_focus' => '5★ Hotel & Barista',
                    'tag_cert' => 'BNSP & Barista',
                    'url' => url('/jurusan/htl'),
                    'icon_svg' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>',
                ],
            ];
        @endphp

        <section class="py-16 sm:py-24 bg-[#FAF9F6] text-slate-800 border-b border-slate-200/80 relative overflow-hidden scroll-mt-20" 
                 id="jurusan">
            <span id="fasilitas" class="absolute -top-20"></span>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                
                <!-- Section Header (Editorial, Tenang & Berwibawa) -->
                <div class="max-w-3xl mb-12 sm:mb-16 text-left" data-animate="fade-up">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-[#EBF4FC] text-[#002752] border border-[#002752]/20 mb-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#002752]"></span>
                        <span>Program Keahlian Terakreditasi Unggul</span>
                    </span>
                    <h2 class="text-2xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 leading-tight">
                        4 Pilihan Karir Masa Depan di Wikrama
                    </h2>
                    <p class="mt-3 text-base sm:text-lg text-slate-600 leading-relaxed font-normal">
                        Seluruh program keahlian memadukan kurikulum vokasi industri mutakhir, fasilitas laboratorium modern berstandar kerja, dan sertifikasi profesi resmi LSP-P1 BNSP.
                    </p>
                </div>

                <!-- ── Grid 4 Kolom: 4 Kartu Destinasi Vertikal Sesuai Referensi Gambar ── -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 items-stretch" data-animate="fade-up">
                    @foreach($departments as $code => $dept)
                    <article class="group relative rounded-[28px] overflow-hidden flex flex-col justify-end p-5 sm:p-6 text-white transition-all duration-300 hover:-translate-y-1.5 hover:shadow-2xl border border-white/10"
                             style="background-color: {{ $dept['bg_color'] }}; min-height: 520px;">
                        
                        <!-- Lapisan Visual: Foto Lab & Gradasi Dissolve Halus ke Latar Gelap Kartu -->
                        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                            <img src="{{ $dept['image'] }}" 
                                 alt="{{ $dept['name'] }}" 
                                 class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-out"
                                 loading="lazy">
                            
                            <!-- Gradasi Mulus: Foto di atas membaur halus ke warna kartu di bagian bawah -->
                            <div class="absolute inset-0"
                                 style="background: linear-gradient(180deg, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.35) 30%, {{ $dept['bg_color'] }} 62%, {{ $dept['bg_color'] }} 100%);">
                            </div>
                        </div>

                        <!-- Ikon Frosted Glass Melayang di Pojok Kanan Atas (Kontras Tinggi dengan Dark Glass) -->
                        <div class="absolute top-5 right-5 z-20 pointer-events-none">
                            <div class="w-10 h-10 rounded-full bg-slate-950/50 backdrop-blur-md border border-white/30 flex items-center justify-center text-white shadow-md group-hover:bg-slate-950/70 transition-all">
                                {!! $dept['icon_svg'] !!}
                            </div>
                        </div>

                        <!-- Konten Kartu di Bagian Bawah (Sesuai Hirarki Referensi Gambar) -->
                        <div class="relative z-10 flex flex-col justify-end space-y-3.5 text-left w-full mt-auto">
                            
                            <!-- Baris Judul & Badge Kode Jurusan / Akreditasi -->
                            <div class="flex items-start justify-between gap-2.5">
                                <h3 class="text-xl sm:text-2xl font-extrabold text-white leading-tight tracking-tight">
                                    {{ $dept['name'] }}
                                </h3>
                                <div class="shrink-0 px-2.5 py-1 rounded-full bg-white/20 backdrop-blur-md border border-white/25 text-white text-xs font-mono font-bold shadow-xs">
                                    {{ $dept['code'] }} &bull; A
                                </div>
                            </div>

                            <!-- Deskripsi Singkat 2-3 Baris Tenang & Nyaman Dibaca -->
                            <p class="text-xs sm:text-[13px] text-slate-200/90 leading-relaxed font-normal line-clamp-3">
                                {{ $dept['desc'] }}
                            </p>

                            <!-- 3 Lencana Pill Kaca Frosted (Rating, Fokus, Lisensi) -->
                            <div class="flex flex-wrap items-center gap-1.5 pt-0.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-white/15 backdrop-blur-sm border border-white/20 text-[11px] font-medium text-white/95">
                                    <svg class="w-3 h-3 text-[#F9D900]" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <span>{{ $dept['tag_rating'] }}</span>
                                </span>
                                <span class="px-2.5 py-1 rounded-full bg-white/15 backdrop-blur-sm border border-white/20 text-[11px] font-medium text-white/95">
                                    {{ $dept['tag_focus'] }}
                                </span>
                                <span class="px-2.5 py-1 rounded-full bg-white/15 backdrop-blur-sm border border-white/20 text-[11px] font-medium text-white/95">
                                    {{ $dept['tag_cert'] }}
                                </span>
                            </div>

                            <!-- Tombol Putih Penuh ("Book now" Style -> "Lihat Detail Jurusan") -->
                            <div class="pt-2">
                                <a href="{{ $dept['url'] }}" 
                                   class="w-full py-3 px-4 rounded-full bg-white text-slate-900 text-xs sm:text-sm font-bold text-center block shadow-md hover:bg-slate-100 hover:shadow-lg hover:scale-[1.02] active:scale-[0.98] transition-all">
                                    Lihat Detail Jurusan
                                </a>
                            </div>

                        </div>
                    </article>
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
