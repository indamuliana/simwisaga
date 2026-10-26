    <!-- ── Prestige High-Performance Navy Gradient Institutional Footer ── -->
    @php
        $isSubpage = !request()->is('/');
    @endphp
    <style>
        #kontak,
        footer#kontak {
            background: #001833 !important;
            background: linear-gradient(180deg, #002244 0%, #001833 45%, #000D1C 100%) !important;
            color: #cbd5e1 !important;
        }
        #kontak .footer-accreditation-bar {
            background-color: rgba(0, 15, 35, 0.75) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12) !important;
        }
    </style>
    <footer class="text-slate-300 relative overflow-hidden" id="kontak" style="background: #001833; background: linear-gradient(180deg, #002244 0%, #001833 45%, #000D1C 100%) !important; color: #cbd5e1 !important;">
        
        <!-- Subtle Ambient Lighting Glow Orbs & Dot Matrix Mesh -->
        <div class="absolute inset-0 pointer-events-none z-0">
            <div class="absolute -top-32 -right-32 w-[30rem] h-[30rem] rounded-full bg-blue-600/15 blur-3xl"></div>
            <div class="absolute -bottom-32 -left-32 w-[28rem] h-[28rem] rounded-full bg-[#00519E]/20 blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 rounded-full bg-sky-500/5 blur-3xl"></div>
            <div class="absolute inset-0 bg-[radial-gradient(rgba(255,255,255,0.06)_1px,transparent_1px)] [background-size:24px_24px] opacity-40"></div>
        </div>

        <!-- Tier 1: Institutional Accreditation & Excellence Badges -->
        <div class="footer-accreditation-bar border-b border-white/10 bg-black/25 backdrop-blur-md py-4 relative z-10" style="background-color: rgba(0, 15, 35, 0.75) !important; border-bottom: 1px solid rgba(255, 255, 255, 0.12) !important;">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-center justify-between gap-4 text-xs font-semibold text-blue-100/90">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#FBBC05] shadow-[0_0_8px_#FBBC05]"></span>
                        <span>SMK Pusat Keunggulan (Center of Excellence)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#34A853] shadow-[0_0_8px_#34A853]"></span>
                        <span>Terakreditasi A (Unggul) BAN-SM</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#4285F4] shadow-[0_0_8px_#4285F4]"></span>
                        <span>Sekolah Adiwiyata Mandiri Berbudaya Lingkungan</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#EA4335] shadow-[0_0_8px_#EA4335]"></span>
                        <span>Sertifikasi Kompetensi BNSP &amp; Industri Global</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-18 lg:py-20 relative z-10">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-10">
                
                <!-- School Info & Crest -->
                <div class="sm:col-span-2 lg:col-span-5 space-y-4">
                    <div class="flex items-center space-x-3.5">
                        <img src="{{ $logoUrl }}" alt="Logo {{ $schoolName }}" class="h-11 sm:h-13 w-auto object-contain">
                        <div>
                            <span class="text-base sm:text-lg font-bold text-white block leading-tight">{{ $schoolName }}</span>
                            <span class="text-xs text-[#F5D84A] font-semibold">{{ $cfg['branding']['badge'] ?? 'SMK Pusat Keunggulan • Terakreditasi A Unggul' }}</span>
                        </div>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-200 leading-relaxed max-w-sm font-editorial italic">
                        &ldquo;{{ $cfg['branding']['motto_primary'] ?? 'Ilmu yang Amaliah, Amal yang Ilmiah, Akhlakul Karimah' }}&rdquo;
                    </p>
                    <p class="text-xs text-blue-100/75 leading-relaxed max-w-sm">
                        Sekolah Menengah Kejuruan Pusat Keunggulan berakreditasi A Unggul di Kabupaten Garut, berfokus pada teknologi mutakhir, bisnis digital, dan hospitaliti pariwisata berbudaya lingkungan.
                    </p>
                    <div class="text-xs text-blue-100/70 pt-1">
                        Sistem Informasi Sekolah Terpadu: <span class="font-bold text-white">{{ $appName }}</span> ({{ $sekolah->kepanjangan_aplikasi ?? 'Sistem Informasi Wikrama Garut' }})
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="lg:col-span-3 space-y-3">
                    <h4 class="text-xs font-bold text-[#F5D84A] uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#F5D84A]"></span>
                        <span>NAVIGASI UTAMA</span>
                    </h4>
                    <ul class="space-y-2.5 text-xs font-medium">
                        @if($footerMenus->isNotEmpty())
                            @foreach($footerMenus as $fm)
                                @php
                                    $footerUrl = str_starts_with($fm->url, 'http://') || str_starts_with($fm->url, 'https://')
                                        ? $fm->url
                                        : (str_starts_with($fm->url, '#') ? ($isSubpage ? url('/' . $fm->url) : $fm->url) : url($fm->url));
                                @endphp
                                <li>
                                    <a href="{{ $footerUrl }}" target="{{ $fm->target }}" class="hover:text-[#F5D84A] transition-colors inline-flex items-center gap-1 hover:translate-x-1 duration-200">
                                        <span>&rsaquo;</span>
                                        <span>{{ $fm->label }}</span>
                                    </a>
                                </li>
                            @endforeach
                        @else
                            <li><a href="{{ $isSubpage ? url('/#beranda') : '#beranda' }}" class="hover:text-[#F5D84A] transition-colors inline-flex items-center gap-1 hover:translate-x-1 duration-200"><span>&rsaquo;</span><span>Beranda Utama</span></a></li>
                            <li><a href="{{ $isSubpage ? url('/#filosofi') : '#filosofi' }}" class="hover:text-[#F5D84A] transition-colors inline-flex items-center gap-1 hover:translate-x-1 duration-200"><span>&rsaquo;</span><span>Filosofi &amp; Karakter</span></a></li>
                            <li><a href="{{ $isSubpage ? url('/#mengenal') : '#mengenal' }}" class="hover:text-[#F5D84A] transition-colors inline-flex items-center gap-1 hover:translate-x-1 duration-200"><span>&rsaquo;</span><span>Mengenal Lebih Dekat</span></a></li>
                            <li><a href="{{ url('/jurusan') }}" class="hover:text-[#F5D84A] transition-colors inline-flex items-center gap-1 hover:translate-x-1 duration-200"><span>&rsaquo;</span><span>4 Program Keahlian</span></a></li>
                            <li><a href="{{ $isSubpage ? url('/#dinamika-siswa') : '#dinamika-siswa' }}" class="hover:text-[#F5D84A] transition-colors inline-flex items-center gap-1 hover:translate-x-1 duration-200"><span>&rsaquo;</span><span>Dinamika Kehidupan Siswa</span></a></li>
                            <li><a href="{{ $isSubpage ? url('/#fasilitas') : '#fasilitas' }}" class="hover:text-[#F5D84A] transition-colors inline-flex items-center gap-1 hover:translate-x-1 duration-200"><span>&rsaquo;</span><span>Fasilitas &amp; Laboratorium</span></a></li>
                            <li><a href="{{ url('/berita') }}" class="hover:text-[#F5D84A] transition-colors inline-flex items-center gap-1 hover:translate-x-1 duration-200"><span>&rsaquo;</span><span>Portal Berita &amp; Prestasi</span></a></li>
                            <li><a href="{{ $isSubpage ? url('/#agenda') : '#agenda' }}" class="hover:text-[#F5D84A] transition-colors inline-flex items-center gap-1 hover:translate-x-1 duration-200"><span>&rsaquo;</span><span>Agenda Kegiatan</span></a></li>
                            <li><a href="{{ $isSubpage ? url('/#rapor-mutu') : '#rapor-mutu' }}" class="hover:text-[#F5D84A] transition-colors inline-flex items-center gap-1 hover:translate-x-1 duration-200"><span>&rsaquo;</span><span>Rapor Mutu Kemendikdasmen</span></a></li>
                            <li><a href="{{ $isSubpage ? url('/#sekolah-teladan') : '#sekolah-teladan' }}" class="hover:text-[#F5D84A] transition-colors inline-flex items-center gap-1 hover:translate-x-1 duration-200"><span>&rsaquo;</span><span>Sekolah Teladan Nasional</span></a></li>
                            <li><a href="{{ $cfg['branding']['brochure_url'] ?? 'http://brosur.smkwikrama1garut.sch.id/' }}" target="_blank" rel="noopener" class="text-[#F5D84A] font-bold hover:underline inline-flex items-center gap-1 pt-1"><span>Unduh Brosur Resmi</span> &rarr;</a></li>
                        @endif
                    </ul>
                </div>

                <!-- Contact & Social -->
                <div class="sm:col-span-2 lg:col-span-4 space-y-3">
                    <h4 class="text-xs font-bold text-[#F5D84A] uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#F5D84A]"></span>
                        <span>KAMPUS &amp; KONTAK</span>
                    </h4>
                    <div class="space-y-3.5 text-xs text-slate-300">
                        <div class="flex items-start gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center shrink-0 mt-0.5 border border-white/10">
                                <svg class="w-3.5 h-3.5 text-[#F5D84A]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <a href="{{ $cfg['contact']['maps_url'] ?? 'https://maps.app.goo.gl/MXd5JUXK8SPAcAhn9' }}" target="_blank" rel="noopener" class="leading-relaxed hover:text-white transition-colors">
                                {{ $cfg['contact']['address'] ?? 'Jl. Otto Iskandardinata Kp. Tanjung Ds. Pasawahan Kec. Tarogong Kaler, Kabupaten Garut, Jawa Barat 44151' }} <span class="text-[#F5D84A] underline font-bold block sm:inline mt-0.5">(Lihat di Google Maps &rarr;)</span>
                            </a>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center shrink-0 border border-white/10">
                                <svg class="w-3.5 h-3.5 text-[#F5D84A]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <a href="{{ $cfg['contact']['phone_url'] ?? 'tel:081323314430' }}" class="hover:text-white transition-colors">{{ $cfg['contact']['phone'] ?? '0813-2331-4430 / 0897-1064-617' }}</a>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center shrink-0 border border-white/10">
                                <svg class="w-3.5 h-3.5 text-[#F5D84A]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <a href="mailto:{{ $cfg['contact']['email'] ?? 'info@smkwikrama1garut.sch.id' }}" class="hover:text-white transition-colors truncate">{{ $cfg['contact']['email'] ?? 'info@smkwikrama1garut.sch.id' }}</a>
                        </div>

                        <!-- Social Media Channel Icons -->
                        <div class="pt-3 flex items-center gap-2.5">
                            @if(!empty($cfg['contact']['instagram']))
                            <a href="{{ $cfg['contact']['instagram'] }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-[#00519E] border border-white/10 flex items-center justify-center text-white transition-all shadow-xs hover:scale-105" title="Instagram Resmi">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                            @endif
                            @if(!empty($cfg['contact']['facebook']))
                            <a href="{{ $cfg['contact']['facebook'] }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-[#00519E] border border-white/10 flex items-center justify-center text-white transition-all shadow-xs hover:scale-105" title="Facebook Resmi">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            @endif
                            @if(!empty($cfg['contact']['youtube']))
                            <a href="{{ $cfg['contact']['youtube'] }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-[#00519E] border border-white/10 flex items-center justify-center text-white transition-all shadow-xs hover:scale-105" title="YouTube Channel Resmi">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                            </a>
                            @endif
                            @if(!empty($cfg['contact']['tiktok']))
                            <a href="{{ $cfg['contact']['tiktok'] }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-[#00519E] border border-white/10 flex items-center justify-center text-white transition-all shadow-xs hover:scale-105" title="TikTok Resmi">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.24 1.07-.14 1.61.24 1.64 1.82 2.89 3.5 2.75 1.34-.03 2.56-.83 3.09-2.05.29-.62.4-1.31.39-2 .03-4.81.01-9.62.02-14.43z"/></svg>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between text-xs text-blue-100/60 gap-3 text-center sm:text-left">
                <p>&copy; {{ date('Y') }} {{ $schoolName }}. Seluruh hak cipta dilindungi.</p>
                <p>{{ $appName }} &bull; Sistem Informasi Terintegrasi SMK Wikrama 1 Garut</p>
            </div>
        </div>
    </footer>
