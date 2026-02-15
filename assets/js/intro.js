 window.addEventListener('load', () => {
    const heroContent = document.getElementById('hero-content');
    if(heroContent) {
        setTimeout(() => {
            heroContent.classList.remove('opacity-0', 'scale-95', 'translate-y-10');
            heroContent.classList.add('opacity-100', 'scale-100', 'translate-y-0');
        }, 500);
    }
});