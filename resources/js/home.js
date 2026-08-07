import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

const kurangGerak = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/* ---------------------------------------------------------
   1. Three.js — adegan mekanis di hero
   Dimuat lazy (dynamic import) + hanya saat kanvas terlihat,
   supaya bundle three.js tidak menghambat render awal halaman.
--------------------------------------------------------- */
function inisialisasiSceneHero() {
    const container = document.getElementById('hero-canvas');
    if (!container) return;

    const io = new IntersectionObserver(async (entries) => {
        if (!entries[0].isIntersecting) return;
        io.disconnect();

        const THREE = await import('three');
        jalankanSceneHero(THREE, container);
    });
    io.observe(container);
}

function jalankanSceneHero(THREE, container) {

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(42, container.clientWidth / container.clientHeight, 0.1, 100);
    camera.position.set(0, 0, 9);

    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true, powerPreference: 'low-power' });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.setSize(container.clientWidth, container.clientHeight);
    renderer.setClearColor(0x000000, 0);
    container.appendChild(renderer.domElement);

    const merahRajawali = 0xd2373b;
    const steel = 0x8a93a3;

    const grup = new THREE.Group();
    scene.add(grup);

    // Roda gigi utama — torus knot merepresentasikan mekanik bengkel
    const geometriInti = new THREE.TorusKnotGeometry(2.1, 0.42, 180, 24, 2, 3);
    const materialInti = new THREE.MeshStandardMaterial({
        color: merahRajawali,
        metalness: 0.65,
        roughness: 0.32,
        emissive: 0x1a0303,
        emissiveIntensity: 0.4,
    });
    const inti = new THREE.Mesh(geometriInti, materialInti);
    grup.add(inti);

    // Cincin luar tipis — kesan roda gigi/pelek
    const geometriCincin = new THREE.TorusGeometry(3.4, 0.045, 16, 100);
    const materialCincin = new THREE.MeshStandardMaterial({
        color: steel,
        metalness: 0.8,
        roughness: 0.25,
    });
    const cincinX = new THREE.Mesh(geometriCincin, materialCincin);
    cincinX.rotation.x = Math.PI / 2.2;
    grup.add(cincinX);

    const cincinY = new THREE.Mesh(geometriCincin, materialCincin.clone());
    cincinY.rotation.y = Math.PI / 2.6;
    cincinY.scale.setScalar(0.82);
    grup.add(cincinY);

    // Baut kecil mengorbit
    const baut = [];
    const geometriBaut = new THREE.IcosahedronGeometry(0.14, 0);
    for (let i = 0; i < 10; i++) {
        const material = new THREE.MeshStandardMaterial({
            color: i % 3 === 0 ? merahRajawali : steel,
            metalness: 0.7,
            roughness: 0.35,
        });
        const mesh = new THREE.Mesh(geometriBaut, material);
        const sudut = (i / 10) * Math.PI * 2;
        const radius = 4.1 + Math.sin(i) * 0.3;
        mesh.position.set(Math.cos(sudut) * radius, Math.sin(sudut * 1.3) * 1.4, Math.sin(sudut) * radius);
        baut.push({ mesh, sudut, radius });
        grup.add(mesh);
    }

    scene.add(new THREE.AmbientLight(0xffffff, 0.55));
    const keyLight = new THREE.DirectionalLight(0xffffff, 1.1);
    keyLight.position.set(4, 5, 6);
    scene.add(keyLight);
    const rimLight = new THREE.PointLight(0xd2373b, 1.4, 20);
    rimLight.position.set(-5, -2, 4);
    scene.add(rimLight);

    let targetRotX = 0;
    let targetRotY = 0;

    container.addEventListener('pointermove', (e) => {
        const rect = container.getBoundingClientRect();
        const x = (e.clientX - rect.left) / rect.width - 0.5;
        const y = (e.clientY - rect.top) / rect.height - 0.5;
        targetRotY = x * 0.6;
        targetRotX = y * 0.4;
    });

    let tersembunyi = document.hidden;
    document.addEventListener('visibilitychange', () => {
        tersembunyi = document.hidden;
    });

    const clock = new THREE.Clock();
    let frameId;

    function render() {
        frameId = requestAnimationFrame(render);
        if (tersembunyi) return;

        const t = clock.getElapsedTime();

        if (!kurangGerak) {
            grup.rotation.y += 0.0028;
            grup.rotation.x = THREE.MathUtils.lerp(grup.rotation.x, targetRotX, 0.04);
            grup.rotation.y += (targetRotY - 0) * 0.0006;

            baut.forEach(({ mesh, sudut, radius }, i) => {
                const a = sudut + t * 0.35;
                mesh.position.x = Math.cos(a) * radius;
                mesh.position.z = Math.sin(a) * radius;
                mesh.position.y = Math.sin(a * 1.7 + i) * 1.4;
                mesh.rotation.x += 0.02;
                mesh.rotation.y += 0.015;
            });
        }

        renderer.render(scene, camera);
    }
    render();

    const observerUkuran = new ResizeObserver(() => {
        const { clientWidth: w, clientHeight: h } = container;
        if (w === 0 || h === 0) return;
        camera.aspect = w / h;
        camera.updateProjectionMatrix();
        renderer.setSize(w, h);
    });
    observerUkuran.observe(container);

    window.addEventListener('beforeunload', () => {
        cancelAnimationFrame(frameId);
        renderer.dispose();
    });
}

