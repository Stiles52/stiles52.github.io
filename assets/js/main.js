/* ORIGIN - MAIN SCRIPT
    Gère les sons, les transitions de page, les icônes et le mode dyslexique.
*/

document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Initialisation des icônes Lucide
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // 2. Initialisation des Sons (Créés en JS pour ne plus encombrer le HTML)
    const audioPath = 'assets/audio/';
    const hoverSound = new Audio(audioPath + 'hover.mp3');
    const clickSound = new Audio(audioPath + 'click.mp3');
    
    // Volumes globaux
    hoverSound.volume = 0.2;
    clickSound.volume = 0.1;

    // 3. Fonction principale pour attacher les sons et transitions
    window.attachSounds = function() {
        // Cible tous les éléments interactifs
        const elements = document.querySelectorAll('a, button, .glass-panel, .cursor-pointer, .sfx-link');
        
        elements.forEach(el => {
            // Évite les doublons si déjà attaché
            if(el.dataset.soundAttached) return;
            el.dataset.soundAttached = "true";

            // Son au survol
            el.addEventListener('mouseenter', () => {
                hoverSound.currentTime = 0;
                hoverSound.play().catch(() => {}); // catch évite les erreurs si l'utilisateur n'a pas interagi
            });

            // Clic : Son + Transition
            el.addEventListener('click', (e) => {
                // Jouer le son
                clickSound.currentTime = 0;
                clickSound.play().catch(() => {});

                // Gestion de la transition de page (Fade Out)
                const link = e.currentTarget;
                // Si c'est un lien <a> valide, qui ne s'ouvre pas dans un nouvel onglet, et n'est pas une ancre (#)
                if (link.tagName === 'A' && link.href && link.target !== "_blank" && !link.href.includes('#') && !link.href.includes('javascript')) {
                    e.preventDefault(); // Bloque le changement immédiat
                    document.body.classList.add('fade-out'); // Ajoute la classe CSS
                    
                    setTimeout(() => {
                        window.location.href = link.href; // Change de page après 500ms
                    }, 500);
                }
            });
        });
    };

    // Lancer l'attachement au chargement de la page
    window.attachSounds();
});

// 4. Fonction Globale : Mode Dyslexique
// Elle cherche automatiquement le conteneur principal selon la page
window.toggleDyslexicMode = function() {
    // Liste des IDs possibles où appliquer la police
    const targets = ['page-body', 'reader-scroll-area', 'main-container'];
    let targetEl = null;

    // Trouve le premier ID existant sur la page actuelle
    for (const id of targets) {
        const el = document.getElementById(id);
        if (el) {
            targetEl = el;
            break;
        }
    }

    // Si on a trouvé un conteneur, on switch le style
    if (targetEl) {
        if (targetEl.classList.contains('font-dyslexic')) {
            targetEl.classList.remove('font-dyslexic');
            targetEl.style.fontFamily = ""; 
            targetEl.style.lineHeight = "";
            targetEl.style.letterSpacing = "";
            targetEl.style.wordSpacing = "";
        } else {
            targetEl.classList.add('font-dyslexic');
            targetEl.style.fontFamily = "Verdana, Helvetica, Arial, sans-serif";
            targetEl.style.lineHeight = "2";
            targetEl.style.letterSpacing = "0.05em";
            targetEl.style.wordSpacing = "0.1em";
        }
    } else {
        console.warn("Origin System: Aucune zone de texte trouvée pour le mode dyslexique.");
    }
};

// 5. Animation Scroll (C'EST ICI QUE CA MANQUAIT)
const revealElements = document.querySelectorAll('.reveal, #page-footer'); 
const revealOnScroll = () => {
    const windowHeight = window.innerHeight;
    const elementVisible = 150; 
    revealElements.forEach((el) => {
        const elementTop = el.getBoundingClientRect().top;
        if (elementTop < windowHeight - elementVisible) {
            el.classList.add('active');
        }
    });
};
window.addEventListener('scroll', revealOnScroll);
revealOnScroll();

// 5. Particules
const particlesContainer = document.getElementById('particles-container');
if(particlesContainer) {
    for (let i = 0; i < 50; i++) {
        const div = document.createElement('div');
        div.classList.add('particle');
        div.style.width = Math.random() * 3 + 'px';
        div.style.height = div.style.width;
        div.style.top = Math.random() * 100 + '%';
        div.style.left = Math.random() * 100 + '%';
        div.style.animationDelay = Math.random() * 5 + 's';
        particlesContainer.appendChild(div);
    }
}

//6. Copyright Dynamique
const currentYear = new Date().getFullYear();
document.getElementById('made-by').textContent = "2025 - " + currentYear + " par L'équipe d'Origin";
document.getElementById('copyright').textContent = `© OriginRp, ${currentYear}. Tous droits réservés. Reproduction strictement interdite.`;