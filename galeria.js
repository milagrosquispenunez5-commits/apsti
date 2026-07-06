//========================
// CARRUSEL + LIGHTBOX GALERIA
//========================

(() => {

    const carrusel = document.querySelector(".carrusel");

    if(!carrusel) return;

    const viewport = carrusel.querySelector(".carrusel-viewport");
    const track = carrusel.querySelector(".carrusel-track");
    const btnPrev = carrusel.querySelector(".carrusel-prev");
    const btnNext = carrusel.querySelector(".carrusel-next");

    const lightbox = document.getElementById("lightbox");
    const lightboxImg = lightbox.querySelector(".lightbox-img");
    const lightboxPrev = lightbox.querySelector(".lightbox-prev");
    const lightboxNext = lightbox.querySelector(".lightbox-next");
    const lightboxCerrar = lightbox.querySelector(".lightbox-cerrar");

    const originales = Array.from(track.querySelectorAll(".carrusel-slide"));
    const total = originales.length;

    originales.forEach((slide, i) => { slide.dataset.realIndex = i; });

    // clones invisibles en cada extremo para que el loop sea continuo (sin salto)
    const clonInicio = originales[total - 1].cloneNode(true);
    const clonFinal = originales[0].cloneNode(true);

    clonInicio.classList.add("clon");
    clonFinal.classList.add("clon");

    track.insertBefore(clonInicio, originales[0]);
    track.appendChild(clonFinal);

    const slides = Array.from(track.querySelectorAll(".carrusel-slide"));
    // slides[0] = clon del ultimo | slides[1..total] = reales | slides[total+1] = clon del primero

    let posicion = 1;
    let indiceLightbox = 0;
    let autoplayId = null;
    let lightboxAbierto = false;

    function centrar(pos, animar){

        const activa = slides[pos];

        const offset = activa.offsetLeft - (viewport.clientWidth - activa.offsetWidth) / 2;

        track.style.transition = animar ? "" : "none";

        track.style.transform = `translateX(-${offset}px)`;

        slides.forEach((s, i) => s.classList.toggle("activo", i === pos));

        if(!animar){

            track.getBoundingClientRect();

            track.style.transition = "";

        }

    }

    function irADom(pos, animar = true){

        posicion = Math.max(0, Math.min(pos, slides.length - 1));

        centrar(posicion, animar);

    }

    function siguiente(){
        irADom(posicion + 1);
    }

    function anterior(){
        irADom(posicion - 1);
    }

    track.addEventListener("transitionend", (e) => {

        if(e.propertyName !== "transform") return;

        if(posicion === slides.length - 1){

            irADom(1, false);

        } else if(posicion === 0){

            irADom(slides.length - 2, false);

        }

    });

    // --- autoplay ---

    function iniciarAutoplay(){

        detenerAutoplay();

        autoplayId = setInterval(siguiente, 3000);

    }

    function detenerAutoplay(){

        if(autoplayId){

            clearInterval(autoplayId);

            autoplayId = null;

        }

    }

    btnNext.addEventListener("click", () => { siguiente(); iniciarAutoplay(); });
    btnPrev.addEventListener("click", () => { anterior(); iniciarAutoplay(); });

    carrusel.addEventListener("mouseenter", detenerAutoplay);
    carrusel.addEventListener("mouseleave", iniciarAutoplay);
    carrusel.addEventListener("focusin", detenerAutoplay);
    carrusel.addEventListener("focusout", iniciarAutoplay);

    carrusel.addEventListener("keydown", (e) => {

        if(e.key === "ArrowRight"){ siguiente(); iniciarAutoplay(); }

        if(e.key === "ArrowLeft"){ anterior(); iniciarAutoplay(); }

    });

    // --- lightbox (navega sobre las fotos reales, independiente del carrusel) ---

    function actualizarLightboxImg(){

        const original = originales[indiceLightbox];

        const img = original.querySelector("img");

        lightboxImg.src = img.src;
        lightboxImg.alt = img.alt;

    }

    function abrirLightbox(real){

        indiceLightbox = real;

        actualizarLightboxImg();

        irADom(real + 1, false);

        lightbox.classList.add("abierto");
        lightbox.setAttribute("aria-hidden", "false");

        lightboxAbierto = true;

        detenerAutoplay();

    }

    function cerrarLightbox(){

        lightbox.classList.remove("abierto");
        lightbox.setAttribute("aria-hidden", "true");

        lightboxAbierto = false;

        iniciarAutoplay();

    }

    function lightboxSiguiente(){

        indiceLightbox = (indiceLightbox + 1) % total;

        actualizarLightboxImg();

    }

    function lightboxAnterior(){

        indiceLightbox = (indiceLightbox - 1 + total) % total;

        actualizarLightboxImg();

    }

    slides.forEach((slide) => {

        slide.querySelector("button").addEventListener("click", () => {

            abrirLightbox(parseInt(slide.dataset.realIndex, 10));

        });

    });

    lightboxNext.addEventListener("click", lightboxSiguiente);
    lightboxPrev.addEventListener("click", lightboxAnterior);
    lightboxCerrar.addEventListener("click", cerrarLightbox);

    lightbox.addEventListener("click", (e) => {

        if(e.target === lightbox) cerrarLightbox();

    });

    document.addEventListener("keydown", (e) => {

        if(!lightboxAbierto) return;

        if(e.key === "Escape") cerrarLightbox();

        if(e.key === "ArrowRight") lightboxSiguiente();

        if(e.key === "ArrowLeft") lightboxAnterior();

    });

    irADom(1, false);
    iniciarAutoplay();

})();
