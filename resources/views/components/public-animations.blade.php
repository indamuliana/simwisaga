<!-- ── GSAP + ScrollTrigger + Lenis Smooth Scroll Integration (Public Pages) ── -->
<style>
    /* Lenis Smooth Scroll Core Styles */
    html.lenis, html.lenis body {
        height: auto;
    }
    .lenis.lenis-smooth {
        scroll-behavior: auto !important;
    }
    .lenis.lenis-smooth [data-lenis-prevent] {
        overscroll-behavior: contain;
    }
    .lenis.lenis-stopped {
        overflow: hidden;
    }
    .lenis.lenis-scrolling iframe {
        pointer-events: none;
    }

    /* Pastikan seluruh container data-animate selalu terlihat normal agar tidak tertahan opacity: 0 oleh CSS lama */
    [data-animate] {
        opacity: 1 !important;
        transform: none !important;
    }

    /* Word-by-word Slide Up Masked Reveal */
    .gsap-word-mask {
        display: inline-block;
        overflow: hidden;
        vertical-align: top;
        padding: 0 0.05em 0.12em 0.05em;
        margin: 0 -0.05em -0.12em -0.05em;
    }
    .gsap-word {
        display: inline-block;
        will-change: transform, opacity;
    }
</style>

<!-- Load CDN: Lenis, GSAP, and ScrollTrigger -->
<script src="https://unpkg.com/lenis@1.1.18/dist/lenis.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let lenis = null;

    // ── 1. Inisialisasi Lenis Smooth Scroll ──
    if (typeof Lenis !== 'undefined') {
        lenis = new Lenis({
            duration: 1.2,
            easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
            smoothWheel: true,
            smoothTouch: false, // Sentuhan mobile tetap native untuk performa optimal
        });

        // Sinkronisasi Lenis dengan GSAP ScrollTrigger
        if (typeof ScrollTrigger !== 'undefined' && typeof gsap !== 'undefined') {
            lenis.on('scroll', ScrollTrigger.update);
            gsap.ticker.add((time) => {
                lenis.raf(time * 1000);
            });
            gsap.ticker.lagSmoothing(0);
        } else {
            function raf(time) {
                lenis.raf(time);
                requestAnimationFrame(raf);
            }
            requestAnimationFrame(raf);
        }

        // Smooth scroll untuk link jangkar internal (#jurusan, #fakta, #kontak, dll.) dengan kompensasi tinggi navbar
        document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
            anchor.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                if (targetId && targetId !== '#' && targetId.length > 1) {
                    const targetEl = document.querySelector(targetId);
                    if (targetEl) {
                        e.preventDefault();
                        const navEl = document.getElementById('main-nav-container') || document.querySelector('header');
                        const navHeight = navEl ? navEl.offsetHeight : 80;
                        lenis.scrollTo(targetEl, { offset: -(navHeight + 20) });
                    }
                }
            });
        });

        // Hentikan scroll Lenis saat modal / mobile drawer aktif
        const modalObserver = new MutationObserver(() => {
            const hasOpenModal = document.body.classList.contains('overflow-hidden') ||
                                 document.querySelector('[role="dialog"]:not([style*="display: none"]):not([style*="display:none"])');
            if (hasOpenModal) {
                lenis.stop();
            } else {
                lenis.start();
            }
        });
        modalObserver.observe(document.body, { attributes: true, childList: true, subtree: true });
    }

    // ── 2. GSAP & ScrollTrigger Animations ──
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger);

        // A. Judul Muncul Perkata saat di-scroll (Bebas FOUC / Anti-Kedip)
        const headings = document.querySelectorAll('main section h2, header h1, [data-reveal-title]');
        headings.forEach((title) => {
            if (title.dataset.gsapReady || title.closest('#hero') || title.classList.contains('no-reveal')) return;
            title.dataset.gsapReady = "true";

            // Cek apakah posisi judul SUDAH di dalam layar saat pertama kali halaman dimuat
            const rect = title.getBoundingClientRect();
            const isInViewportOnLoad = rect.top < window.innerHeight * 0.85;

            // Ekstrak text nodes untuk dipisahkan perkata tanpa merusak struktur HTML / tag <br>
            const textNodes = [];
            function walk(node) {
                if (node.nodeType === Node.TEXT_NODE && node.textContent.trim().length > 0) {
                    textNodes.push(node);
                } else if (node.nodeType === Node.ELEMENT_NODE && node.nodeName !== 'BR' && !node.classList.contains('no-split')) {
                    node.childNodes.forEach(walk);
                }
            }
            walk(title);

            textNodes.forEach((node) => {
                const words = node.textContent.split(/(\s+)/);
                const fragment = document.createDocumentFragment();

                words.forEach((word) => {
                    if (/^\s+$/.test(word)) {
                        fragment.appendChild(document.createTextNode(word));
                    } else if (word.length > 0) {
                        const mask = document.createElement('span');
                        mask.className = 'gsap-word-mask';
                        const inner = document.createElement('span');
                        inner.className = 'gsap-word';
                        inner.textContent = word;
                        mask.appendChild(inner);
                        fragment.appendChild(mask);
                    }
                });

                node.parentNode.replaceChild(fragment, node);
            });

            const words = title.querySelectorAll('.gsap-word');
            if (words.length > 0) {
                if (isInViewportOnLoad) {
                    // Jika SUDAH terlihat saat pertama kali buka, jangan disembunyikan agar tidak ada kedipan hilang-lalu-muncul
                    gsap.set(words, { yPercent: 0, opacity: 1 });
                } else {
                    // Hanya animasikan dari bawah jika posisinya memang masih di bawah layar (belum terjangkau)
                    gsap.from(words, {
                        yPercent: 115,
                        opacity: 0,
                        duration: 0.8,
                        ease: "power3.out",
                        stagger: 0.04,
                        scrollTrigger: {
                            trigger: title,
                            start: "top 88%",
                            toggleActions: "play none none none",
                            once: true,
                        }
                    });
                }
            }
        });

        // B. Motion untuk Masuk Cards (Staggered Entrance Animation)
        // Group 1: 4 Kartu Kompetensi Keahlian (#jurusan)
        const jurusanCards = document.querySelectorAll('#jurusan .jurusan-card');
        if (jurusanCards.length > 0) {
            const grid = document.querySelector('#jurusan .grid');
            const inView = grid && grid.getBoundingClientRect().top < window.innerHeight * 0.85;
            if (inView) {
                gsap.set(jurusanCards, { y: 0, opacity: 1 });
            } else {
                gsap.from(jurusanCards, {
                    y: 55,
                    opacity: 0,
                    duration: 0.85,
                    stagger: 0.12,
                    ease: "power2.out",
                    scrollTrigger: {
                        trigger: '#jurusan .grid',
                        start: "top 85%",
                        toggleActions: "play none none none",
                        once: true,
                    },
                    onComplete: () => {
                        gsap.set(jurusanCards, { clearProps: "transform" });
                    }
                });
            }
        }

        // Group 2: 3 Kartu Prestasi Bukti Dedikasi (#fakta)
        const faktaCards = document.querySelectorAll('#fakta .fakta-card');
        if (faktaCards.length > 0) {
            const grid = document.querySelector('#fakta .grid');
            const inView = grid && grid.getBoundingClientRect().top < window.innerHeight * 0.85;
            if (inView) {
                gsap.set(faktaCards, { y: 0, opacity: 1, scale: 1 });
            } else {
                gsap.from(faktaCards, {
                    y: 50,
                    opacity: 0,
                    scale: 0.96,
                    duration: 0.8,
                    stagger: 0.14,
                    ease: "power2.out",
                    scrollTrigger: {
                        trigger: '#fakta .grid',
                        start: "top 85%",
                        toggleActions: "play none none none",
                        once: true,
                    },
                    onComplete: () => {
                        gsap.set(faktaCards, { clearProps: "transform,scale" });
                    }
                });
            }
        }

        // Group 3: Kartu Artikel Berita (#berita) - Selalu Tampak Solid & Meluncur Halus
        const beritaCards = document.querySelectorAll('#berita article');
        if (beritaCards.length > 0) {
            gsap.from(beritaCards, {
                y: 35,
                duration: 0.7,
                stagger: 0.07,
                ease: "power2.out",
                scrollTrigger: {
                    trigger: '#berita',
                    start: "top 88%",
                    toggleActions: "play none none none",
                    once: true,
                },
                onComplete: () => {
                    gsap.set(beritaCards, { clearProps: "all" });
                }
            });
        }

        // Group 4: Kartu Agenda Akademik (#agenda) - Selalu Tampak Solid & Meluncur Halus
        const agendaCards = document.querySelectorAll('#agenda article');
        if (agendaCards.length > 0) {
            gsap.from(agendaCards, {
                y: 35,
                duration: 0.7,
                stagger: 0.09,
                ease: "power2.out",
                scrollTrigger: {
                    trigger: '#agenda',
                    start: "top 88%",
                    toggleActions: "play none none none",
                    once: true,
                },
                onComplete: () => {
                    gsap.set(agendaCards, { clearProps: "all" });
                }
            });
        }

        // Refresh ScrollTrigger setelah layout & gambar selesai dimuat
        window.addEventListener('load', () => {
            ScrollTrigger.refresh();
        });
    }

    // ── 3. Content Safety Fallback Insurance (Ponytail Guarantee) ──
    setTimeout(() => {
        document.querySelectorAll('.gsap-word').forEach(w => {
            w.style.opacity = '1';
            w.style.transform = 'none';
        });
        document.querySelectorAll('#jurusan .jurusan-card, #fakta .fakta-card, #berita article, #agenda article').forEach(c => {
            c.style.opacity = '1';
            c.style.transform = 'none';
        });
    }, 1000);
});
</script>
