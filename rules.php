<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://unpkg.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src https://fonts.gstatic.com; img-src 'self' data: https:; frame-src https://www.youtube-nocookie.com; connect-src 'self';">

    <title>RÈGLEMENT - ORIGIN</title>

    <link rel="icon" type="image/png" href="assets/images/favicon.png">

    <link href="./assets/css/style.css" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>

        .glass-panel {
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(34, 211, 238, 0.2);
            box-shadow: 0 0 30px rgba(34, 211, 238, 0.05);
            transition: all 0.3s ease;
        }

        .scanline {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(to bottom, transparent 50%, rgba(0,0,0,0.3) 51%);
            background-size: 100% 4px; pointer-events: none; z-index: 40; opacity: 0.3;
        }
        
        .fade-in { animation: fadeIn 0.5s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* ═══ rules.php — Responsive fixes ═══ */

        /* H1 "CENTRE ADMINISTRATIF" : 3.85rem trop grand */
        @media (max-width: 768px) {
            header h1 { font-size: 1.9rem !important; line-height: 1.2; }
        }
        @media (max-width: 480px) {
            header h1 { font-size: 1.5rem !important; }
        }

        @media (max-width: 768px) {
            /* Paragraphes : supprime le justify global sur mobile */
            p, .text-justify { text-align: left !important; }
            /* Exception : sous-titre header reste centré */
            header p { text-align: center !important; }

            /* H3 section headers (Règles Générales, Communication…) : 1.85rem → trop large */
            main h3 {
                font-size: 1.2rem !important;
                line-height: 1.4;
                flex-wrap: wrap;
            }

            /* Glass-panels : padding réduit sur mobile */
            .glass-panel { padding: 1rem !important; }

            /* Footer inline : fix justify + font-size hérité de la règle p{} globale */
            #page-footer p {
                text-align: left !important;
                font-size: inherit !important;
                line-height: inherit !important;
            }
        }
    </style>
