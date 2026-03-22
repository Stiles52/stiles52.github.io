<style>
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&display=swap');
    body { font-family: 'JetBrains Mono', monospace; background-color: black; color: white; overflow: hidden; }

    /* Effet Glitch GHOST_PROTOCOL*/
    .glitch { position: relative; animation: glitch-anim 1s infinite; }
    .glitch::before, .glitch::after {
        content: attr(data-text); position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.8;
    }
    .glitch::before { color: #0ff; clip-path: polygon(0 0, 100% 0, 100% 45%, 0 45%); animation: glitch-anim-2 3s infinite linear alternate-reverse; transform: translate(-2px); }
    .glitch::after { color: #f00; clip-path: polygon(0 55%, 100% 55%, 100% 100%, 0 100%); animation: glitch-anim-2 2s infinite linear alternate-reverse; transform: translate(2px); }

    @keyframes glitch-anim-2 {
        0% { clip-path: inset(40% 0 61% 0); }
        20% { clip-path: inset(92% 0 1% 0); }
        40% { clip-path: inset(43% 0 1% 0); }
        60% { clip-path: inset(25% 0 58% 0); }
        80% { clip-path: inset(54% 0 7% 0); }
        100% { clip-path: inset(58% 0 43% 0); }
    }
    
    .scanline {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: linear-gradient(to bottom, transparent 50%, rgba(0,0,0,0.3) 51%);
        background-size: 100% 4px; pointer-events: none; z-index: 40; opacity: 0.3;
    }

    body {
        transition: opacity 0.5s ease-in-out;
    }
    body.fade-out {
        opacity: 0;
    }
    
</style>
<main class="flex flex-col items-center justify-center h-screen relative p-6 w-full">
    <div class="border border-red-500/50 bg-red-900/10 p-8 md:p-12 rounded text-center max-w-2xl relative shadow-[0_0_50px_rgba(220,38,38,0.2)]">
        
        <i data-lucide="alert-octagon" class="w-20 h-20 text-red-500 mx-auto mb-6 animate-pulse"></i>
        
        <h1 class="text-6xl md:text-8xl font-bold text-white mb-2 glitch" data-text="404">404</h1>
        
        <h2 class="text-red-500 uppercase mt-6">
            ERREUR SYSTÈME :
        </h2>
        <h3 class="text-red-500 uppercase mb-6">
            DONNÉES CORROMPUES
        </h3>
        
        <blockquote class="mb-8 text-center">
            L'URL demandée est introuvable sur le réseau Origin.<br>
            Le fichier a peut-être été supprimé par la Fédération ou n'a jamais existé. 
        </blockquote> 
        <div class="flex flex-col md:flex-row gap-4 justify-center">
            <a href="index.html" class="origin-btn btn--graphic btn--danger uppercase">
                <i data-lucide="power" class="w-4 h-4"></i> Redémarrer le Système
            </a>
        </div>

        <div class="absolute bottom-2 left-0 w-full">
            <p class="text-[9px] text-red-500/50 font-mono text-center">ERROR_CODE: 0x000_NULL_POINTER</p>
        </div>
    </div>
</main>