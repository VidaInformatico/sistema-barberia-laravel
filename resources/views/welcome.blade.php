<x-app-layout>
    @if ($slides->count() > 0)
        <section id="hero-slider"
            class="relative h-[85vh] min-h-[600px] w-full overflow-hidden bg-gray-950 text-white select-none">

            @foreach ($slides as $slide)
                <div
                    class="slide absolute inset-0 w-full h-full {{ $loop->first ? 'opacity-100 z-10' : 'opacity-0 z-0' }} transition-all duration-1000 ease-in-out">
                    <div class="absolute inset-0 bg-cover bg-center transition-transform duration-[8000ms] ease-out scale-100 slide-bg"
                        style="background-image: url('{{ Storage::url($slide->image_url) }}');">
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-r from-gray-950 via-gray-950/80 md:via-gray-950/70 to-transparent z-10">
                    </div>

                    <div class="relative max-w-7xl mx-auto h-full px-4 sm:px-6 flex flex-col justify-center z-20 pb-16 sm:pb-0">
                        @if ($slide->subtitle)
                            <span
                                class="slide-subtitle text-amber-500 font-bold uppercase tracking-widest text-[10px] sm:text-xs md:text-sm mb-3 sm:mb-4 translate-y-8 opacity-0 transition-all duration-700 delay-300">
                                {{ $slide->subtitle }}
                            </span>
                        @endif

                        <h1
                            class="slide-title text-xl sm:text-3xl md:text-4xl font-black tracking-tighter uppercase max-w-2xl leading-tight sm:leading-none mb-4 sm:mb-6 translate-y-8 opacity-0 transition-all duration-700 delay-500">
                            {!! $slide->title !!}
                        </h1>

                        @if ($slide->text)
                            <p
                                class="slide-text text-gray-400 max-w-md text-xs sm:text-sm md:text-base font-medium mb-6 sm:mb-8 translate-y-8 opacity-0 transition-all duration-700 delay-700">
                                {{ $slide->text }}
                            </p>
                        @endif

                        <!-- Botones del Slide (Responsivos en columna para móviles) -->
                        <div
                            class="slide-cta translate-y-8 opacity-0 transition-all duration-700 delay-900 flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4 max-w-sm sm:max-w-none">
                            <a href="{{ $slide->btn1_url }}"
                                class="bg-amber-500 hover:bg-amber-600 text-gray-950 px-6 sm:px-8 py-3.5 sm:py-4 rounded-sm text-xs font-bold uppercase tracking-widest transition-all active:scale-95 flex items-center justify-center gap-2 text-center shadow-lg shadow-amber-500/20">
                                <span>{{ $slide->btn1_text }}</span>
                                @if ($slide->btn1_icon)
                                    <i class="{{ $slide->btn1_icon }} text-xs"></i>
                                @endif
                            </a>

                            @if ($slide->btn2_text && $slide->btn2_url)
                                <a href="{{ $slide->btn2_url }}"
                                    class="border border-white/20 hover:border-white hover:bg-white/10 text-white px-6 sm:px-8 py-3.5 sm:py-4 rounded-sm text-xs font-bold uppercase tracking-widest transition-all flex items-center justify-center gap-2 text-center">
                                    <span>{{ $slide->btn2_text }}</span>
                                    @if ($slide->btn2_icon)
                                        <i class="{{ $slide->btn2_icon }} text-xs"></i>
                                    @endif
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Controles de Flechas (Mejor ubicados y adaptados en móvil) -->
            <div class="absolute bottom-12 sm:bottom-10 right-4 sm:right-6 md:right-12 z-30 flex gap-2">
                <button id="prev-slide"
                    class="w-10 h-10 sm:w-12 sm:h-12 border border-white/20 bg-gray-950/60 backdrop-blur-sm hover:bg-white hover:text-gray-950 hover:border-white transition-all text-xs sm:text-sm flex items-center justify-center rounded-sm">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <button id="next-slide"
                    class="w-10 h-10 sm:w-12 sm:h-12 border border-white/20 bg-gray-950/60 backdrop-blur-sm hover:bg-white hover:text-gray-950 hover:border-white transition-all text-xs sm:text-sm flex items-center justify-center rounded-sm">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>

            <!-- Barras de Progreso Inferiores -->
            <div
                class="absolute bottom-0 left-0 w-full z-30 px-4 sm:px-6 max-w-7xl left-1/2 -translate-x-1/2 flex gap-2 sm:gap-4 h-1 mb-4 sm:mb-6 pointer-events-none">
                @foreach ($slides as $slide)
                    <div class="indicator-bar flex-1 bg-white/20 relative rounded-full overflow-hidden">
                        <div
                            class="progress-fill absolute left-0 top-0 h-full w-0 bg-amber-500 {{ $loop->first ? 'transition-all duration-[6000ms] linear' : '' }}">
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section
        class="{{ $slides->count() > 0 ? '' : 'mt-20' }} scroll-mt-20 relative w-full overflow-hidden bg-gray-900 text-white"
        id="section-service">

        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat bg-fixed opacity-10 z-0"
            style="background-image: url('https://images.unsplash.com/photo-1517832606589-7a598b647192?auto=format&fit=crop&q=80&w=1800');">
        </div>

        <div class="absolute inset-0 z-10 bg-cover bg-center bg-no-repeat bg-fixed opacity-40 pointer-events-none"
            style="background-image: url('https://images.unsplash.com/photo-1503951914875-452162b0f3f1?auto=format&fit=crop&q=80&w=1800');">
            <div class="absolute inset-0 bg-gradient-to-b from-gray-950/40 via-transparent to-gray-950/40"></div>
        </div>

        <div class="relative mx-auto px-6 py-12 md:py-16 z-20">

            <div
                class="flex flex-col md:flex-row md:items-end justify-between mb-16 md:mb-24 gap-6 border-b border-white/10 pb-10">
                <div class="max-w-xl">
                    <span class="text-amber-500 font-bold uppercase tracking-widest text-xs md:text-sm block mb-3">
                        Nuestros Servicios
                    </span>
                    <h2 class="text-2xl md:text-3xl font-black tracking-tighter uppercase leading-none">
                        Cortes Profesionales &
                        <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-amber-600">Servicios
                            de Barbería.</span>
                    </h2>
                </div>
                <div class="max-w-md">
                    <p class="text-gray-400 text-sm md:text-base font-medium leading-relaxed">
                        Dominamos el arte de la barbería con técnicas precisas, atención al detalle y un enfoque en el
                        estilo personal de cada cliente.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($services as $index => $service)
                    <div
                        class="group relative bg-gray-950/75 backdrop-blur-md border border-white/10 p-8 rounded-sm transition-all duration-500 hover:border-amber-500/50 hover:-translate-y-2 hover:shadow-2xl flex flex-col justify-between h-80">
                        <div>
                            <div class="flex items-center justify-between mb-6">
                                <div
                                    class="w-12 h-12 border border-amber-500/30 text-amber-500 flex items-center justify-center text-xl rounded-sm transition-colors group-hover:bg-amber-500 group-hover:text-gray-950 duration-500">
                                    <i class="fa-solid fa-scissors"></i>
                                </div>
                                <span
                                    class="text-xs font-mono text-gray-500 tracking-widest group-hover:text-amber-400 transition-colors">
                                    /{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>
                            <h3
                                class="text-xl font-bold uppercase tracking-tight mb-3 text-white group-hover:text-amber-400 transition-colors">
                                {{ $service->name }}
                            </h3>
                            <p class="text-gray-400 text-xs md:text-sm font-medium leading-relaxed line-clamp-3">
                                {{ $service->description }}
                            </p>
                        </div>
                        <a href="{{ route('reservation', $service->slug) }}"
                            class="flex items-center justify-between border-t border-white/5 pt-4 mt-4">
                            <span
                                class="text-lg font-black text-amber-500 tracking-tight">{{ $service->formatted_price }}</span>
                            <span
                                class="text-[10px] font-bold uppercase tracking-widest text-gray-400 group-hover:text-white transition-colors flex items-center gap-1">
                                Detalles <i
                                    class="fa-solid fa-arrow-right text-[9px] translate-x-0 group-hover:translate-x-1 transition-transform"></i>
                            </span>
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="mt-16 text-center">
                <a href="{{ route('shop') }}"
                    class="inline-flex items-center gap-3 border border-white/20 hover:border-amber-500 hover:bg-amber-500 hover:text-gray-950 px-10 py-4 rounded-sm text-xs font-bold uppercase tracking-widest transition-all duration-300 group">
                    Nuestro Productos
                    <i class="fa-solid fa-arrow-right-long transition-transform group-hover:translate-x-2"></i>
                </a>
            </div>

        </div>
    </section>

    <section class="bg-stone-950 py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">

            <div class="text-center mb-12">
                <h2 class="text-base font-semibold text-amber-500 uppercase tracking-wide">Nuestro Trabajo</h2>
                <p class="mt-2 text-3xl font-extrabold text-white sm:text-4xl">Galería de Resultados</p>
                <p class="mt-4 max-w-2xl mx-auto text-xl text-stone-400">
                    Detalles que marcan la diferencia. Haz clic para ampliar.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($gallery as $photo)
                    <div onclick="openLightbox('image', '{{ $photo->image_url }}', '{{ $photo->title }} - por {{ $photo->barber->user->name ?? 'Barbero' }}')"
                        class="group relative overflow-hidden rounded-xl bg-stone-900 border border-stone-800 aspect-square shadow-lg cursor-pointer">

                        <img src="{{ $photo->image_url }}" alt="{{ $photo->title }}"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">

                        <div
                            class="absolute inset-0 bg-gradient-to-t from-stone-950 via-stone-950/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4">
                            <span class="text-xs font-semibold text-amber-500 uppercase tracking-wider mb-1">
                                {{ $photo->title }}
                            </span>
                            <h3 class="text-white font-bold text-lg">{{ $photo->description }}</h3>
                            <p class="text-xs text-stone-400 mt-1">Por: {{ $photo->barber->user->name ?? 'Equipo' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div id="lightbox"
            class="hidden fixed inset-0 z-50 flex flex-col items-center justify-center bg-stone-950/95 backdrop-blur-md p-4 transition-all duration-300">
            <button onclick="closeLightbox()"
                class="absolute top-6 right-6 text-stone-400 hover:text-white transition-colors bg-stone-900/50 p-2 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="max-w-4xl w-full flex flex-col items-center justify-center dynamic-content-wrapper">
                <div id="lightbox-media-container" class="w-full max-h-[75vh] flex justify-center items-center"></div>
                <p id="lightbox-caption" class="mt-4 text-center text-lg font-medium text-stone-300 tracking-wide px-4">
                </p>
            </div>
        </div>
    </section>

    <section class="relative w-full bg-gray-950 text-white py-6 md:py-12 border-t border-white/5">
        <div class="max-w-7xl mx-auto px-6">

            <div class="text-center max-w-2xl mx-auto mb-20">
                <span class="text-amber-500 font-bold uppercase tracking-widest text-xs md:text-sm block mb-3">
                    Artesanos de la Imagen
                </span>
                <h2 class="text-3xl md:text-5xl font-black tracking-tighter uppercase leading-none mb-6">
                    Nuestros
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-amber-600">
                        Barbers.
                    </span>
                </h2>
                <p class="text-gray-400 text-sm md:text-base font-medium leading-relaxed">
                    Un equipo élite avalado por academias internacionales, dedicados por entero a la perfección técnica
                    y el cuidado de tu estilo.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($barbers as $barber)
                    <div
                        class="group relative w-full h-[350px] bg-gray-900 overflow-hidden border border-white/10 rounded-sm">

                        @if ($barber->user->avatar)
                            <div class="absolute inset-0 w-full h-full bg-cover bg-center transition-transform duration-700 ease-out scale-100 group-hover:scale-105"
                                style="background-image: url('{{ asset('storage/' . $barber->user->avatar) }}');">
                            </div>
                        @else
                            <div
                                class="absolute inset-0 w-full h-full flex items-center justify-center bg-gray-800 transition-transform duration-700 ease-out group-hover:scale-105">
                                <span class="text-6xl font-black text-gray-700 select-none">
                                    {{ collect(explode(' ', $barber->full_name))->map(fn($n) => $n[0])->take(2)->implode('') }}
                                </span>
                            </div>
                        @endif

                        <div
                            class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-950/40 to-transparent z-10 transition-opacity duration-500 group-hover:opacity-90">
                        </div>

                        <a href="{{ route('barber.show', $barber->user->slug) }}"
                            class="absolute bottom-0 left-0 w-full p-6 z-20 transition-transform duration-500 group-hover:-translate-y-16">
                            <span class="text-amber-500 font-bold uppercase tracking-widest text-[10px] block mb-1">
                                {{ $barber->specialty }}
                            </span>
                            <h3 class="text-xl font-black uppercase tracking-tight text-white">
                                {{ $barber->full_name }}
                            </h3>
                        </a>

                        <div
                            class="absolute bottom-0 left-0 w-full h-16 bg-amber-500 z-20 translate-y-full group-hover:translate-y-0 transition-transform duration-500 ease-out flex items-center justify-between px-6">
                            <span class="text-gray-950 text-xs font-bold uppercase tracking-wider">
                                Exp: {{ $barber->experience_years }} años
                            </span>
                            <div class="flex items-center gap-4 text-gray-950">
                                @if ($barber->instagram)
                                    <a href="https://instagram.com/{{ $barber->instagram }}" target="_blank"
                                        class="hover:text-white transition-colors">
                                        <i class="fa-brands fa-instagram text-base"></i>
                                    </a>
                                @endif
                                <i class="fa-solid fa-scissors text-sm"></i>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="relative w-full bg-gray-900 text-white py-12 md:py-16 border-t border-white/5">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat bg-fixed z-0"
            style="background-image: url('https://images.unsplash.com/photo-1503951914875-452162b0f3f1?auto=format&fit=crop&q=80&w=1800');">
        </div>

        <div class="relative mx-auto px-6 z-10 max-w-7xl">
            <div class="flex flex-col items-center text-center max-w-2xl mx-auto mb-16">
                <span class="text-amber-500 font-bold uppercase tracking-widest text-xs block mb-3">La
                    Experiencia</span>
                <h2 class="text-3xl md:text-4xl font-black tracking-tighter uppercase">Lo que dicen nuestros <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-amber-600">Miembros.</span>
                </h2>
            </div>

            <div class="swiper testimonialSwiper overflow-hidden">
                <div class="swiper-wrapper">
                    @foreach ($ratings as $rating)
                        <div class="swiper-slide h-auto">
                            <div
                                class="backdrop-blur-md p-8 border border-white/10 rounded-sm flex flex-col justify-between h-72 hover:border-amber-500/30 transition-all duration-300">
                                <div>
                                    <div class="flex gap-1 text-amber-500 text-xs mb-6">
                                        @for ($i = 0; $i < 5; $i++)
                                            <i
                                                class="fa-solid fa-star {{ $i < $rating->score ? '' : 'opacity-30' }}"></i>
                                        @endfor
                                    </div>
                                    <p class="text-gray-300 text-sm font-medium italic leading-relaxed">
                                        "{{ $rating->comment }}"
                                    </p>
                                </div>
                                <div class="border-t border-white/5 pt-4 mt-6 flex flex-col">
                                    <span class="text-sm font-bold uppercase tracking-wider text-white">
                                        {{ $rating->user->name ?? 'Cliente Anónimo' }}
                                    </span>
                                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mt-0.5">
                                        {{ $rating->created_at->format('M, Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination mt-8"></div>
            </div>
        </div>

    </section>

    <livewire:front.contact-form />

    @push('css')
        <!-- CSS de Swiper -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    @endpush

    @push('js')
        <!-- JS de Swiper -->
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

        @if ($slides->count() > 0)
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const slides = document.querySelectorAll('.slide');
                    const prevBtn = document.getElementById('prev-slide');
                    const nextBtn = document.getElementById('next-slide');
                    const progressFills = document.querySelectorAll('.progress-fill');

                    let currentIndex = 0;
                    let sliderInterval;
                    const slideDuration = 6000; // 6 segundos por diapositiva

                    function animateTexts(slide, action) {
                        const subtitle = slide.querySelector('.slide-subtitle');
                        const title = slide.querySelector('.slide-title');
                        const text = slide.querySelector('.slide-text');
                        const cta = slide.querySelector('.slide-cta');
                        const bg = slide.querySelector('.slide-bg');

                        if (action === 'in') {
                            // Animación de entrada de textos con escalonamiento
                            [subtitle, title, text, cta].forEach(el => {
                                if (el) {
                                    el.classList.remove('translate-y-8', 'opacity-0');
                                    el.classList.add('translate-y-0', 'opacity-100');
                                }
                            });
                            // Inicia el zoom cinemático
                            if (bg) bg.classList.add('scale-110');
                        } else {
                            // Resetear elementos al salir
                            [subtitle, title, text, cta].forEach(el => {
                                if (el) {
                                    el.classList.remove('translate-y-0', 'opacity-100');
                                    el.classList.add('translate-y-8', 'opacity-0');
                                }
                            });
                            if (bg) bg.classList.remove('scale-110');
                        }
                    }

                    // Reinicia las barras de carga
                    function updateBars() {
                        progressFills.forEach((fill, index) => {
                            fill.style.transition = 'none';
                            fill.style.width = '0%';
                            if (index === currentIndex) {
                                // Forzar reflujo de renderizado para aplicar la animación limpia
                                fill.offsetHeight;
                                fill.style.transition = `width ${slideDuration}ms linear`;
                                fill.style.width = '100%';
                            }
                        });
                    }

                    function showSlide(index) {
                        // Desactivar el slide actual
                        slides[currentIndex].classList.remove('opacity-100', 'z-10');
                        slides[currentIndex].classList.add('opacity-0', 'z-0');
                        animateTexts(slides[currentIndex], 'out');

                        // Actualizar índice
                        currentIndex = (index + slides.length) % slides.length;

                        // Activar el nuevo slide
                        slides[currentIndex].classList.remove('opacity-0', 'z-0');
                        slides[currentIndex].classList.add('opacity-100', 'z-10');
                        animateTexts(slides[currentIndex], 'in');

                        updateBars();
                    }

                    function startAutoplay() {
                        clearInterval(sliderInterval);
                        sliderInterval = setInterval(() => {
                            showSlide(currentIndex + 1);
                        }, slideDuration);
                    }

                    // Eventos de botones manuales
                    nextBtn.addEventListener('click', () => {
                        showSlide(currentIndex + 1);
                        startAutoplay();
                    });

                    prevBtn.addEventListener('click', () => {
                        showSlide(currentIndex - 1);
                        startAutoplay();
                    });

                    // Inicializar el primer Slide al cargar
                    setTimeout(() => {
                        animateTexts(slides[0], 'in');
                        updateBars();
                        startAutoplay();
                    }, 100);
                });
            </script>
        @endif

        <script>
            window.addEventListener('scroll', () => {
                const navbar = document.getElementById('navbar');
                if (window.scrollY > 40) {
                    navbar.classList.remove('h-20', 'bg-white/80');
                    navbar.classList.add('h-16', 'bg-white', 'shadow-lg', 'border-transparent');
                } else {
                    navbar.classList.remove('h-16', 'bg-white', 'shadow-lg', 'border-transparent');
                    navbar.classList.add('h-20', 'bg-white/80', 'border-gray-200/50');
                }
            });
        </script>

        <script>
            function openLightbox(type, src, caption) {
                const lightbox = document.getElementById('lightbox');
                const container = document.getElementById('lightbox-media-container');
                const captionText = document.getElementById('lightbox-caption');

                container.innerHTML = '';

                // Inyectar el elemento correcto
                if (type === 'image') {
                    container.innerHTML =
                        `<img src="${src}" class="max-w-full max-h-[75vh] rounded-lg shadow-2xl object-contain animate-fade-in">`;
                } else if (type === 'video') {
                    container.innerHTML = `
                    <video src="${src}" controls autoplay class="max-w-full max-h-[75vh] rounded-lg shadow-2xl object-contain" style="outline:none;">
                        Tu navegador no soporta videos.
                    </video>`;
                }

                // Asignar texto e interactividad
                captionText.textContent = caption;
                lightbox.classList.remove('hidden');
                document.body.classList.add('overflow-hidden'); // Evita el scroll de fondo
            }

            function closeLightbox() {
                const lightbox = document.getElementById('lightbox');
                const container = document.getElementById('lightbox-media-container');

                // Pausar y limpiar para que el video no siga sonando en fondo
                container.innerHTML = '';
                lightbox.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeLightbox();
            });
        </script>

        <script>
            new Swiper(".testimonialSwiper", {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: true,
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
                autoplay: {
                    delay: 4000,
                    disableOnInteraction: false,
                },
                breakpoints: {
                    640: {
                        slidesPerView: 2
                    },
                    1024: {
                        slidesPerView: 3
                    },
                },
            });
        </script>
    @endpush
</x-app-layout>
