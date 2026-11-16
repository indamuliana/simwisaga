        <!-- ── Babak 5: Warta Fotojurnalistik Kampus & Fasilitas Vokasi ── -->
        <section class="py-16 sm:py-24 bg-[#FAF9F6] text-slate-800 border-b border-slate-200/80 relative scroll-mt-20" id="dinamika-siswa">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                
                <!-- Section Header -->
                <div class="max-w-3xl mb-12 sm:mb-16 text-left" data-animate="fade-up">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-[#EBF4FC] text-[#002752] border border-[#002752]/20 mb-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#002752]"></span>
                        <span>Dokumentasi Jurnalistik Kampus &bull; Suasana Nyata</span>
                    </span>
                    <h2 class="text-2xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 leading-tight">
                        Dinamika Santri Vokasi &amp; Sarana Kampus
                    </h2>
                    <p class="mt-3 text-base sm:text-lg text-slate-600 leading-relaxed font-normal">
                        Catatan visual keseharian siswa di SMK Wikrama 1 Garut: ketekunan riset teknologi di laboratorium, keteladanan akhlak santri, dan torehan prestasi kompetisi nasional.
                    </p>
                </div>

                <!-- Format Fotojurnalistik: 1 Berita Foto Utama (Lead Story) + 4 Warta Pendukung -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-stretch">
                    
                    <!-- Lead Photo Story (Kiri: 6 Cols) -->
                    <div class="lg:col-span-6 text-left flex flex-col" data-animate="fade-up">
                        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/90 shadow-xs flex-1 flex flex-col justify-between">
                            <div>
                                <div class="aspect-[16/10] w-full rounded-2xl overflow-hidden bg-slate-100 relative">
                                    <img src="{{ asset('images/web/achievement-lkbb.jpg') }}" 
                                         alt="Prestasi Juara LKBB Paskibra Nasional" 
                                         class="w-full h-full object-cover object-top">
                                    <span class="absolute top-4 left-4 px-3 py-1 rounded-full text-xs font-bold bg-[#002752] text-white shadow-xs">
                                        Prestasi Nasional &bull; Paskibra
                                    </span>
                                </div>

                                <div class="pt-5 px-1">
                                    <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-2">
                                        <span>Gebyar Pagar 2025</span>
                                        <span class="text-slate-300">&bull;</span>
                                        <span class="font-semibold text-[#002752]">Tingkat Nasional</span>
                                    </div>
                                    <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 leading-snug">
                                        Regu Paskibra SMK Wikrama 1 Garut Memborong 4 Piala Juara LKBB Tingkat Nasional
                                    </h3>
                                    <p class="mt-3 text-xs sm:text-sm text-slate-600 leading-relaxed font-normal">
                                        Melalui kedisiplinan baris-berbaris berstandar tinggi, ketahanan fisik, dan kekompakan formasi, kontingen siswa Wikrama membuktikan pembiasaan karakter tangguh yang diasah setiap hari di kampus.
                                    </p>
                                </div>
                            </div>

                            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-[#002752]">
                                <span>Kedisiplinan &amp; Ketangkasan Santri</span>
                                <span>Liputan Utama &rarr;</span>
                            </div>
                        </div>
                    </div>

                    <!-- 4 Foto Cerita Pendukung (Kanan: 6 Cols dalam Grid 2x2) -->
                    <div class="lg:col-span-6 grid grid-cols-1 sm:grid-cols-2 gap-4.5" data-animate="fade-up" data-animate-delay="1">
                        
                        <!-- Story 1: Lab Koding & AI PPLG -->
                        <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs flex flex-col justify-between text-left group hover:border-[#002752]/30 transition-all">
                            <div class="aspect-[16/10] w-full rounded-xl overflow-hidden bg-slate-100 mb-3.5">
                                <img src="{{ asset('images/landing/lab-komputer-pplg.jpg') }}" 
                                     alt="Lab Komputer PPLG" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-[#002752] block mb-1">
                                    Lab Software &amp; AI
                                </span>
                                <h4 class="text-xs sm:text-sm font-bold text-slate-900 leading-snug">
                                    Eksplorasi Algoritma &amp; Fullstack Web
                                </h4>
                                <p class="text-[11px] text-slate-500 mt-1 line-clamp-2 leading-relaxed font-normal">
                                    Siswa PPLG mengasah logika Machine Learning dan pengembangan aplikasi modern dengan workstation standar industri.
                                </p>
                            </div>
                        </div>

                        <!-- Story 2: Prestasi Panahan PORPROV -->
                        <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs flex flex-col justify-between text-left group hover:border-[#002752]/30 transition-all">
                            <div class="aspect-[16/10] w-full rounded-xl overflow-hidden bg-slate-100 mb-3.5">
                                <img src="{{ asset('images/web/achievement-panahan.jpg') }}" 
                                     alt="Prestasi Atlet Panahan Siswa TJKT" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-[#D97706] block mb-1">
                                    Prestasi Olahraga
                                </span>
                                <h4 class="text-xs sm:text-sm font-bold text-slate-900 leading-snug">
                                    Siswa TJKT Lolos Kualifikasi PORPROV XV
                                </h4>
                                <p class="text-[11px] text-slate-500 mt-1 line-clamp-2 leading-relaxed font-normal">
                                    Rahadian Maheswara Zulhan membuktikan santri vokasi Wikrama unggul di bidang teknologi sekaligus olahraga panahan.
                                </p>
                            </div>
                        </div>

                        <!-- Story 3: Karakter Santri & Tahfidz Al-Qur'an -->
                        <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs flex flex-col justify-between text-left group hover:border-[#002752]/30 transition-all">
                            <div class="aspect-[16/10] w-full rounded-xl overflow-hidden bg-slate-100 mb-3.5">
                                <img src="{{ asset('images/web/activity-spiritual.jpg') }}" 
                                     alt="Spiritual & Boarding School" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-[#002752] block mb-1">
                                    Islamic Boarding
                                </span>
                                <h4 class="text-xs sm:text-sm font-bold text-slate-900 leading-snug">
                                    Tahfidz Al-Qur'an &amp; Shalat Dhuha
                                </h4>
                                <p class="text-[11px] text-slate-500 mt-1 line-clamp-2 leading-relaxed font-normal">
                                    Menyemai ketenangan batin, hafalan Al-Qur'an, dan adab santun di Masjid Asiyah sebagai pilar karakter mulia.
                                </p>
                            </div>
                        </div>

                        <!-- Story 4: Harvest Day & Pertanian Organik -->
                        <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs flex flex-col justify-between text-left group hover:border-[#002752]/30 transition-all">
                            <div class="aspect-[16/10] w-full rounded-xl overflow-hidden bg-slate-100 mb-3.5">
                                <img src="{{ asset('images/web/activity-learning.jpg') }}" 
                                     alt="Harvest Day & Pertanian Organik" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-[#D97706] block mb-1">
                                    Karakter &amp; Alam
                                </span>
                                <h4 class="text-xs sm:text-sm font-bold text-slate-900 leading-snug">
                                    Harvest Day &amp; Tadabbur Alam
                                </h4>
                                <p class="text-[11px] text-slate-500 mt-1 line-clamp-2 leading-relaxed font-normal">
                                    Edukasi pertanian berkelanjutan dan menanam padi langsung di sawah untuk mengasah rasa syukur dan kerja keras.
                                </p>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </section>