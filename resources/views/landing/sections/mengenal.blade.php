<!-- ── 3. "Mengenal Wikrama" Signature Editorial Split Section (Sesuai Referensi Visual) ── -->
<section class="mengenal-section" id="mengenal">
    <!-- Subtle Ambient Background Light -->
    <div class="mengenal-ambient-bg" aria-hidden="true">
        <div class="mengenal-blur-blob mengenal-blur-amber"></div>
        <div class="mengenal-blur-blob mengenal-blur-blue"></div>
    </div>

    <style>
        /* ── Scoped Styling: Menjamin render 100% presisi tanpa dependensi utility Vite Tailwind ── */
        .mengenal-section {
            position: relative;
            background-color: #ffffff;
            color: #1e293b;
            padding: 80px 0;
            overflow: hidden;
            font-family: inherit;
        }
        @media (max-width: 640px) {
            .mengenal-section {
                padding: 48px 0;
            }
        }
        .mengenal-ambient-bg {
            position: absolute;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
            z-index: 0;
        }
        .mengenal-blur-blob {
            position: absolute;
            width: 380px;
            height: 380px;
            border-radius: 9999px;
            filter: blur(80px);
            opacity: 0.55;
        }
        .mengenal-blur-amber {
            top: -60px;
            right: -60px;
            background-color: #FEF3C7;
        }
        .mengenal-blur-blue {
            bottom: -60px;
            left: -60px;
            background-color: #E0F2FE;
        }
        .mengenal-container {
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 24px;
            position: relative;
            z-index: 10;
        }
        .mengenal-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 48px;
            align-items: center;
        }
        @media (min-width: 1024px) {
            .mengenal-grid {
                grid-template-columns: 46% 54%;
                gap: 56px;
            }
        }

        /* ── SISI KIRI: COLLAGE ORGANIK ── */
        .mengenal-collage {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            align-items: center;
            max-width: 480px;
            margin: 0 auto;
        }
        @media (min-width: 1024px) {
            .mengenal-collage {
                max-width: none;
                margin: 0;
            }
        }

        /* Sub-kolom Kiri: Foto Kubah Arched Atas + Stat Card Oranye Bawah */
        .mengenal-subcol {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        /* Foto 1: Tall Arched Portrait (Kubah) */
        .mengenal-photo-arched {
            width: 100%;
            aspect-ratio: 3 / 4;
            border-radius: 120px 120px 20px 20px;
            overflow: hidden;
            box-shadow: 0 10px 24px -4px rgba(0, 48, 95, 0.12);
            background-color: #f1f5f9;
            position: relative;
        }
        .mengenal-photo-arched img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.5s ease;
        }
        .mengenal-photo-arched:hover img {
            transform: scale(1.04);
        }

        /* Badge 2: Amber Card dengan Lengkungan Khas (30 Years Reference) */
        .mengenal-badge-stat {
            background-color: #F5A623;
            color: #ffffff;
            border-radius: 20px 20px 20px 48px;
            padding: 16px 18px;
            box-shadow: 0 8px 20px -4px rgba(245, 166, 35, 0.4);
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .mengenal-stat-icon-wrap {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background-color: #ffffff;
            color: #F5A623;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }
        .mengenal-stat-number {
            display: block;
            font-size: 17px;
            font-weight: 800;
            line-height: 1.15;
            color: #ffffff;
            letter-spacing: -0.3px;
        }
        .mengenal-stat-label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.95);
            margin-top: 3px;
            line-height: 1.25;
        }

        /* Foto 3: Circle with Amber Dashed Ring */
        .mengenal-circle-wrap {
            width: 100%;
            max-width: 190px;
            margin: 0 auto;
            aspect-ratio: 1 / 1;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .mengenal-dashed-ring {
            position: absolute;
            inset: 0;
            border: 2.5px dashed #F5A623;
            border-radius: 50%;
            animation: mengenalRotateRing 36s linear infinite;
            pointer-events: none;
        }
        @keyframes mengenalRotateRing {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .mengenal-circle-photo {
            width: 86%;
            height: 86%;
            border-radius: 50%;
            overflow: hidden;
            box-shadow: 0 8px 20px -3px rgba(0, 48, 95, 0.12);
            background-color: #f1f5f9;
        }
        .mengenal-circle-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.5s ease;
        }
        .mengenal-circle-wrap:hover .mengenal-circle-photo img {
            transform: scale(1.06);
        }

        /* Foto 4: Landscape Sudut Kanan Bawah Melengkung Luas */
        .mengenal-photo-curved {
            width: 100%;
            aspect-ratio: 4 / 3;
            border-radius: 20px 20px 72px 20px;
            overflow: hidden;
            box-shadow: 0 10px 24px -4px rgba(0, 48, 95, 0.12);
            background-color: #f1f5f9;
            position: relative;
        }
        .mengenal-photo-curved img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.5s ease;
        }
        .mengenal-photo-curved:hover img {
            transform: scale(1.04);
        }

        /* ── SISI KANAN: KONTEN EDITORIAL ── */
        .mengenal-content {
            display: flex;
            flex-direction: column;
        }

        /* Eyebrow / Tag */
        .mengenal-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
        }
        .mengenal-eyebrow-icon {
            color: #F5A623;
            width: 17px;
            height: 17px;
        }
        .mengenal-eyebrow-text {
            color: #F5A623;
            font-weight: 800;
            font-size: 12.5px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            border-bottom: 2px solid #F5A623;
            padding-bottom: 2px;
        }

        /* Main Heading */
        .mengenal-title {
            color: #00305F;
            font-size: clamp(26px, 3.2vw, 42px);
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -0.6px;
            margin: 0 0 16px 0;
        }
        .mengenal-title-highlight {
            color: #F5A623;
        }

        /* Subtitle / Description */
        .mengenal-desc {
            color: #64748B;
            font-size: 15px;
            line-height: 1.7;
            margin: 0 0 24px 0;
            font-weight: 400;
        }

        /* Middle Row: Fitur Kiri + Quote Sage Kanan */
        .mengenal-mid-row {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            align-items: stretch;
            margin-bottom: 28px;
        }
        @media (min-width: 640px) {
            .mengenal-mid-row {
                grid-template-columns: 58% 42%;
                gap: 20px;
            }
        }

        /* Kolom Fitur */
        .mengenal-feature-list {
            display: flex;
            flex-direction: column;
            gap: 18px;
            justify-content: center;
        }
        .mengenal-feature-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }
        .mengenal-feature-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 2px solid #F5A623;
            background-color: #FFF9ED;
            color: #F5A623;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .mengenal-feature-heading {
            font-size: 14.5px;
            font-weight: 700;
            color: #00305F;
            line-height: 1.35;
            margin: 0 0 3px 0;
        }
        .mengenal-feature-text {
            font-size: 12.5px;
            color: #64748B;
            line-height: 1.5;
            margin: 0;
        }

        /* Kotak Quote Sage Modern */
        .mengenal-quote-box {
            background-color: #F1F6F4;
            border: 1px solid #E2E8F0;
            border-radius: 18px;
            padding: 20px 18px 18px 18px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }
        .mengenal-quote-body {
            font-size: 12.5px;
            color: #475569;
            line-height: 1.6;
            margin: 0 0 14px 0;
            position: relative;
            z-index: 2;
        }
        .mengenal-quote-footer {
            position: relative;
            z-index: 2;
        }
        .mengenal-quote-name {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #00305F;
        }
        .mengenal-quote-role {
            display: block;
            font-size: 10.5px;
            color: #94A3B8;
        }
        .mengenal-quote-watermark {
            position: absolute;
            right: 12px;
            bottom: 2px;
            font-family: Georgia, serif;
            font-size: 52px;
            font-weight: 900;
            line-height: 1;
            color: #107B65;
            opacity: 0.8;
            user-select: none;
            pointer-events: none;
        }

        /* Baris Bawah: Button Pill Amber + Widget Hotline */
        .mengenal-action-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 24px;
            padding-top: 20px;
            border-top: 1px solid #F1F5F9;
        }
        .mengenal-btn-cta {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background-color: #F5A623;
            color: #ffffff !important;
            font-size: 12.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 13px 26px;
            border-radius: 9999px;
            box-shadow: 0 4px 14px rgba(245, 166, 35, 0.35);
            text-decoration: none;
            transition: all 0.25s ease;
        }
        .mengenal-btn-cta:hover {
            background-color: #E0951B;
            box-shadow: 0 6px 18px rgba(245, 166, 35, 0.45);
            transform: translateY(-1px);
        }
        .mengenal-hotline {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            transition: transform 0.2s ease;
        }
        .mengenal-hotline:hover {
            transform: translateX(2px);
        }
        .mengenal-hotline-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background-color: #00305F;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0, 48, 95, 0.25);
            transition: background-color 0.2s ease;
        }
        .mengenal-hotline:hover .mengenal-hotline-icon {
            background-color: #00519E;
        }
        .mengenal-hotline-meta {
            display: flex;
            flex-direction: column;
        }
        .mengenal-hotline-label {
            font-size: 11px;
            font-weight: 700;
            color: #F5A623;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.2;
        }
        .mengenal-hotline-number {
            font-size: 14.5px;
            font-weight: 800;
            color: #00305F;
            line-height: 1.3;
            letter-spacing: -0.2px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }
    </style>

    <div class="mengenal-container" data-animate="fade-up">

        @php
            $tag = $cfg['get_to_know']['tag'] ?? 'Mengenal Lebih Dekat';
            $title = $cfg['get_to_know']['title'] ?? 'Menemukan Potensi Sejati di Wikrama';
            $subtitle = $cfg['get_to_know']['subtitle'] ?? 'Di SMK Wikrama 1 Garut, kami menghadirkan ekosistem pembelajaran holistik yang memadukan keunggulan kompetensi teknologi industri, pembinaan karakter santri, serta kesiapan karir internasional.';
            
            $img1 = \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($cfg['get_to_know']['image_1'] ?? null, 'images/web/activity-learning.jpg');
            $img2 = \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($cfg['get_to_know']['image_2'] ?? null, 'images/web/activity-students.jpg');
            $img3 = \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($cfg['get_to_know']['image_3'] ?? null, 'images/web/facility-lab.jpg');

            $statNumber = $cfg['get_to_know']['stat_number'] ?? '25+ Tahun';
            $statLabel = $cfg['get_to_know']['stat_label'] ?? 'Pengabdian & Mutu Vokasi Unggul';

            $quoteText = $cfg['welcome']['quote'] ?? 'Mendidik anak bangsa dengan hati dan teknologi sehingga melahirkan generasi pemimpin berkarakter mulia pembawa kemaslahatan umat.';
        @endphp

        <!-- ── 2-Column Responsive Split Layout (Sesuai Referensi Gambar) ── -->
        <div class="mengenal-grid">

            <!-- ── SISI KIRI: COLLAGE ORGANIK ── -->
            <div class="mengenal-collage">
                
                <!-- Sub-kolom Kiri: Foto Kubah Panjang Atas + Badge Statistik Oranye Bawah -->
                <div class="mengenal-subcol">
                    <!-- Foto 1: Tall Arched Portrait -->
                    <div class="mengenal-photo-arched">
                        <img src="{{ $img1 }}" 
                             alt="Pembelajaran Siswa Wikrama" 
                             loading="lazy">
                    </div>

                    <!-- Badge 2: Amber Pill Card (Persis Referensi: 30 Years) -->
                    <div class="mengenal-badge-stat">
                        <div class="mengenal-stat-icon-wrap">
                            <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div style="min-width: 0;">
                            <span class="mengenal-stat-number">{{ $statNumber }}</span>
                            <span class="mengenal-stat-label">{{ $statLabel }}</span>
                        </div>
                    </div>
                </div>

                <!-- Sub-kolom Kanan: Foto Lingkaran Dashed Ring Atas + Foto Curved Sudut Kanan Bawah -->
                <div class="mengenal-subcol">
                    <!-- Foto 3: Circular Photo with Golden Dashed Ring -->
                    <div class="mengenal-circle-wrap">
                        <div class="mengenal-dashed-ring"></div>
                        <div class="mengenal-circle-photo">
                            <img src="{{ $img2 }}" 
                                 alt="Aktivitas Kolaboratif Siswa" 
                                 loading="lazy">
                        </div>
                    </div>

                    <!-- Foto 4: Curved Bottom-Right Corner -->
                    <div class="mengenal-photo-curved">
                        <img src="{{ $img3 }}" 
                             alt="Fasilitas Laboratorium Vokasi" 
                             loading="lazy">
                    </div>
                </div>

            </div>

            <!-- ── SISI KANAN: KONTEN NARASI EDITORIAL ── -->
            <div class="mengenal-content">
                
                <!-- Eyebrow Tag -->
                <div class="mengenal-eyebrow">
                    <svg class="mengenal-eyebrow-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span class="mengenal-eyebrow-text">{{ $tag }}</span>
                </div>

                <!-- Main Headline (2-Tone Brand Colors) -->
                <h2 class="mengenal-title">
                    Menemukan Potensi Sejati, <span class="mengenal-title-highlight">Menginspirasi</span> Masa Depan.
                </h2>

                <!-- Lead Paragraph -->
                <p class="mengenal-desc">
                    {{ $subtitle }}
                </p>

                <!-- Middle Row: 2 Feature Items + 1 Soft Quote Card -->
                <div class="mengenal-mid-row">
                    
                    <!-- 2 Bullet Features -->
                    <div class="mengenal-feature-list">
                        <!-- Item 1 -->
                        <div class="mengenal-feature-item">
                            <div class="mengenal-feature-icon">
                                <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="mengenal-feature-heading">Kurikulum Vokasi &amp; Kompetensi Keahlian</h3>
                                <p class="mengenal-feature-text">Laboratorium modern, kurikulum 4 kompetensi keahlian industri, dan sertifikasi profesi internasional.</p>
                            </div>
                        </div>

                        <!-- Item 2 -->
                        <div class="mengenal-feature-item">
                            <div class="mengenal-feature-icon">
                                <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="mengenal-feature-heading">Daya Saing &amp; Karakter Santri</h3>
                                <p class="mengenal-feature-text">Pembiasaan adab luhur Wikrama, program tahfidz Al-Qur'an, dan penyaluran kerja skala global.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Soft Green/Sage Quote Card (Sesuai Kotak Kanan di Referensi) -->
                    <div class="mengenal-quote-box">
                        <p class="mengenal-quote-body">
                            "{{ Str::limit($quoteText, 130) }}"
                        </p>
                        <div class="mengenal-quote-footer">
                            <span class="mengenal-quote-name">Kunedi, S.Si.</span>
                            <span class="mengenal-quote-role">Kepala Sekolah</span>
                        </div>
                        <!-- Big Quotation Mark Watermark (99) -->
                        <div class="mengenal-quote-watermark" aria-hidden="true">
                            ”
                        </div>
                    </div>

                </div>

                <!-- Bottom Action Row: Solid Amber Pill CTA + Contact Call Widget -->
                <div class="mengenal-action-row">
                    <!-- CTA Pill Button -->
                    <a href="#jurusan" class="mengenal-btn-cta">
                        <span>Jelajahi Ekosistem</span>
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>

                    <!-- Call Widget -->
                    <a href="https://wa.me/6281323314430" target="_blank" rel="noopener" class="mengenal-hotline">
                        <div class="mengenal-hotline-icon">
                            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div class="mengenal-hotline-meta">
                            <span class="mengenal-hotline-label">Hubungi Kami</span>
                            <span class="mengenal-hotline-number">
                                +62 813-2331-4430
                            </span>
                        </div>
                    </a>
                </div>

            </div>

        </div>

    </div>
</section>
