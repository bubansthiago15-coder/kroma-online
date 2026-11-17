document.addEventListener('DOMContentLoaded', () => {
  const carousel = document.querySelector('[data-hero-carousel]');
  if (!carousel) return;

  const slides = [...carousel.querySelectorAll('.hero-slide')];
  const dots = [...carousel.querySelectorAll('.hero-dots button')];
  const previous = carousel.querySelector('.hero-prev');
  const next = carousel.querySelector('.hero-next');
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  let current = Math.max(0, slides.findIndex(slide => slide.classList.contains('is-active')));
  let timer = null;
  let pointerStart = null;

  const show = index => {
    current = (index + slides.length) % slides.length;
    slides.forEach((slide, position) => {
      const active = position === current;
      slide.classList.toggle('is-active', active);
      slide.setAttribute('aria-hidden', String(!active));
      if (active) slide.removeAttribute('inert');
      else slide.setAttribute('inert', '');
    });
    dots.forEach((dot, position) => dot.setAttribute('aria-current', String(position === current)));
  };

  const stop = () => {
    if (timer !== null) window.clearInterval(timer);
    timer = null;
  };
  const start = () => {
    stop();
    if (!reducedMotion && !document.hidden) {
      timer = window.setInterval(() => show(current + 1), 3000);
    }
  };

  previous?.addEventListener('click', () => { show(current - 1); start(); });
  next?.addEventListener('click', () => { show(current + 1); start(); });
  dots.forEach((dot, index) => dot.addEventListener('click', () => { show(index); start(); }));
  document.addEventListener('visibilitychange', start);
  carousel.addEventListener('keydown', event => {
    if (event.key === 'ArrowLeft') { show(current - 1); start(); }
    if (event.key === 'ArrowRight') { show(current + 1); start(); }
  });
  carousel.addEventListener('pointerdown', event => { pointerStart = event.clientX; }, { passive: true });
  carousel.addEventListener('pointerup', event => {
    if (pointerStart === null) return;
    const delta = event.clientX - pointerStart;
    pointerStart = null;
    if (Math.abs(delta) > 45) { show(current + (delta < 0 ? 1 : -1)); start(); }
  }, { passive: true });

  show(current);
  start();
});
