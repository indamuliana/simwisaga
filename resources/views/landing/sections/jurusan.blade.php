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


                        <!-- Konten Kartu di Bagian Bawah (Sesuai Hirarki Referensi Gambar) -->
                        <div class="relative z-10 flex flex-col justify-end space-y-3.5 text-left w-full mt-auto">
                            
                            <!-- Baris Judul Jurusan -->
                            <div>
                                <h3 class="text-xl sm:text-2xl font-extrabold text-white leading-tight tracking-tight">
                                    {{ $dept['name'] }}
                                </h3>
                            </div>

                            <!-- Deskripsi Singkat 2-3 Baris Tenang & Nyaman Dibaca -->
                            <p class="text-xs sm:text-[13px] text-slate-200/90 leading-relaxed font-normal line-clamp-3">
                                {{ $dept['desc'] }}
                            </p>

                            <!-- Tombol Solid Google Primary Blue ("Lihat Detail Jurusan") -->
                            <div class="pt-2">
                                <a href="{{ $dept['url'] }}" 
                                   style="background-color: #1A73E8;"
                                   class="w-full py-3 px-4 rounded-full text-white text-xs sm:text-sm font-bold text-center block shadow-md hover:bg-[#1557B0] hover:shadow-lg hover:scale-[1.02] active:scale-[0.98] transition-all">
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
