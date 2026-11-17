        <!-- ── Babak 5: Warta Fotojurnalistik Kampus & Fasilitas Vokasi ── -->
        @php
            $smCfg = $cfg['student_moments'] ?? [];
            $rawMoments = $smCfg['items'] ?? [];
            $activeMoments = collect($rawMoments)
                ->filter(fn($it) => !isset($it['is_active']) || (bool)$it['is_active'] === true)
                ->values();

            // Default fallback jika database/config kosong
            if ($activeMoments->isEmpty()) {
                $activeMoments = collect([
                    [
                        'badge' => 'Prestasi Nasional • Paskibra',
                        'sub' => 'Gebyar Pagar 2025 • Tingkat Nasional',
                        'title' => 'Regu Paskibra SMK Wikrama 1 Garut Memborong 4 Piala Juara LKBB Tingkat Nasional',
                        'desc' => 'Melalui kedisiplinan baris-berbaris berstandar tinggi, ketahanan fisik, dan kekompakan formasi, kontingen siswa Wikrama membuktikan pembiasaan karakter tangguh yang diasah setiap hari di kampus.',
                        'image' => 'images/web/achievement-lkbb.jpg',
                        'footer_tag' => 'Kedisiplinan & Ketangkasan Santri',
                        'url' => '#',
                    ],
                    [
                        'badge' => 'Lab Software & AI',
                        'sub' => 'Praktik Kejuruan',
                        'title' => 'Eksplorasi Algoritma & Fullstack Web',
                        'desc' => 'Siswa PPLG mengasah logika Machine Learning dan pengembangan aplikasi modern dengan workstation standar industri.',
                        'image' => 'images/landing/lab-komputer-pplg.jpg',
                        'accent' => '#002752',
                        'url' => '#',
                    ],
                    [
                        'badge' => 'Prestasi Olahraga',
                        'sub' => 'Kualifikasi PORPROV',
                        'title' => 'Siswa TJKT Lolos Kualifikasi PORPROV XV',
                        'desc' => 'Rahadian Maheswara Zulhan membuktikan santri vokasi Wikrama unggul di bidang teknologi sekaligus olahraga panahan.',
                        'image' => 'images/web/achievement-panahan.jpg',
                        'accent' => '#D97706',
                        'url' => '#',
                    ],
                    [
                        'badge' => 'Islamic Boarding',
                        'sub' => 'Spiritual & Karakter',
                        'title' => 'Tahfidz Al-Qur\'an & Shalat Dhuha',
                        'desc' => 'Menyemai ketenangan batin, hafalan Al-Qur\'an, dan adab santun di Masjid Asiyah sebagai pilar karakter mulia.',
                        'image' => 'images/web/activity-spiritual.jpg',
                        'accent' => '#002752',
                        'url' => '#',
                    ],
                    [
                        'badge' => 'Karakter & Alam',
                        'sub' => 'Harvest Day Outdoor',
                        'title' => 'Harvest Day & Tadabbur Alam',
                        'desc' => 'Edukasi pertanian berkelanjutan dan menanam padi langsung di sawah untuk mengasah rasa syukur dan kerja keras.',
                        'image' => 'images/web/activity-learning.jpg',
                        'accent' => '#D97706',
                        'url' => '#',
                    ],
                ]);
            }

            $leadStory = $activeMoments->first();
            $gridStories = $activeMoments->slice(1, 4)->values();

            $resolveImg = function($path, $fallback = 'images/web/campus-environment.jpg') {
                if (empty($path)) return asset($fallback);
                return class_exists(\App\Modules\AdministrasiWeb\Models\WebSetting::class)
                    ? \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($path, $fallback)
                    : asset($path);
            };
        @endphp

        <section class="py-16 sm:py-24 bg-[#FAF9F6] text-slate-800 border-b border-slate-200/80 relative scroll-mt-20" id="dinamika-siswa">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                
                <!-- Section Header -->
                <div class="max-w-3xl mb-12 sm:mb-16 text-left" data-animate="fade-up">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-[#EBF4FC] text-[#002752] border border-[#002752]/20 mb-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#002752]"></span>
                        <span>{{ !empty($smCfg['tag']) ? $smCfg['tag'] : 'Dokumentasi Jurnalistik Kampus • Suasana Nyata' }}</span>
                    </span>
                    <h2 class="text-2xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 leading-tight">
                        {{ !empty($smCfg['title']) ? $smCfg['title'] : 'Dinamika Santri Vokasi & Sarana Kampus' }}
                    </h2>
                    <p class="mt-3 text-base sm:text-lg text-slate-600 leading-relaxed font-normal">
                        {{ !empty($smCfg['subtitle']) ? $smCfg['subtitle'] : 'Catatan visual keseharian siswa di SMK Wikrama 1 Garut: ketekunan riset teknologi di laboratorium, keteladanan akhlak santri, dan torehan prestasi kompetisi nasional.' }}
                    </p>
                </div>

                <!-- Format Fotojurnalistik: 1 Berita Foto Utama (Lead Story) + 4 Warta Pendukung -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-stretch">
                    
                    <!-- Lead Photo Story (Kiri: 6 Cols) -->
                    @if($leadStory)
                    @php
                        $leadImgUrl = $resolveImg($leadStory['image'] ?? null, 'images/web/achievement-lkbb.jpg');
                        $leadBadge = $leadStory['badge'] ?? ($leadStory['cat_label'] ?? 'Prestasi Nasional • Santri');
                        $leadSub = $leadStory['sub'] ?? ($leadStory['cat_label'] ?? 'Tingkat Nasional');
                        $leadTitle = $leadStory['title'] ?? 'Dinamika Kegiatan Siswa SMK Wikrama 1 Garut';
                        $leadDesc = $leadStory['desc'] ?? '';
                        $leadFooter = $leadStory['footer_tag'] ?? ($leadStory['cta_text'] ?? 'Kedisiplinan & Ketangkasan Santri');
                        $leadUrl = $leadStory['url'] ?? '#';
                    @endphp
                    <div class="lg:col-span-6 text-left flex flex-col" data-animate="fade-up">
                        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/90 shadow-xs flex-1 flex flex-col justify-between">
                            <div>
                                <div class="aspect-[16/10] w-full rounded-2xl overflow-hidden bg-slate-100 relative">
                                    <img src="{{ $leadImgUrl }}" 
                                         alt="{{ $leadTitle }}" 
                                         class="w-full h-full object-cover object-top"
                                         loading="lazy">
                                    <span class="absolute top-4 left-4 px-3 py-1 rounded-full text-xs font-bold bg-[#002752] text-white shadow-xs">
                                        {{ $leadBadge }}
                                    </span>
                                </div>

                                <div class="pt-5 px-1">
                                    <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-2">
                                        <span>{{ $leadSub }}</span>
                                    </div>
                                    <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 leading-snug">
                                        {{ $leadTitle }}
                                    </h3>
                                    <p class="mt-3 text-xs sm:text-sm text-slate-600 leading-relaxed font-normal">
                                        {{ $leadDesc }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-[#002752]">
                                <span>{{ $leadFooter }}</span>
                                @if(!empty($leadUrl) && $leadUrl !== '#')
                                <a href="{{ $leadUrl }}" class="hover:underline flex items-center gap-1">
                                    <span>Liputan Utama</span> &rarr;
                                </a>
                                @else
                                <span>Liputan Utama &rarr;</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- 4 Foto Cerita Pendukung (Kanan: 6 Cols dalam Grid 2x2) -->
                    <div class="lg:col-span-6 grid grid-cols-1 sm:grid-cols-2 gap-4.5" data-animate="fade-up" data-animate-delay="1">
                        @foreach($gridStories as $idx => $st)
                        @php
                            $stImg = $resolveImg($st['image'] ?? null, 'images/landing/lab-komputer-pplg.jpg');
                            $stBadge = $st['badge'] ?? ($st['cat_label'] ?? 'Aktivitas');
                            $stTitle = $st['title'] ?? '';
                            $stDesc = $st['desc'] ?? '';
                            $stAccent = $st['accent'] ?? ($idx % 2 === 0 ? '#002752' : '#D97706');
                        @endphp
                        <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs flex flex-col justify-between text-left group hover:border-[#002752]/30 transition-all">
                            <div class="aspect-[16/10] w-full rounded-xl overflow-hidden bg-slate-100 mb-3.5">
                                <img src="{{ $stImg }}" 
                                     alt="{{ $stTitle }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                     loading="lazy">
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider block mb-1"
                                      style="color: {{ $stAccent }};">
                                    {{ $stBadge }}
                                </span>
                                <h4 class="text-xs sm:text-sm font-bold text-slate-900 leading-snug">
                                    {{ $stTitle }}
                                </h4>
                                <p class="text-[11px] text-slate-500 mt-1 line-clamp-2 leading-relaxed font-normal">
                                    {{ $stDesc }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                </div>

            </div>
        </section>