/* ---------------------------------------------------------
   2. GSAP — entrance & scroll reveal
--------------------------------------------------------- */
function inisialisasiHeroEntrance() {
    if (kurangGerak) return;

    const heroCard = document.getElementById('hero-left-card');
    if (!heroCard) return;

    gsap.fromTo(heroCard, 
        { opacity: 0, y: 35 }, 
        { opacity: 1, y: 0, duration: 1.0, ease: 'power4.out', delay: 0.1, force3D: true }
    );
}

function inisialisasiAnimasiScroll() {
    if (kurangGerak) {
        document.querySelectorAll('[data-reveal]').forEach((el) => el.classList.add('is-visible'));
        return;
    }

    gsap.utils.toArray('[data-reveal]').forEach((el) => {
        if (el.closest('#beranda')) return; // Skip hero section as it is handled by inisialisasiHeroEntrance
        gsap.fromTo(
            el,
            { opacity: 0, y: 28 },
            {
                opacity: 1,
                y: 0,
                duration: 0.7,
                ease: 'power3.out',
                scrollTrigger: {
                    trigger: el,
                    start: 'top 85%',
                    once: true,
                },
            }
        );
    });

    gsap.utils.toArray('[data-reveal-stagger]').forEach((kontainer) => {
        const anak = kontainer.querySelectorAll('[data-reveal-item]');
        gsap.fromTo(
            anak,
            { opacity: 0, y: 24 },
            {
                opacity: 1,
                y: 0,
                duration: 0.6,
                ease: 'power3.out',
                stagger: 0.09,
                scrollTrigger: {
                    trigger: kontainer,
                    start: 'top 85%',
                    once: true,
                },
            }
        );
    });

    // Navbar menyusut saat scroll
    const nav = document.getElementById('nav-utama');
    if (nav) {
        ScrollTrigger.create({
            start: 'top -60',
            end: 99999,
            toggleClass: { targets: nav, className: 'nav-menyusut' },
        });
    }

    // Parallax lembut pada hero
    const heroBg = document.getElementById('hero-canvas');
    if (heroBg) {
        gsap.to(heroBg, {
            yPercent: 12,
            ease: 'none',
            scrollTrigger: {
                trigger: heroBg,
                start: 'top top',
                end: 'bottom top',
                scrub: 0.6,
            },
        });
    }
}