</head>
<body class="bg-black min-h-screen flex flex-col pb-4 md:pb-0">

    <div class="scanline"></div>

    <!-- NAVIGATION -->
    <?php include './assets/elements/header.php'; ?>

    <header class="pt-32 pb-8 px-6 text-center relative z-10">
        <h1 class="font-bold text-white mb-4">CENTRE <span class="text-cyan-400">ADMINISTRATIF</span></h1>
        <p class="text-gray-400 text-xs md:text-sm uppercase text-center">Règlementation du serveur</p>
    </header>

    <main class="max-w-5xl mx-auto px-6 relative z-10 mb-40 min-h-[50vh]">

        <div class="fade-in space-y-16">
            
            <div class="border-l-4 border-red-500 pl-6 py-4 bg-red-900/10">
                <p class="text-sm text-gray-300 italic font-medium">
                    "La méconnaissance de la loi n'est pas une excuse. En rejoignant Origin, vous acceptez l'intégralité de ces règles."
                </p>
            </div>

            <div class="text-center mb-8">
                <p class="text-xs text-gray-400">
                    Il est essentiel de lire cette page pour comprendre les règles et les attentes qui garantiront une expérience agréable pour tous.
                    <br>Votre candidature fera office de carte d'identité, essentielle pour rejoindre notre plateforme.
                </p>
            </div>

            <section>
                <h3 class="text-cyan-400 text-xl font-bold uppercase mb-6 flex items-center gap-3 border-b border-gray-800 pb-2">
                    <span class="text-gray-600">01.</span> Règles Générales
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="glass-panel p-4 border-l-2 border-cyan-500">
                        <strong class="text-cyan-400 text-sm uppercase mb-1 block">Respect & Courtoisie</strong>
                        <p class="text-xs text-gray-400">Traitez tous les joueurs avec respect. Les insultes, le harcèlement et les comportements discriminatoires sont strictement interdits.</p>
                    </div>
                    <div class="glass-panel p-4 border-l-2 border-red-500 bg-red-900/5">
                        <strong class="text-red-500 text-sm uppercase mb-1 block">RP Érotique (Strictement Interdit)</strong>
                        <p class="text-xs text-gray-400">Toute tentative de RP érotique (RPQ) sera sévèrement sanctionnée.</p>
                    </div>
                    <div class="glass-panel p-4 col-span-1 md:col-span-2">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <strong class="text-white text-xs uppercase block mb-1">Publicité</strong>
                                <p class="text-[10px] text-gray-500">Interdite pour d'autres serveurs/projets sans autorisation.</p>
                            </div>
                            <div>
                                <strong class="text-white text-xs uppercase block mb-1">Tricherie</strong>
                                <p class="text-[10px] text-gray-500">Exploitation de bugs ou utilisation de mods interdits = Ban.</p>
                            </div>
                            <div>
                                <strong class="text-white text-xs uppercase block mb-1">Alt F4</strong>
                                <p class="text-[10px] text-gray-500">Déconnexion en pleine scène interdite.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section>
                <h3 class="text-purple-400 text-xl font-bold uppercase mb-6 flex items-center gap-3 border-b border-gray-800 pb-2">
                    <span class="text-gray-600">02.</span> Communication
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="glass-panel p-4">
                        <strong class="text-white text-sm uppercase mb-1 block flex items-center gap-2"><i data-lucide="mic-off" class="w-4 h-4 text-purple-400"></i> Vocal HRP</strong>
                        <p class="text-xs text-gray-400">Interdit. Utilisez exclusivement le chat local ou global pour le Hors-RP.</p>
                    </div>
                    <div class="glass-panel p-4">
                        <strong class="text-white text-sm uppercase mb-1 block">Langage & Spam</strong>
                        <p class="text-xs text-gray-400">Langage approprié exigé. Pas de propos vulgaires ou de spam.</p>
                    </div>
                    <div class="glass-panel p-4 col-span-1 md:col-span-2 border border-purple-500/30">
                        <strong class="text-purple-300 text-sm uppercase mb-2 block">Utilisation des Références & Mèmes</strong>
                        <ul class="text-xs text-gray-400 list-disc pl-4 space-y-1">
                            <li>Tolérées avec parcimonie dans les moments calmes / Off-RP.</li>
                            <li><span class="text-red-400">Strictement déconseillées</span> lors des scènes sérieuses (combat, fuite, urgence).</li>
                            <li>Sanction immédiate si utilisées pour "faire rire la galerie" en pleine tension (Atteinte à l'immersion).</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section>
                <h3 class="text-orange-400 text-xl font-bold uppercase mb-6 flex items-center gap-3 border-b border-gray-800 pb-2">
                    <span class="text-gray-600">03.</span> Règles de Jeu (Gameplay)
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="glass-panel p-4 hover:bg-orange-900/10 transition-colors">
                        <strong class="text-orange-400 text-xs uppercase block mb-1">Metagaming</strong>
                        <p class="text-[10px] text-gray-400">Divulguer ou utiliser des infos HRP en RP est interdit.</p>
                    </div>
                    <div class="glass-panel p-4 hover:bg-orange-900/10 transition-colors">
                        <strong class="text-orange-400 text-xs uppercase block mb-1">PowerGaming</strong>
                        <p class="text-[10px] text-gray-400">Actions impossibles ou forcées sans l'accord de l'autre joueur.</p>
                    </div>
                    <div class="glass-panel p-4 hover:bg-orange-900/10 transition-colors">
                        <strong class="text-orange-400 text-xs uppercase block mb-1">Pain & Fear RP</strong>
                        <p class="text-[10px] text-gray-400">Réagir de manière réaliste à la douleur et à la peur.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="glass-panel p-4">
                        <strong class="text-white text-sm uppercase mb-1 block">Combat & Mort</strong>
                        <ul class="text-xs text-gray-400 space-y-1">
                            <li><strong class="text-red-400">Freekill :</strong> Tuer sans raison valable est interdit.</li>
                            <li><strong class="text-red-400">RevengeKill :</strong> Se venger de sa propre mort est interdit.</li>
                        </ul>
                    </div>
                    <div class="glass-panel p-4">
                        <strong class="text-white text-sm uppercase mb-1 block">Cohérence & Physique</strong>
                        <ul class="text-xs text-gray-400 space-y-1">
                            <li><strong class="text-yellow-400">Déplacement :</strong> Bunny Hop (sauter) et Chicken Run (courir partout) interdits.</li>
                            <li><strong class="text-yellow-400">Identité :</strong> Pas de noms de célébrités (ex: Harry Potter).</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section>
                <h3 class="text-green-400 text-xl font-bold uppercase mb-6 flex items-center gap-3 border-b border-gray-800 pb-2">
                    <span class="text-gray-600">04.</span> Construction & Propriété
                </h3>
                <div class="glass-panel p-6 border-l-2 border-green-500">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <strong class="text-green-400 text-sm uppercase mb-2 block">Règles de Bâtiment</strong>
                            <ul class="text-xs text-gray-400 list-disc pl-4 space-y-2">
                                <li>Constructions réalistes et cohérentes avec l'univers.</li>
                                <li>Pas de constructions abusives ou inesthétiques.</li>
                                <li>Ne modifiez pas les bâtiments des autres sans accord.</li>
                            </ul>
                        </div>
                        <div>
                            <strong class="text-green-400 text-sm uppercase mb-2 block">Propriété</strong>
                            <p class="text-xs text-gray-400 mb-2">Respectez les propriétés privées.</p>
                            <div class="bg-green-900/20 p-2 border border-green-500/30 text-center rounded">
                                <span class="text-xs text-green-300 font-bold">Ticket Staff OBLIGATOIRE avant tout vol.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section>
                <h3 class="text-yellow-400 text-xl font-bold uppercase mb-6 flex items-center gap-3 border-b border-gray-800 pb-2">
                    <span class="text-gray-600">05.</span> Événements & Interactions
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="glass-panel p-4 text-center">
                        <i data-lucide="calendar" class="w-6 h-6 text-yellow-400 mx-auto mb-2"></i>
                        <strong class="text-white text-xs uppercase block">Animations</strong>
                        <p class="text-[10px] text-gray-500 mt-1">Doivent être validées par le staff/animateurs.</p>
                    </div>
                    <div class="glass-panel p-4 text-center">
                        <i data-lucide="users" class="w-6 h-6 text-yellow-400 mx-auto mb-2"></i>
                        <strong class="text-white text-xs uppercase block">Immersion</strong>
                        <p class="text-[10px] text-gray-500 mt-1">Évitez tout comportement disruptif.</p>
                    </div>
                    <div class="glass-panel p-4 text-center">
                        <i data-lucide="bot" class="w-6 h-6 text-yellow-400 mx-auto mb-2"></i>
                        <strong class="text-white text-xs uppercase block">PNJ</strong>
                        <p class="text-[10px] text-gray-500 mt-1">Usage cohérent et respectueux uniquement.</p>
                    </div>
                </div>
            </section>

        </div>

    </main>

    <?php include './assets/elements/footer.html'; ?>

    <script>
        lucide.createIcons();
    </script>
    <script src="assets/js/main.js" defer></script>