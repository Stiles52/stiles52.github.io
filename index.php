<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://unpkg.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src https://fonts.gstatic.com; img-src 'self' data: https:; frame-src https://www.youtube-nocookie.com; connect-src 'self';">

    <title>ORIGIN</title>

    <link rel="icon" type="image/png" href="assets/images/favicon.png">

    <link href="./assets/css/style.css" rel="stylesheet">
    <link href="./assets/css/easter-egg-mars.css" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-black min-h-screen relative">

    <!-- SONS -->
    <audio id="sfx-boot" src="assets/audio/boot.mp3" preload="auto"></audio>
    <audio id="sfx-anthem" src="assets/audio/anthem.mp3" preload="auto"></audio>

    <!-- EASTER EGG : CONSOLE -->
    <div id="easter-egg-console">
        <div class="scanlines"></div>
        <div id="console-output" class="text-xs md:text-sm space-y-1 font-mono text-green-400 overflow-y-auto max-h-[80vh]"></div>
        
        <div id="loading-area" class="mt-4">
            <div class="h-1 w-full bg-gray-900 overflow-hidden border border-green-500/30">
                <div id="console-bar" class="h-full bg-green-500 w-0"></div>
            </div>
        </div>

        <div id="input-line" class="hidden flex items-center gap-2 mt-2 text-xs md:text-sm font-mono text-green-400 border-t border-green-900/50 pt-2">
            <span class="text-green-600">guest@origin:~$</span>
            <input type="text" id="console-input" class="bg-transparent border-none outline-none text-green-400 flex-1 focus:ring-0" autocomplete="off" spellcheck="false">
        </div>
    </div>

    <!-- BACKGROUND & PARTICLES -->
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute w-full h-full bg-black"></div>
        <div id="particles-container"></div>
        <div class="fixed bottom-[-150px] left-1/2 -translate-x-1/2 w-[600px] h-[400px] bg-cyan-500/15 blur-[120px] rounded-full pointer-events-none z-0"></div>
    </div>

    <!-- NAVIGATION -->
    <?php include './assets/elements/header.php'; ?>

    <!-- HEADER -->
    <header class="relative h-screen w-full flex flex-col justify-center items-center z-10">
        <div id="hero-content" class="relative transition-all duration-[2000ms] ease-out opacity-0 scale-95 translate-y-10">
            <div class="relative flex items-center justify-center group cursor-pointer p-10">
                <div class="absolute w-[120%] h-[120%] border border-cyan-500/20 rounded-full animate-[spin-slow_20s_linear_infinite]"></div>
                <div class="absolute w-[110%] h-[110%] border border-dashed border-cyan-400/30 rounded-full animate-[spin-reverse-slow_30s_linear_infinite]"></div>
                <div class="absolute w-[100%] h-[100%] border-2 border-t-cyan-500/60 border-r-transparent border-b-cyan-500/60 border-l-transparent rounded-full animate-[spin-slow_10s_linear_infinite]"></div>
                <div class="absolute -inset-10 bg-cyan-400/20 blur-3xl rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-1000 animate-pulse"></div>
                <h1 class="font-bold animate-float drop-shadow-[0_0_15px_rgba(34,211,238,0.8)] tracking-widest z-10 text-center relative">ORIGIN</h1>
            </div>
            <div id="system-status" class="text-center text-xs tracking-[1em] mt-12 uppercase animate-pulse transition-colors duration-500">Connecting...</div>
        </div>
        <div class="absolute bottom-10 animate-bounce opacity-70">
            <div class="flex flex-col items-center gap-2">
                <span class="text-[10px] uppercase tracking-widest text-cyan-400">Scroll</span>
                <i data-lucide="arrow-down" class="w-6 h-6 text-cyan-400"></i>
            </div>
        </div>
    </header>

    <!-- SECTION PRÉSENTATION -->
    <section class="relative w-full min-h-screen flex items-center justify-center py-24 px-6 md:px-20 z-10 bg-gradient-to-b from-transparent to-black/90">
        <div class="max-w-7xl w-full mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div class="reveal border-l-2 border-cyan-500/50 pl-6">
                <div class="flex items-center gap-3 mb-6">
                    <i data-lucide="globe" class="w-5 h-5 text-cyan-400"></i>
                    <span class="uppercase tracking-wider text-sm font-bold opacity-70 text-cyan-100">Présentation</span>
                </div>
                <h2 class="mb-8">L'Origine du Monde</h2>
                
                <p class="mb-10">
                    Origin est un serveur RolePlay (RP) sur Minecraft. Le RP consiste à incarner un personnage et à développer ses caractéristiques avec crédibilité, à l'instar d'un acteur.<br>
                    <br>
                    Le projet a démarré en 2019 entre amis, évoluant rapidement d'un simple serveur survie vers un projet RP "Apocalypse" qui a engendré une forte visibilité. Le succès a été particulièrement marqué par le projet phare "Labyrinthe".<br>
                    <br>
                    Néanmoins, Origin est actuellement en pause indéterminée (février 2023) par manque de personnel, ce qui a mené l'équipe à se reconcentrer sur des projets que nous connaissons bien.
                </p>
                
                <a href="lore.html" class="sfx-link origin-btn btn--graphic btn--info">
                    <span>Découvrir le Lore</span>
                    <i data-lucide="arrow-right" class="btn--icon"></i>
                </a>
            </div>

            <div class="reveal w-full aspect-video relative group border border-cyan-500/50 shadow-[0_0_30px_rgba(34,211,238,0.1)] overflow-hidden bg-black">
                <iframe width="100%" height="100%" src="https://www.youtube-nocookie.com/embed/_LbMPILZL-w" title="Origin Presentation" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen class="w-full h-full object-cover"></iframe>
                <div class="absolute top-0 left-0 w-4 h-4 border-t-2 border-l-2 border-cyan-400 pointer-events-none"></div>
                <div class="absolute top-0 right-0 w-4 h-4 border-t-2 border-r-2 border-cyan-400 pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 w-4 h-4 border-b-2 border-l-2 border-cyan-400 pointer-events-none"></div>
                <div class="absolute bottom-0 right-0 w-4 h-4 border-b-2 border-r-2 border-cyan-400 pointer-events-none"></div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <?php include './assets/elements/footer.html'; ?>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/easter-egg-mars.js"></script>
    <script src="assets/js/intro.js"></script>
    <script src="assets/js/matinal.js"></script>
    <script src="assets/js/statut-system.js"></script>
</body>
</html>