/* ---------------------------------------------------------
   3. Status buka/tutup real-time (Asia/Jakarta)
--------------------------------------------------------- */
function inisialisasiStatusBuka() {
    const target = document.querySelectorAll('[data-status-buka]');
    if (!target.length) return;

    const jamBukaMenit = 7 * 60 + 30;
    const jamTutupMenit = 17 * 60;

    function perbarui() {
        const sekarang = new Date(
            new Date().toLocaleString('en-US', { timeZone: 'Asia/Jakarta' })
        );
        const menitSekarang = sekarang.getHours() * 60 + sekarang.getMinutes();
        const buka = menitSekarang >= jamBukaMenit && menitSekarang < jamTutupMenit;

        target.forEach((el) => {
            el.textContent = buka ? 'Buka Sekarang' : 'Tutup';
            const badgeParent = el.closest('[data-status-badge]');
            const dot = badgeParent ? badgeParent.querySelector('[data-status-dot]') : null;

            if (badgeParent) {
                if (buka) {
                    badgeParent.className = 'inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 shadow-sm';
                    if (dot) dot.className = 'w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse';
                } else {
                    badgeParent.className = 'inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-rose-500/30 text-rose-200 border border-rose-500/50 shadow-sm';
                    if (dot) dot.className = 'w-2.5 h-2.5 rounded-full bg-rose-400 animate-pulse';
                }
            }
        });
    }

    perbarui();
    setInterval(perbarui, 60000);
}

/* ---------------------------------------------------------
   4. Menu mobile
--------------------------------------------------------- */
function inisialisasiMenuMobile() {
    const tombol = document.getElementById('tombol-menu-mobile');
    const menu = document.getElementById('menu-mobile');
    if (!tombol || !menu) return;

    tombol.addEventListener('click', () => {
        const terbuka = menu.classList.toggle('is-open');
        tombol.setAttribute('aria-expanded', terbuka ? 'true' : 'false');
    });

    menu.querySelectorAll('a').forEach((a) =>
        a.addEventListener('click', () => {
            menu.classList.remove('is-open');
            tombol.setAttribute('aria-expanded', 'false');
        })
    );
}

/* ---------------------------------------------------------
   5. Animasi Hitung Angka (Counter Animation)
--------------------------------------------------------- */
function inisialisasiHitungAngka() {
    const elemenHitung = document.querySelectorAll('[data-counter]');
    if (!elemenHitung.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const target = parseInt(el.getAttribute('data-counter'), 10);
                const suffix = el.getAttribute('data-suffix') || '';
                const durasi = 1800;
                const fps = 60;
                const totalFrame = Math.round((durasi / 1000) * fps);
                let frame = 0;

                const timer = setInterval(() => {
                    frame++;
                    const progress = frame / totalFrame;
                    const nilaiSaatIni = Math.floor(target * (1 - Math.pow(1 - progress, 3)));
                    el.textContent = nilaiSaatIni.toLocaleString('id-ID') + suffix;

                    if (frame >= totalFrame) {
                        el.textContent = target.toLocaleString('id-ID') + suffix;
                        clearInterval(timer);
                    }
                }, 1000 / fps);

                observer.unobserve(el);
            }
        });
    }, { threshold: 0.4 });

    elemenHitung.forEach((el) => observer.observe(el));
}

function inisialisasiHeroCarousel() {
    const slides = document.querySelectorAll('.hero-bg-slide');
    if (slides.length < 2) return;

    let currentIndex = 0;
    setInterval(() => {
        slides[currentIndex].classList.remove('opacity-100');
        slides[currentIndex].classList.add('opacity-0');
        currentIndex = (currentIndex + 1) % slides.length;
        slides[currentIndex].classList.remove('opacity-0');
        slides[currentIndex].classList.add('opacity-100');
    }, 5500);
}

document.addEventListener('DOMContentLoaded', () => {
    // inisialisasiSceneHero(); // Disabled to preserve exact Stitch hero image background
    inisialisasiHeroCarousel();
    inisialisasiHeroEntrance();
    inisialisasiAnimasiScroll();
    inisialisasiStatusBuka();
    inisialisasiMenuMobile();
    inisialisasiHitungAngka();
});
