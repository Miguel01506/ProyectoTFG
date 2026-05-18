document.addEventListener('DOMContentLoaded', function() {
    const carousels = document.querySelectorAll('.post-media-carousel');

    carousels.forEach(carousel => {
        const track = carousel.querySelector('.media-track');
        const btnPrev = carousel.querySelector('.btn-media-prev');
        const btnNext = carousel.querySelector('.btn-media-next');
        const dots = carousel.querySelectorAll('.media-dot');
        const slides = carousel.querySelectorAll('.media-slide');
        const totalSlides = slides.length;
        let currentIndex = 0;

        function updateCarousel() {
            // Aplica la traslación porcentual exacta basándose en el ancho del contenedor hijo
            track.style.transform = `translateX(-${currentIndex * 100}%)`;
            
            // Modifica la activación visual de los indicadores de posición
            dots.forEach(dot => dot.classList.remove('active'));
            if (dots[currentIndex]) {
                dots[currentIndex].classList.add('active');
            }

            // Oculta las flechas de control en los extremos correspondientes
            if (btnPrev) {
                if (currentIndex === 0) btnPrev.classList.add('hidden');
                else btnPrev.classList.remove('hidden');
            }
            
            if (btnNext) {
                if (currentIndex === totalSlides - 1) btnNext.classList.add('hidden');
                else btnNext.classList.remove('hidden');
            }
        }

        if (btnPrev) {
            btnPrev.addEventListener('click', function(e) {
                e.preventDefault();
                if (currentIndex > 0) {
                    currentIndex--;
                    updateCarousel();
                }
            });
        }

        if (btnNext) {
            btnNext.addEventListener('click', function(e) {
                e.preventDefault();
                if (currentIndex < totalSlides - 1) {
                    currentIndex++;
                    updateCarousel();
                }
            });
        }

        dots.forEach(dot => {
            dot.addEventListener('click', function(e) {
                e.preventDefault();
                currentIndex = parseInt(this.getAttribute('data-slide-index'));
                updateCarousel();
            });
        });

        // Inicialización del estado base del elemento
        updateCarousel();
    });
});