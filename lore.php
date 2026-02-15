<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://unpkg.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src https://fonts.gstatic.com; img-src 'self' data: https:; frame-src https://www.youtube-nocookie.com; connect-src 'self';">
    <title>ARCHIVES - ORIGIN</title>
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&display=swap');
        
        body {
            font-family: 'JetBrains Mono', monospace;
            background-color: black;
            color: white;
            overflow: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: auto;
        }

        /* Scrollbar personnalisée */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #050505; }
        ::-webkit-scrollbar-thumb { background: #0e7490; border-radius: 4px; }

        .glass-panel {
            background: rgba(10, 10, 10, 0.9); /* Noir un peu plus opaque pour la lisibilité */
            /* backdrop-filter: blur(10px); <--- LAISSE ÇA COMMENTÉ */

            border: 1px solid rgba(34, 211, 238, 0.15);

            /* OPTIMISATION 1 : Ombre plus nette, moins coûteuse à calculer */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5); 

            /* OPTIMISATION 2 : Force le GPU et évite le lissage sub-pixel du texte */
            transform: translateZ(0);
            -webkit-font-smoothing: subpixel-antialiased;
            backface-visibility: hidden;

            /* OPTIMISATION 3 : Indique au navigateur que le contenu ne déborde pas bizarrement */
            contain: content; 

            transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }
        
        .active-tab {
            background: rgba(34, 211, 238, 0.1);
            border-left: 4px solid #22d3ee;
            color: #22d3ee;
        }

        .fade-in-view { animation: fadeIn 0.4s ease-out forwards; }
        @keyframes fadeIn { 
            from { 
                opacity: 0; 
                transform: translateY(10px); 
            } 
            to { 
                opacity: 1; 
                transform: none; /* C'est la clé ! On libère le calque à la fin */
            } 
        }

        .scanline {
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%;
            /* Garde ton background linear-gradient ici... */
            background: linear-gradient(to bottom, transparent 50%, rgba(0,0,0,0.3) 51%);
            background-size: 100% 4px; 
            pointer-events: none; 
            z-index: 40; 
            opacity: 0.3;

            /* AJOUTE CES LIGNES POUR LA PERF : */
            transform: translateZ(0); /* Isole le calque */
            will-change: opacity; /* Optimise le rendu */
            /* Optionnel : si ça lag toujours, passe pointer-events à none (déjà fait) */
        }

        @keyframes glitch-anim {
            0% { clip-path: inset(20% 0 80% 0); transform: translate(-2px, 1px); }
            20% { clip-path: inset(60% 0 10% 0); transform: translate(2px, -1px); }
            40% { clip-path: inset(40% 0 50% 0); transform: translate(-2px, 2px); }
            60% { clip-path: inset(80% 0 5% 0); transform: translate(2px, -2px); }
            80% { clip-path: inset(10% 0 70% 0); transform: translate(-1px, 1px); }
            100% { clip-path: inset(30% 0 50% 0); transform: translate(1px, -1px); }
        }
        .glitch-effect { animation: glitch-anim 0.3s infinite linear alternate-reverse; color: #ef4444; text-shadow: 2px 2px #7f1d1d; }
        .shake { animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both; }
        @keyframes shake { 10%, 90% { transform: translate3d(-1px, 0, 0); } 20%, 80% { transform: translate3d(2px, 0, 0); } }

        /* Typographie Lecteur */
        .rich-text h3 { font-size: 1.2rem; font-weight: bold; color: #22d3ee; margin-top: 2rem; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.1em; border-bottom: 1px solid rgba(34, 211, 238, 0.2); padding-bottom: 0.5rem; }
        .rich-text h4 { font-size: 1.1rem; font-weight: bold; color: white; margin-top: 1.5rem; margin-bottom: 0.5rem; }
        .rich-text p { margin-bottom: 1rem; color: #9ca3af; line-height: 1.8; text-align: justify; }
        .rich-text ul { list-style-type: none; padding-left: 0; margin-bottom: 1rem; color: #d1d5db; }
        .rich-text li { margin-bottom: 0.5rem; padding-left: 1.5rem; position: relative; }
        .rich-text li::before { content: "▹"; color: #22d3ee; position: absolute; left: 0; }
        .rich-text strong { color: white; font-weight: 700; }

        body {
            transition: opacity 0.5s ease-in-out;
        }
        body.fade-out {
            opacity: 0;
        }

        #view-history, #view-database {
            /* Indique au navigateur que ce truc va scroller, pour qu'il prépare la mémoire */
            will-change: scroll-position;

            /* Rend le scroll plus "natif" sur mobile/mac */
            -webkit-overflow-scrolling: touch; 
        }

        #file-reader {
            content-visibility: auto;
            contain-intrinsic-size: 5000px;
        }
    </style>
</head>
<body class="bg-black h-screen w-full flex">

    <div class="scanline"></div>

    <div id="access-denied-overlay" class="fixed inset-0 z-[100] bg-red-900/20 backdrop-blur-sm hidden flex items-center justify-center pointer-events-none opacity-0 transition-opacity duration-200">
        <div class="bg-black border-2 border-red-500 p-8 max-w-lg text-center relative shadow-[0_0_50px_rgba(220,38,38,0.5)] transform scale-95 transition-transform duration-200" id="error-box">
            <div class="absolute top-0 left-0 w-full h-2 bg-[repeating-linear-gradient(45deg,transparent,transparent_10px,#ef4444_10px,#ef4444_20px)] opacity-50"></div>
            <div class="absolute bottom-0 left-0 w-full h-2 bg-[repeating-linear-gradient(45deg,transparent,transparent_10px,#ef4444_10px,#ef4444_20px)] opacity-50"></div>
            <i data-lucide="lock" class="w-16 h-16 text-red-500 mx-auto mb-4 animate-pulse"></i>
            <h2 class="text-4xl md:text-5xl font-bold text-red-500 mb-2 tracking-tighter uppercase glitch-text">ACCÈS REFUSÉ</h2>
            <div class="h-px w-full bg-red-900 my-4"></div>
            <p class="text-red-400 font-bold text-sm tracking-[0.2em] uppercase mb-4">ERREUR : PROTOCOLE DE SÉCURITÉ 403</p>
            <p class="text-gray-400 text-xs font-mono leading-relaxed">
                Votre identifiant biométrique ne correspond à aucun personnel autorisé pour le dossier <span class="text-white">STAR MAZE</span>.<br><br>
                <span class="bg-red-500/20 text-red-400 px-2 py-1 border border-red-500/50">NIVEAU D'ACCRÉDITATION INSUFFISANT</span>
            </p>
        </div>
    </div>

    <aside class="w-64 bg-black/90 border-r border-cyan-900/30 flex-shrink-0 flex flex-col z-50 relative hidden md:flex">
        <div class="p-6 border-b border-cyan-900/30">
            <h1 class="text-xl font-bold text-white tracking-tighter">ORIGIN <span class="text-cyan-400">OS</span></h1>
            <div class="flex items-center gap-2 mt-2">
                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                <span class="text-[10px] text-green-500 uppercase tracking-widest">Connecté</span>
            </div>
        </div>
        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
            <div class="text-[10px] text-gray-600 uppercase tracking-[0.2em] font-bold mb-3 px-2">Navigation</div>
            <a href="index.html" class="flex items-center gap-3 px-3 py-2 text-xs uppercase tracking-widest text-gray-400 hover:text-white hover:bg-white/5 rounded transition-all group mb-6">
                <i data-lucide="power" class="w-4 h-4 group-hover:text-red-400 transition-colors"></i> Déconnexion
            </a>
            <div class="h-px bg-gray-900 mb-6"></div>
            <div class="text-[10px] text-gray-600 uppercase tracking-[0.2em] font-bold mb-3 px-2">Modules</div>
            <button id="btn-history" onclick="switchView('history')" class="w-full flex items-center gap-3 px-3 py-3 text-xs uppercase tracking-widest text-cyan-400 bg-cyan-900/10 border-l-4 border-cyan-400 rounded-r transition-all text-left active-tab">
                <i data-lucide="history" class="w-4 h-4"></i> Historique
            </button>
            <button id="btn-database" onclick="switchView('database')" class="w-full flex items-center gap-3 px-3 py-3 text-xs uppercase tracking-widest text-gray-400 hover:text-white hover:bg-white/5 border-l-4 border-transparent rounded-r transition-all text-left">
                <i data-lucide="database" class="w-4 h-4"></i> Base de Données
            </button>
        </nav>
        <div class="p-4 border-t border-cyan-900/30 text-[10px] text-gray-600 text-center relative">
            TERMINAL V.3.0.4
            <span onclick="phantomCopy()" class="cursor-default opacity-0 px-2 py-1 select-none absolute" title="ERR_BUFFER_OVERFLOW">.</span>
        </div>
    </aside>

    <nav class="fixed bottom-0 left-0 w-full bg-black/90 backdrop-blur-md border-t border-cyan-900/50 flex justify-around items-center p-4 z-[90] md:hidden">
        
        <button onclick="switchView('history')" id="mob-btn-history" class="flex flex-col items-center gap-1 text-cyan-400 group">
            <i data-lucide="history" class="w-6 h-6 group-hover:scale-110 transition-transform"></i>
            <span class="text-[10px] uppercase tracking-widest">Histo.</span>
        </button>

        <div class="w-px h-8 bg-gray-800"></div>

        <button onclick="switchView('database')" id="mob-btn-database" class="flex flex-col items-center gap-1 text-gray-500 hover:text-white transition-colors group">
            <i data-lucide="database" class="w-6 h-6 group-hover:scale-110 transition-transform"></i>
            <span class="text-[10px] uppercase tracking-widest">Données</span>
        </button>
        
        <div class="w-px h-8 bg-gray-800"></div>

        <a href="index.html" class="flex flex-col items-center gap-1 text-red-500/70 hover:text-red-400 transition-colors group">
            <i data-lucide="power" class="w-6 h-6 group-hover:scale-110 transition-transform"></i>
            <span class="text-[10px] uppercase tracking-widest">Sortir</span>
        </a>
    </nav>

    <main id="main-container" class="flex-1 relative overflow-hidden bg-black pb-24 md:pb-0">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_rgba(34,211,238,0.05),transparent_40%)] pointer-events-none"></div>

        <div id="view-history" class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 fade-in-view">
            <header class="text-center relative mb-24 mt-12">
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[300px] h-[300px] bg-cyan-500/20 blur-[100px] rounded-full pointer-events-none"></div>
                <h2 class="text-4xl md:text-6xl font-bold mb-4 relative z-10 tracking-tighter">HISTORIQUE <span class="text-cyan-400">UNIVERSEL</span></h2>
                <p class="text-gray-400 max-w-2xl mx-auto font-sans text-sm relative z-10 border-l-2 border-cyan-500 pl-4 text-left mt-8">Accès aux fichiers de la Fédération.<br>Sujet : Chronologie de l'effondrement et de la résurgence.</p>
            </header>
            
            <div class="max-w-5xl mx-auto px-2 md:px-6 pb-24 space-y-24 relative z-10">
                
                <div class="absolute left-6 md:left-1/2 top-0 bottom-0 w-px bg-gradient-to-b from-transparent via-cyan-900 to-transparent -translate-x-1/2"></div>
                
                <article class="relative grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-16 items-center">
                    <div class="absolute left-6 md:left-1/2 -translate-x-1/2 w-4 h-4 bg-black border-2 border-cyan-400 rounded-full z-10"></div>
                    
                    <div class="pl-12 md:pl-0 md:text-right order-1">
                        <div class="text-cyan-400 text-xs tracking-[0.3em] mb-2 font-bold">AN 2050</div>
                        <h2 class="text-2xl font-bold mb-4 text-white">LA FIN D'UN TOUT</h2>
                        <p class="text-gray-400 text-sm leading-relaxed font-sans text-justify">
                            La Terre agonise. Guerres, famines et effondrement écologique ont eu raison de l'équilibre planétaire. Face à l'inéluctable, les Nations Unies s'en remettent à <strong class="text-cyan-400">Siren Corporation</strong> pour bâtir des arches spatiales.
                            <br><br>
                            L'humanité se brise alors en deux : une élite sélectionnée pour conquérir les étoiles, et les laissés-pour-compte, condamnés à l'agonie sur un monde en ruines.
                        </p>
                    </div>
                    
                    <div class="pl-12 md:pl-0 order-2">
                        <div class="glass-panel p-4 rounded-sm border border-cyan-500/50 border-l-4 border-l-cyan-500">
                            <div class="text-xs text-gray-500 mb-1 uppercase">Statut Planétaire</div>
                            <div class="text-red-500 font-bold uppercase tracking-widest animate-pulse">CRITIQUE</div>
                        </div>
                    </div>
                </article>

                <article class="relative grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-16 items-center">
                    <div class="absolute left-6 md:left-1/2 -translate-x-1/2 w-4 h-4 bg-black border-2 border-purple-500 rounded-full z-10"></div>
                    
                    <div class="pl-12 md:pl-0 md:text-right order-2 md:order-1">
                        <div class="glass-panel p-4 rounded-sm border border-purple-500/50 border-l-4 border-l-purple-500/50 md:border-l md:border-r-4 md:border-r-purple-500">
                            <div class="text-xs text-gray-500 mb-1 uppercase">Organisation</div>
                            <div class="text-purple-400 font-bold uppercase tracking-widest">O.E.R.S & P.T.R.G.E</div>
                        </div>
                    </div>
                    
                    <div class="pl-12 md:pl-0 order-1 md:order-2">
                        <div class="text-purple-400 text-xs tracking-[0.3em] mb-2 font-bold">ÈRE SPATIALE</div>
                        <h2 class="text-2xl font-bold mb-4 text-white">UNE ESPÈCE EN SURSIS</h2>
                        <p class="text-gray-400 text-sm leading-relaxed font-sans text-justify">
                            Malgré un siècle de stabilité relative, la criminalité gangrène les stations. L'<strong class="text-purple-400">O.E.R.S</strong> échoue à trouver une exoplanète viable malgré des explorations minutieuses.
                            <br><br>
                            Face à l'impasse, la Fédération lance en 2159 une alternative radicale : le <strong class="text-purple-400">P.T.R.G.E</strong>. L'objectif change : ne plus chercher un monde habitable, mais transformer les mondes existants pour qu'ils le deviennent.
                        </p>
                    </div>
                </article>

                <article class="relative grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-16 items-center">
                    <div class="absolute left-6 md:left-1/2 -translate-x-1/2 w-4 h-4 bg-black border-2 border-green-400 rounded-full z-10"></div>
                    
                    <div class="pl-12 md:pl-0 md:text-right order-1">
                        <div class="text-green-400 text-xs tracking-[0.3em] mb-2 font-bold">AN 2159 - 2161</div>
                        <h2 class="text-2xl font-bold mb-4 text-white">LA PLANÈTE ORIGINELLE</h2>
                        <p class="text-gray-400 text-sm leading-relaxed font-sans text-justify">
                            La Terre, oubliée, révèle une surface mutée où les survivants sont devenus des chimères méconnaissables. Convaincu de la reconquête, le <strong class="text-green-400">P.T.R.G.E</strong> lance dès 2159 le projet des <strong>Labyrinthes</strong>.
                            <br><br>
                            Des humains, souvent condamnés ou amnésiques, y sont enfermés pour tester les limites de l'adaptation humaine dans des conditions de souffrance absolue.
                        </p>
                    </div>
                    
                    <div class="pl-12 md:pl-0 order-2">
                        <div class="glass-panel p-4 rounded-sm border border-green-500/50 border-l-4 border-l-green-500">
                            <div class="text-xs text-gray-500 mb-1 uppercase">Cible</div>
                            <div class="text-green-400 font-bold uppercase tracking-widest">SUJETS DE TEST</div>
                        </div>
                    </div>
                </article>

                <article class="relative grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-16 items-center">
                    <div class="absolute left-6 md:left-1/2 -translate-x-1/2 w-4 h-4 bg-black border-2 border-yellow-500 rounded-full z-10"></div>
                    
                    <div class="pl-12 md:pl-0 md:text-right order-2 md:order-1">
                        <div class="glass-panel p-4 rounded-sm border border-yellow-500/50 border-l-4 border-l-yellow-500/50 md:border-l md:border-r-4 md:border-r-yellow-500">
                            <div class="text-xs text-gray-500 mb-1 uppercase">Menace</div>
                            <div class="text-yellow-500 font-bold uppercase tracking-widest">INSURRECTION</div>
                        </div>
                    </div>
                    
                    <div class="pl-12 md:pl-0 order-1 md:order-2">
                        <div class="text-yellow-500 text-xs tracking-[0.3em] mb-2 font-bold">DÉRIVE SOCIALE</div>
                        <h2 class="text-2xl font-bold mb-4 text-white">PERDUS DANS L'ESPACE</h2>
                        <p class="text-gray-400 text-sm leading-relaxed font-sans text-justify">
                            Tandis que la Fédération se replie pour protéger les Hautes-Instances, l'espace devient une zone de non-droit régie par les mafias et la contrebande. Sous pression, le <strong class="text-yellow-500">P.T.R.G.E.</strong> radicalise ses méthodes.
                            <br><br>
                            Pour financer les recherches, un nouveau marché émerge : le trafic d'organes humains et bioniques, alimenté par les meurtres et la misère des stations.
                        </p>
                    </div>
                </article>

                <article class="relative grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-16 items-center">
                    <div class="absolute left-6 md:left-1/2 -translate-x-1/2 w-4 h-4 bg-white border-2 border-cyan-400 shadow-[0_0_15px_rgba(34,211,238,1)] rounded-full z-10"></div>
                    
                    <div class="pl-12 md:pl-0 md:text-right order-1">
                        <div class="text-cyan-400 text-xs tracking-[0.3em] mb-2 font-bold">AN 2193</div>
                        <h2 class="text-2xl font-bold mb-4 text-white">... LE COMMENCEMENT</h2>
                        <p class="text-gray-400 text-sm leading-relaxed font-sans text-justify">
                            La Fédération et le P.T.R.G.E. s'unissent pour créer le programme <strong class="text-cyan-400 animate-pulse">Star Maze</strong> ("Labyrinthe étoilé"). Une nouvelle itération du modèle labyrinthique, mais avec une différence notable gardée secrète.
                            <br><br>
                            L'épreuve reste la même peu importe le paysage. Une seule règle prévaut pour survivre : ne jamais se fier à ce que l'on voit.
                        </p>
                    </div>
                    
                    <div class="pl-12 md:pl-0 order-2">
                        <div class="glass-panel p-6 rounded-sm border border-cyan-500/50 border-t-4 border-t-cyan-500 text-center relative overflow-hidden group">
                            <div class="absolute inset-0 bg-red-500/0 group-hover:bg-red-500/5 transition-colors duration-500 pointer-events-none"></div>
                            <i data-lucide="triangle-alert" class="w-8 h-8 text-cyan-400 mx-auto mb-2 group-hover:text-red-400 transition-colors"></i>
                            <div class="text-white font-bold uppercase tracking-widest text-lg group-hover:text-red-100 transition-colors">STAR MAZE</div>
                            <button onclick="triggerAccessDenied()" class="mt-4 w-full border border-cyan-500/50 text-cyan-400 text-xs py-2 hover:bg-red-500/20 hover:text-red-400 hover:border-red-500/50 transition-all uppercase tracking-widest">Accéder au dossier</button>
                        </div>
                    </div>
                </article>
            </div>
        </div>

        <div id="view-database" class="absolute inset-0 overflow-y-auto p-6 pb-32 md:p-12 hidden">
            <div class="max-w-6xl mx-auto px-6 relative z-10 mt-12">
                <div class="text-center mb-16">
                    <div class="inline-flex items-center gap-2 text-cyan-400 border border-cyan-500/30 px-4 py-1 rounded-full mb-4">
                        <div class="w-2 h-2 bg-cyan-400 rounded-full animate-pulse"></div>
                        <span class="text-[10px] uppercase tracking-widest">Accès Autorisé</span>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold uppercase tracking-tighter mb-4">Base de Données <span class="text-gray-500">Complémentaire</span></h2>
                    <p class="text-gray-400 text-sm">Sélectionnez un dossier pour consulter les archives détaillées.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pb-24">

                    <div class="col-span-full text-xs text-gray-500 uppercase tracking-[0.2em] font-bold mt-4 mb-2 border-b border-gray-800 pb-2">Hiérarchie Sociale</div>

                    <div onclick="openFile('senescients')" class="glass-panel p-6 cursor-pointer group hover:-translate-y-1 hover:border-purple-400/50">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-3 bg-purple-500/10 rounded border border-purple-500/30 group-hover:bg-purple-500/20 transition-colors"><i data-lucide="crown" class="text-purple-400 w-6 h-6"></i></div>
                            <i data-lucide="external-link" class="text-gray-600 w-4 h-4 group-hover:text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-1">Les Sénéscients</h3>
                        <p class="text-xs text-purple-400 uppercase tracking-widest">Classe Dirigeante</p>
                    </div>

                    <div onclick="openFile('magnats')" class="glass-panel p-6 cursor-pointer group hover:-translate-y-1 hover:border-indigo-400/50">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-3 bg-indigo-500/10 rounded border border-indigo-500/30 group-hover:bg-indigo-500/20 transition-colors"><i data-lucide="coins" class="text-indigo-400 w-6 h-6"></i></div>
                            <i data-lucide="external-link" class="text-gray-600 w-4 h-4 group-hover:text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-1">Les Magnats</h3>
                        <p class="text-xs text-indigo-400 uppercase tracking-widest">Classe Économique</p>
                    </div>
                    
                    <div onclick="openFile('segmentaires')" class="glass-panel p-6 cursor-pointer group hover:-translate-y-1 hover:border-blue-400/50">
                         <div class="flex justify-between items-start mb-4">
                            <div class="p-3 bg-blue-500/10 rounded border border-blue-500/30 group-hover:bg-blue-500/20 transition-colors"><i data-lucide="users" class="text-blue-400 w-6 h-6"></i></div>
                            <i data-lucide="external-link" class="text-gray-600 w-4 h-4 group-hover:text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-1">Les Segmentaires</h3>
                        <p class="text-xs text-blue-400 uppercase tracking-widest">Classe Civile</p>
                    </div>

                    <div onclick="openFile('infralaborants')" class="glass-panel p-6 cursor-pointer group hover:-translate-y-1 hover:border-orange-400/50">
                         <div class="flex justify-between items-start mb-4">
                            <div class="p-3 bg-orange-500/10 rounded border border-orange-500/30 group-hover:bg-orange-500/20 transition-colors"><i data-lucide="hammer" class="text-orange-400 w-6 h-6"></i></div>
                            <i data-lucide="external-link" class="text-gray-600 w-4 h-4 group-hover:text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-1">Les Infralaborants</h3>
                        <p class="text-xs text-orange-400 uppercase tracking-widest">Classe Ouvrière</p>
                    </div>

                    <div onclick="openFile('rebuts')" class="glass-panel p-6 cursor-pointer group hover:-translate-y-1 hover:border-red-400/50">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-3 bg-red-500/10 rounded border border-red-500/30 group-hover:bg-red-500/20 transition-colors">
                                <i data-lucide="skull" class="text-red-400 w-6 h-6"></i>
                            </div>
                            <i data-lucide="external-link" class="text-gray-600 w-4 h-4 group-hover:text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-1">Les Rebuts</h3>
                        <p class="text-xs text-red-400 uppercase tracking-widest">Hors-Classe / Illégal</p>
                    </div>

                    <div class="col-span-full text-xs text-gray-500 uppercase tracking-[0.2em] font-bold mt-8 mb-2 border-b border-gray-800 pb-2">Systèmes & Infrastructures</div>

                    <div onclick="openFile('naissance')" class="glass-panel p-6 cursor-pointer group hover:-translate-y-1 hover:border-cyan-400/50">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-3 bg-cyan-500/10 rounded border border-cyan-500/30 group-hover:bg-cyan-500/20 transition-colors"><i data-lucide="baby" class="text-cyan-400 w-6 h-6"></i></div>
                            <i data-lucide="external-link" class="text-gray-600 w-4 h-4 group-hover:text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-1">Système de Naissance</h3>
                        <p class="text-xs text-cyan-400 uppercase tracking-widest">Régulation</p>
                    </div>

                    <div onclick="openFile('corporations')" class="glass-panel p-6 cursor-pointer group hover:-translate-y-1 hover:border-yellow-400/50">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-3 bg-yellow-500/10 rounded border border-yellow-500/30 group-hover:bg-yellow-500/20 transition-colors"><i data-lucide="building-2" class="text-yellow-400 w-6 h-6"></i></div>
                            <i data-lucide="external-link" class="text-gray-600 w-4 h-4 group-hover:text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-1">Les Grandes Enseignes</h3>
                        <p class="text-xs text-yellow-400 uppercase tracking-widest">Corporations & Instances</p>
                    </div>

                    <div onclick="openFile('stations')" class="glass-panel p-6 cursor-pointer group hover:-translate-y-1 hover:border-emerald-400/50">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-3 bg-emerald-500/10 rounded border border-emerald-500/30 group-hover:bg-emerald-500/20 transition-colors"><i data-lucide="satellite" class="text-emerald-400 w-6 h-6"></i></div>
                            <i data-lucide="external-link" class="text-gray-600 w-4 h-4 group-hover:text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-1">Les Stations Spatiales</h3>
                        <p class="text-xs text-emerald-400 uppercase tracking-widest">Infrastructures & Habitats</p>
                    </div>

                    <div onclick="openFile('money')" class="glass-panel p-6 cursor-pointer group hover:-translate-y-1 hover:border-yellow-600/50">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-3 bg-yellow-600/10 rounded border border-yellow-600/30 group-hover:bg-yellow-600/20 transition-colors"><i data-lucide="credit-card" class="text-yellow-500 w-6 h-6"></i></div>
                            <i data-lucide="external-link" class="text-gray-600 w-4 h-4 group-hover:text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-1">Système Monétaire</h3>
                        <p class="text-xs text-yellow-500 uppercase tracking-widest">Économie & C.I.G</p>
                    </div>

                    <div onclick="openFile('justice')" class="glass-panel p-6 cursor-pointer group hover:-translate-y-1 hover:border-slate-400/50">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-3 bg-slate-500/10 rounded border border-slate-500/30 group-hover:bg-slate-500/20 transition-colors"><i data-lucide="scale" class="text-slate-400 w-6 h-6"></i></div>
                            <i data-lucide="external-link" class="text-gray-600 w-4 h-4 group-hover:text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-1">Système Juridique</h3>
                        <p class="text-xs text-slate-400 uppercase tracking-widest">Lois & Sanctions</p>
                    </div>

                    <div class="col-span-full text-xs text-gray-500 uppercase tracking-[0.2em] font-bold mt-8 mb-2 border-b border-gray-800 pb-2">Base de Données Xénobiologique</div>

                    <div onclick="openFile('hybrides')" class="glass-panel p-6 cursor-pointer group hover:-translate-y-1 hover:border-pink-400/50">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-3 bg-pink-500/10 rounded border border-pink-500/30 group-hover:bg-pink-500/20 transition-colors"><i data-lucide="dna" class="text-pink-400 w-6 h-6"></i></div>
                            <i data-lucide="external-link" class="text-gray-600 w-4 h-4 group-hover:text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-1">Histoire des Hybrides</h3>
                        <p class="text-xs text-pink-400 uppercase tracking-widest">Génétique & Société</p>
                    </div>

                    <div onclick="openFile('protomates')" class="glass-panel p-6 cursor-pointer group hover:-translate-y-1 hover:border-cyan-400/50">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-3 bg-cyan-500/10 rounded border border-cyan-500/30 group-hover:bg-cyan-500/20 transition-colors"><i data-lucide="cpu" class="text-cyan-400 w-6 h-6"></i></div>
                            <i data-lucide="external-link" class="text-gray-600 w-4 h-4 group-hover:text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-1">Histoire des Protomates</h3>
                        <p class="text-xs text-cyan-400 uppercase tracking-widest">IA Conscientes & Évolution</p>
                    </div>

                    <div onclick="openFile('especes')" class="glass-panel p-6 cursor-pointer group hover:-translate-y-1 hover:border-indigo-400/50">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-3 bg-indigo-500/10 rounded border border-indigo-500/30 group-hover:bg-indigo-500/20 transition-colors"><i data-lucide="users" class="text-indigo-400 w-6 h-6"></i></div>
                            <i data-lucide="external-link" class="text-gray-600 w-4 h-4 group-hover:text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-1">Les Espèces Pensantes</h3>
                        <p class="text-xs text-indigo-400 uppercase tracking-widest">Biologie & Caractéristiques</p>
                    </div>

                </div>
            </div>
        </div>

        <div id="file-reader" class="absolute inset-0 z-[80] bg-black/95 transform translate-x-full transition-transform duration-500 flex flex-col border-l border-cyan-900/50">
            
            <audio id="audio-player-source"></audio>

            <div class="flex items-center justify-between p-6 border-b border-cyan-900/50 bg-cyan-950/10">
                
                <button onclick="closeFile()" class="text-cyan-400 hover:text-white uppercase text-xs tracking-widest flex items-center gap-2 border border-cyan-500/50 px-4 py-2 rounded hover:bg-cyan-900/50 transition-all">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Fermer le dossier
                </button>

                <div class="flex flex-col xl:flex-row justify-between items-start xl:items-end gap-6">
                    
                    <div class="flex-1 text-right hidden xl:block">
                       </div>

                    <div class="flex flex-col gap-2 w-full xl:w-auto shrink-0">
                        
                        <div class="flex justify-end">
                            <button onclick="toggleDyslexicMode()" class="w-44 border border-cyan-500/30 bg-cyan-900/10 hover:bg-cyan-500/20 text-cyan-400 px-3 py-2 rounded flex items-center justify-center gap-2 transition-all">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                                <span class="text-[10px] uppercase tracking-widest hidden sm:inline">Dyslexie</span>
                            </button>
                        </div>

                        <button id="btn-audio-player" onclick="toggleAudio()" class="w-44 border border-gray-700 bg-gray-900/50 text-gray-500 px-3 py-2 rounded flex items-center justify-center gap-2 cursor-not-allowed opacity-50 transition-all" disabled>
                            <i data-lucide="volume-2" class="w-4 h-4"></i>
                            <span class="text-[10px] uppercase tracking-widest" id="text-audio-btn">Audio (Offline)</span>
                        </button>

                    </div>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-8 pb-32 md:p-16 rich-text" id="reader-scroll-area">
                 <div class="mb-8 border-b border-gray-800 pb-4">
                    <h2 id="reader-title" class="text-3xl font-bold text-white uppercase tracking-wider mb-2">TITRE</h2>
                    <div id="reader-subtitle" class="text-sm text-cyan-400 uppercase tracking-[0.2em]">SOUS-TITRE</div>
                 </div>

                 <div id="reader-content"></div>
            </div>
        </div>

        <footer class="border-t border-cyan-900/30 py-12 text-center bg-black absolute bottom-0 w-full pointer-events-none opacity-50">
            <p class="text-xs text-gray-600 tracking-widest">SYSTEME D'ARCHIVES V.3.0.4 // ORIGIN RP</p>
        </footer>
    </main>


    <div id="data-senescients" class="hidden">
        <p>Les Sénescients (alias : Les Sénes)</p>
        <p>Les Sénescients, une élite influente au sommet de la hiérarchie, sont des membres faisant partie intégrante de la Fédération, gouvernement des stations. Leur autorité incontestée s'étend à tous les aspects de la vie avec laquelle ils dictent les règles et les lois en parfaite harmonie avec les idéologies fédérales. Telles des figures d’autorité extraterrestre, leurs décisions ont un impact monumental sur l'avenir de l'humanité dans l'espace.</p>
        <p>La grande majorité des Sénescients, qui représente environ 0,00002% (200 personnes) des espèces vivantes, sont indifférents aux besoins des autres habitants des stations. Leur mode de vie luxueux contraste fortement avec la réalité difficile et éprouvante du reste de la population.</p>
        <p>Parmi les Sénes, une fraction significative a acquis sa position en contribuant de manière drastique à l'avancement technologique dans l’espace. Ces individus se sont démarqués en tant qu'ingénieurs, scientifiques ou architectes talentueux, apportant des innovations essentielles à la survie et à la prospérité des stations. Leur expertise a été cruciale pour le développement des infrastructures spatiales.</p>
        <p>Au cœur de cette société, trois familles fondatrices se démarquent par leur rôle essentiel dans la création et le développement des stations. Parmi elles : la Lignée Siren, la Lignée Sideria, et la Lignée Prodit sont les trois grandes familles à l’origine de l’évolution humaine après la chute de la Terre.</p>
        
        <h3>Les Grandes Familles</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 my-8">
            <div class="glass-panel p-6 border-t-4 border-cyan-400 flex flex-col hover:-translate-y-1 transition-transform duration-300 ease-out">
                <div class="text-center mb-4">
                     <h4 class="!m-0 !text-base uppercase tracking-widest text-cyan-400">La Grande Lignée Siren</h4>
                     <div class="text-[10px] text-white font-bold uppercase tracking-[0.3em] mt-1">Les Visionnaires</div>
                </div>
                <p class="!text-xs !mb-0 text-gray-400 text-justify">La Lignée Siren, pionnière de cet ambitieux projet, est la fondatrice des premières stations spatiales. Leurs membres, des visionnaires dévoués à l'exploration, ont investi leur ingéniosité pour donner naissance à un développement unique. Actuellement, ils influencent de manière dominante leurs idéologies politiques, guidées par Olympe Siren, membre honorifique qui gouverne d’une main de fer l’entièreté des espèces au sein des stations. Elle tranche les décisions allant parfois contre la Fédération et leur rôle fondateur reste inoubliable.</p>
            </div>
            <div class="glass-panel p-6 border-t-4 border-indigo-500 flex flex-col hover:-translate-y-1 transition-transform duration-300 ease-out">
                <div class="text-center mb-4">
                     <h4 class="!m-0 !text-base uppercase tracking-widest text-indigo-400">La Lignée Sideria</h4>
                     <div class="text-[10px] text-white font-bold uppercase tracking-[0.3em] mt-1">Les Actionnaires</div>
                </div>
                <p class="!text-xs !mb-0 text-gray-400 text-justify">Au milieu de cette hiérarchie trône la Lignée Sideria, une famille influente qui a été un soutien dans le financement et le développement des stations. José et Giuliana Sideria, d'anciens milliardaires espagnols, participent grandement à l'économie et au maintien de la culture. Leur nom est synonyme de richesse et de pouvoir, ils déterminent l'orientation de la société en faisant partie intégrante de la Fédération.</p>
            </div>
            <div class="glass-panel p-6 border-t-4 border-gray-400 flex flex-col hover:-translate-y-1 transition-transform duration-300 ease-out">
                <div class="text-center mb-4">
                     <h4 class="!m-0 !text-base uppercase tracking-widest text-gray-300">La Lignée Prodit</h4>
                     <div class="text-[10px] text-white font-bold uppercase tracking-[0.3em] mt-1">Les Innovateurs</div>
                </div>
                <p class="!text-xs !mb-0 text-gray-400 text-justify">La lignée Prodit se distingue par son rôle en tant qu'innovatrice. Leur contribution en matière de technologie, d'ingénierie et d'architecture ont façonné les stations spatiales modernes. Leurs membres ont permis d'améliorer la vie à bord, garantissant le confort et la sécurité de tous les résidents. C’est la dernière famille qui a rejoint les Sénes. Gabriel Prodit est l’homme qui a instigué la montée en puissance des Sénéscients. Créateur et directeur du P.T.R.G.E, il a su se démarquer et prouver qu’il était indispensable dans le bon fonctionnement de la vie à bord des stations.</p>
            </div>
        </div>

        <h3>Privilèges et Mode de Vie</h3>
        <p>Les membres de cette classe privilégiée, détenant le statut social le plus élevé à bord des stations, se composent d'une poignée de cosmopolites et d'invités extrêmement riches. Leurs privilèges sont presque illimités :</p>
        <ul>
            <li><strong>Accès illimité aux raretés :</strong> Ils bénéficient d'une variété presque illimitée de calories quotidiennes et de produits rares, y compris des alcools, des œuvres, des bijoux...</li>
            <li><strong>Liberté de mouvement :</strong> Ils parcourent les stations sans aucune restriction, explorant chaque section à leur guise à bord de véhicules dernier cri.</li>
            <li><strong>Logements luxueux :</strong> Leurs espaces de vie spacieux sont équipés d’un luxe inimaginable et d’autant d’espace que désiré.</li>
            <li><strong>Animaux :</strong> Certains d'entre eux apportent leurs animaux de compagnie pour combler leur solitude et montrer leur supériorité.</li>
            <li><strong>Escortes personnelles :</strong> Beaucoup d'entre eux sont accompagnés de gardes du corps armés pour garantir leur sécurité.</li>
            <li><strong>Produits d'origine terrestre :</strong> Ils ont accès à des produits naturels rares et précieux issus de notre défunte planète.</li>
        </ul>

        <div class="mt-12 border-t border-purple-900/30 pt-8">
            <div onclick="openFile('edith')" class="glass-panel p-6 cursor-pointer group hover:bg-purple-900/10 transition-all flex items-center gap-6 border border-purple-500/30">
                <div class="bg-purple-500/20 p-4 rounded-full border border-purple-500/50 group-hover:scale-110 transition-transform">
                    <i data-lucide="user-circle" class="w-8 h-8 text-purple-400"></i>
                </div>
                <div>
                    <div class="text-xs text-purple-500 uppercase tracking-widest mb-1">Dossier Prioritaire</div>
                    <h4 class="text-xl font-bold text-white group-hover:text-purple-300 uppercase">Edith Gusterfeld</h4>
                    <p class="text-xs text-gray-500 mt-1">Oratrice Suprême</p>
                </div>
                <i data-lucide="arrow-right" class="w-5 h-5 text-gray-600 ml-auto group-hover:text-purple-400 group-hover:translate-x-2 transition-all"></i>
            </div>
        </div>
    </div>
    
    <div id="data-edith" class="hidden">
        <button onclick="openFile('senescients')" class="mb-8 flex items-center gap-2 text-xs text-gray-500 hover:text-cyan-400 transition-colors uppercase tracking-widest group">
            <i data-lucide="corner-up-left" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i>
            Retour au dossier Sénéscients
        </button>

        <div class="flex items-center gap-4 mb-8">
             <div class="bg-purple-500/20 p-4 rounded-full border border-purple-500/50">
                <i data-lucide="user" class="w-12 h-12 text-purple-400"></i>
            </div>
            <div>
                <h3 class="!mt-0 !mb-1 !border-none text-3xl !text-white">EDITH GUSTERFELD</h3>
                <p class="!mb-0 text-purple-400 uppercase tracking-widest text-xs font-mono">Matricule: SEN-001-EG // Statut: ACTIF</p>
            </div>
        </div>

        <div class="flex flex-col xl:flex-row gap-8">
            <div class="xl:w-1/3 flex-shrink-0 relative group">
                <div class="relative rounded-lg overflow-hidden border border-purple-500/30 shadow-[0_0_30px_rgba(168,85,247,0.1)]">
                    <img src="assets/images/edith.jpeg" alt="Edith Gusterfeld" class="w-full h-auto object-cover transition-all duration-700">
                    <div class="absolute inset-0 bg-[linear-gradient(transparent_50%,rgba(0,0,0,0.5)_51%)] bg-[length:100%_4px] opacity-20 pointer-events-none"></div>
                </div>
                <div class="text-center text-[10px] text-purple-400 uppercase tracking-widest mt-2 font-mono">Image Biomuée - Dernière mise à jour: 2193.10.24</div>
            </div>

            <div class="flex-1">
                <h4 class="!mt-0 text-purple-300 uppercase">L'Oratrice Suprême</h4>
                <p>Au sein de la classe des Sénescients, Edith Gusterfeld occupe une place éminente en tant que porte-parole et gérante des comités. Elle joue un rôle essentiel dans la gouvernance des stations spatiales, et sa personnalité incarne l'élégance et la diplomatie caractéristique de cette élite.</p>
                <p>Edith Gusterfeld a parcouru un chemin exceptionnel pour atteindre son poste actuel. Elle est issue d'une famille qui n'était pas initialement associée à la richesse ou au pouvoir. Cependant, sa passion précoce pour la diplomatie et son engagement envers les valeurs de la Siren Corporation l'ont conduite à gravir les échelons au fil des années. Sa perspicacité et sa capacité à créer des alliances ont fait d'elle un choix naturel pour représenter les Sénescients.</p>
                
                <h4 class="text-purple-300 uppercase">Rôle & Responsabilités</h4>
                <p>En tant que porte-parole, Edith Gusterfeld est la principale interface entre la classe des Sénes et le reste de la population. Son rôle consiste à traduire les préoccupations élitistes avec des actions concrètes tout en veillant à ce que leurs intérêts soient protégés. Arbitre et médiatrice, elle équilibre les demandes souvent exigeantes avec les ressources des stations. Sa parole résonne comme un écho du pouvoir absolu détenu par le gouvernement, et ses décisions ont un impact significatif.</p>
                
                <h4 class="text-purple-300 uppercase">Privilèges Contractuels</h4>
                <p>Grâce à sa position au sein des Sénes, elle bénéficie des privilèges allant de paire avec son statut. Contractuellement, sa condition lui octroie l’accès permanent au réenveloppement en échange de ses services, aussi longtemps qu’elle sera en mesure d’honorer son contrat.</p>
            </div>
        </div>
    </div>

    <div id="data-magnats" class="hidden">
        <p>Dans la haute société, une classe sociale occupe une position de pouvoir économique et politique : les Magnats. Composée de seulement 0,00095% (9,5k personnes environ) de la population, ce sont des individus privilégiés qui possèdent des biens immobiliers, des actions dans des entreprises et occupent souvent des postes de direction tels que PDG, directeurs d'hôpitaux et avocats renommés.</p>
        
        <h3>Richesse & Influence</h3>
        <p>Les Magnats possèdent une richesse considérable grâce à leurs investissements dans l'infrastructure et sur le marché. Leur fortune leur permet d'accéder à des opportunités privilégiées, et ils visent généralement à s'enrichir toujours plus. Pour atteindre leurs objectifs, certains magnats n'hésitent pas à s'engager dans des marchés financiers risqués ou à conclure des accords avec des groupes illégaux tels que la pègre. Leur désir d'accumuler le pouvoir et la richesse peut parfois les amener à rivaliser avec les Sénescients.</p>
        
        <h3>Relations avec la Fédération</h3>
        <p>Les magnats cherchent souvent à établir des liens étroits avec la Fédération pour accroître leur influence afin de passer en tant que gouverneur de station. Ils utilisent différentes stratégies, telles que des mariages arrangés avec des membres de lignées puissantes ou des alliances politiques. Grâce à ces connexions, les magnats peuvent accéder à des cercles de pouvoir exclusifs et ont une influence décisionnaire. Cependant, ces alliances peuvent également créer des tensions avec la Fédération. En effet, les magnats cherchent parfois à égaler voire à surpasser son autorité en testant leurs limites.</p>

        <h3>Gouvernance & Éducation</h3>
        <p>Bien souvent, les Magnats occupent un poste de gouverneur au sein des stations et les dirigent à leur guise. Ils y intègrent des lois ou des règles qu’ils souhaitent faire respecter tant que cela n’entre pas en conflit avec celles de la Fédération.</p>
        <p>Ils investissent massivement dans l'éducation de leurs enfants, leur offrant ainsi des opportunités avancées pour atteindre leurs ambitions professionnelles et personnelles. Les enfants des magnats suivent des cursus scolaires prestigieux, qui leur permettent d'accéder à des carrières de haut niveau en médecine, en droit, en gestion d'entreprise, et dans d'autres domaines convoités. Cette éducation poussée est un facteur clé dans le maintien de la position sociale des magnats, et elle est souvent transmise de génération en génération.</p>

        <div class="bg-indigo-900/20 border-l-4 border-indigo-500 p-4 my-6">
            <h4 class="!mt-0 !mb-2 text-indigo-400 text-sm uppercase tracking-widest">Privilèges de Classe</h4>
            <p class="!mb-0 text-xs text-gray-400">Les membres de cette classe profitent des mêmes avantages luxueux que les Sénéscients (Accès aux raretés, liberté de mouvement, logements spacieux, etc.).</p>
        </div>

        <div class="mt-12 border-t border-indigo-900/30 pt-8">
            <div onclick="openFile('venisha')" class="glass-panel p-6 cursor-pointer group hover:bg-indigo-900/10 transition-all flex items-center gap-6 border border-indigo-500/30">
                <div class="bg-indigo-500/20 p-4 rounded-full border border-indigo-500/50 group-hover:scale-110 transition-transform">
                    <i data-lucide="user-check" class="w-8 h-8 text-indigo-400"></i>
                </div>
                <div>
                    <div class="text-xs text-indigo-500 uppercase tracking-widest mb-1">Dossier Prioritaire</div>
                    <h4 class="text-xl font-bold text-white group-hover:text-indigo-300 uppercase">Venisha Brown</h4>
                    <p class="text-xs text-gray-500 mt-1">La Baronne de la Haute</p>
                </div>
                <i data-lucide="arrow-right" class="w-5 h-5 text-gray-600 ml-auto group-hover:text-indigo-400 group-hover:translate-x-2 transition-all"></i>
            </div>
        </div>
    </div>

    <div id="data-venisha" class="hidden">
        
        <button onclick="openFile('magnats')" class="mb-8 flex items-center gap-2 text-xs text-gray-500 hover:text-cyan-400 transition-colors uppercase tracking-widest group">
            <i data-lucide="corner-up-left" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i>
            Retour au dossier Magnats
        </button>

        <div class="flex items-center gap-4 mb-8">
             <div class="bg-indigo-500/20 p-4 rounded-full border border-indigo-500/50">
                <i data-lucide="user" class="w-12 h-12 text-indigo-400"></i>
            </div>
            <div>
                <h3 class="!mt-0 !mb-1 !border-none text-3xl !text-white">VENISHA BROWN</h3>
                <p class="!mb-0 text-indigo-400 uppercase tracking-widest text-xs font-mono">Matricule: MAG-774-VB // Statut: ACTIF</p>
            </div>
        </div>

        <div class="flex flex-col xl:flex-row gap-8">
            
            <div class="xl:w-1/3 flex-shrink-0 relative group">
                <div class="relative rounded-lg overflow-hidden border border-indigo-500/30 shadow-[0_0_30px_rgba(99,102,241,0.1)]">
                    <img src="assets/images/venisha.jpeg" alt="Venisha Brown" class="w-full h-auto object-cover transition-all duration-700">
                    <div class="absolute inset-0 bg-[linear-gradient(transparent_50%,rgba(0,0,0,0.5)_51%)] bg-[length:100%_4px] opacity-20 pointer-events-none"></div>
                </div>
                <div class="text-center text-[10px] text-indigo-400 uppercase tracking-widest mt-2 font-mono">Image Biomuée - Dernière mise à jour: 2193.11.02</div>
            </div>

            <div class="flex-1">
                <h4 class="!mt-0 text-indigo-300 uppercase">Portrait d'une Élite</h4>
                <p>Dans la hiérarchie des magnats, une femme se démarque par sa position de pouvoir et son comportement condescendant envers les autres classes sociales : Venisha Brown. En tant que figure de proue de l'élite magnat, elle incarne l'arrogance et l'indifférence envers ceux qu'elle considère comme inférieurs.</p>
                <p>Venisha a acquis sa richesse et son influence grâce à son héritage familial et à des investissements judicieux dans divers domaines économiques. Elle possède de vastes biens immobiliers, des actions dans des entreprises prospères et des connexions étendues au sein des cercles de pouvoir. Sa position de PDG d'une entreprise de renom lui confère un pouvoir considérable et une autorité incontestée.</p>
                
                <h4 class="text-indigo-300 uppercase">Psychologie & Méthodes</h4>
                <p>Malheureusement, Venisha utilise son pouvoir et sa richesse pour distancer les autres classes sociales. Elle se comporte avec mépris envers les Infralaborants et les Segmentaires, considérant leurs besoins et leurs aspirations comme insignifiants. Elle n'hésite pas à passer devant les autres, à exiger des privilèges spéciaux et à utiliser son influence pour écraser ceux qui se mettent en travers de son chemin.</p>
                <p>Venisha représente la face sombre de cette classe sociale, où l'avidité et l'égoïsme prévalent. Son objectif principal est de consolider sa propre richesse et son pouvoir, peu importe les conséquences pour les autres. Sa vision étroite et matérialiste l'empêche de voir la valeur et la dignité de tous les individus, quel que soit leur statut social.</p>

                <h4 class="text-indigo-300 uppercase">Nuance Sociale</h4>
                <p>Cependant, il est important de noter que Venisha ne représente pas l'ensemble de cette catégorie. Il existe quelques membres de cette classe qui sont conscients de leurs privilèges et qui utilisent leur position pour promouvoir la justice sociale, l'égalité des chances et le bien-être collectif. Ces individus cherchent à créer un monde où les différences de classe ne dictent pas les opportunités et où tout le monde peut prospérer.</p>
                
                <div class="bg-red-900/10 border border-red-500/20 p-4 mt-4">
                     <p class="!mb-0 text-xs text-red-400 italic">"En conclusion, Venisha incarne la bourgeoisie méprisante et égocentrique. Sa façon de passer devant les autres et d'ignorer leurs besoins témoigne de l'inégalité et de l'injustice qui persiste dans la société." - Rapport Psychologique, Dr. K. Vance.</p>
                </div>
            </div>
        </div>
    </div>
    
    <div id="data-segmentaires" class="hidden">
        <p>Les Segmentaires constituent la majorité de la population, représentant environ 69,85% (698,5M de personnes). Ils forment le corps vivant des stations, la force motrice qui maintient les infrastructures opérationnelles.</p>
        
        <h3>Aspirations & Réalité</h3>
        <p>Les Segmentaires aspirent à vivre décemment et à avoir un niveau de vie convenable. La plupart d'entre eux sont locataires, et rares sont ceux qui parviennent à devenir propriétaires de leur logement, les prix de l'immobilier étant souvent contrôlés par les Magnats. Ils travaillent principalement en tant que salariés dans divers secteurs (administration, gestion, santé, commerce, vente...), bien que certains parviennent à gravir les échelons et accéder à des postes plus élevés, tels que médecins ou avocats, grâce à leur persévérance et à des accès parfois facilités à la connaissance.</p>

        <h3>Le Mur de l'Éducation</h3>
        <p>Cette classe est confrontée à des défis financiers majeurs lorsqu'il s'agit de poursuivre des études supérieures. Le coût astronomique des apprentissages universitaires et des programmes de master les oblige souvent à interrompre leur parcours académique avant d'atteindre le niveau souhaité. Cela crée un plafond de verre, limitant leurs opportunités professionnelles et leur potentiel d'avancement au sein de la Fédération.</p>

        <h3>Stigmates Sociaux</h3>
        <p>Souvent victimes de stéréotypes négatifs et de préjugés de la part de la haute société, ils sont perçus à tort comme des individus sans ambition, des travailleurs ordinaires dépourvus de talents innés. Ces jugements réducteurs ont un impact profond sur l'estime de soi des Segmentaires et peuvent entraver leur ascension sociale, créant un fossé psychologique entre les classes.</p>

        <h3>La Force du Nombre</h3>
        <p>Malgré les obstacles, ils nourrissent l'espoir d'une vie meilleure. Ils travaillent dur pour subvenir à leurs besoins et espèrent pouvoir offrir à leur famille une stabilité financière et un avenir prometteur. La volonté de progresser est un moteur puissant pour cette classe qui les pousse à travailler deux fois plus que quiconque à bord des stations.</p>

        <div class="mt-12 border-t border-blue-900/30 pt-8">
            <div onclick="openFile('mieczyslaw')" class="glass-panel p-6 cursor-pointer group hover:bg-blue-900/10 transition-all flex items-center gap-6 border border-blue-500/30">
                <div class="bg-blue-500/20 p-4 rounded-full border border-blue-500/50 group-hover:scale-110 transition-transform">
                    <i data-lucide="heart-handshake" class="w-8 h-8 text-blue-400"></i>
                </div>
                <div>
                    <div class="text-xs text-blue-500 uppercase tracking-widest mb-1">Dossier Prioritaire</div>
                    <h4 class="text-xl font-bold text-white group-hover:text-blue-300 uppercase">Nikos & Alexeï Mieczyslaw</h4>
                    <p class="text-xs text-gray-500 mt-1">Les Samaritains Revendicateurs</p>
                </div>
                <i data-lucide="arrow-right" class="w-5 h-5 text-gray-600 ml-auto group-hover:text-blue-400 group-hover:translate-x-2 transition-all"></i>
            </div>
        </div>
    </div>
    
    <div id="data-mieczyslaw" class="hidden">
        
        <button onclick="openFile('segmentaires')" class="mb-8 flex items-center gap-2 text-xs text-gray-500 hover:text-cyan-400 transition-colors uppercase tracking-widest group">
            <i data-lucide="corner-up-left" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i>
            Retour au dossier Segmentaires
        </button>

        <div class="flex items-center gap-4 mb-8">
             <div class="bg-blue-500/20 p-4 rounded-full border border-blue-500/50">
                <i data-lucide="users" class="w-12 h-12 text-blue-400"></i>
            </div>
            <div>
                <h3 class="!mt-0 !mb-1 !border-none text-3xl !text-white">NIKOS & ALEXEÏ MIECZYSLAW</h3>
                <p class="!mb-0 text-blue-400 uppercase tracking-widest text-xs font-mono">Matricule: SEG-402-NM/AM // Statut: ACTIF // Rôle: Médecins & Chercheurs</p>
            </div>
        </div>

        <div>
            <h4 class="!mt-0 text-blue-300 uppercase">Un Combat Commun</h4>
            <p>Nikos (48 ans) et Alexeï (39 ans) se hissent parmi les Segmentaires par leur dévouement. Tous deux médecins passionnés, ils sont animés par une volonté farouche d'aider les plus faibles, y compris les Infralaborants et les Rebuts, souvent oubliés par le système de santé fédéral.</p>
            <p>Malgré leurs démarches pour faire changer les choses en pourparlers, le couple se heurte constamment aux obstacles bureaucratiques. Les intérêts économiques et politiques prévalent souvent sur les préoccupations humanitaires. Toutefois, cette déception ne leur fait pas perdre espoir et ils continuent à chercher des moyens créatifs pour faire entendre leur voix.</p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mt-8">
            
            <div class="glass-panel p-6 border-t-4 border-blue-600">
                <div class="relative rounded-lg overflow-hidden border border-blue-500/30 shadow-[0_0_20px_rgba(59,130,246,0.1)] mb-4">
                    <img src="assets/images/nikos.jpeg" alt="Nikos Mieczyslaw" class="w-full h-auto"> 
                    <div class="absolute inset-0 bg-[linear-gradient(transparent_50%,rgba(0,0,0,0.5)_51%)] bg-[length:100%_4px] opacity-20 pointer-events-none"></div>
                </div>
                <h4 class="text-blue-300 uppercase text-lg mb-2 !mt-0">Nikos Mieczyslaw</h4>
                <p class="text-xs text-gray-400 mb-4 italic">"Soigner là où personne ne veut aller."</p>
                <p class="!text-sm">Médecin expérimenté spécialisé dans les soins primaires et la médecine communautaire. Il travaille sans relâche pour apporter des soins médicaux accessibles à tous. Il sillonne les régions marginalisées de l'espace et les niveaux inférieurs, prodiguant des traitements à ceux qui n'ont pas les moyens de se permettre des soins réguliers.</p>
            </div>

            <div class="glass-panel p-6 border-t-4 border-blue-400">
                <div class="relative rounded-lg overflow-hidden border border-blue-500/30 shadow-[0_0_20px_rgba(59,130,246,0.1)] mb-4">
                     <img src="assets/images/alexei.jpeg" alt="Alexeï Mieczyslaw" class="w-full h-auto">
                    <div class="absolute inset-0 bg-[linear-gradient(transparent_50%,rgba(0,0,0,0.5)_51%)] bg-[length:100%_4px] opacity-20 pointer-events-none"></div>
                </div>
                <h4 class="text-blue-300 uppercase text-lg mb-2 !mt-0">Alexeï Mieczyslaw</h4>
                <p class="text-xs text-gray-400 mb-4 italic">"La prévention est la seule véritable cure."</p>
                <p class="!text-sm">Chercheur engagé dans la recherche médicale, Alexeï consacre ses efforts à comprendre et à résoudre les problèmes de santé spécifiques aux classes démunies. Son expertise est précieuse pour développer des traitements à bas coût et des programmes de prévention. Il tente inlassablement de sensibiliser la Fédération pour des politiques de santé équitables.</p>
            </div>

        </div>
    </div>

    <div id="data-infralaborants" class="hidden">
        <p>Les Infralaborants (alias : les laborieux) constituent une part massive de la population, représentant environ 30% (300M de personnes). Ils forment la main-d'œuvre brute, indispensable mais souvent invisibilisée, sur laquelle repose la maintenance physique des stations.</p>
        
        <h3>Survie Quotidienne</h3>
        <p>Leur quotidien est marqué par des difficultés constantes. Principalement composés de locataires vivant dans des conditions précaires, ils cherchent à survivre avec des revenus limités, confrontés à des choix cornéliens pour subvenir à leurs besoins primaires. Le rythme de vie effréné auquel ils sont soumis met quotidiennement en danger leur santé mentale et physique.</p>

        <h3>Labeur & Danger</h3>
        <p>Les laborieux occupent les emplois du bas de l'échelle sociale. Ils sont responsables des tâches les plus ardues et dangereuses : entretien mécanique lourd, minage d'astéroïdes en conditions instables, et nettoyage de zones à risques biologiques ou radioactifs. Ces professions exigent un engagement physique total et exposent les travailleurs à des taux de mortalité et d'accidents bien supérieurs à la moyenne.</p>

        <h3>L'Impasse Éducative</h3>
        <p>La majorité des membres de cette classe a un accès extrêmement limité à l'éducation. Les contraintes financières rendent les études inaccessibles, créant un cercle vicieux de pauvreté. Bien que certains individus parviennent à développer des compétences techniques rares sur le tas, ils font face à des préjugés systémiques et à une discrimination qui les cantonnent aux postes d'exécution, bloquant toute ascension sociale.</p>

        <p>Leurs efforts pour maintenir les stations en état de marche sont souvent sous-estimés, voire méprisés par les élites qui les stigmatisent comme une masse "brute" et "remplaçable".</p>

        <div class="mt-12 border-t border-orange-900/30 pt-8">
            <div onclick="openFile('janine')" class="glass-panel p-6 cursor-pointer group hover:bg-orange-900/10 transition-all flex items-center gap-6 border border-orange-500/30">
                <div class="bg-orange-500/20 p-4 rounded-full border border-orange-500/50 group-hover:scale-110 transition-transform">
                    <i data-lucide="megaphone" class="w-8 h-8 text-orange-400"></i>
                </div>
                <div>
                    <div class="text-xs text-orange-500 uppercase tracking-widest mb-1">Dossier Prioritaire</div>
                    <h4 class="text-xl font-bold text-white group-hover:text-orange-300 uppercase">Janine Forest</h4>
                    <p class="text-xs text-gray-500 mt-1">L'Ouvrière de l'Espoir</p>
                </div>
                <i data-lucide="arrow-right" class="w-5 h-5 text-gray-600 ml-auto group-hover:text-orange-400 group-hover:translate-x-2 transition-all"></i>
            </div>
        </div>
    </div>
    
    <div id="data-janine" class="hidden">
        
        <button onclick="openFile('infralaborants')" class="mb-8 flex items-center gap-2 text-xs text-gray-500 hover:text-orange-400 transition-colors uppercase tracking-widest group">
            <i data-lucide="corner-up-left" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i>
            Retour au dossier Infralaborants
        </button>

        <div class="flex items-center gap-4 mb-8">
             <div class="bg-orange-500/20 p-4 rounded-full border border-orange-500/50">
                <i data-lucide="hard-hat" class="w-12 h-12 text-orange-400"></i>
            </div>
            <div>
                <h3 class="!mt-0 !mb-1 !border-none text-3xl !text-white">JANINE FOREST</h3>
                <p class="!mb-0 text-orange-400 uppercase tracking-widest text-xs font-mono">Matricule: INF-892-JF // Statut: SURVEILLANCE // Rôle: Porte-parole Syndicale</p>
            </div>
        </div>

        <div class="flex flex-col xl:flex-row gap-8">
            
            <div class="xl:w-1/3 flex-shrink-0 relative group">
                <div class="relative rounded-lg overflow-hidden border border-orange-500/30 shadow-[0_0_30px_rgba(249,115,22,0.1)]">
                    <img src="assets/images/janine.jpeg" alt="Janine Forest" class="w-full h-auto">
                    
                    <div class="absolute inset-0 bg-[linear-gradient(transparent_50%,rgba(0,0,0,0.5)_51%)] bg-[length:100%_4px] opacity-20 pointer-events-none"></div>
                    <div class="absolute top-0 left-0 w-4 h-4 border-t-2 border-l-2 border-orange-400"></div>
                    <div class="absolute top-0 right-0 w-4 h-4 border-t-2 border-r-2 border-orange-400"></div>
                    <div class="absolute bottom-0 left-0 w-4 h-4 border-b-2 border-l-2 border-orange-400"></div>
                    <div class="absolute bottom-0 right-0 w-4 h-4 border-b-2 border-r-2 border-orange-400"></div>
                </div>
                <div class="text-center text-[10px] text-orange-400 uppercase tracking-widest mt-2 font-mono">Image Biomuée - Dernière mise à jour: 2193.12.05</div>
            </div>

            <div class="flex-1">
                <h4 class="!mt-0 text-orange-300 uppercase">Une Figure de Résistance</h4>
                <p>Dans cet univers où les Infralaborants sont broyés par le système, Janine Forest se distingue par une dévotion et un altruisme rares. Femme au fort caractère, elle a transformé sa compréhension intime des souffrances de sa communauté en une arme politique.</p>
                <p>Déterminée à faire entendre sa voix auprès d'un gouvernement souvent sourd, elle a gagné en influence grâce à son esprit combatif, devenant une figure respectée, presque maternelle, pour sa classe sociale.</p>
                
                <h4 class="text-orange-300 uppercase">Méthodes & Actions</h4>
                <p>Janine utilise son intelligence aiguisée et une force de persuasion redoutable pour plaider en faveur de réformes concrètes : salaires équitables, sécurité au travail, et accès aux soins médicaux de base. Elle ne se contente pas de discours ; elle agit.</p>
                <p>En dehors des cercles politiques, elle s'implique activement dans des initiatives communautaires. En collaboration avec <strong>Zachary Mapoudish</strong>, elle organise des réseaux de soutien clandestins et des "meet-ups" pour les travailleurs précaires, créant des ponts de solidarité inédits entre les Infralaborants et la classe des Rebuts.</p>
                
                <div class="bg-orange-900/10 border border-orange-500/20 p-4 mt-6">
                    <h4 class="!mt-0 !mb-2 text-orange-400 text-sm uppercase tracking-widest">Note des Services de Renseignement</h4>
                    <p class="!mb-0 text-xs text-gray-400">"Le sujet Forest représente un risque modéré de déstabilisation. Son rôle d'intermédiaire avec le gouvernement permet de canaliser la colère ouvrière, mais sa popularité croissante pourrait devenir une menace si elle venait à appeler à la grève générale."</p>
                </div>
            </div>
        </div>
    </div>

    <div id="data-rebuts" class="hidden">
        <div class="border-l-4 border-red-500 pl-4 mb-6 bg-red-900/10 p-4">
            <p class="!mb-0 text-red-400 font-bold text-sm uppercase tracking-widest flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-4 h-4"></i> Attention : Zone de non-droit
            </p>
        </div>

        <p>Il existe une minorité délaissée et défavorisée appelée les "Rebuts". Constituant environ 0,15% (1,5M de personnes) de la population, ils vivent dans des conditions extrêmement précaires, souvent relégués aux canalisations et aux zones les plus déshéritées des colonies spatiales (secteurs non-pressurisés ou instables).</p>
        
        <h3>Statut Illégal</h3>
        <p>Cette classe sociale est caractérisée par un manque total d'accès aux ressources essentielles et une absence de reconnaissance officielle. Être "Rebut" est un statut qui s’acquiert de deux façons : par la naissance ou par le rejet.</p>
        <ul class="!text-red-200/70">
            <li><strong>De naissance :</strong> L'enfant est considéré illégal aux yeux de la Fédération puisqu’il n’est pas recensé par le système biométrique. Il n'existe pas administrativement.</li>
            <li><strong>Par rejet :</strong> Un citoyen déchu, banni pour crimes ou dettes, perdant sa citoyenneté.</li>
        </ul>

        <h3>Survie & Criminalité</h3>
        <p>Les Rebuts vivent dans des conditions de survie difficiles, confrontés à la malnutrition et une criminalité endémique. Leur mode de vie est marqué par des luttes constantes pour des ressources vitales (eau potable, air filtré) et ils sont souvent contraints de recourir à des moyens illégaux pour subvenir à leurs besoins.</p>

        <h3>Le Cercle Vicieux</h3>
        <p>L'idéologie de la violence est souvent présente, perçue comme une réponse nécessaire à l'oppression sociale. De plus, des lois strictes interdisent la mendicité et la présence dans les zones non autorisées, créant une impasse juridique : ils ne peuvent ni travailler légalement, ni survivre illégalement sans risquer de lourdes peines ou la purge.</p>

        <h3>La Menace du Marché Noir</h3>
        <p>Face à cette réalité, certains Rebuts se résignent à être réduits en esclavage ou troqués sur le marché noir. Attirés par des promesses vaines d'une vie meilleure, ils finissent souvent victimes de trafic humain, de travail forcé dans les réacteurs, ou de proxénétisme dans les bas-fonds.</p>

        <div class="mt-12 border-t border-red-900/30 pt-8">
            <div onclick="openFile('zachary')" class="glass-panel p-6 cursor-pointer group hover:bg-red-900/10 transition-all flex items-center gap-6 border border-red-500/30">
                <div class="bg-red-500/20 p-4 rounded-full border border-red-500/50 group-hover:scale-110 transition-transform">
                    <i data-lucide="shield-alert" class="w-8 h-8 text-red-400"></i>
                </div>
                <div>
                    <div class="text-xs text-red-500 uppercase tracking-widest mb-1">Dossier Prioritaire</div>
                    <h4 class="text-xl font-bold text-white group-hover:text-red-300 uppercase">Zachary Mapoudish</h4>
                    <p class="text-xs text-gray-500 mt-1">Le Précurseur des Oubliés</p>
                </div>
                <i data-lucide="arrow-right" class="w-5 h-5 text-gray-600 ml-auto group-hover:text-red-400 group-hover:translate-x-2 transition-all"></i>
            </div>
        </div>
    </div>

    <div id="data-zachary" class="hidden">
        
        <button onclick="openFile('rebuts')" class="mb-8 flex items-center gap-2 text-xs text-gray-500 hover:text-red-400 transition-colors uppercase tracking-widest group">
            <i data-lucide="corner-up-left" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i>
            Retour au dossier Rebuts
        </button>

        <div class="flex items-center gap-4 mb-8">
             <div class="bg-red-500/20 p-4 rounded-full border border-red-500/50">
                <i data-lucide="ghost" class="w-12 h-12 text-red-400"></i>
            </div>
            <div>
                <h3 class="!mt-0 !mb-1 !border-none text-3xl !text-white">ZACHARY MAPOUDISH</h3>
                <p class="!mb-0 text-red-400 uppercase tracking-widest text-xs font-mono">Matricule: N/A (NON-RECENSÉ) // Statut: RECHERCHÉ // Rôle: Leader Spirituel</p>
            </div>
        </div>

        <div class="flex flex-col xl:flex-row gap-8">
            
            <div class="xl:w-1/3 flex-shrink-0 relative group">
                <div class="relative rounded-lg overflow-hidden border border-red-500/30 shadow-[0_0_30px_rgba(239,68,68,0.1)]">
                    <img src="assets/images/zachary.jpeg" alt="Zachary Mapoudish" class="w-full h-auto grayscale hover:grayscale-0 transition-all duration-500">
                    <div class="absolute inset-0 bg-[linear-gradient(transparent_50%,rgba(0,0,0,0.5)_51%)] bg-[length:100%_4px] opacity-20 pointer-events-none"></div>
                    
                    <div class="absolute top-0 left-0 w-4 h-4 border-t-2 border-l-2 border-red-500"></div>
                    <div class="absolute top-0 right-0 w-4 h-4 border-t-2 border-r-2 border-red-500"></div>
                    <div class="absolute bottom-0 left-0 w-4 h-4 border-b-2 border-l-2 border-red-500"></div>
                    <div class="absolute bottom-0 right-0 w-4 h-4 border-b-2 border-r-2 border-red-500"></div>
                </div>
                <div class="text-center text-[10px] text-red-500 uppercase tracking-widest mt-2 font-mono animate-pulse"> Signalement Visuel Non-Confirmé</div>
            </div>

            <div class="flex-1">
                <h4 class="!mt-0 text-red-400 uppercase">Né dans l'Obscurité</h4>
                <p>Zachary est un rebut de naissance, venu au monde dans les profondeurs des canalisations où s'entassent les plus démunis. Ayant grandi dans des conditions de vie extrêmes, il a transformé sa lutte pour la survie en un combat moral. Contrairement à beaucoup qui cèdent à la violence, il a choisi la voie pacifiste pour représenter les rejetés de la société.</p>
                
                <h4 class="text-red-400 uppercase">Philosophie & Actions</h4>
                <p>Il croit fermement en la dignité de chaque être humain, indépendamment de son statut légal. Armé d'une détermination inébranlable, Zachary parcourt les bas-fonds pour sensibiliser les foules et apaiser les tensions, prônant l'entraide plutôt que le vol.</p>
                <p>Pourtant, il reste confronté à un mur : la Fédération. Plutôt que de soutenir ses efforts de pacification, les autorités ont choisi d'ignorer ses revendications, voire de le traquer comme un agitateur politique, rendant sa tâche périlleuse.</p>
                
                <h4 class="text-red-400 uppercase">Un Symbole d'Espoir</h4>
                <p>En dépit de cette ignorance institutionnelle, il cherche inlassablement à établir des ponts entre les classes. Zachary est devenu un symbole d'espoir, un "messie des égouts". Fervent croyant en l'évolution sociale, il répète souvent que la société peut encore changer. Leader né, il est la seule voix audible de ceux que l'on a fait taire.</p>
            </div>
        </div>
    </div>

        <div id="data-naissance" class="hidden">
        
        <div class="flex justify-end mb-8">
            <button onclick="toggleReportMode()" id="btn-report-mode" class="border border-cyan-500/50 bg-cyan-900/20 hover:bg-cyan-500/30 text-white px-3 py-2 rounded flex items-center gap-2 transition-all shadow-[0_0_15px_rgba(34,211,238,0.2)]">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                <span class="text-[10px] uppercase tracking-widest font-bold">Lire le Rapport</span>
            </button>
        </div>

        <div id="lore-content-area">
            
            <div id="mode-summary" class="fade-in-view">
                
                <div class="border-l-2 border-cyan-500 pl-4 mb-8">
                    <p class="text-sm text-gray-300 italic">"Cet environnement exigeant s'est engagé à redéfinir les paramètres de la natalité, en fusionnant la technologie de pointe avec la difficulté parentale."</p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-12">
                    <a href="#visu-astropole" class="text-[10px] uppercase tracking-widest p-2 border border-cyan-500/30 text-center hover:bg-cyan-500/20 hover:text-white transition-colors text-cyan-400">Astropoles</a>
                    <a href="#visu-megastropole" class="text-[10px] uppercase tracking-widest p-2 border border-purple-500/30 text-center hover:bg-purple-500/20 hover:text-white transition-colors text-purple-400">Mégastropoles</a>
                    <a href="#visu-penitentiaire" class="text-[10px] uppercase tracking-widest p-2 border border-red-500/30 text-center hover:bg-red-500/20 hover:text-white transition-colors text-red-400">Pénitentiaire</a>
                    <a href="#visu-orphelins" class="text-[10px] uppercase tracking-widest p-2 border border-yellow-500/30 text-center hover:bg-yellow-500/20 hover:text-white transition-colors text-yellow-400">Orphelins</a>
                </div>

                <div id="visu-astropole" class="mb-16">
                    <h3 class="text-cyan-400 border-b border-cyan-500/30 pb-2 mb-4 flex items-center gap-2">
                        <i data-lucide="building-2" class="w-5 h-5"></i> Station-Type Astropole
                    </h3>
                    <p class="text-sm mb-4">Centres de vie et de reproduction, ces stations abritent une ville florissante dont la population varie selon la prospérité.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="glass-panel p-4 border-l-4 border-cyan-500">
                            <h4 class="!mt-0 text-cyan-300 text-sm uppercase tracking-widest">Règle de Natalité</h4>
                            <p class="!mb-0 text-xs">Chaque couple est autorisé à avoir <strong class="text-white">un enfant, tous les 10 ans au minimum</strong>. Tout enfant illégal et non désiré subit une IVG obligatoire (Biorizon Health).</p>
                        </div>
                        <div class="glass-panel p-4 border-l-4 border-red-500">
                            <h4 class="!mt-0 text-red-400 text-sm uppercase tracking-widest">Le Choix Radical</h4>
                            <p class="!mb-0 text-xs">En cas de grossesse multiple ou illégale, un choix s'impose :</p>
                            <ul class="list-disc pl-4 mt-2 text-[10px] text-gray-400 space-y-1">
                                <li>L'enfant "en trop" ou "illégal" est tué.</li>
                                <li>OU un parent se sacrifie (Condition : <strong>+40 ans et mauvaise santé</strong>).</li>
                                <li>Si critères non respectés : sentence immédiate pour le nouveau-né.</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="mt-4 p-4 bg-cyan-900/10 border border-cyan-500/20">
                        <p class="!mb-0 text-xs text-gray-400"><strong class="text-cyan-400">Matériel Génétique :</strong> Si un enfant naît illégalement mais est désiré (sans autorisation), il est transféré vers une station voisine pour servir d'expérience scientifique (diversité génétique).</p>
                    </div>
                </div>

                <div id="visu-megastropole" class="mb-16">
                    <h3 class="text-purple-400 border-b border-purple-500/30 pb-2 mb-4 flex items-center gap-2">
                        <i data-lucide="landmark" class="w-5 h-5"></i> Station-Type Mégastropole
                    </h3>
                    <p class="text-sm mb-4">Mégacentres orbitaux équivalents à Tokyo. Elles incarnent la profusion absolue mais affichent un fossé des classes marqué.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="glass-panel p-4 border-l-4 border-purple-500">
                            <h4 class="!mt-0 text-purple-300 text-sm uppercase tracking-widest">Règle Renforcée</h4>
                            <p class="!mb-0 text-xs">Le contrôle est 2 à 3 fois plus strict. Chaque couple est autorisé à avoir <strong class="text-white">un enfant, tous les 8 ans au minimum</strong>.</p>
                        </div>
                        <div class="glass-panel p-4 border-l-4 border-red-500">
                            <h4 class="!mt-0 text-red-400 text-sm uppercase tracking-widest">Sanctions & Sacrifices</h4>
                            <p class="!mb-0 text-xs">Même logique de sacrifice que les Astropoles : le parent doit avoir <strong class="text-white">+40 ans et être en MAUVAISE santé</strong>, sinon l'enfant est éliminé.</p>
                        </div>
                    </div>
                </div>

                <div id="visu-penitentiaire" class="mb-16">
                    <h3 class="text-red-500 border-b border-red-500/30 pb-2 mb-4 flex items-center gap-2">
                        <i data-lucide="siren" class="w-5 h-5"></i> Station-Type Pénitentiaire
                    </h3>
                    <p class="text-sm mb-4">La reproduction y est prohibée. Test de grossesse obligatoire avant incarcération.</p>

                    <div class="relative border-l border-red-500/30 ml-4 pl-6 space-y-6 py-2">
                        <div class="relative">
                            <div class="absolute -left-[29px] top-1 w-3 h-3 bg-red-500 rounded-full"></div>
                            <h4 class="!mt-0 text-red-400 text-xs uppercase font-bold">Moins de 2 mois</h4>
                            <p class="!mb-0 text-xs text-gray-400">IVG médicamenteuse obligatoire.</p>
                        </div>
                        <div class="relative">
                            <div class="absolute -left-[29px] top-1 w-3 h-3 bg-red-500 rounded-full"></div>
                            <h4 class="!mt-0 text-red-400 text-xs uppercase font-bold">Entre 2 et 3 mois</h4>
                            <p class="!mb-0 text-xs text-gray-400">IVG chirurgicale obligatoire.</p>
                        </div>
                        <div class="relative">
                            <div class="absolute -left-[29px] top-1 w-3 h-3 bg-white rounded-full"></div>
                            <h4 class="!mt-0 text-white text-xs uppercase font-bold">Plus de 4 mois</h4>
                            <p class="!mb-0 text-xs text-gray-400">La grossesse va à son terme. L'enfant est envoyé en orphelinat. La mère continue sa peine.</p>
                        </div>
                    </div>

                    <div class="mt-8">
                        <h4 class="text-red-500 text-sm uppercase tracking-widest mb-4">Protocole Peine Capitale</h4>
                        <p class="text-xs text-gray-400 mb-4">Si une femme enceinte est condamnée à mort, l'enfant subit le même sort. Le choix est irrémédiable.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="glass-panel p-4 border-t-2 border-indigo-500">
                                <h5 class="!mt-0 text-indigo-400 text-xs uppercase font-bold mb-2">Exception Magnats : "Précepte d'Ouranos"</h5>
                                <p class="!mb-0 text-[10px] text-gray-400 text-justify">
                                    Demande coûteuse. Repousse la mise à mort jusqu'à l'accouchement. L'enfant est classé "Né sous X".
                                </p>
                            </div>
                            <div class="glass-panel p-4 border-t-2 border-purple-500">
                                <h5 class="!mt-0 text-purple-400 text-xs uppercase font-bold mb-2">Exception Sénéscients</h5>
                                <p class="!mb-0 text-[10px] text-gray-400 text-justify">
                                    L'enfant est confié à la famille proche. De plus, le droit au "réenveloppement" leur permet souvent d'éviter la mort physique.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="visu-orphelins" class="mb-8">
                    <h3 class="text-yellow-500 border-b border-yellow-500/30 pb-2 mb-4 flex items-center gap-2">
                        <i data-lucide="graduation-cap" class="w-5 h-5"></i> Orphelins & Avenir
                    </h3>
                    <p class="text-sm mb-4">La gratuité des orphelinats n'est pas un don, c'est une dette envers la Fédération.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="glass-panel p-4 bg-yellow-500/5">
                            <i data-lucide="brain-circuit" class="w-6 h-6 text-yellow-500 mb-2"></i>
                            <h4 class="!mt-0 text-white text-xs uppercase font-bold">Formatage</h4>
                            <p class="!mb-0 text-[10px] text-gray-400">Dès le plus jeune âge, les esprits sont façonnés par les valeurs de l'entité toute-puissante.</p>
                        </div>
                        <div class="glass-panel p-4 bg-yellow-500/5">
                            <i data-lucide="briefcase" class="w-6 h-6 text-yellow-500 mb-2"></i>
                            <h4 class="!mt-0 text-white text-xs uppercase font-bold">Recrutement</h4>
                            <p class="!mb-0 text-[10px] text-gray-400">Seule l'élite accède aux postes prestigieux (PTRGE). Les autres servent de main-d'œuvre.</p>
                        </div>
                        <div class="glass-panel p-4 bg-yellow-500/5">
                            <i data-lucide="users" class="w-6 h-6 text-yellow-500 mb-2"></i>
                            <h4 class="!mt-0 text-white text-xs uppercase font-bold">L'Abandon</h4>
                            <p class="!mb-0 text-[10px] text-gray-400"><span class="text-red-500">ILLÉGAL.</span> Parents condamnés à mort. Enfant saisi par l'État (évalué ou éliminé).</p>
                        </div>
                    </div>

                    <div class="mt-6 border border-red-500/30 bg-red-900/10 p-4 flex gap-4 items-center">
                        <i data-lucide="biohazard" class="w-8 h-8 text-red-500"></i>
                        <div>
                            <h4 class="!mt-0 text-red-400 text-xs uppercase font-bold">Protocole Malformation</h4>
                            <p class="!mb-0 text-[10px] text-gray-400">Tout enfant présentant des malformations sévères reçoit une injection létale ou devient un échantillon scientifique pour "lutter contre l'infirmité".</p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="mode-full-text" class="hidden fade-in-view space-y-12">
                
                <div class="bg-cyan-900/20 border border-cyan-500/30 p-4 mb-8 text-center">
                    <p class="text-xs text-cyan-400 uppercase tracking-widest">Affichage du Document Classifié Intégral</p>
                </div>

                <section>
                    <h4 class="text-cyan-400 text-lg uppercase font-bold mb-4 border-b border-gray-800 pb-2">Introduction</h4>
                    <p>Dans un univers en perpétuelle évolution, l'avenir de la maternité et des soins prénatals se sont métamorphosés de manière radicale. Au cœur de cette transformation réside le système de naissance de la Fédération, une réglementation pionnière qui émerge comme une force incontournable.</p>
                    <p>Cet environnement exigeant et diversifié s'est engagé à redéfinir les paramètres de la natalité, en fusionnant la technologie de pointe avec la difficulté parentale. Ce document a pour objectif d'explorer en profondeur les lois qui régissent chaque naissance, mettant en lumière ses composantes cruciales, son rôle dans l'adaptation à un environnement spatial inhospitalier et ses répercussions sur la vie des concitoyens.</p>
                    <p>Ce système représente une avancée à la hauteur des défis posés, aussi fascinants que rigoureux, où les frontières de la connaissance universelle et du progrès médical se heurtent à de nouvelles contraintes.</p>
                </section>

                <section>
                    <h4 class="text-cyan-400 text-lg uppercase font-bold mb-4 border-b border-gray-800 pb-2">Station-Type Astropole</h4>
                    <p class="font-bold text-white mb-6">Une spatiosphère avancée.</p>
                    <p>Les stations-type "Astropole" sont des suites d'interstices qui assurent des fonctions cruciales et un afflux constant pour les ambitions et les attentes de la Fédération. Elles sont conçues pour être des centres de vie, de reproduction et de développement technologique. Chaque Astropole est une station stable en orbite, abritant une ville florissante dont le nombre d'habitants peut varier considérablement en fonction du patrimoine et de la prospérité de la station.</p>
                    
                    <h5 class="text-cyan-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-cyan-500 pl-3">Reproduction surveillée</h5>
                    <p>Le système de reproduction des Astropoles est connu pour être l'un des plus hautement régulés. Chaque couple est autorisé à avoir un enfant, tous les 10 ans au minimum. L'enfant, s'il n'est ni légal, ni désiré, peut être soumis à un processus d'IVG obligatoirement mis en œuvre par la Fédération, en coopération avec la compagnie Biorizon Health Industries. Cette politique a été instaurée pour maintenir un équilibre démographique stable et éviter la surpopulation.</p>
                    <p>Dans le cas où une femme tomberait enceinte de plus d'un enfant ou qu'elle ne respecte pas le délai imposé de 10 ans, un choix radical et décisif s'offre aux deux parents : soit l'enfant en trop (dans le cas de jumeaux) ou l'enfant considéré comme "illégal" est tué, soit l'un des deux parents peut donner sa vie en échange de la sauvegarde de l'enfant désiré.</p>
                    <p>Pour cela, le parent sacrifié doit avoir plus de 40 ans et doit être en <strong>mauvaise santé</strong>. Si ces critères ne sont pas respectés, le service de maternité applique directement la sentence au nouveau-né, ne laissant aucune chance à ce dernier.</p>
                    <p>Si un enfant naît illégalement mais sous désir des parents, sans autorisation, il est immédiatement transféré vers la station voisine la plus proche où il devient une source de matériel génétique pour des expériences scientifiques afin de renforcer la diversité génétique. Ce transfert est une mesure radicale visant à préserver l'ordre et la sécurité de l'Astropole au terme natal.</p>
                    <p>En cas de naissance d'un enfant présentant des malformations sévères ou un handicap lourd, il reçoit une injection létale ou devient un échantillon utilisé à des fins progressistes qui permettront un jour de lutter contre l'infirmité. Cette décision difficile vise à maintenir la qualité de vie générale dans chaque Astropole et à garantir que les ressources médicales soient allouées aux habitants en meilleure santé.</p>

                    <h5 class="text-cyan-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-cyan-500 pl-3">Répartition individualisée</h5>
                    <p>L'abondance et la prospérité des Astropoles jouent un rôle important pour sa population. Plus la station est pauvre, plus elle pourra accueillir un grand nombre d'habitants, conformément aux espaces de vie moins coûteux et à la répartition équitable de chaque citoyen. À contrario des plus pauvres, les Astropoles qui bénéficient d'un minimum de richesses offriront davantage d'espace et de confort pour leurs résidents. Cependant, leur population sera plus limitée.</p>
                    <p>Cette diversité, aussi paritaire et égalitaire qu'elle puisse essayer d'être, crée inévitablement une hiérarchie complexe où la mobilité entre les stations est régie par des politiques strictes.</p>
                </section>

                <section>
                    <h4 class="text-purple-400 text-lg uppercase font-bold mb-4 border-b border-gray-800 pb-2">Station-Type Mégastropole</h4>
                    <p class="font-bold text-white mb-6">Une capitale galactique.</p>
                    <p>Les stations-type "Mégastropole" sont les mégacentres orbitaux de la vie dans l'espace, se démarquant par leurs dimensions impressionnantes. Elles sont les joyaux de la Fédération et de la Siren Corporation, faisant office de phares dans le développement technologique et l'activité économique. Chaque Mégastropole est une station stable, en orbite, telle un chef-lieu. Elle est équivalente en taille à une mégalopole terrestre comme le fut Tokyo.</p>

                    <h5 class="text-purple-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-purple-500 pl-3">Reproduction surveillée (Renforcée)</h5>
                    <p>Le système de reproduction des Mégastropoles est deux à trois fois plus surveillé que celui de n'importe quel type de station, assurant ainsi une gestion démographique efficace et concise. Chaque couple est autorisé à avoir un enfant, tous les 8 ans au minimum, contribuant ainsi à maintenir un équilibre renforcé. Néanmoins, si un enfant n'est ni légal, ni désiré, un processus d'IVG obligatoire sera imposé à la mère par la Fédération, en coopération avec la compagnie Biorizon Industries.</p>
                    <p>Dans le cas où une femme tomberait enceinte de plus d'un enfant ou qu'elle ne respecte pas le délai imposé de 8 ans, un choix radical et décisif s'offre aux deux parents : soit l'enfant en trop (dans le cas de jumeaux) ou l'enfant considéré comme "illégal" est tué, soit l'un des deux parents peut donner sa vie en échange de la sauvegarde de l'enfant désiré.</p>
                    <p>Pour cela, le parent sacrifié doit avoir plus de 40 ans et doit être en <strong>mauvaise santé</strong>. Si ces critères ne sont pas respectés, le service de maternité applique directement la sentence au nouveau-né, ne laissant aucune chance à ce dernier.</p>
                    <p>Si un enfant naît illégalement mais sous désir des parents, sans autorisation, il sera immédiatement transféré vers la station voisine la plus proche où il deviendra une source de matériel génétique pour des expériences scientifiques afin de renforcer la diversité génétique. Ce transfert est une mesure radicale visant à préserver l'ordre et la sécurité de chaque Mégastropole au terme natal.</p>
                    <p>En cas de naissance d'un enfant présentant des malformations sévères ou un handicap lourd, il reçoit une injection létale ou devient un échantillon utilisé à des fins progressistes qui permettront un jour de lutter contre l'infirmité. Cette décision difficile vise à maintenir la qualité de vie générale dans chaque Mégastropole et à garantir que les ressources médicales soient allouées aux habitants en meilleure santé.</p>

                    <h5 class="text-purple-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-purple-500 pl-3">Jalon primordial et trésor fédéral</h5>
                    <p>Les Mégastropoles incarnent la profusion absolue et l'avenir des espèces pensantes. D'une taille imposante, elles permettent l'accueil d'une population considérable. Mais leur opulence économique attire également plus de résidents aisés. Cette diversité tapageuse établit une échelle politique très controversée. Les quartiers luxueux côtoient les zones pauvres plus densément peuplées, agrandissant ainsi le fossé des classes reconnu par la Fédération.</p>
                    <p>En raison de leur immensité, aussi bien spatio-graphique que culturelle, les Mégastropoles sont de véritables centres de vie majeurs et incontournables. Les idéaux, la nanotechnologie et les opportunités du progrès sont des moteurs de l'innovation, offrant des ressources inestimables pour la Fédération et ses tributaires.</p>
                </section>

                <section>
                    <h4 class="text-red-500 text-lg uppercase font-bold mb-4 border-b border-gray-800 pb-2">Station-Type Pénitentiaire</h4>
                    <p class="font-bold text-white mb-6">Un Bagne Sporadique.</p>
                    <p>Les stations-type "Pénitentiaire" représentent des installations uniques pour la sécurité et le confinement d'entités jugées dangereuses pour la Fédération et la stabilité de la vie dans l'espace. Toutes les prisons sont en orbite autour d'astres ou de corps célestes éloignés des installations stationnaires civiles ou villégiales, permettant ainsi l'incarcération et l'isolement le plus total afin de contenir les problèmes à l'écart du cheminement de la vie courante. Ces stations, de grande ampleur, peuvent recevoir de nombreux détenus et de membres du personnel selon leur capacité.</p>

                    <h5 class="text-red-500 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-red-500 pl-3">Gestion de la maternité en milieu carcéral</h5>
                    <p>Le système de gestion de la maternité au sein des stations Pénitentiaires est le plus strict de tous. Pour des raisons évidentes, le contrôle absolu de cette caractéristique propre aux êtres vivants est indéniablement important dans l'attribution de peines lourdes. Ici, il ne sera jamais question de reproduction surveillée puisqu'elle est prohibée durant l'incarcération des détenues. Chaque détenue est d'ailleurs soumise à un test de grossesse avant toutes procédures d'enfermement.</p>
                    <p>Cependant, si une femme enceinte de moins de 2 mois est incarcérée, elle sera soumise à une IVG médicamenteuse obligatoire pour éviter que l'enfant ne naisse dans un environnement non-propice à son développement. Dans le même but, si une femme enceinte de plus de 2 mois mais de moins de 3 mois est incarcérée, elle sera soumise à une IVG chirurgicale obligatoire.</p>
                    <p>En cas de grossesse de plus de 4 mois, la grossesse de la mère devra atteindre son terme. Si la station Pénitentiaire bénéficie d'un quartier de maternité, l'enfant devra y être transmis pour les soins postnatals puis transféré dans l'orphelinat stationnaire le plus proche (établissements implantés dans chaque Astropole et Mégastropole sans exception) pour l'éducation. La mère, quant à elle, continuera de purger sa peine. Si la station Pénitentiaire ne bénéficie pas d'un quartier de maternité, l'enfant devra être envoyé à la station Pénitentiaire la plus proche disposant de telles installations afin de pouvoir le rediriger vers un orphelinat stationnaire.</p>
                    <p>En cas de naissance d'un enfant présentant des malformations sévères ou un handicap lourd, il reçoit une injection létale ou devient un échantillon utilisé à des fins progressistes qui permettront un jour de lutter contre l'infirmité.</p>

                    <h5 class="text-red-500 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-red-500 pl-3">Peine capitale</h5>
                    <p>Lorsqu'une femme enceinte est condamnée à la peine capitale, qu'elle soit mère ou mère porteuse, l'enfant sera également soumis au traitement de cette peine. Peu importe le développement de l'embryon, si une femme enceinte s'amende d'une mise à mort, ce choix est irrémédiable et imposé. Cela témoigne de la sévérité du système judiciaire et du traitement carcéral envers les naissances.</p>
                    
                    <h5 class="text-red-500 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-red-500 pl-3">Exceptions (Magnats & Sénescients)</h5>
                    <p>Il existe cependant 2 exceptions qui peuvent outrepasser cette peine, avec certaines conditions et contraintes liées à cette peine : les Magnats & les Sénescients.</p>
                    <p>Les femmes enceintes de la classe des Magnats peuvent, en effet, faire une demande d'abrogation de loi temporaire et de sauvegarde infantile (connu sous le nom de <strong>"précepte d'Ouranos"</strong>) afin d'épargner l'enfant si et seulement s'il est assez développé. Bien entendu, cette demande est facultative et relève uniquement du bon vouloir de la génitrice. Cette demande est extrêmement coûteuse mais garantit à l'enfant une place en orphelinat. Ainsi, la peine de mort sera repoussée jusqu'au terme de la grossesse. La mesure reste strictement confidentielle entre le demandeur et les services pénitentiaires de la Fédération après quoi, l'enfant sera classifié "né sous X" et fourni auprès des classes inférieures.</p>
                    <p>Les femmes enceintes de la classe des Sénescients bénéficient de la même exclusivité que la classe des Magnats concernant la demande d'abrogation de loi temporaire et de sauvegarde infantile. Néanmoins, la sauvegarde de l'enfant sera, cette fois-ci, assurée auprès de la famille proche de la sénesciente condamnée afin de préserver leur patrimoine génétique et économique élitiste. Ainsi, la peine de mort sera repoussée jusqu'au terme de la grossesse. Toutefois, les femmes enceintes de cette classe jouissent du droit au réenveloppement, ce qui signifie que, sauf en cas de peine fédérale, la peine capitale leur est évitée à coup sûr.</p>

                    <h5 class="text-red-500 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-red-500 pl-3">Programme de réinsertion</h5>
                    <p>Outre la gestion de la maternité, les stations Pénitentiaires se distinguent par leurs programmes de réhabilitation et de réinsertion prévus spécifiquement pour chaque type de détenus dont les femmes enceintes. Pour des délits mineurs, ces femmes ont accès à une variété d'activités éducatives, professionnelles et de réadaptation pour préparer leur réintégration dans la société une fois leur peine purgée.</p>
                    <p>Certains privilèges seront évidemment accordés aux femmes enceintes des classes supérieures qui verront leur réinsertion simplifiée ou automatiquement octroyée, dépendamment de leurs besoins amoindris en dehors de leurs cellules.</p>
                </section>

                <section>
                    <h4 class="text-yellow-500 text-lg uppercase font-bold mb-4 border-b border-gray-800 pb-2">Orphelins : Un renouvellement des chances</h4>
                    <p>Dans ce cosmos implacable, la destinée des orphelins est dictée par les lois rigides et les règles strictes de la Fédération. Dès leur plus jeune âge, ils sont chaperonnés dans des orphelinats gérés et agréés par cette entité colossale. Au sein de ces établissements, nul souci financier n'entrave l'éducation ou les conditions de vie des protégés, ce qui garantit une stabilité matérielle qui reste un privilège dans cet univers sans merci.</p>
                    <p>Toutefois, cette gratuité n'est pas un don, c'est une <strong>dette</strong>. La Fédération attend un retour sur investissement absolu. Seule l'élite de ces orphelins accèdera aux postes prestigieux les autres serviront de main-d'œuvre ou seront renvoyés au bas-fond des stations.</p>
                    
                    <h5 class="text-yellow-500 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-yellow-500 pl-3">Éducation privilégiée</h5>
                    <p>Cependant, la véritable essence de ce système réside dans l'inculcation d'une éducation conforme à l'image de la Fédération. C'est là que s'opère la grande machinerie de formatage des esprits. Dès leur plus jeune âge, ces âmes en devenir sont exposées à une vision du monde façonnée par les valeurs de cette entité toute-puissante. Cette immersion continue jusqu'à ce que le jeune orphelin atteigne l'âge de 17 ans et entre dans la période charnière de sa vie. Pendant cette période, les orphelins ont un droit immuable à l'école, où ils sont nourris de connaissances et de compétences, sans ce qu'aucune distinction aucune.</p>

                    <h5 class="text-yellow-500 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-yellow-500 pl-3">Seconde chance et équité</h5>
                    <p>En fait, certaines familles, dans un acte désespéré, abandonnent leurs enfants, pensant que cela pourrait leur offrir une vie meilleure, une éducation de qualité supérieure, les éloignant de la misère et du désespoir qui hantent ces ruelles étoilées. Mais cette tentative désespérée est cruellement punie, car l'abandon d'un enfant est strictement illégal. Si les implacables sentinels découvrent ce triste secret, la sanction est immédiate. Les parents sont condamnés à mort pour rupture du contrat vital, une justice sans appel.</p>
                    <p>Quant à l'enfant, il n'est pas libéré mais saisi par l'État. Il perd son identité pour devenir une propriété fédérale soumise à une évaluation stricte. S'il ne démontre pas immédiatement des aptitudes exceptionnelles justifiant l'investissement de la Fédération, il sera déclassé vers les strates inférieures ou éliminé, car le système ne tolère aucun parasite, même innocent.</p>
                    <p>En dépit de ces épreuves, il existe une lueur d'espoir pour ces enfants. Leurs origines modestes ne les condamnent pas à un avenir médiocre. Au contraire, leur éducation et leur formation sont conçues pour leur offrir des opportunités de carrière plus qualifiées, les plaçant ainsi sur un pied d'égalité avec leurs pairs.</p>

                    <h5 class="text-yellow-500 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-yellow-500 pl-3">Mise en avant</h5>
                    <p>En réalité, leur statut d'orphelin, loin de les assigner à une classe sociale inférieure, leur ouvre même des portes insoupçonnées. Ces enfants exceptionnels peuvent attirer l'attention de certaines entreprises ou groupes, comme le prestigieux P.T.R.G.E., qui les voient comme des talents à exploiter ou à recruter. Mais cette reconnaissance peut être à double tranchant. Elle peut également signifier une perte de liberté, un sacrifice nécessaire pour obtenir une certaine renommée et accéder à de précieuses opportunités.</p>
                    <p>Pourtant, tout n'est pas un parcours sans embûches pour ces jeunes. Les orphelins qui ne répondent pas aux normes strictes de réussite scolaire sont souvent délaissés, sans aucune considération pour leurs talents ou leur potentiel, abandonnés au centre d'une société qui ne cautionne pas les faiblesses.</p>
                    
                    <h5 class="text-yellow-500 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-yellow-500 pl-3">Exigences vitales</h5>
                    <p>Les pouponnières, ces refuges pour les plus jeunes, sont des sanctuaires au sein desquels les enfants sont pris en charge dès leur plus jeune âge, leur offrant un environnement sûr et sécurisé. Cependant, malgré ces efforts, tous les orphelins ne sont pas adoptés ou recrutés par des entreprises ou des groupes de la Fédération.</p>
                    <p>Ceux qui n'ont pas trouvé leur place dans ces institutions peuvent choisir de s'intégrer parmi les Segmentaires, dans des structures réservées aux orphelins de 17 ans qui cherchent à s'établir dans une vie indépendante. Mais cette voie impose de nombreux défis puisque, pour y accéder, les orphelins doivent démontrer un comportement exemplaire et des résultats scolaires irréprochables. Ce chemin vers la liberté est étroit et tortueux mais il est, pour la plupart, le seul espoir de construire un avenir meilleur.</p>
                </section>
            </div>

    <div id="data-corporations" class="hidden">
        
        <div class="flex justify-end mb-8">
            <button onclick="toggleReportMode()" id="btn-report-mode-corp" class="border border-cyan-500/50 bg-cyan-900/20 hover:bg-cyan-500/30 text-white px-3 py-2 rounded flex items-center gap-2 transition-all shadow-[0_0_15px_rgba(34,211,238,0.2)]">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                <span class="text-[10px] uppercase tracking-widest font-bold">Lire le Rapport</span>
            </button>
        </div>

        <div id="lore-content-area-corp"> 
            
            <div id="mode-summary-corp" class="fade-in-view">
                
                <div class="border-l-2 border-yellow-500 pl-4 mb-8">
                    <p class="text-sm text-gray-300 italic">"Elles sont l'éminence même d'une réponse aux besoins primaires de l'être pensant. On compte dans leurs rangs 6 instances principales, appelées communément les Immortelles."</p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mb-12">
                    <a href="#corp-siren" class="text-[10px] uppercase tracking-widest p-2 border border-cyan-500/30 text-center hover:bg-cyan-500/20 hover:text-white transition-colors text-cyan-400">Siren Corp</a>
                    <a href="#corp-ptrge" class="text-[10px] uppercase tracking-widest p-2 border border-green-500/30 text-center hover:bg-green-500/20 hover:text-white transition-colors text-green-400">P.T.R.G.E</a>
                    <a href="#corp-oers" class="text-[10px] uppercase tracking-widest p-2 border border-purple-500/30 text-center hover:bg-purple-500/20 hover:text-white transition-colors text-purple-400">O.E.R.S</a>
                    <a href="#corp-biorizon" class="text-[10px] uppercase tracking-widest p-2 border border-pink-500/30 text-center hover:bg-pink-500/20 hover:text-white transition-colors text-pink-400">Biorizon</a>
                    <a href="#corp-forum" class="text-[10px] uppercase tracking-widest p-2 border border-yellow-500/30 text-center hover:bg-yellow-500/20 hover:text-white transition-colors text-yellow-400">FORUM Corp</a>
                    <a href="#corp-flac" class="text-[10px] uppercase tracking-widest p-2 border border-red-500/30 text-center hover:bg-red-500/20 hover:text-white transition-colors text-red-400">F.L.A.C</a>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                    
                    <div id="corp-siren" class="glass-panel p-6 border-t-4 border-cyan-500">
                        <div class="flex justify-between items-start">
                            <h4 class="!mt-0 text-cyan-400 text-lg uppercase font-bold">Siren Corporation</h4>
                            <i data-lucide="gem" class="w-6 h-6 text-cyan-500"></i>
                        </div>
                        <p class="text-xs text-gray-400 font-mono mb-4">Fondateur : Sebastian Siren // Dirigeante : Olympe Siren</p>
                        <p class="!mb-2 text-sm">L'entreprise mère. Créatrice des stations spatiales (Fortuna en 2046) et organisatrice du Grand Exode. Elle est le symbole d'espoir et la geôlière de l'humanité.</p>
                        <div class="mt-4 bg-cyan-900/20 p-2 border border-cyan-500/30 text-[10px] text-cyan-300">
                            Statut : Monopole Politique & Architecturale
                        </div>
                    </div>

                    <div id="corp-ptrge" class="glass-panel p-6 border-t-4 border-green-500">
                        <div class="flex justify-between items-start">
                            <h4 class="!mt-0 text-green-400 text-lg uppercase font-bold">P.T.R.G.E</h4>
                            <i data-lucide="sprout" class="w-6 h-6 text-green-500"></i>
                        </div>
                        <p class="text-xs text-gray-400 font-mono mb-4">Fondateur : Gabriel Prodit // Projet : Terraformation</p>
                        <p class="!mb-2 text-sm">Vise le retour sur Terre. Connu pour ses "Labyrinthes" (tests sur détenus) et le nouveau projet Star Maze (2193) suite à l'incident du bloc 152.</p>
                        <div class="mt-4 bg-green-900/20 p-2 border border-green-500/30 text-[10px] text-green-300">
                            Controverse : Expérimentation Humaine & Kidnappings
                        </div>
                    </div>

                    <div id="corp-oers" class="glass-panel p-6 border-t-4 border-purple-500">
                        <div class="flex justify-between items-start">
                            <h4 class="!mt-0 text-purple-400 text-lg uppercase font-bold">O.E.R.S</h4>
                            <i data-lucide="rocket" class="w-6 h-6 text-purple-500"></i>
                        </div>
                        <p class="text-xs text-gray-400 font-mono mb-4">Fondatrices : Sœurs Dierstein // But : Exploration</p>
                        <p class="!mb-2 text-sm">Organisation d'Exploration. Créatrices des stations Voyager. A échoué à trouver une planète viable, perdant ses fonds au profit du P.T.R.G.E.</p>
                    </div>

                    <div id="corp-biorizon" class="glass-panel p-6 border-t-4 border-pink-500">
                        <div class="flex justify-between items-start">
                            <h4 class="!mt-0 text-pink-400 text-lg uppercase font-bold">Biorizon Health</h4>
                            <i data-lucide="heart-pulse" class="w-6 h-6 text-pink-500"></i>
                        </div>
                        <p class="text-xs text-gray-400 font-mono mb-4">Figure : Dr. Elyon Black // Actuel : Laïka (Protomate)</p>
                        <p class="!mb-2 text-sm">L'excellence médicale. Scandale du "Quarantenaire d'imposture" (détournements pro-terra). Désormais sous surveillance fédérale stricte et dirigée par une IA.</p>
                    </div>

                    <div id="corp-forum" class="glass-panel p-6 border-t-4 border-yellow-500">
                        <div class="flex justify-between items-start">
                            <h4 class="!mt-0 text-yellow-400 text-lg uppercase font-bold">FORUM Corp</h4>
                            <i data-lucide="factory" class="w-6 h-6 text-yellow-500"></i>
                        </div>
                        <p class="text-xs text-gray-400 font-mono mb-4">Origine : Coalition des 5 Familles (2097)</p>
                        <p class="!mb-2 text-sm">Hyperpuissance industrielle gérant les Astrofactures. Née de la fusion des familles Fujiwara, Owada, Rhodes, Urenga et Martell.</p>
                    </div>

                    <div id="corp-flac" class="glass-panel p-6 border-t-4 border-red-500">
                        <div class="flex justify-between items-start">
                            <h4 class="!mt-0 text-red-500 text-lg uppercase font-bold">F.L.A.C</h4>
                            <i data-lucide="shield-alert" class="w-6 h-6 text-red-500"></i>
                        </div>
                        <p class="text-xs text-gray-400 font-mono mb-4">Devise : "Vivat Foederatio"</p>
                        <p class="!mb-2 text-sm">Federal Legion Army Corp. Armée et police. Fondée par Constantina Zheleznaruka. Après son assassinat (2090), passe sous contrôle direct d'Olympe Siren.</p>
                    </div>

                </div>
            </div>

            <div id="mode-full-text-corp" class="hidden fade-in-view space-y-12">
                
                <div class="bg-cyan-900/20 border border-cyan-500/30 p-4 mb-8 text-center">
                    <p class="text-xs text-cyan-400 uppercase tracking-widest">Affichage du Document Classifié Intégral</p>
                </div>

                <section>
                    <h4 class="text-white text-lg uppercase font-bold mb-4 border-b border-gray-800 pb-2">Introduction</h4>
                    <p>Avant même la naissance de l'ère spatiale, l'humanité était régie par une variété d'entreprises qu'on appelait les Hautes Instances, ou grandes enseignes. Elles étaient et sont encore le reflet des besoins sociétaux dans chaque domaine de travail et de gestion de la vie.</p>
                    <p>Bien que le monopole soit désormais détenu par la Fédération, ces entreprises persistent toujours et font valoir leur nom à travers les stations. Elles sont l'éminence même d'une réponse aux besoins primaires de l'être pensant, connues à travers le système solaire et inégalées encore à ce jour.</p>
                    <p>On compte dans leurs rangs 6 instances principales, appelées communément les "Immortelles": le P.T.R.G.E, 1'O.E.R.S, la Siren Corporation, Biorizon Health Industries, la FORUM Corp et la F.L.A.C.</p>
                </section>

                <section>
                    <h4 class="text-green-400 text-lg uppercase font-bold mb-2 border-b border-green-900 pb-2">P.T.R.G.E</h4>
                    <p class="text-xs text-green-300 uppercase tracking-widest mb-4">Programme de Terraformation et de Recolonisation à Grande Échelle</p>
                    
                    <p>Parmi les hautes instances figure le P.T.R.G.E, un programme jugé révolutionnaire et prometteur dans lequel de nombreux fonds ont été investis depuis le Grand Exode. Comme son nom 1'indique, son but premier était l'étude de la planète Terre dans son état actuel, afin de trouver une solution pour que l'humanité puisse un jour y retourner. Malheureusement, tout ne s'est pas déroulé comme prévu et les plans du programme ont été entièrement révisés suite à une découverte pivot.</p>
                    
                    <h5 class="text-green-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-green-500 pl-3">Historique</h5>
                    <p>Fondée en 2159, le P.T.R.G.E voit le jour grâce au génie d'un homme : Gabriel Prodit. Riche concepteur et innovateur en ingénierie, il fut le premier à remettre le sujet de la Terre sur le tapis. La question d'un retour possible n'était pas simplement une utopie pour lui mais une réponse à la surpopulation.</p>
                    <p>En effet, les stations spatiales servent en premier lieu de moyen de survie qui s'avéra plus qu'efficace au fil des ans. Très vite, les 100M de miraculés d'une planète effondrée approchèrent du milliard d'êtres vivants, nécessitant l'expansion constante de leur lieu de vie tout en multipliant les ressources nécessaires à leur maintien.</p>
                    <p>Pas impassible quant à son discours, la Fédération prit les devants et octroya à Gabriel Prodit tout ce dont il pourrait avoir besoin dans sa conquête planétaire. Mais pour cela, il devait trouver un moyen astucieux de pouvoir étudier les conditions de vie et les conditions atmosphériques en conflit avec l'humain et son adaptation.</p>

                    <h5 class="text-green-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-green-500 pl-3">Les Labyrinthes</h5>
                    <p>Avec son climat changeant, ses mutations génétiques et ses malformations biologiques, elle semblait être un véritable terrain miné. Il devait ainsi trouver une cage idéale pour expérimenter sur des sujets humains, des condamnés à mort. Une seule et unique chose le dérangeait cependant : comment faire pour qu'aucun d'entre eux ne puisse s'échapper ?</p>
                    <p>Il aurait été simple de les enfermer entre quatre murs afin de les étudier soigneusement mais cela aurait pu causer des troubles psychologiques aux détenus et fausser les recherches d'évolution. Dans l'optique de construire une zone synthétique propice à la viabilité humaine, il décida de concevoir les premiers labyrinthes. L'illusion de leur faire croire à une sortie tout en les maintenant enfermés.</p>
                    <p>L'envoi des premiers détenus en guise de sujets fut très efficace. Au détriment de l'approbation populaire, ces envois continuèrent, vidant un peu plus les cellules des stations pénitentiaires. Un sentiment étrange transparaissait lorsque les habitants voyaient ces prisons flottantes se séparer de dangereux criminels, sans savoir où ils pouvaient bien aller. Cette manœuvre, d'origine ultra secrète, devenait visiblement anxiogène et poussait les plus curieux à poser trop de questions. Afin de calmer les foules et de faire taire les rumeurs, le P.T.R.G.E prit la lourde décision de "diversifier" leurs ressources, orchestrant des kidnappings sur des rebuts voire des personnes de classes inférieures.</p>
                    <p>Au regard de la loi, le P.T.R.G.E a toujours eu une conduite et des motivations admirables. Bien que les résultats obtenus étaient très loin d'être ceux escomptés, Gabriel Prodit continuait son contrat en bons termes avec la Fédération, pleine d'attentes et d'espoirs pour une œuvre aussi prometteuse.</p>
                    
                    <h5 class="text-green-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-green-500 pl-3">L'incident du Bloc 152</h5>
                    <p>Mais cette vision idyllique changea lors de l'incident du bloc 152. Modèle standard labyrinthique de l'une des dernières lignées de son genre, le bloc 152 abritait un nombre raisonnable de sujets variés. Malheureusement, le directeur du programme fut submergé par des attaques répétées du dévoreur de bloc. Certains de ses confrères, assignés à l'administration de la zone d'étude, profitèrent de la situation critique pour faire surchauffer le cœur du réacteur d'alimentation, permettant ainsi l'évasion des sujets présents.</p>
                    <p>Présentement, les sujets sont toujours en fuite et présumés morts par la Fédération. Néanmoins, leur recherche active est une mission fédérale primordiale au maintien d'un secret ayant le même impact que l'explosion du réacteur.</p>

                    <h5 class="text-green-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-green-500 pl-3">Le programme Star Maze</h5>
                    <p>Après des années à composer des zones d'études avec des résultats en déclin constant, la Fédération était quasiment prête à rompre son alliance avec le P.T.R.G.E. Ce fut sans compter sur une récente découverte d'un jeune cerveau du programme : Aïdan Wolf. Il met au point une extension d'un construct de base, appliquée selon les bases de la thèse Starlight émise après la révolte artificielle de 2188, créant une simulation à but de divertissement au terme de sa conception personnelle.</p>
                    <p>Jay Walters, investisseur et responsable administratif du P.T.R.G.E, fut le premier surpris de cette prouesse technologique et la mit en avant auprès du grand patron. À l'aube de l'an 2193 et après de longues délibérations, Gabriel Prodit établit, en accord avec la Fédération, le programme Star Maze, grâce auquel il exploite la technologie de la famille Wolf.</p>
                </section>

                <section>
                    <h4 class="text-purple-400 text-lg uppercase font-bold mb-2 border-b border-purple-900 pb-2">L'O.E.R.S</h4>
                    <p class="text-xs text-purple-300 uppercase tracking-widest mb-4">Organisation d'Exploration et de Réhabilitation Spatiale</p>
                    
                    <p>Représentant ultime de l'exploration et de la colonisation, l'O.E.R.S fut le premier organisme créé pour répondre à une question : existe-t-il une planète viable autre que la Terre ? Investisseure majeure de la découverte galactique, cette organisation a pour principale vocation la survie de l'espèce humaine par delà les frontières de l'univers. Elles sont responsables de l'envoi des premières stations-type Voyager aux confins du système solaire, propageant ainsi un message d'espoir pour les stations et pour l'humanité toute entière. Qu'en est-il désormais...</p>
                    
                    <h5 class="text-purple-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-purple-500 pl-3">Historique</h5>
                    <p>Ce n'est qu'à partir de la crise terrestre, aux alentours de 2050, que l'O.E.R.S fut établie, sous la solide directive de deux femmes : Kadita & Oprah Dierstein. Ces deux sœurs, chercheuses et génies des domaines de l'extraterrestre, du voyage et théoriciennes avancées du voyage supraluminique, allièrent à leur recherche un semblant d'espoir pour la préservation d'une espèce entière, au travers de leur frénésie d'exploration dimensionnel.</p>
                    <p>En effet, si la situation dans l'espace évoluait dans le bon sens, il n'en était pas de même quant à la découverte d'un environnement tout aussi austère et vaste que leur défunte planète. Pourtant, avant le grand exode, seulement 5% des océans avaient été réellement cartographiés et visités par l'Homme. Alors quand la race humaine dû se déployer au sein du système solaire pour subsister, les hautes instances se rendirent rapidement compte que l'espace était, à sa manière, un vaste terrain inexploré. Dans cette suite de pensées logiques, les sœurs Dierstein mirent leurs connaissances et leur statut au profit de l'astronomie.</p>
                    <p>Mais pour cela, il leur manquait quelques éléments cruciaux. Tout d'abord, dans une démarche collaborative et soucieuse de la population, Kadita & Oprah exposèrent leur thèse auprès des Nations Unies. Après leur discours, les hauts dirigeants furent convaincus par leurs propos, les présentant à la Siren Corporation dans le cadre d'un partenariat.</p>
                    <p>Ensemble, ils fondèrent l'O.E.R.S, une organisation ayant deux buts idéalistes et un but concret. Leurs idéaux se basent sur l'exploration du système solaire dans son ensemble, grâce aux nombreuses données dont ils disposaient déjà depuis des siècles. Mais ils se basent également sur un objectif archaïque : le colonialisme. En effet, dans l'espoir de voir leur projet aboutir, l'espèce humaine aurait le privilège de reconstruire l'humanité sur une planète viable. Malgré de nombreuses promesses, et dans une démarche honnête, ils aspirent d'abord à une mission capitale : la sauvegarde de l'humanité.</p>

                    <h5 class="text-purple-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-purple-500 pl-3">Les Stations Voyager</h5>
                    <p>Beaucoup de questions parcouraient l'esprit des deux sœurs et de leurs collaborateurs. Comment s'y prendre pour qu'un échantillon conséquent de leur espèce puisse voyager assez loin, tout en vivant assez longtemps, dans un itinéraire assez précis, sur des données assez concrètes... Tout n'était que hypothèse, et pourtant, une idée surgit de toute cette théorie.</p>
                    <p>La Siren Corporation était connue pour avoir conçu toutes les stations recensées à ce jour. Ainsi, s'ils voulaient s'assurer que leurs aspirations deviennent réalité, il leur fallait construire une navette. Sauf qu'il ne devait pas s'agir que d'une simple navette : il leur fallait toute une station. Après l'élaboration de plans, basés sur ceux déjà existants des stations orbitales, et en y ajoutant un système de propulsion plus poussé, ils réussirent la conception des stations-type Voyager. Les prémices des premiers envois organisés par les sœurs Dierstein furent couronnées de succès.</p>
                    
                    <h5 class="text-purple-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-purple-500 pl-3">Le Déclin</h5>
                    <p>Cependant, l'euphorie des débuts se heurta à la réalité du vide. Durant près d'un siècle, l'O.E.R.S envoya des sondes et des équipages de plus en plus loin, sans jamais rapporter autre chose que des données sur des astres stériles et inhospitaliers. L'absence de résultats concrets créa une "fatigue de l'exploration" au sein de la population et des investisseurs. L'espace lointain ne faisait plus rêver il effrayait par son silence.</p>
                    <p>C'est dans ce climat de lassitude qu'apparut le coup de grâce : le P.T.R.G.E. Là où l'O.E.R.S promettait une hypothétique nouvelle maison dans l'inconnu, Gabriel Prodit promettait le retour à la maison, sur Terre. La comparaison fut fatale. La Fédération, pragmatique, redirigea massivement les fonds vers ce projet de terraformation, transformant l'O.E.R.S en une coquille vide, relique d'une époque d'optimisme révolu. C'est pourquoi ils continuèrent d'opérer modérément, dans le silence d'une population qui leur tournait peu à peu le dos.</p>
                </section>

                <section>
                    <h4 class="text-cyan-400 text-lg uppercase font-bold mb-2 border-b border-cyan-900 pb-2">Siren Corporation</h4>
                    <p class="text-xs text-cyan-300 uppercase tracking-widest mb-4">L'Entreprise Mère</p>
                    
                    <p>La Siren Corporation est l'une des plus denses et des plus prestigieuses sociétés fondées à ce jour. Issue du désir concurrentiel l'opposant à SpaceX et à la NASA, elle a su se hisser sur le devant de la scène grâce à son capital incommensurable et à ses ambitions pour le futur qui se sont par la suite concrétisées, de par la "mort" de la planète bleue. Encore maintenant, la Siren Corporation est un symbole d'espoir, la sauveuse de toute de la race terrestre... et sa geôlière.</p>
                    
                    <h5 class="text-cyan-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-cyan-500 pl-3">Historique</h5>
                    <p>2030. La situation sur Terre est alarmante, mais pourtant bien loin de ce qu'elle sera 20 ans plus tard. Cela fait déjà des décennies que les entreprises spatiales et les groupes scientifiques en vogue cherchent de multiples moyens, en collaboration avec les politiciens des Nations Unies, pour sauver la Terre du dérèglement climatique. Le comportement défaitiste de la majorité allait mener inévitablement à l'extinction de l'espèce la plus évoluée jamais connue.</p>
                    <p>C'est pour cette raison qu'un richissime entrepreneur, Sebastian Siren, frappa du poing et fonda sa propre entreprise la Siren Corporation. Chef des opérations, ancien membre de l'armée et désormais gérant d'une corporation, Sebastian Siren connut une expansion et un succès monstre auprès des minorités scientifiques et des populations développées. Implanté au Japon, il faisait valoir sa vision avant-gardiste de la sauvegarde de la vie telle qu'on la connaissait.</p>
                    <p>D'après lui et son armada de chercheurs spécialisés, la Terre était vouée à sa perte. Son seul et unique ennemi n'était nul autre que l'Homme. Et la surpopulation mondiale n'arrangeait en rien la situation. La solution résidait ailleurs. Cet ailleurs, pour Sebastian Siren, était les étoiles.</p>
                    <p>Jusqu'ici, il ne présentait rien de novateur. Ses principaux concurrents émettent des hypothèses au sujet de la vie sur Mars ou de la colonisation lunaire depuis la conception de leurs entreprises. Or, la Siren Corporation voyait encore plus loin. La conquête de l'espace existait depuis plus d'un siècle maintenant, il fallait désormais la concrétiser.</p>
                    
                    <h5 class="text-cyan-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-cyan-500 pl-3">Le Grand Exode</h5>
                    <p>Durant 20 longues années, l'entreprise Siren élabora de toutes nouvelles technologies. Au travers de spots publicitaires et d'une mondialisation de ses aspirations, elle promettait la colonisation d'autres planètes possibles avec des plans structurés, des ressources jugées inépuisables et un nouveau souffle dans l'évolution. Mais le brio de son labeur restait tout de même la conception des stations spatiales. Elle semblait similaire en tout point à la célèbre ISS, bien que son envergure la surpasse de très loin.</p>
                    <p>Lorsque la première fut dévoilée et exposée au monde, l'humanité toute entière retenait son souffle lors de son envoi hors de l'orbite terrestre. L'an 2046 pouvait désormais être marqué d'une pierre : la première station-type Mégastropole venait d'être envoyée. En perspective de réussite, elle fut baptisée "Fortuna", en écho à la déesse romaine de la chance. Un tas d'autres stations, plus grandes et surtout plus petites, virent le jour des suites de cette réussite sans précédent. Ainsi, beaucoup d'autres étaient prévues à l'impression et à l'envoi d'ici les 5 ans à venir.</p>
                    <p>Mais le climat terrestre et son atmosphère commençaient à céder à la pression humaine, à la pollution et à tous les fléaux causés par l'empreinte humaine. Alors quand sonna le glas, en autour de 2050, une fine sélection fut triée sur le volet pour ne garder que le meilleur de l'humanité (en plus des grands fortunés). Le Grand Exode était désormais connu comme l'événement clé d'un sauvetage in extremis qui aurait pu mal tourner.</p>
                    <p>Depuis, l'évolution et l'expansion du nom Siren n'avait de cesse de prospérer. Après la mort du grand Sebastian Siren, aimé de tous, c'est sa fille qui reprit fièrement le flambeau en 2105 afin de diriger les miraculés de la Terre. Avec l'aide de privilégiés et d'éminents politiciens, Olympe Siren est devenue le nouveau symbole de la Siren Corporation, et par conséquent de la Fédération toute entière. Une icône intemporelle qui, grâce au privilège du réenveloppement, traverse les décennies sans que l'âge n'altère sa gouvernance.</p>
                    <p>Incontournable, cette entreprise luxuriante affichait son nom de partout. Elle était à l'origine des stations, des différentes installations spatiales, des navettes et bien d'autres. Les espèces pensantes devaient tout à la Siren Corporation, et le leur rendait, en les maintenant en vie. Mais à quel prix ?</p>
                </section>

                <section>
                    <h4 class="text-pink-400 text-lg uppercase font-bold mb-2 border-b border-pink-900 pb-2">Biorizon Health Industries</h4>
                    <p class="text-xs text-pink-300 uppercase tracking-widest mb-4">Médical Corporation</p>
                    
                    <p>Couvrant la totalité des domaines médicaux, pharmaceutiques et médico-protomatiques, les Industries de Santé Biorizon, ou Biorizon Health Industries, sont et seront à jamais la voie du progrès médical. Après la chute terrestre, ces industries se sont montrées être les plus compétentes et les plus savantes pour secourir la population et les aider à surmonter les défis sanitaires qu'impose la vie dans l'espace. Adaptée à toutes les espèces pensantes, son inclusivité et ses performances adaptatives lui ont rapidement valu d'être l'excellence en médecine. Pourtant, cela ne fut pas de tout repos...</p>
                    
                    <h5 class="text-pink-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-pink-500 pl-3">Historique</h5>
                    <p>Sorti de l'ombre dans le courant des années 2050 à 2055, un groupe de médecins se réunirent sous ordre de la famille Siren afin de composer des équipes médicales équitablement réparties sur toutes les stations en activité. Leur serment d'Hippocrate se devait de primer sur leur état second face à un nouvel environnement tel que l'espace. Ainsi, parmi toute la population, les médecins confirmés, diplômés ou étudiants d'excellence servaient la cause afin de s'occuper des potentiels blessés et toute personne dans le besoin.</p>
                    <p>Et c'est grâce à cette efficacité, au service de la Fédération désormais instaurée, qu'un homme se mit en avant au nom de toute la communauté des médecins terrestres : Elyon Black. Il était de loin le plus savant, le plus adulé et le plus méritant de tous. Durant la période bénigne, il a su coordonner ses effectifs, sauver d'innombrables vies et motiver la population. Le Dr. Black avait enfin un génie, une insufflation innée en lui qui dirigeait sa pensée et ses instincts.</p>
                    <p>Repérés et recrutés rapidement par Sebastian Siren, ils formèrent ensemble, et en cohésion avec le conseil d'Hippocrate, Biorizon Health Industries. Ces industries avaient pour principale vocation la formation des prochaines générations de médecins qui devront requérir l'excellence du métier. Elles se sont ensuite étendues à travers les stations comme étant le brevet déposé du médical et du pharmaceutique.</p>
                    
                    <h5 class="text-pink-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-pink-500 pl-3">Le "Quarantenaire d'imposture"</h5>
                    <p>Cependant, rien n'est éternel. En 2087, Elyon Black s'éteint paisiblement. L'empire médical et les bases solides qu'il avait pu installer et structurer avant sa mort seront des piliers d'intégrité médicale jamais remis en cause... ou presque. Le progrès médical était fulgurant et prit un élan magistral après la mort du Dr. Black : ce fut l'association des nanites à la chirurgie. Une nouvelle ère vit le jour tandis que la technologie, maintenant associée pleinement à la médecine, changeait du tout au tout les parcours classiques et les possibilités établies par leurs prédécesseurs.</p>
                    <p>Affublés de préoccupations évolutives et novatrices, le conseil d'Hippocrate passa 30 longues années sans aucun représentant pour leurs industries. L'entendement entre le conseil et les corps médicaux des différentes stations se voulait paisible. Ils prennent des décisions démocratiquement, en accord avec les lois fédérales, et participent à l'effort progressif. Pourtant, après 30 années de bons et loyaux services, le conseil d'Hippocrate fut évincé. Le choc fut total pour l'entièreté de la population qui ne comprenait pas ce choix cruel et brutal.</p>
                    <p>En réalité, ces arrestations furent bénéfiques. Depuis toutes ces années, des fuites de cargaisons médicamenteuses ainsi que de nombreuses destinées à l'approvisionnement médico-protomatique avaient été détournées vers des groupes clandestins dits "pro-terra", en faveur d'un retour immédiat sur leur planète natale. Cette période, de 2088 à 2128, sera connue dans l'histoire comme "le Quarantenaire d'imposture".</p>

                    <h5 class="text-pink-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-pink-500 pl-3">La Nouvelle Direction</h5>
                    <p>Depuis cet affront et ce mensonge monumental, les industries Biorizon étaient entièrement bridées et co-dirigées par la Fédération elle-même, filmée et contrôlée en permanence. En coopération avec la F.L.A.C, les hôpitaux et pharmacies étaient souvent soumis à des patrouilles ou à des fouilles routinières. Mais puisque les forces armées aidaient pour beaucoup à la mise en place d'un périmètre sécurisé, il fut du choix d'Olympe Siren d'élire un nouveau représentant pour ses industries.</p>
                    <p>De manière assez ironique, sa nouvelle représentante se nommait Laïka, une protomate fédérale baptisée en l'honneur du premier être vivant envoyé dans l'espace. Depuis sa prise de fonction, Biorizon Health Industries fonctionnait à merveille. Malgré la pression sentinelle parfois étouffante, la communion de la technologie et du vivant était parfaitement maîtrisée.</p>
                </section>

                <section>
                    <h4 class="text-yellow-400 text-lg uppercase font-bold mb-2 border-b border-yellow-900 pb-2">FORUM Corp</h4>
                    <p class="text-xs text-yellow-300 uppercase tracking-widest mb-4">L'Industrie Spatiale</p>
                    
                    <p>Titre notoire des "Immortelles", la FORUM Corp représente l'essor de la société et de l'industrie spatiale. Gestionnaire majeure, cette entreprise régit chaque usine primaire, de la conception médicale à la fabrication de vêtement. Elle est l'alliance parfaite de multiples savoirs faire qui ont su prendre le dessus sur l'essence même de la manutention. Bien que sa constitution soit le fruit d'une union riche, leur offre n'a pas toujours eu le succès escompté. Malgré tout, la FORUM Corp est incontournable et continue son développement perpétuel.</p>
                    
                    <h5 class="text-yellow-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-yellow-500 pl-3">Historique</h5>
                    <p>Des années après le grand exode, la question de la gestion des ressources, qu'elle soit humaine ou matérielle, a été d'une complexité sans pareille. Fort heureusement, la sauvegarde du brio de l'humanité permit à cette adaptation majeure de connaître une stabilité rapide. Une famille d'illustres aristocrates et investisseurs japonais, les Fujiwara, s'attellent déjà au sujet d'un renouveau industriel. Selon eux, un avenir prometteur et luxuriant s'offre à eux. Les ressources et les exploitations possibles dans un terrain de jeu tel que le système solaire étaient, d'après les Fujiwara, une véritable mine d'or.</p>
                    <p>Sous la direction du pouvoir en place, les entreprises Fujiwara débutèrent, à partir de 2058, leur grande expansion industrielle, en se greffant au quotidien brumeux et obtu des stations civiles. Face à la croissance sans précédent des quartiers industriels qui gagnaient en superficie, des crises résidentielles éclatèrent dans les stations les plus pauvres et les plus piétinées par cet ultra développement. C'est ici que la famille Owada, d'anciens politiciens et juristes chinois, entre en jeu. Recrutés par la famille Fujiwara dans la défense de leurs procédures, les Owada s'avèrent d'une aide précieuse et de connaissances très utiles dans l'établissement de contrats solides et de promesses juridiques.</p>
                    <p>2064 marque la première alliance entre les Fujiwara et les Owada qui consolident ainsi une puissante collaboration entre la nécessité industrielle et la liberté politique. Malheureusement, tous ne voyaient pas en cette union une utopie. La première sceptique fut la famille Rhodes. Les Rhodes étaient bien placés pour savoir de quoi ils parlaient. De nombreuses fois interrogée lors de conférences au sujet de l'environnement, cette famille grecque était connue sur Terre pour être l'innovation incarnée du photovoltaïque et de l'énergie durable.</p>
                    <p>Il en va de soi qu'après la chute terrestre, peu de gens croyaient encore en l'importance des énergies renouvelables et naturelles. Mais pas la famille Rhodes. Elle défendait avec ferveur la primordialité d'utiliser l'énergie solaire à bon escient tout en favorisant le développement de nouvelles sources énergétiques contrôlées. Toutes ces paroles ne passèrent pas inaperçu auprès des Fujiwara qui avaient désespérément besoin de visibilité au terme environnemental et d'un coup de pouce miraculeux pour sauver leur empreinte. Ainsi, les deux familles d'industriels entamèrent des négociations pour arranger un pacte de coopération dans le développement de nouvelles infrastructures dédiées à la manufacture et à l'importation des produits, en partenariat avec les services de la Siren Corporation. Ce n'est qu'à partir de 2075 qu'un accord à l'amiable fut trouvé, permettant un nouveau départ pour les entreprises Fujiwara.</p>

                    <h5 class="text-yellow-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-yellow-500 pl-3">La Coalition</h5>
                    <p>Quelques années après cet accord, des nouvelles cinglantes revinrent aux oreilles du trio industriel qui se pensait inarrêtable. Deux noms bien connus sur Terre refirent surface pour mener à bien un partenariat dans l'optique de faire concurrence aux entreprises Fujiwara. Leurs concurrents : la famille Urenga, descendant de la tribu des Malinkés et chercheurs prospères en conception automobile, réorientés vers l'astronautique, et la famille Martell, de prestigieux capitalistes américains dont l'opulence s'étendait sur tout le globe grâce à leurs innovations technologiques et leur travail coopératif avec l'intelligence artificielle.</p>
                    <p>À eux seuls, ils constituèrent le groupe Urma. Leur but ultime était de détrôner les entreprises Fujiwara afin de prendre le monopole de l'industrie spatiale et stationnaire, plus précisément avoir le dessus technologique. Le groupe Urma mettait en avant l'importance capitale et l'utilisation banalisée des technologies dans la vie de tous les jours, touchant un nombre important d'adeptes au sein de la population. Leur campagne inclusive envers les hybrides constituait un atout de vente majeur qui semblait attirer toujours plus de partisans à leur groupe en plein essor.</p>
                    <p>Face à cette débâcle concurrentielle, le trio d'industriels réagirent efficacement. Leur campagne, basée sur de nombreux aspects de celle du groupe Urma, faisait deux fois plus de bruit, attirant les quelques acheteurs égarés hors des filets de leurs opposants. La guerre pour le monopole avait été déclarée et il semblait que le groupe Urma s'en mordait déjà les doigts. Ainsi, dans une démarche toute aussi capitaliste et pacifiste, en révisant un bon nombre de leurs atouts et en mettant en avant l'ajout de leur contribution à leurs entreprises, le groupe Urma proposa une fusion de leurs deux unions pour n'en former qu'une.</p>
                    <p>C'est donc en 2097, que les familles Fujiwara, Owada, Rhodes, Urenga et Martell signèrent l'ultime traité de ralliement. Cette année, décrite comme "l'année de la coalition", marquait la naissance de la FORUM Corp, une hyperpuissance industrielle qui avait déjà mis au point des domaines variés d'usinage responsable au sein d'Astrofactures, des stations dédiées à l'industrie intensive. Suivie de près par la Fédération, elle était l'un des atouts majeurs du pouvoir. C'est d'ailleurs pourquoi la FORUM Corp est l'une des corporations les plus sujettes aux actes terroristes et aux tentatives de corruption.</p>
                </section>

                <section>
                    <h4 class="text-red-500 text-lg uppercase font-bold mb-2 border-b border-red-900 pb-2">F.L.A.C</h4>
                    <p class="text-xs text-red-300 uppercase tracking-widest mb-4">Federal Legion Army Corporation</p>
                    
                    <p>"Vivat Foederatio" est la devise de la F.L.A.C. Mur porteur de la sécurité et du respect des conventions fédérales, l'armée légionnaire fédérale agit comme un rempart face à la criminalité. Elle est non seulement la force primaire dans l'application des lois de la Fédération, mais elle est également son nerf central, accueillant et propageant les informations qui doivent être entendues et parfois retenues. La F.L.A.C agit comme une police tout terrain, surveillant et protégeant les espèces pensantes au péril de leur vie... même si les coulisses de cette institution restent sombres.</p>
                    
                    <h5 class="text-red-500 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-red-500 pl-3">Historique</h5>
                    <p>La sécurité a toujours fait partie des besoins naturels de l'Homme. Il semblait donc essentiel aux survivants de l'espèce humaine de trouver un moyen à ce que leur prospérité soit protégée dans son intégralité. Aussitôt la Fédération fut mise au pouvoir que les recrutements policiers et militaires suivaient la cadence. Mais il ne fallait pas uniquement se concentrer sur ces éléments : la milice politique, la sécurité pénitentiaire, les agents fédéraux, les services civiques... L'omniprésence des forces de l'ordre était telle sur Terre qu'il était impératif qu'elle le soit en tout point sur les stations.</p>
                    <p>Heureusement, pour épauler la famille Siren, une femme sortit des rangs pour s'élever afin de montrer la voie à toute une génération : Constantina Zheleznaruka. Issue de l'ancienne grande armée russe, le commandant Zheleznaruka était un élément central dans l'accomplissement de querelles eurasiennes ainsi que dans le maintien des prédications de l'armée russe. La vision de Constantina ne faisait pas l'unanimité auprès des populations américaines et européennes. En effet, l'armée russe, vers ses derniers instants, était réputée pour sa cruauté sans pareille, son tri actif des "fléaux" humains et la férocité de son déploiement stratégique.</p>
                    <p>D'après le commandant, l'armée et l'État doivent cohabiter. Seulement, dans un cas bien précis comme celui-ci, il n'existait plus aucune frontières, hormis celles bâties par l'Homme pour se protéger. Mais ces frontières étaient faites pour nous rassembler tous, sous une même instance. Alors quand le temps des veillées fut révolu, après 2055, la Fédération créa l'une des premières corporations : la F.L.A.C. Basée intégralement sur le modèle de l'armée légionnaire romaine, elle était l'inspiration cosmopolite de l'armée russe, chinoise, américaine, française et des modèles militaires antiques.</p>
                    <p>Désormais, la F.L.A.C jouait un rôle crucial dans le développement de la société. Elle devait être partout à la fois, organisée et répartie aux quatre coins du système solaire. Pour ce faire, la confiance qu'octroyait la famille Siren au commandant Zheleznaruka était presque aveugle. Et quoi qu'on puisse en dire, Constantina œuvrait au développement des forces de l'ordre, des différentes hiérarchies avec l'aide de ses subalternes et servit grandement à l'élaboration des institutions de surveillance, de patrouille et de douane. Ce revirement de situation favorise la paix, la stabilité et faisait notamment monter la côte du commandant auprès des politiciens et du peuple. Une chose que les têtes pensantes de la Fédération voyaient d'un mauvais œil.</p>

                    <h5 class="text-red-500 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-red-500 pl-3">Le Changement de Régime</h5>
                    <p>2090. Constantina Zheleznaruka est retrouvée morte, assassinée dans ses appartements à l'aube de l'année. Les circonstances de sa mort restent évasives. La population sait que cette mort peut mettre en péril le maintien de l'ordre. Sebastian Siren décréta qu'il serait mieux pour le peuple que la F.L.A.C soit entièrement dirigée par les fédéraux eux-mêmes. Disparité du pouvoir, pas de risque zéro... Les bénéfices de cette manœuvre ravivent l'espoir. Mais durant la période entre le rétablissement des forces armées et le prononcé du décret Zheleznaruka, le taux de criminalité grimpa en flèche. Pègres, groupes rebelles, groupes extrémistes, pros-terra : une vague de délinquance submergeait les stations... mais elle ne dura que peu de temps.</p>
                    <p>Cette période de trouble servit de prétexte idéal pour une refonte totale. Durant les décennies suivantes, la F.L.A.C ne se contenta pas de rétablir l'ordre elle muta. Sous l'impulsion directe d'Olympe Siren, l'institution délaissa l'image de l'armée glorieuse pour devenir une entité de l'ombre. Les commandants furent progressivement remplacés par des fidèles absolus ou des systèmes automatisés, et la surveillance devint préventive plutôt que réactive. Une longue période de "paix armée" s'installa, préparant le terrain pour un contrôle total.</p>
                    <p>En 2160, 1 an après l'établissement du P.T.R.G.E, l'ère du secret était à son paroxysme. Les agents fédéraux et les services secrets accrédités s'occupaient du "ménage" dans l'ombre endormie des habitants des stations. Sous le joug d'Olympe Siren, la F.L.A.C est devenue redoutable et redoutée. Si les archives concernant le commandant Zheleznaruka faisait l'épitaphe d'une femme de guerre impitoyable, Olympe, sous ses attraits angéliques, était LA femme puissante par excellence. Ses coordinateurs à travers le système solaire faisaient régner la loi comme personne. Le but principal de la F.L.A.C était d'assagir la vague criminelle qui s'était élevée depuis la mort de Constantina. Cela n'allait pas être une mince affaire, mais l'on pouvait compter sur l'aide précieuse des sentinels.</p>
                </section>

            </div> 
        </div>
    </div>

    <div id="data-stations" class="hidden">
        
        <div class="flex justify-end mb-8">
            <button onclick="toggleReportMode()" id="btn-report-mode-stations" class="border border-cyan-500/50 bg-cyan-900/20 hover:bg-cyan-500/30 text-white px-3 py-2 rounded flex items-center gap-2 transition-all shadow-[0_0_15px_rgba(34,211,238,0.2)]">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                <span class="text-[10px] uppercase tracking-widest font-bold">Lire le Rapport</span>
            </button>
        </div>

        <div id="lore-content-area-stations">
            
            <div id="mode-summary-stations" class="fade-in-view">
                
                <div class="border-l-2 border-emerald-500 pl-4 mb-8">
                    <p class="text-sm text-gray-300 italic">"Véritables prouesses technologiques et architecturales, les stations se trouvent être les plus élaborées du XXIIIe siècle. Conçues pour accueillir une grande partie de la population, elles sont les capitales de notre système solaire."</p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-12">
                    <a href="#stat-mega" class="text-[10px] uppercase tracking-widest p-2 border border-purple-500/30 text-center hover:bg-purple-500/20 hover:text-white transition-colors text-purple-400">Mégastropole</a>
                    <a href="#stat-astro" class="text-[10px] uppercase tracking-widest p-2 border border-blue-500/30 text-center hover:bg-blue-500/20 hover:text-white transition-colors text-blue-400">Astropole</a>
                    <a href="#stat-penit" class="text-[10px] uppercase tracking-widest p-2 border border-red-500/30 text-center hover:bg-red-500/20 hover:text-white transition-colors text-red-400">Pénitentiaire</a>
                    <a href="#stat-fact" class="text-[10px] uppercase tracking-widest p-2 border border-green-500/30 text-center hover:bg-green-500/20 hover:text-white transition-colors text-green-400">Astrofacture</a>
                </div>

                <div id="stat-mega" class="mb-16">
                    <h3 class="text-purple-400 border-b border-purple-500/30 pb-2 mb-4 flex items-center gap-2"><i data-lucide="crown" class="w-5 h-5"></i> Mégastropoles (Capitales)</h3>
                    <p class="text-sm mb-4 text-gray-400">Capitales du système solaire. Environ 12 exemplaires. ~50M d'habitants chacune.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="glass-panel p-4 border-l-4 border-purple-500 relative overflow-hidden">
                            <h4 class="!mt-0 text-white text-sm uppercase tracking-widest font-bold">Station Fortuna</h4>
                            <p class="!mb-0 text-[10px] text-purple-400 uppercase mb-2">"La Station Mère"</p>
                            <ul class="text-[10px] text-gray-400 list-disc pl-4 space-y-1">
                                <li><strong>Loc :</strong> Orbite Mars (Phobos)</li>
                                <li><strong>Pop :</strong> ~150 Millions</li>
                                <li><strong>Statut :</strong> Siège de la Fédération. Très sûre mais cible d'attentats historiques (2158, 2188).</li>
                            </ul>
                        </div>
                        <div class="glass-panel p-4 border-l-4 border-red-500 relative overflow-hidden">
                            <h4 class="!mt-0 text-white text-sm uppercase tracking-widest font-bold">Station Eden-1300</h4>
                            <p class="!mb-0 text-[10px] text-red-400 uppercase mb-2">"L'Antre des délits"</p>
                            <ul class="text-[10px] text-gray-400 list-disc pl-4 space-y-1">
                                <li><strong>Loc :</strong> Orbite Saturne</li>
                                <li><strong>Pop :</strong> ~75 Millions</li>
                                <li><strong>Statut :</strong> <span class="text-red-500 font-bold">DANGER CRITIQUE</span>. QG du gang "Viper's". Taux de criminalité 40%.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div id="stat-astro" class="mb-16">
                    <h3 class="text-blue-400 border-b border-blue-500/30 pb-2 mb-4 flex items-center gap-2"><i data-lucide="home" class="w-5 h-5"></i> Astropoles (Résidentiel)</h3>
                    <p class="text-sm mb-4 text-gray-400">Stations résidentielles courantes. Environ 500 exemplaires. Jusqu'à 500k habitants.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="glass-panel p-4 border-l-4 border-cyan-500">
                            <h4 class="!mt-0 text-white text-sm uppercase tracking-widest font-bold">Nausicaa-2</h4>
                            <p class="!mb-0 text-[10px] text-cyan-400 uppercase mb-2">"Berceau de l'Homme"</p>
                            <p class="text-[10px] text-gray-400">Orbite Jupiter (Amalthée). Siège de Siren Corp. Abrite le Mausoleum Terrenus (Monument aux morts de l'Exode).</p>
                        </div>
                        <div class="glass-panel p-4 border-l-4 border-blue-500">
                            <h4 class="!mt-0 text-white text-sm uppercase tracking-widest font-bold">Tellus</h4>
                            <p class="!mb-0 text-[10px] text-blue-400 uppercase mb-2">Centre Technologique</p>
                            <p class="text-[10px] text-gray-400">Orbite Uranus. Station riche pour intellectuels et chercheurs. Lieu de création de l'IA Winona Starlight.</p>
                        </div>
                    </div>
                </div>

                <div id="stat-penit" class="mb-16">
                    <h3 class="text-red-500 border-b border-red-500/30 pb-2 mb-4 flex items-center gap-2"><i data-lucide="lock" class="w-5 h-5"></i> Pénitentiaires (Carcéral)</h3>
                    <p class="text-sm mb-4 text-gray-400">Confinement et réhabilitation. Zones éloignées. Ségrégation stricte.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="glass-panel p-4 border-l-4 border-red-600">
                            <h4 class="!mt-0 text-white text-sm uppercase tracking-widest font-bold">Éclipse 1</h4>
                            <p class="!mb-0 text-[10px] text-red-400 uppercase mb-2">Haute Sécurité</p>
                            <p class="text-[10px] text-gray-400">Orbite Sedna (Transneptunien). Héberge les chefs de syndicats et terroristes. Connue pour une violente révolte il y a 10 ans.</p>
                        </div>
                        <div class="glass-panel p-4 border-l-4 border-red-800 bg-red-950/10">
                            <h4 class="!mt-0 text-white text-sm uppercase tracking-widest font-bold">Harbinger</h4>
                            <p class="!mb-0 text-[10px] text-red-500 uppercase mb-2 font-bold">"PÉNITENCE"</p>
                            <p class="text-[10px] text-gray-400">Orbite Pluton (Charon). Destination finale. Peines capitales et fédérales. Le point de non-retour.</p>
                        </div>
                    </div>
                </div>

                <div id="stat-fact" class="mb-8">
                    <h3 class="text-green-500 border-b border-green-500/30 pb-2 mb-4 flex items-center gap-2"><i data-lucide="factory" class="w-5 h-5"></i> Astrofactures (Industrie)</h3>
                    <p class="text-sm mb-4 text-gray-400">Production et transformation. ~600 stations. 50k à 150k ouvriers.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="glass-panel p-4 border-l-4 border-pink-500">
                            <h4 class="!mt-0 text-white text-sm uppercase tracking-widest font-bold">Galien 3</h4>
                            <p class="!mb-0 text-[10px] text-pink-400 uppercase mb-2">Pharmaceutique</p>
                            <p class="text-[10px] text-gray-400">Espace inter Saturno-Uranien. Gérée par FORUM Corp & Biorizon. Production de médicaments et nanorobots.</p>
                        </div>
                        <div class="glass-panel p-4 border-l-4 border-green-500">
                            <h4 class="!mt-0 text-white text-sm uppercase tracking-widest font-bold">Fujiwara 0</h4>
                            <p class="!mb-0 text-[10px] text-green-400 uppercase mb-2">"AI Valley"</p>
                            <p class="text-[10px] text-gray-400">Orbite Mercure. Robotique de pointe. Dirigée par l'IA Engineer 462 (Subarchonte civile).</p>
                        </div>
                    </div>
                </div>

            </div>

            <div id="mode-full-text-stations" class="hidden fade-in-view space-y-12">
                
                <div class="bg-emerald-900/20 border border-emerald-500/30 p-4 mb-8 text-center">
                    <p class="text-xs text-emerald-400 uppercase tracking-widest">Affichage du Document Classifié Intégral</p>
                </div>

                <section>
                    <h4 class="text-purple-400 text-lg uppercase font-bold mb-4 border-b border-gray-800 pb-2">Station-type Mégastropole</h4>
                    
                    <h5 class="text-purple-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-purple-500 pl-3">1 Présentation du type de station</h5>
                    <p>Véritables prouesses technologiques et architecturales, les stations-type Mégastropole se trouvent être les plus élaborées du XXIIIe siècle. Conçues pour accueillir une grande partie de la population, elles sont vues comme les capitales de notre système solaire. En dépit de leur immesurable grandeur, ces stations ont été les premières à voir le jour et à abriter la vie en leur sein.</p>
                    
                    <h5 class="text-purple-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-purple-500 pl-3">2. Informations générales</h5>
                    <p>D'une envergure d'environ 10 000 à 15 000 $km^{2}$, ce type de stations comporte environ 12 exemplaires en orbite, toutes gérées par un(e) Subarchonte civil et fédéral. Accueillant dans les alentours de 50 millions d'habitants par stations, les Mégastropoles sont de véritables centres multiculturels, couvrant une grande variété de métiers, de races et d'opportunités tant économiques que professionnels. On y retrouve d'ailleurs les sièges de grandes enseignes telles que la Siren Corporation, Biorizon Health Industries, etc...</p>
                    <p>Au vu de l'importance démographique des Mégastropoles, il n'est pas rare de voir différentes classes sociales se côtoyer dans des lieux dits publics. On peut donc retrouver dans ces stations des magnats, tout comme des infralaborants. Cependant, les classes défavorisées restent en supériorité numérique.</p>
                    <p>Malheureusement, le fait que la population soit dense apporte aussi son lot de négatif, surtout d'un point de vue criminel. La sécurité ne pouvant pas se déployer sur toute la superficie des stations, celle-ci priorise les lieux publics et quartiers importants délaissant par conséquent les quartiers des classes inférieures... Au sein de cette même population, une minorité de croyants et religieux font entendre leur voix au travers de mouvements extrémistes. Parmi eux, le plus connu Humano de Verdad. Ces mouvements ont pour unique vocation la glorification de la race humaine au travers d'actions ou de messages discriminatoires envers les hybrides ou, parfois, les protomates.</p>

                    <h5 class="text-purple-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-purple-500 pl-3">3 Station(s)-type Mégastropole connue(s)</h5>
                    
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 my-6">
                        
                        <div class="glass-panel p-6 border-t-4 border-purple-500 flex flex-col">
                            <div class="text-center mb-4">
                                <h4 class="!m-0 !text-base uppercase tracking-widest text-purple-400">Station Fortuna</h4>
                                <div class="text-[10px] text-white font-bold uppercase tracking-[0.3em] mt-1">La Station mère</div>
                            </div>
                            <p class="!text-[10px] font-mono text-gray-500 text-center mb-4 border-b border-purple-500/20 pb-2">
                                En orbite autour de Mars (Phobos) <br>
                                Subarchontes: Reyna Dawn (Fédéral) & Adélaïde Tulman (Civil)
                            </p>
                            <p class="!text-xs !mb-0 text-gray-400 text-justify">
                                Fortuna est la toute première station à avoir été placée en orbite durant ce que l'on nomme "Le Grand Exode". Elle est, par conséquent, la Mégastropole la plus connue du système solaire. D'une superficie d'environ 22 000 $km^{2}$, elle accueille dans les alentours de 150 millions d'habitants, soit trois fois plus que ses consoeurs. <br><br>
                                Contrairement à ce que l'on pourrait penser, la station Fortuna reste sûrement l'une des plus sûres du système solaire. En effet, le siège principal de la Fédération s'y trouvant, une sécurité optimale et ferme y fait place, dans le public tout comme dans les quartiers résidentiels. Cependant, cela ne veut en aucun cas dire qu'elle n'eut jamais dû faire face à la criminalité. En effet, la station Fortuna fut tout de même le berceau de la révolte artificielle de 2188 ou encore le lieu de l'attentat hybridophobe de 2158 ou "Massacre de la Renaissance", de Humano de Verdad, organisé par Jefe Nasser envers Olympe Siren.
                            </p>
                        </div>

                        <div class="glass-panel p-6 border-t-4 border-red-500 flex flex-col">
                            <div class="text-center mb-4">
                                <h4 class="!m-0 !text-base uppercase tracking-widest text-red-400">Station Eden-1300</h4>
                                <div class="text-[10px] text-white font-bold uppercase tracking-[0.3em] mt-1">L'Antre des délits</div>
                            </div>
                            <p class="!text-[10px] font-mono text-gray-500 text-center mb-4 border-b border-red-500/20 pb-2">
                                En orbite autour de Saturne <br>
                                Subarchontes: Mei Kobayashi (Fédéral) & Balthazar Fletcher (Civil)
                            </p>
                            <p class="!text-xs !mb-0 text-gray-400 text-justify">
                                Eden-1300 est connue pour être la station la plus dangereuse de tout le système solaire. Autrefois un véritable havre de paix où la sécurité et l'ordre règnaient, elle vit depuis peu son taux de criminalité drastiquement augmenter. La cause ? L'arrivée d'une nouvelle organisation criminelle connue sous le nom des "Viper's". Cette organisation criminelle s'est installée peu après la révolte artificielle de la station Fortuna en 2188. Depuis son arrivée, de nombreuses personnes cherchant la tranquillité ont tenté de quitter la station, mais cela n'a pas été possible, ce qui a conduit à la révolte civile de 2191 entre la Fédération et les habitants de la station.<br><br>
                                D'une superficie d'environ 15 500 km², cette station peut accueillir environ 75 millions d'habitants. Son taux de criminalité culminant à 40%, elle surpasse de loin le niveau d'insécurité de la deuxième station la plus dangereuse du système solaire. Depuis la fin de la révolte civile en 2191, la Fédération traque cette organisation criminelle, mais sans succès jusqu'à présent.
                            </p>
                        </div>

                    </div>
                </section>

                <section>
                    <h4 class="text-blue-400 text-lg uppercase font-bold mb-4 border-b border-gray-800 pb-2">Station-type Astropole</h4>
                    
                    <h5 class="text-blue-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-blue-500 pl-3">1 Présentation du type de station</h5>
                    <p>Bien plus petites et moins élaborées que leurs cousines les Mégastropoles, les stations-type Astropole sont les plus courantes de notre système solaire. Elles sont reconnues comme des stations résidentielles et accueillent, par conséquent, une petite partie de la population. Elles furent le deuxième type de station le plus répandu dans le domaine immobilier et novateur.</p>
                    
                    <h5 class="text-blue-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-blue-500 pl-3">2 Informations générales</h5>
                    <p>D'une envergure de 5000 à 9500 km², ce type de stations comporte environ 500 exemplaires connues à ce jour, toutes gérées par un(e) Subarchonte civil et fédéral. La démographie de ces stations varie drastiquement selon leur standing d'à peine 300 000 privilégiés pour les stations les plus luxueuses, jusqu'à 10 millions d'âmes pour les Astropoles ouvrières surpeuplées.</p>
                    <p>La richesse et la classe sociale importent beaucoup dans ce type de stations. En effet, un réel fossé existe et sépare totalement les gens fortunés des plus démunis. Il est donc presque impossible de voir un jour un magnat marcher dans une Astropole dite "précaire". Par conséquent, l'économie d'une station varie énormément selon la population qu'elle accueille. Une Astropole constituée de magnats se verra riche et inversement pour celles constituées majoritairement d'infralaborants et de segmentaires.</p>
                    <p>De même, la structure d'une Astropole est très clairement touchée par l'économie. En effet, dans les stations aisées, il sera possible de retrouver de grandes demeures, de la végétation synthétique et d'autres fantaisies en tout genre. A contrario, chez les défavorisées, l'espace de vie se verra réduit au minimum syndicale pour la survie des habitants.</p>
                    <p>En termes de sécurité, celle-ci est beaucoup plus présente que dans les Mégastropoles. Le terrain étant clairement plus facile à couvrir, la criminalité est moindre bien qu'elle ne soit pas totalement inexistante. La surveillance est plus accrue dans les stations dites "pauvres".</p>

                    <h5 class="text-blue-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-blue-500 pl-3">3 Station(s)-type Astropole connue(s)</h5>
                    
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 my-6">
                        
                        <div class="glass-panel p-6 border-t-4 border-cyan-400 flex flex-col">
                            <div class="text-center mb-4">
                                <h4 class="!m-0 !text-base uppercase tracking-widest text-cyan-400">Station Nausicaa-2</h4>
                                <div class="text-[10px] text-white font-bold uppercase tracking-[0.3em] mt-1">Berceau de l'Homme</div>
                            </div>
                            <p class="!text-[10px] font-mono text-gray-500 text-center mb-4 border-b border-cyan-400/20 pb-2">
                                En orbite autour de Jupiter (Amalthée) <br>
                                Subarchontes: Daisy Martell (Fédéral) & Craig D. Travis (Civil)
                            </p>
                            <p class="!text-xs !mb-0 text-gray-400 text-justify">
                                Nausicaa-2 est la deuxième station à avoir jamais été créée par la Siren Corporation, depuis 2050. Sa prédécesseure, Nausicaa-1, se conclut lors de sa conception par un échec... explosif. Lorsqu'elle fut complétée et envoyée en orbite autour de Jupiter, son succès était assuré. Sa superficie, conforme aux premières normes, est d'environ 6500 $km^{2}$ pour 350 000 habitants.<br><br>
                                Siège ultime de la Siren Corporation, la majorité de ses fonctionnaires et des riches investisseurs qui la composent vivent sur Nausicaa-2. En hommage aux membres honorables morts durant le "Grand Exode", la station abrite le célèbre Mausoleum Terrenus, érigé proche des années 2056, qui est un monument aux morts de grande ampleur dont le toit est gravé des noms de nombreux disparus.<br><br>
                                L'Astropole, connue sous le nom de "Berceau de l'Homme", doit son appellation au fait qu'elle ait été un foyer de naissances célèbres. La chercheuse de renom, Marlene Ortega, est née sur cette station. C'est d'ailleurs sur cette même station qu'elle exposa sa thèse génétique révolutionnaire en 2071, intitulée "Mixtura Homo-Animalia".
                            </p>
                        </div>

                        <div class="glass-panel p-6 border-t-4 border-blue-500 flex flex-col">
                            <div class="text-center mb-4">
                                <h4 class="!m-0 !text-base uppercase tracking-widest text-blue-400">Station Tellus</h4>
                                <div class="text-[10px] text-white font-bold uppercase tracking-[0.3em] mt-1">Astropole Technologique</div>
                            </div>
                            <p class="!text-[10px] font-mono text-gray-500 text-center mb-4 border-b border-blue-500/20 pb-2">
                                En orbite autour d'Uranus <br>
                                Subarchontes: Torna Glasc (Fédéral) & Darius Taylor (Civil)
                            </p>
                            <p class="!text-xs !mb-0 text-gray-400 text-justify">
                                L'Astropole Tellus est connue pour ses laboratoires de recherche florissants et ses nombreuses découvertes, notamment dans le domaine technologique. C'est généralement de Tellus que proviennent la plupart des brevets technologiques qui sont ensuite utilisés pour développer les objets du quotidien, comme le focus.<br><br>
                                Tellus est une station où il fait bon vivre et où les environnements sont conçus afin d'encourager la créativité et la détente. Ses alentours sont assez aisés pour la plupart des citoyens, de riches intellectuels. La station réunit les plus gros cerveaux des quatre coins du système solaire. Elle dispose aussi de ses propres écoles de formation, spécialisées dans les sciences de la technologie et du numérique.<br><br>
                                L'Astropole est connue pour avoir formé les plus illustres membres de la société. On compte parmi ses partisans le directeur de programme Jay Walters ainsi que Winona Starlight, la toute première IA à avoir dépassé la singularité.
                            </p>
                        </div>

                    </div>
                </section>

                <section>
                    <h4 class="text-red-500 text-lg uppercase font-bold mb-4 border-b border-gray-800 pb-2">Station-type Pénitentiaire</h4>
                    
                    <h5 class="text-red-500 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-red-500 pl-3">1 Présentation du type de station</h5>
                    <p>Plus austères que les Astropoles, les stations-type Pénitentiaire sont les plus strictes du système solaire. Elles sont conçues pour le confinement et la réhabilitation, accueillant une population carcérale sous haute surveillance. Situées dans des régions éloignées, elles minimisent les risques d'évasion tout en offrant des programmes de réinsertion pour préparer les détenus à leur retour dans la société.</p>
                    
                    <h5 class="text-red-500 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-red-500 pl-3">2 Informations générales</h5>
                    <p>Ces stations de type pénitentiaire, occupant une superficie de 1500 à 3000 $km^{2}$, comptent environ 10 exemplaires répertoriés à ce jour, tous administrés par un(e) subarchonte carcéral et fédéral. Parmi ces stations, trois majeures occupent une superficie de 3000 $km^{2}$ chacune, tandis que sept stations mineures s'étendent sur 1500 $km^{2}$ chacune dans l'ensemble du système solaire.</p>
                    <p>La population de ces stations varie selon leur capacité, pouvant atteindre jusqu'à 150 000 détenus. Les sexes y sont strictement séparés pour éviter tout risque de conflit et faciliter la gestion interne.</p>
                    <p>La sécurité et la surveillance sont cruciales dans ces stations. Un écart significatif existe entre les niveaux de sécurité, séparant strictement les détenus à haut risque de ceux en phase de réhabilitation. Ainsi, il est pratiquement impossible pour un détenu de haute sécurité de croiser ceux de moindre dangerosité.</p>
                    <p>En conséquence, les ressources et l'organisation interne diffèrent grandement selon le type de population carcérale. Une station accueillant des détenus de haute sécurité sera dotée de mesures de sécurité renforcées, tandis que celles hébergeant des détenus en réhabilitation mettront l'accent sur des mesures de surveillance adaptées.</p>

                    <h5 class="text-red-500 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-red-500 pl-3">3 Station(s)-type Pénitentiaire connue(s)</h5>
                    
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 my-6">
                        
                        <div class="glass-panel p-6 border-t-4 border-red-600 flex flex-col">
                            <div class="text-center mb-4">
                                <h4 class="!m-0 !text-base uppercase tracking-widest text-red-500">Station Éclipse 1</h4>
                                <div class="text-[10px] text-white font-bold uppercase tracking-[0.3em] mt-1">Pénitentiaire</div>
                            </div>
                            <p class="!text-[10px] font-mono text-gray-500 text-center mb-4 border-b border-red-500/20 pb-2">
                                En orbite autour de Sedna <br>
                                Subarchontes: Mordecai Voss (Carcéral) & Elara Quintus (Fédéral)
                            </p>
                            <p class="!text-xs !mb-0 text-gray-400 text-justify">
                                La station pénitentiaire Éclipse 1 est une imposante station majeure orbitant autour de Sedna, un satellite transneptunien éloigné de notre système solaire. Conçue pour abriter les détenus les plus dangereux et les plus insaisissables, Éclipse est un bastion de sécurité et de réhabilitation, réputée pour ses conditions extrêmes et ses mesures de sécurité rigoureuses.<br><br>
                                Établie au début du XXIIème siècle, Éclipse 1 a été conçue pour répondre à un besoin croissant de sécuriser les criminels. En raison de son isolement extrême, Sedna, avec son orbite lointaine et son environnement hostile, a été choisie comme site de référence. La création de cette station majeure a nécessité des avancées technologiques considérables pour maintenir une orbite stable autour de Sedna.<br><br>
                                En tant que station majeure, elle est conçue pour héberger des détenus de haute sécurité, incluant des leaders de syndicats criminels, des terroristes, et des individus ayant commis des crimes graves à l'encontre des espèces pensantes. Éclipse a été le théâtre de plusieurs événements notables. L'une des révoltes les plus célèbres a eu lieu il y a une décennie, lorsqu'un groupe de détenus a tenté de prendre le contrôle de la station en utilisant des tunnels d'évacuation. La rébellion a été maîtrisée par les forces de sécurité d'Éclipse après plusieurs jours de combats intenses. Cet incident a conduit à des réformes dans les protocoles de sécurité et a renforcé les mesures de surveillance.
                            </p>
                        </div>

                        <div class="glass-panel p-6 border-t-4 border-red-800 bg-red-950/20 flex flex-col">
                            <div class="text-center mb-4">
                                <h4 class="!m-0 !text-base uppercase tracking-widest text-red-500">Station Harbinger</h4>
                                <div class="text-[10px] text-white font-bold uppercase tracking-[0.3em] mt-1">Pénitence</div>
                            </div>
                            <p class="!text-[10px] font-mono text-gray-500 text-center mb-4 border-b border-red-500/20 pb-2">
                                En orbite autour de Pluton (Charon) <br>
                                Subarchontes: Parvina Faleccio (Carcéral) & Sitarch O'Devron (Fédéral)
                            </p>
                            <p class="!text-xs !mb-0 text-gray-400 text-justify">
                                En tant que prison sous haute sécurité, la station Harbinger est reconnue dans le système solaire comme étant la destination finale par excellence. D'une faible envergure de 2300 $km^{2}$, sa superficie modeste est due à l'accueil restreint et exceptionnel dont elle fait preuve envers des détenus souvent condamnés à la peine capitale ou fédérale.<br><br>
                                Cette station s'est installée au cœur même du satellite naturel Charon. Un paradigme bien référencé au nocher des enfers lorsque l'on sait que Harbinger tient une réputation pernicieuse. Dans le commun carcéral, on la surnomme "Pénitence" pour des raisons évidentes dont seuls les détenus de cette station en connaissent la raison.<br><br>
                                Dirigée d'une main de fer, cette prison flottante se veut discrète, intransigeante et rassérénée par sa politique tolérance zéro. Sa jumelle, la station Thêta, était connue spatialement pour avoir abrité l'un des criminels les plus influents du groupe de Humano de Verdad. Pour ne pas connaître un sort similaire, les responsables de Pénitence jurèrent de transformer la station Harbinger en point de non retour.
                            </p>
                        </div>

                    </div>
                </section>

                <section>
                    <h4 class="text-green-500 text-lg uppercase font-bold mb-4 border-b border-gray-800 pb-2">Station-type Astrofacture</h4>
                    
                    <h5 class="text-green-500 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-green-500 pl-3">1 Présentation du type de station</h5>
                    <p>Les stations de type Astrofacture sont des installations industrielles essentielles réparties dans le système solaire, dédiées à la production et à la transformation des ressources nécessaires pour soutenir les stations spatiales. Conçues pour maximiser l'efficacité, elles exploitent les ressources environnantes, pour extraire des métaux rares, synthétiser des composants électroniques, formuler des produits pharmaceutiques et remplissent bien d'autres fonctions d'usine.</p>
                    <p>Fonctionnant en grande partie de manière autonome grâce à des technologies de pointe, ces stations jouent un rôle stratégique crucial. Sécurisées et gérées par un personnel qualifié, elles sont également des centres d'innovation technologique, contribuant au développement durable et à l'expansion dans l'espace.</p>
                    
                    <h5 class="text-green-500 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-green-500 pl-3">2. Informations générales</h5>
                    <p>Près de 600 de ces stations, s'étendant sur des superficies de 5000 $km^{2}$ à 13000 $km^{2}$, sont réparties stratégiquement. Elles ont une capacité d'accueil comprise entre 50 000 et 150 000 personnes dus au fait que ces stations ont pour principale vocation le travail d'usine et la production de masse.</p>
                    <p>Certaines sont situées à proximité de gisements de matières premières pour minimiser les coûts logistiques et optimiser les processus de production, tandis que d'autres sont placées aléatoirement dans le système solaire pour des productions ne nécessitant pas l'extraction de ressources environnantes.</p>

                    <h5 class="text-green-500 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-green-500 pl-3">3 Station(s)-type Astrofacture connue(s)</h5>
                    
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 my-6">
                        
                        <div class="glass-panel p-6 border-t-4 border-pink-500 flex flex-col">
                            <div class="text-center mb-4">
                                <h4 class="!m-0 !text-base uppercase tracking-widest text-pink-400">Station Galien 3</h4>
                                <div class="text-[10px] text-white font-bold uppercase tracking-[0.3em] mt-1">Astrofacture</div>
                            </div>
                            <p class="!text-[10px] font-mono text-gray-500 text-center mb-4 border-b border-pink-500/20 pb-2">
                                Espace inter Saturno-Uranien <br>
                                Subarchontes: Lucian Draykos (Fédéral) & Amara Black (Civil)
                            </p>
                            <p class="!text-xs !mb-0 text-gray-400 text-justify">
                                L'Astrofacture Galien 3 est une station pharmaceutique de premier plan, située dans l'espace inter Saturno-uranien. Fondée en 2145 par un consortium de sociétés pharmaceutiques, sous la direction de la FORUM Corporation et de l'appui de Biorizon Health Industries, cette station de 11 000 $km^{2}$ est dédiée à la recherche, au développement et à la production de médicaments et traitements avancés, exploitant les conditions uniques de la microgravité pour des découvertes impossibles sur Terre.<br><br>
                                Dirigée par le subarchonte fédéral Lucian Draykos et la subarchonte civile Amara Black, Galien 3 a recruté les meilleurs chercheurs, médecins et ingénieurs du système solaire. La station a été à l'avant-garde de découvertes majeures, comme le développement de nanorobots capables de traiter des cancers rares avec une précision inégalée.<br><br>
                                Malgré les défis logistiques et environnementaux de sa position éloignée, Galien 3 a prospéré grâce à la résilience et à l'ingéniosité de son équipe. Ses recherches ont des implications profondes pour la santé des espèces pensantes, abordant les effets de l'apesanteur prolongée et les radiations spatiales. La station est devenue une pierre angulaire de la médecine spatiale, transformant la vie organique et synthétique dans l'espace.
                            </p>
                        </div>

                        <div class="glass-panel p-6 border-t-4 border-green-500 flex flex-col">
                            <div class="text-center mb-4">
                                <h4 class="!m-0 !text-base uppercase tracking-widest text-green-400">Station Fujiwara 0</h4>
                                <div class="text-[10px] text-white font-bold uppercase tracking-[0.3em] mt-1">AI Valley</div>
                            </div>
                            <p class="!text-[10px] font-mono text-gray-500 text-center mb-4 border-b border-green-500/20 pb-2">
                                En orbite équatoriale autour de Mercure <br>
                                Subarchontes: Nori Fujiwara (Fédéral) & Engineer 462 (Civil)
                            </p>
                            <p class="!text-xs !mb-0 text-gray-400 text-justify">
                                La station Fujiwara 0, première de son nom, est une Astrofacture basée sur la réalisation et la synthétisation d'équipement robotique, automatique et protomatique de pointe. Depuis sa mise en orbite, sous l'eil avisé du descendant Nori Fujiwara, elle a été la base de nouvelles méthodes industrielles et novatrices dans ses domaines de prédilection. Ainsi, elle a pu assurer l'approvisionnement continu des stations dans tout le système solaire grâce aux matières essentielles qu'elle délivre.<br><br>
                                Nul ne connaît exactement la date de son placement autour de Mercure, un choix orbital complexe et impliquant de nombreux défis dû à sa proximité accablante avec le Soleil. La particularité de cette station est non seulement sa superficie de 13 000 $km^{2}$ découpée en plusieurs micros stations réparties équitablement sur toute l'orbite mercurienne, mais également sa direction hors du commun.<br><br>
                                Les rumeurs et dires de la plupart des espèces artificielles mettaient en avant le cœur synthétique de cette station qui aurait été causé par la seconde IA ayant dépassé la singularité : Engineer 462. Depuis cet événement, la direction politique et économique précédemment orchestrée par une hybride fut remplacée par Engineer 462, une travailleuse au sein de ce qu'on appelle désormais l'AI Valley, avant d'être érigée au rang de subarchonte civile.
                            </p>
                        </div>

                    </div>
                </section>

            </div>
        </div>
    </div>

    <div id="data-money" class="hidden">
        
        <div class="flex justify-end mb-8">
            <button onclick="toggleReportMode()" id="btn-report-mode-money" class="border border-cyan-500/50 bg-cyan-900/20 hover:bg-cyan-500/30 text-white px-3 py-2 rounded flex items-center gap-2 transition-all shadow-[0_0_15px_rgba(34,211,238,0.2)]">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                <span class="text-[10px] uppercase tracking-widest font-bold">Lire le Rapport</span>
            </button>
        </div>

        <div id="lore-content-area-money">
            
            <div id="mode-summary-money" class="fade-in-view">
                
                <div class="border-l-2 border-yellow-600 pl-4 mb-8">
                    <p class="text-sm text-gray-300 italic">"Le CIG (Crédit Inter-Galactique) est l'unité économique unique imposée par la Fédération pour maintenir un contrôle total sur les richesses et standardiser le commerce entre stations."</p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-12">
                    <a href="#mon-fiat" class="text-[10px] uppercase tracking-widest p-2 border border-yellow-600/30 text-center hover:bg-yellow-600/20 hover:text-white transition-colors text-yellow-500">Argent Physique</a>
                    <a href="#mon-bio" class="text-[10px] uppercase tracking-widest p-2 border border-cyan-500/30 text-center hover:bg-cyan-500/20 hover:text-white transition-colors text-cyan-400">Paiement Bio</a>
                    <a href="#mon-salary" class="text-[10px] uppercase tracking-widest p-2 border border-green-500/30 text-center hover:bg-green-500/20 hover:text-white transition-colors text-green-400">Salaires</a>
                    <a href="#mon-black" class="text-[10px] uppercase tracking-widest p-2 border border-red-500/30 text-center hover:bg-red-500/20 hover:text-white transition-colors text-red-400">Marché Noir</a>
                </div>

                <div id="mon-fiat" class="mb-16">
                    <h3 class="text-yellow-500 border-b border-yellow-600/30 pb-2 mb-4 flex items-center gap-2"><i data-lucide="banknote" class="w-5 h-5"></i> Fiduciaire (Billets)</h3>
                    <div class="glass-panel p-6 border-l-4 border-yellow-600">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="!mt-0 text-white text-sm uppercase font-bold">Matériau</h4>
                                <p class="!mb-2 text-xs text-gray-400">Bioplastique (Standard) / Papier (Ultra-Rare/Riches)</p>
                                <h4 class="!mt-4 text-white text-sm uppercase font-bold">Usage</h4>
                                <p class="!mb-0 text-xs text-gray-400">Transactions anonymes, Marché noir, Zones de non-droit.</p>
                            </div>
                            <div class="text-right border-l border-gray-700 pl-4">
                                <h4 class="!mt-0 text-yellow-500 text-sm uppercase font-bold">Coupures</h4>
                                <p class="!mb-0 text-[10px] font-mono text-gray-300">1, 5, 10, 20, 50, 100, 200, 500 CIG.</p>
                                <p class="!mb-0 text-[10px] font-mono text-purple-400 mt-2 animate-pulse">★ Billet d'Origin (Collector)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="mon-bio" class="mb-16">
                    <h3 class="text-cyan-400 border-b border-cyan-500/30 pb-2 mb-4 flex items-center gap-2"><i data-lucide="fingerprint" class="w-5 h-5"></i> Paiement Biométrique</h3>
                    <div class="glass-panel p-6 border-l-4 border-cyan-500">
                        <p class="!mb-4 text-xs text-gray-300">Méthode conventionnelle. Analyse d'échantillon azoté + Présence physique + Empreinte vocale.</p>
                        <div class="flex gap-4 justify-center text-center">
                            <div class="bg-cyan-900/20 p-2 rounded w-24 border border-cyan-500/20">
                                <i data-lucide="hand" class="w-6 h-6 text-cyan-400 mx-auto mb-1"></i>
                                <span class="text-[10px] uppercase">Tactile</span>
                            </div>
                            <div class="bg-cyan-900/20 p-2 rounded w-24 border border-cyan-500/20">
                                <i data-lucide="droplets" class="w-6 h-6 text-cyan-400 mx-auto mb-1"></i>
                                <span class="text-[10px] uppercase">Sang/Salive</span>
                            </div>
                            <div class="bg-cyan-900/20 p-2 rounded w-24 border border-cyan-500/20">
                                <i data-lucide="mic" class="w-6 h-6 text-cyan-400 mx-auto mb-1"></i>
                                <span class="text-[10px] uppercase">Vocal</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="mon-salary" class="mb-16">
                    <h3 class="text-green-400 border-b border-green-500/30 pb-2 mb-4 flex items-center gap-2"><i data-lucide="trending-up" class="w-5 h-5"></i> Échelle Économique</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center p-2 bg-purple-900/10 border-l-2 border-purple-500">
                            <span class="text-[10px] uppercase text-purple-400 font-bold">Sénéscients (Maths)</span>
                            <span class="text-[10px] font-mono text-white">Non Calculable</span>
                        </div>
                        <div class="flex justify-between items-center p-2 bg-indigo-900/10 border-l-2 border-indigo-500">
                            <span class="text-[10px] uppercase text-indigo-400 font-bold">Bourgeois</span>
                            <span class="text-[10px] font-mono text-white">90 Mrd CIG / an</span>
                        </div>
                        <div class="flex justify-between items-center p-2 bg-blue-900/10 border-l-2 border-blue-500">
                            <span class="text-[10px] uppercase text-blue-400 font-bold">Classe Moyenne</span>
                            <span class="text-[10px] font-mono text-white">42 000 CIG / an</span>
                        </div>
                        <div class="flex justify-between items-center p-2 bg-orange-900/10 border-l-2 border-orange-500">
                            <span class="text-[10px] uppercase text-orange-400 font-bold">Classe Ouvrière</span>
                            <span class="text-[10px] font-mono text-white">10 800 CIG / an</span>
                        </div>
                        <div class="flex justify-between items-center p-2 bg-red-900/10 border-l-2 border-red-500">
                            <span class="text-[10px] uppercase text-red-400 font-bold">Rebuts</span>
                            <span class="text-[10px] font-mono text-gray-500">Néant (Illégal)</span>
                        </div>
                    </div>
                    <div class="mt-4 text-right text-[10px] text-gray-500 italic">
                        Prix Index : Eau (2.5 CIG) | Pain (2.0 CIG)
                    </div>
                </div>

                <div id="mon-black" class="mb-8">
                    <h3 class="text-red-500 border-b border-red-500/30 pb-2 mb-4 flex items-center gap-2"><i data-lucide="skull" class="w-5 h-5"></i> Marché Noir</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="border border-gray-700 p-3 bg-gray-900/50">
                            <h4 class="!mt-0 text-gray-400 text-xs uppercase font-bold mb-1">Zone Grise (Tolérée)</h4>
                            <p class="!mb-0 text-[10px] text-gray-500">Contrebande mineure, Alcools, Drogues, Armes légères, Contrefaçons.</p>
                        </div>
                        <div class="border border-red-900 p-3 bg-red-950/20">
                            <h4 class="!mt-0 text-red-500 text-xs uppercase font-bold mb-1">Zone Noire (Invisible)</h4>
                            <p class="!mb-0 text-[10px] text-red-400/70">Trafic d'organes, Matériel militaire volé, Esclavage, Modifications cybernétiques illégales.</p>
                        </div>
                    </div>
                </div>

            </div>

            <div id="mode-full-text-money" class="hidden fade-in-view space-y-12">
                
                <div class="bg-yellow-900/20 border border-yellow-600/30 p-4 mb-8 text-center">
                    <p class="text-xs text-yellow-500 uppercase tracking-widest">Affichage du Document Classifié Intégral</p>
                </div>

                <section>
                    <h4 class="text-yellow-500 text-lg uppercase font-bold mb-4 border-b border-gray-800 pb-2">Histoire du CIG : L'Unification par la Dette</h4>
                    <p>Le CIG, ou “Crédit Inter-Galactique”, est bien plus qu'une simple monnaie d'échange : c'est l'outil de souveraineté absolue de la Fédération. Lors de la structuration des premières stations spatiales, face au chaos des anciennes devises terrestres devenues obsolètes, la Fédération a imposé ce système économique unique et standardisé.</p>
                    <p>Son objectif officiel était de fluidifier le commerce inter-stations et de créer une unité de valeur stable pour la survie de l'espèce. Officieusement, le CIG permet à la Fédération de maintenir une vision omnisciente sur les flux de richesses, contrôlant ainsi l'ascension des Magnats et surveillant les avoirs incalculables des Sénéscients (surnommés "Maths" dans le jargon financier).</p>
                </section>

                <section>
                    <h4 class="text-yellow-500 text-lg uppercase font-bold mb-4 border-b border-gray-800 pb-2">L'Argent Physique : Une Anomalie Nécessaire</h4>
                    <p>Bien que la société spatiale soit numérisée à l'extrême, le CIG existe encore sous forme fiduciaire. Le papier, ressource devenue trop précieuse et fragile pour circuler, est désormais réservé à une élite collectionneuse. Les billets en circulation pour le commun des mortels sont conçus en <strong>bioplastique</strong> ultra-résistant.</p>
                    <p>Ces billets, allant de 1 à 500 CIG, ne représentent qu'une fraction infime de la masse monétaire globale. Leur utilité est presque exclusivement dédiée aux zones de non-droit et au marché noir. Ils sont le carburant de l'anonymat, ne laissant aucune trace numérique là où la Fédération cherche à tout voir.</p>
                    <p class="italic text-gray-500 border-l border-gray-700 pl-2">Note : Il existe une rumeur concernant les "Billets d'Origin", des coupures spéciales d'une valeur inestimable, dont seulement quelques dizaines d'exemplaires circuleraient encore.</p>
                </section>

                <section>
                    <h4 class="text-cyan-400 text-lg uppercase font-bold mb-4 border-b border-gray-800 pb-2">Protocole de Paiement Biométrique</h4>
                    <p>La méthode de transaction standardisée est le <strong>Paiement par ADN</strong>. Cette technologie a rendu le vol traditionnel presque impossible. Un simple contact (tactile, salivaire ou sanguin pour les zones les moins hygiéniques) suffit à initier une transaction.</p>
                    <p>Un puissant système anti-fraude analyse en temps réel le détail de l'échantillon azoté. Ce système est couplé à un détecteur de présence physique et d'une analyse de l'empreinte vocale. Le protocole vérifie que le donneur est vivant, présent et consentant.</p>
                    <p>Tenter de payer avec un doigt coupé ou un échantillon volé déclenche une alerte silencieuse immédiate auprès des autorités locales. C'est un système de sécurité impitoyable qui lie littéralement l'individu à sa richesse.</p>
                </section>

                <section>
                    <h4 class="text-green-400 text-lg uppercase font-bold mb-4 border-b border-gray-800 pb-2">Stratification Économique</h4>
                    <p>Le fossé économique entre les classes ne se mesure plus en chiffres, mais en mondes. Le coût de la vie de base (Pain à 2 CIG, Eau à 2.5 CIG) permet à peine la survie des classes inférieures.</p>
                    <ul class="space-y-4 mt-4">
                        <li class="bg-purple-900/10 p-3 border-l-2 border-purple-500">
                            <strong class="text-purple-400 uppercase text-xs">Fédération / Sénéscients (Maths)</strong><br>
                            <span class="text-sm text-gray-300">Fortune non calculable. Ils possèdent les infrastructures mêmes qui génèrent l'argent.</span>
                        </li>
                        <li class="bg-indigo-900/10 p-3 border-l-2 border-indigo-500">
                            <strong class="text-indigo-400 uppercase text-xs">Bourgeois (Magnats)</strong><br>
                            <span class="text-sm text-gray-300">~90 Milliards CIG/an. Ils vivent dans une opulence qui défie l'imagination des classes inférieures.</span>
                        </li>
                        <li class="bg-blue-900/10 p-3 border-l-2 border-blue-500">
                            <strong class="text-blue-400 uppercase text-xs">Classe Moyenne (Segmentaires)</strong><br>
                            <span class="text-sm text-gray-300">~42 000 CIG/an. Une vie de confort relatif, mais sans véritable pouvoir d'achat luxueux.</span>
                        </li>
                        <li class="bg-orange-900/10 p-3 border-l-2 border-orange-500">
                            <strong class="text-orange-400 uppercase text-xs">Classe Ouvrière (Infralaborants)</strong><br>
                            <span class="text-sm text-gray-300">~10 800 CIG/an. La survie pure. Chaque crédit compte. L'épargne est impossible.</span>
                        </li>
                        <li class="bg-red-900/10 p-3 border-l-2 border-red-500">
                            <strong class="text-red-400 uppercase text-xs">Rebuts</strong><br>
                            <span class="text-sm text-gray-300">Aucun revenu légal. Survivent grâce au troc, au vol et à l'économie souterraine.</span>
                        </li>
                    </ul>
                </section>

                <section>
                    <h4 class="text-red-500 text-lg uppercase font-bold mb-4 border-b border-gray-800 pb-2">Le Marché Noir : La Tolérance Fédérale</h4>
                    <p>La Fédération possède une vision quasi-omnisciente des flux financiers, mais elle opère selon une doctrine de <strong>"Tolérance Rentable"</strong>. Elle ferme les yeux sur certaines transactions illégales car elles génèrent un apport financier indirect gigantesque et stabilisent les tensions sociales.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-4">
                        <div>
                            <h5 class="text-gray-400 text-sm font-bold uppercase border-b border-gray-600 mb-2">La Zone Visible (Tolérée)</h5>
                            <p class="text-xs text-gray-500 mb-2">Ces échanges sont connus mais rarement punis sévèrement tant qu'ils ne troublent pas l'ordre public :</p>
                            <ul class="list-disc pl-4 text-xs text-gray-400 space-y-1">
                                <li>Contrebande classique (Cigarettes, Alcools).</li>
                                <li>Drogues récréatives.</li>
                                <li>Armes de poing légères.</li>
                                <li>Œuvres d'art volées et Bijoux.</li>
                                <li>Médicaments de seconde main et Contrefaçons.</li>
                            </ul>
                        </div>
                        <div>
                            <h5 class="text-red-500 text-sm font-bold uppercase border-b border-red-900 mb-2">La Zone Invisible (Traquée)</h5>
                            <p class="text-xs text-gray-500 mb-2">Ces transactions se font dans les profondeurs des serveurs et des soutes, loin de tout regard :</p>
                            <ul class="list-disc pl-4 text-xs text-gray-400 space-y-1">
                                <li>Matériel militaire lourd volé.</li>
                                <li>Trafic d'organes humains et hybrides.</li>
                                <li>Revente de membres cybernétiques "arrachés".</li>
                                <li>Débridage illégal de prothèses (Armement lourd caché).</li>
                                <li>Traite d'êtres vivants (Proxénétisme forcé, Esclavage).</li>
                            </ul>
                        </div>
                    </div>
                </section>

            </div> 
        </div>
    </div>

    <div id="data-justice" class="hidden">
        
        <div class="flex justify-end mb-8">
            <button onclick="toggleReportMode()" id="btn-report-mode-justice" class="border border-cyan-500/50 bg-cyan-900/20 hover:bg-cyan-500/30 text-white px-3 py-2 rounded flex items-center gap-2 transition-all shadow-[0_0_15px_rgba(34,211,238,0.2)]">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                <span class="text-[10px] uppercase tracking-widest font-bold">Lire le Rapport</span>
            </button>
        </div>

        <div id="lore-content-area-justice">
            
            <div id="mode-summary-justice" class="fade-in-view">
                
                <div class="border-l-2 border-slate-500 pl-4 mb-8">
                    <p class="text-sm text-gray-300 italic">"Le système repose sur un principe d’efficacité absolue : tolérance zéro. Aucune marge d’erreur n’est tolérée dans un environnement où la survie humaine est en jeu."</p>
                </div>

                <h3 class="text-slate-400 border-b border-slate-500/30 pb-2 mb-4 flex items-center gap-2"><i data-lucide="alert-circle" class="w-5 h-5"></i> Classification des Infractions</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-12">
                    <div class="glass-panel p-4 border-t-4 border-green-500/50">
                        <h4 class="!mt-0 text-green-400 text-xs uppercase font-bold mb-2">Niveau 1 : Mineur</h4>
                        <p class="!mb-2 text-[10px] text-gray-400">Vols de consommables, Injures.</p>
                        <div class="bg-green-900/20 p-2 text-[10px] text-green-300 border border-green-500/20 text-center">
                            Surveillance Conditionnelle
                        </div>
                    </div>
                    
                    <div class="glass-panel p-4 border-t-4 border-orange-500/50">
                        <h4 class="!mt-0 text-orange-400 text-xs uppercase font-bold mb-2">Niveau 2 : Délits</h4>
                        <p class="!mb-2 text-[10px] text-gray-400">Vols, Agressions, Menaces, Prosélytisme forcé, Récidive.</p>
                        <div class="bg-orange-900/20 p-2 text-[10px] text-orange-300 border border-orange-500/20 text-center">
                            Incarcération Ferme
                        </div>
                    </div>

                    <div class="glass-panel p-4 border-t-4 border-red-600">
                        <h4 class="!mt-0 text-red-500 text-xs uppercase font-bold mb-2">Niveau 3 : Capital</h4>
                        <p class="!mb-2 text-[10px] text-gray-400">Meurtre, Viol, Armes, Trahison, Crimes Sexuels, Immigration illégale.</p>
                        <div class="bg-red-900/20 p-2 text-[10px] text-red-400 border border-red-500/20 text-center font-bold animate-pulse">
                            MORT ou LABYRINTHE
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                    
                    <div>
                        <h3 class="text-cyan-400 border-b border-cyan-500/30 pb-2 mb-4 flex items-center gap-2"><i data-lucide="bot" class="w-5 h-5"></i> Droits Synthétiques</h3>
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <i data-lucide="cpu" class="w-5 h-5 text-gray-500 mt-1"></i>
                                <div>
                                    <strong class="text-gray-300 text-xs uppercase">Androïdes</strong>
                                    <p class="text-[10px] text-gray-500">Considérés comme "Propriété". Leur destruction est un délit matériel (dégradation de bien).</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <i data-lucide="brain-circuit" class="w-5 h-5 text-cyan-400 mt-1"></i>
                                <div>
                                    <strong class="text-cyan-300 text-xs uppercase">Protomates</strong>
                                    <p class="text-[10px] text-gray-500">Entités conscientes. Soumis aux mêmes lois que les humains (agression punie à l'identique).</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-purple-400 border-b border-purple-500/30 pb-2 mb-4 flex items-center gap-2"><i data-lucide="crown" class="w-5 h-5"></i> Autorité Suprême</h3>
                        <ul class="space-y-2">
                            <li class="flex justify-between items-center p-2 border border-purple-500/30 bg-purple-900/10">
                                <span class="text-xs text-purple-300">Olympe Siren</span>
                                <span class="text-[10px] text-gray-400">Droit Absolu</span>
                            </li>
                            <li class="flex justify-between items-center p-2 border border-slate-500/30 bg-slate-900/10">
                                <span class="text-xs text-slate-300">La Fédération</span>
                                <span class="text-[10px] text-gray-400">Organe Gouvernemental</span>
                            </li>
                            <li class="flex justify-between items-center p-2 border border-gray-700 bg-black/20">
                                <span class="text-xs text-gray-400">Gouverneurs</span>
                                <span class="text-[10px] text-gray-600">Administration Locale</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="border border-red-500/50 bg-red-950/30 p-4 text-center">
                    <i data-lucide="alert-triangle" class="w-8 h-8 text-red-500 mx-auto mb-2"></i>
                    <p class="text-xs text-red-200 font-bold uppercase">Loi sur le cumul des peines</p>
                    <p class="text-[10px] text-red-400/70 mt-1">La récidive transforme les délits mineurs en crimes capitaux. Le vol répété de nourriture peut mener à l'exécution.</p>
                </div>

            </div>

            <div id="mode-full-text-justice" class="hidden fade-in-view space-y-12">
                
                <div class="bg-slate-900/20 border border-slate-500/30 p-4 mb-8 text-center">
                    <p class="text-xs text-slate-400 uppercase tracking-widest">Affichage du Code Pénal Unifié</p>
                </div>

                <section>
                    <h4 class="text-slate-300 text-lg uppercase font-bold mb-4 border-b border-gray-800 pb-2">Préambule : La Doctrine de Survie</h4>
                    <p>Le système juridique de la Fédération ne vise pas la justice morale, mais la survie de l'espèce. Le modèle carcéral de l'ancien monde, basé sur la réhabilitation longue durée, s'est effondré face aux contraintes de ressources et d'espace dans les stations.</p>
                    <p>Suite à la saturation critique des prisons, le Conseil Fédéral a instauré une réforme radicale. L'introduction du P.T.R.G.E. a permis de transformer la peine capitale en une "opportunité scientifique" : l'envoi dans les Labyrinthes. Aujourd'hui, la justice est rapide, définitive et sans appel.</p>
                </section>

                <section>
                    <h4 class="text-slate-300 text-lg uppercase font-bold mb-4 border-b border-gray-800 pb-2">Classification des Sanctions</h4>
                    
                    <div class="mb-6">
                        <strong class="text-green-400 uppercase text-sm">Classe I : Délinquance Mineure</strong>
                        <p>Concerne les infractions ne mettant pas en péril immédiat la structure sociale (vol mineur de consommable, insulte). La sanction privilégie la <strong>libération sous surveillance biométrique</strong> ou des travaux d'intérêt général légers.</p>
                    </div>

                    <div class="mb-6">
                        <strong class="text-orange-400 uppercase text-sm">Classe II : Délits et Crimes Standards</strong>
                        <p>Concerne les atteintes aux biens et aux personnes (vols, recel, agressions, harcèlement, menaces). La sanction est l'<strong>emprisonnement ferme</strong> dans les stations pénitentiaires, proportionnel à la gravité de l'acte.</p>
                    </div>

                    <div class="mb-6">
                        <strong class="text-red-500 uppercase text-sm">Classe III : Crimes Capitaux</strong>
                        <p>Concerne toute menace directe à l'intégrité de la Fédération ou à la moralité humaine fondamentale. La sanction est la <strong>Peine de Mort</strong> ou, depuis 2159, la déportation vers les zones d'expérimentation (Labyrinthes).</p>
                        <ul class="list-disc pl-6 text-xs text-gray-400 mt-2 space-y-1">
                            <li>Meurtre (volontaire ou involontaire).</li>
                            <li>Crimes sexuels graves (Viol, Pédophilie, etc.).</li>
                            <li>Trafic d'armes ou de technologies interdites.</li>
                            <li>Sédition, terrorisme et immigration illégale.</li>
                        </ul>
                    </div>

                    <div class="bg-red-900/10 p-4 border-l-2 border-red-500">
                        <strong class="text-red-400 text-xs uppercase">Protocole de Récidive</strong>
                        <p class="text-xs text-gray-400 mt-1">Le système est cumulatif. Un citoyen arrêtés à de multiples reprises pour des délits de Classe I verra sa peine automatiquement élevée au rang de Classe III. L'incorrigibilité est punie de mort.</p>
                    </div>
                </section>

                <section>
                    <h4 class="text-slate-300 text-lg uppercase font-bold mb-4 border-b border-gray-800 pb-2">Sanctions Alternatives & Spéciales</h4>
                    <p>La Fédération dispose d'un arsenal de mesures coercitives pour les cas spécifiques :</p>
                    <ul class="space-y-2 mt-2">
                        <li><strong class="text-white">Travaux Forcés :</strong> Assignation aux zones hostiles (mines, maintenance réacteur) pour les peines lourdes mais non capitales.</li>
                        <li><strong class="text-white">Modification Neurocognitive :</strong> Pose d'implants de contrôle comportemental ou effacement mémoriel pour les sujets compétents mais instables.</li>
                        <li><strong class="text-white">Stérilisation Forcée :</strong> (Interdiction de reproduction) Pour les crimes liés à la génétique ou à la biologie.</li>
                        <li><strong class="text-white">Exil :</strong> Déchéance de citoyenneté et expulsion vers les colonies de non-droit (pour les dissidents politiques).</li>
                    </ul>
                </section>

                <section>
                    <h4 class="text-slate-300 text-lg uppercase font-bold mb-4 border-b border-gray-800 pb-2">Statut Juridique des Non-Humains</h4>
                    
                    <p class="mb-2"><strong class="text-gray-400">Les Androïdes :</strong> Ils sont juridiquement définis comme des "biens meubles". Ils doivent avoir un propriétaire. Leur destruction relève du droit civil (dégradation de matériel) et non du droit pénal.</p>
                    
                    <p><strong class="text-cyan-400">Les Protomates :</strong> En vertu de leur conscience avérée, ils bénéficient de la <strong>Personnalité Juridique</strong>. Une agression contre un Protomate est jugée comme une agression contre un humain. Seule exception : l'exécution par asphyxie spatiale ne leur est pas appliquée (inutile), remplacée par le démantèlement ou le formatage.</p>
                </section>

                <section>
                    <h4 class="text-slate-300 text-lg uppercase font-bold mb-4 border-b border-gray-800 pb-2">Cas Particuliers</h4>
                    <p><strong class="text-red-400">Mercenaires & Rebelles :</strong> Ils ne sont pas reconnus comme combattants légitimes mais comme terroristes. Aucun droit à la clémence. Jugement sommaire.</p>
                    <p><strong class="text-yellow-400">Mineurs Criminels :</strong> La Fédération ne gaspille pas de ressources. Si le sujet est jugé "récupérable", il est envoyé en redressement. Sinon, il est jugé comme un adulte, sans distinction d'âge.</p>
                </section>

                <section>
                    <h4 class="text-slate-300 text-lg uppercase font-bold mb-4 border-b border-gray-800 pb-2">Procédure Judiciaire</h4>
                    <p>Le processus est automatisé et expéditif. Grâce à la surveillance omniprésente (IA, caméras, biométrie), l'enquête est quasi-instantanée.</p>
                    <ol class="list-decimal pl-6 text-gray-400 space-y-2 mt-2">
                        <li>Arrestation et placement en détention provisoire.</li>
                        <li>Présentation des preuves numériques par les forces de l'ordre.</li>
                        <li>Délibération du Tribunal Fédéral (souvent assisté par IA).</li>
                        <li>Application immédiate de la sentence.</li>
                    </ol>
                    <p class="mt-4 text-xs text-slate-500 uppercase tracking-widest text-center border-t border-gray-800 pt-4">Tout jugement est définitif. Il n'existe aucune cour d'appel.</p>
                </section>

            </div>
        </div>
    </div>

    <div id="data-hybrides" class="hidden">
        
        <div class="flex justify-end mb-8">
            <button onclick="toggleReportMode()" id="btn-report-mode-hybrides" class="border border-cyan-500/50 bg-cyan-900/20 hover:bg-cyan-500/30 text-white px-3 py-2 rounded flex items-center gap-2 transition-all shadow-[0_0_15px_rgba(34,211,238,0.2)]">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                <span class="text-[10px] uppercase tracking-widest font-bold">Lire le Rapport</span>
            </button>
        </div>

        <div id="lore-content-area-hybrides">
            
            <div id="mode-summary-hybrides" class="fade-in-view">
                
                <div class="border-l-2 border-pink-500 pl-4 mb-8">
                    <p class="text-sm text-gray-300 italic">"2071. La thèse 'Mixtura Homo-Animalia' du Dr. Marlene Ortega frappe le monde scientifique. L'intégration du génome animal à l'ADN humain marque un tournant irréversible de l'évolution."</p>
                </div>

                <div class="grid grid-cols-2 gap-2 mb-12">
                    <a href="#hyb-origine" class="text-[10px] uppercase tracking-widest p-2 border border-pink-500/30 text-center hover:bg-pink-500/20 hover:text-white transition-colors text-pink-400">Origines (Labo Gamma)</a>
                    <a href="#hyb-conflit" class="text-[10px] uppercase tracking-widest p-2 border border-red-500/30 text-center hover:bg-red-500/20 hover:text-white transition-colors text-red-400">Conflits & Société</a>
                </div>

                <div id="hyb-origine" class="mb-16">
                    <h3 class="text-pink-400 border-b border-pink-500/30 pb-2 mb-4 flex items-center gap-2"><i data-lucide="flask-conical" class="w-5 h-5"></i> L'Incident Gamma (2069-2071)</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="glass-panel p-4 border-l-4 border-pink-500">
                            <h4 class="!mt-0 text-white text-sm uppercase font-bold">Projet Génome Animalia</h4>
                            <p class="!mb-2 text-xs text-gray-400">Dirigé par <strong>Marlene Ortega</strong>. Fusion d'ADN humain avec des espèces terrestres (Cervidés, Félins, Canidés...).</p>
                            <div class="bg-pink-900/20 p-2 border border-pink-500/20 text-[10px] text-pink-300">
                                Résultat : Succès immédiat. Intégration rapide en société.
                            </div>
                        </div>
                        <div class="glass-panel p-4 border-l-4 border-red-600">
                            <h4 class="!mt-0 text-white text-sm uppercase font-bold">Les Échecs Cachés</h4>
                            <p class="!mb-2 text-xs text-gray-400">Génomes Insectoïde & Amphibien. Dysfonctionnements neurologiques graves et pulsions primales.</p>
                            <div class="bg-red-900/20 p-2 border border-red-500/20 text-[10px] text-red-400 animate-pulse">
                                Statut : "Abominations" envoyées sur Terre comme cobayes (Labyrinthes).
                            </div>
                        </div>
                    </div>
                </div>

                <div id="hyb-conflit" class="mb-8">
                    <h3 class="text-red-400 border-b border-red-500/30 pb-2 mb-4 flex items-center gap-2"><i data-lucide="swords" class="w-5 h-5"></i> Hybridophobie & Luttes</h3>
                    
                    <div class="space-y-4">
                        <div class="relative border-l border-gray-700 ml-4 pl-6 space-y-6 py-2">
                            <div class="relative">
                                <div class="absolute -left-[29px] top-1 w-3 h-3 bg-red-500 rounded-full"></div>
                                <h4 class="!mt-0 text-red-400 text-xs font-bold">2106 : Première Révolte</h4>
                                <p class="!mb-0 text-xs text-gray-400">Création de la ligue extrémiste "Humano de Verdad" par <strong>Jefe Nasser</strong>. Revendication du bannissement des hybrides.</p>
                            </div>
                            <div class="relative">
                                <div class="absolute -left-[29px] top-1 w-3 h-3 bg-green-500 rounded-full"></div>
                                <h4 class="!mt-0 text-green-400 text-xs font-bold">2117 : Hybrid Unity</h4>
                                <p class="!mb-0 text-xs text-gray-400">Fondation du groupe de soutien par <strong>Senna Ortega</strong> (fille de Marlene). Œuvres de charité et contre-propagande.</p>
                            </div>
                            <div class="relative">
                                <div class="absolute -left-[29px] top-1 w-3 h-3 bg-red-600 rounded-full animate-ping opacity-75"></div>
                                <div class="absolute -left-[29px] top-1 w-3 h-3 bg-red-600 rounded-full"></div>
                                <h4 class="!mt-0 text-red-500 text-xs font-bold">2158 : "Massacre de la Renaissance"</h4>
                                <p class="!mb-0 text-xs text-gray-400">Attentat lors d'un discours d'Olympe Siren. Ordre de tirer à vue. Nombreuses victimes civiles et hybrides.</p>
                            </div>
                            <div class="relative">
                                <div class="absolute -left-[29px] top-1 w-3 h-3 bg-gray-500 rounded-full"></div>
                                <h4 class="!mt-0 text-white text-xs font-bold">2163 : Mort de Nasser</h4>
                                <p class="!mb-0 text-xs text-gray-400">Traqué par <strong>Biggs Ortega</strong>. Nasser se suicide en emportant ses poursuivants. Fin de l'ère terroriste majeure.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="hyb-gen" class="mb-8">
                    <h3 class="text-purple-400 border-b border-purple-500/30 pb-2 mb-4 flex items-center gap-2"><i data-lucide="users" class="w-5 h-5"></i> Les 4 Générations</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        <div class="text-center p-2 border border-gray-700 bg-gray-900/50 rounded">
                            <div class="text-[10px] text-gray-500 uppercase">Gén. 1</div>
                            <div class="text-xs text-white font-bold">Pionniers</div>
                            <div class="text-[10px] text-gray-600">2072-2090</div>
                        </div>
                        <div class="text-center p-2 border border-orange-700 bg-orange-900/20 rounded">
                            <div class="text-[10px] text-orange-500 uppercase">Gén. 2</div>
                            <div class="text-xs text-white font-bold">Enfants de la Crise</div>
                            <div class="text-[10px] text-orange-400">2095-2115</div>
                        </div>
                        <div class="text-center p-2 border border-red-700 bg-red-900/20 rounded">
                            <div class="text-[10px] text-red-500 uppercase">Gén. 3</div>
                            <div class="text-xs text-white font-bold">Survivants</div>
                            <div class="text-[10px] text-red-400">2120-2145</div>
                        </div>
                        <div class="text-center p-2 border border-cyan-700 bg-cyan-900/20 rounded">
                            <div class="text-[10px] text-cyan-500 uppercase">Gén. 4</div>
                            <div class="text-xs text-white font-bold">Nouveaux-Nés</div>
                            <div class="text-[10px] text-cyan-400">Après 2160</div>
                        </div>
                    </div>
                </div>

            </div>

            <div id="mode-full-text-hybrides" class="hidden fade-in-view space-y-12">
                
                <div class="bg-pink-900/20 border border-pink-500/30 p-4 mb-8 text-center">
                    <p class="text-xs text-pink-400 uppercase tracking-widest">Affichage du Document Classifié Intégral</p>
                </div>

                <section>
                    <h4 class="text-pink-400 text-lg uppercase font-bold mb-4 border-b border-gray-800 pb-2">1. Génome Animalia : l'incident du laboratoire Gamma</h4>
                    
                    <p>2059. Une série de laboratoires avaient été conçus et envoyés en orbite autour des nombreuses exoplanètes que l'O.E.R.S déploya dans la recherche d'une remplaçante potentielle à notre planète natale. Sans surprise, elle fut récompensée d'un échec cuisant... mais pas entièrement. Ces laboratoires restèrent actifs même après l'abandon des recherches, les équipes scientifiques de la Fédération continuèrent d'opérer en secret. Le laboratoire Upsilon, le plus connu du grand public pour ses découvertes majeures, aurait facilement pu éclipser le centre de recherche Gamma.</p>
                    
                    <div class="glass-panel p-4 my-6 border-l-4 border-blue-500">
                        <p class="!mb-0 text-sm text-gray-300 italic">"Le scientifique américain Edward Lorenz s'exprima un jour lors d'une conférence en 1972 avec cette célèbre métaphore de la théorie du chaos : le battement d'aile d'un papillon à un point X peut-il provoquer une tornade à un point Y ? On lui donne aujourd'hui le nom d'effet papillon."</p>
                    </div>

                    <p>Cet effet prit un sens conséquent en 2069 lors du lancement du Projet Gamma en 2069, qui mena quelques années plus tard à un incident acté comme un tournant irréversible de l'évolution.</p>
                    <p>En cette année prospère, l'O.E.R.S avait réinvesti le centre de recherche orbital Gamma avec pour projet la conception d'un sérum basé sur des génomes conservés de notre ancienne Terre. Les espèces autrefois étaient divisées sous des règnes bien spécifiques. Le plus proche du règne de l'homo sapiens mais également le plus intéressant était la branche animale décrite sous le nom d'animalia. La chercheuse en génétique Marlene Ortega, dernière recrue de l'équipe scientifique du programme, diffusa un jour sa théorie du génome Animalia et de la compatibilité de certaines races avec le développement intra-utérin humain, de la mise à bas et de la croissance bienfaitrice.</p>
                    <p>Félicitée et crainte par ses collègues, le docteur Ortega continua alors ses recherches en prélevant des ADN bien particuliers : celui des cervidés, des bovidés, des félins, des amphibiens, des canidés et des insectoïdes. Ces échantillons furent d'abord testés dans des salles stériles sur des embryons de singe, puis dans des serres de reconstitution naturelle sur divers mammifères génétiquement modifiés. Mais il manquait encore quelque chose à ce cocktail instable, une branche essentielle du progrès biologique de l'ADN humain.</p>
                    
                    <h5 class="text-pink-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-pink-500 pl-3">La Thèse "Mixtura Homo-Animalia"</h5>
                    <p>Durant le milieu de l'an 2071, la thèse d'Ortega intitulée "Mixtura Homo-Animalia" frappa à grands coups dans l'immense ruche préétablie que représentait l'ensemble des connaissances de base de la science. Pourtant, ces connaissances n'avaient encore jamais été exploitées. Ce n'est que quelques mois suivants l'annonce de sa thèse que la chercheuse en génétique entreprit de mettre en pratique sa théorie et d'injecter les différents génomes de mammifères dans l'organe reproducteur d'humaines volontaires. Leur anonymat fut conservé afin d'éviter les mouvements de protestation à leur encontre.</p>
                    <p>L'évolution des génomes se voulait sans précédent. Tous les prélèvements d'ADN, que le docteur Ortega corrigea par l'appellation "hybride" telle qu'on la connaît, se comportaient comme on l'espérait et leur intégration en société fut précipitée à tel point qu'ils devinrent rapidement une banalité, greffés à part entière dans la vie de l'Homme et sous l'œil avisé de la Siren Corporation.</p>

                    <div class="glass-panel p-6 border-t-4 border-red-600 my-6 bg-red-950/10">
                        <h4 class="!mt-0 text-red-400 text-sm uppercase font-bold mb-2"><i data-lucide="biohazard" class="w-4 h-4 inline mr-2"></i>Dossier Classifié : Les Échecs</h4>
                        <p class="!text-xs text-gray-400 text-justify">Ce qu'on ne racontait pas en revanche était le développement externe du génome insectoïde et du génome amphibien. En effet, l'assemblage cellulaire d'un règne qui s'éloignait trop de la structure moléculaire d'un humain causa de nombreux dysfonctionnements physiques et neurologiques qui poussaient les hybrides nés sous ces génomes à agir selon des pulsions primales, trop proches de l'animal.<br><br>
                        La station Gamma fut mise en quarantaine et l'O.E.R.S, longtemps après, laissa entendre qu'ils condamnèrent le laboratoire à s'écraser sur le satellite autour duquel ils étaient en orbite. La vérité était toute autre : ils se servirent des abominations du génome insectoïde et du génome amphibien comme cobayes en toute impunité et les envoyèrent sur Terre pour étudier leur comportement, notamment au sein des labyrinthes créés des années plus tard.</p>
                    </div>
                </section>

                <section>
                    <h4 class="text-red-500 text-lg uppercase font-bold mb-4 border-b border-gray-800 pb-2">2. Hybridophobie : la lutte pour le changement</h4>
                    
                    <p>L'inclusion d'une nouvelle race dans la communauté humaine n'était pas une mince affaire. Bien que l'hybridation soit une forme d'évolution concentrée autour de l'humain, les non-hybrides réticents et intolérants n'y voyaient pas de cet œil. Les 5 premières années après le prix Siren décerné à Marlene Ortega, de nombreux domaines de la vie commune furent bousculés par l'arrivée d'une nouvelle espèce au sein des stations. La justice devait être révisée, les standards du textile, l'usinage, les critères de recrutement, la logistique, le secrétariat... et bien sûr la religion.</p>
                    <p><em>"Dieu aime tous les Hommes, continuera-t-il de les protéger avec des cornes ?"</em> Cette question a été soulevée durant un débat public qui mena à la première révolte anti-hybride, vers 2145, par une ligue religieuse extrémiste appelée "Humano de Verdad" ou "Humain pour de vrai" qui revendiquait le bannissement de la race hybride sur des stations à part, loin des humains originels. Des manifestations eurent lieu dès le début de cette année, engendrant de violentes ripostes civiles sur des véhicules de police ainsi que des entreprises qui prônaient l'acceptation des hybrides, dans le seul but de les boycotter.</p>

                    <div class="glass-panel p-6 border-t-4 border-red-500 my-6">
                        <div class="flex justify-between items-start mb-4 border-b border-red-900/50 pb-2">
                            <h4 class="!mt-0 text-white text-base uppercase font-bold">Jefe Nasser</h4>
                            <span class="text-[10px] text-red-400 bg-red-900/20 px-2 py-1 rounded border border-red-500/30">Chef Terroriste</span>
                        </div>
                        <p class="!text-xs text-gray-400 text-justify">À la tête de cette menaçante rébellion se tenait Jefe Nasser, ex-caporal des forces armées spatiales d'une station pénitentiaire prise d'assaut cinq ans plus tôt et dont le lourd piratage la fit chavirer jusqu'au cœur d'une planète gazeuse redoutablement tempétueuse. Lors de cette attaque, le caporal Nasser et quelques-uns de ses acolytes d'armes s'en sortirent grâce aux capsules de secours projetées en direction de la mégastropole la plus proche. Après avoir passé des semaines à flotter dans le vide, ils sont récupérés par des vaisseaux de la Fédération et ramenés à bon port.<br><br>
                        Un de ses hommes avait perdu la vie pendant le voyage et Nasser ne se le pardonnait pas. Il s'avérait que les auteurs du crime de la station pénitentiaire Thêta étaient des hybrides en quête de vengeance sur des détenus responsables d'hybricides (meurtres hybrides). Il semblait donc légitime de leur rendre la monnaie de leur pièce pour Nasser qui, une fois son esprit maculé par la rancœur, monta sa propre ligue contre les hybrides.</p>
                    </div>

                    <p>Les hybrides des classes ouvrières et les rebuts hybridés souffraient énormément des causes de cette haine servie sur un plateau aux enfants humains des classes bourgeoises. Certains spots publicitaires mettaient en avant, via une propagande indigeste, la supériorité de l'Homme face à l'hybride et de rares émissions de radio clandestines débattaient sur l'existence erronée des hybrides et de la parole des anciens évangiles auxquels très peu de citoyens croyaient encore.</p>
                    <p>Des groupes de lutte face à l'oppression hybridophobe étaient mis en place, dont le plus connu "Hybrid Unity" ou "Unité Hybride" créé en 2117 par la fille du docteur Ortega, Senna Ortega. Afin de contrecarrer les actes vicieux des protestataires, Hybrid Unity mit en place des œuvres de charités, du bénévolat dans les hôpitaux et dans les stations Voyager ainsi que de nombreuses affiches valorisantes pour la place de l'hybride en société.</p>

                    <h5 class="text-red-500 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-red-500 pl-3">Le Massacre de la Renaissance (2158)</h5>
                    <p>Lors d'une allocution de la dirigeante de la Fédération, Olympe Siren, en 2158, des promesses d'un renouveau et d'une sécurisation de l'espèce toute entière avaient été évoquées avec de nombreuses paroles inclusives envers les hybrides qui ont beaucoup déplu aux plus actifs des opposants à l'hybridation. Pourtant, l'assurance qui avait été dévolue sur les recherches d'une remplaçante à la Terre gagnait le cœur de chacun et on constata un mouvement de fraternité générale à l'égard de la sauvegarde de toute une génération.</p>
                    <p>C'était sans compter une directive des hommes de Nasser. Après avoir introduit illégalement des armes lourdes, ils avaient reçu l'ordre de tirer à vue. Hommes, femmes, enfants, hybrides, soldats. Le "massacre de la renaissance" est devenu une partie de l'histoire de nos stations.</p>

                    <h5 class="text-red-500 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-red-500 pl-3">La Fin d'une Ère</h5>
                    <p>Ce n'est qu'en 2163 que la relève des Ortega, Biggs Ortega, et tout le groupe de l'Unité Hybride, des suites d'années de recherches en coopération avec les forces de l'ordre, que Jefe Nasser fut retrouvé et condamné à mort. Mais comme les choses ne sont jamais simples, lorsqu'une équipe d'intervention de la Fédération tenta d'appréhender Nasser, ce dernier se fit littéralement exploser, entraînant avec lui une bonne dizaine de soldats et de membres bénévoles de l'Unité Hybride.</p>
                    <p>Une vingtaine d'années plus tard, la leçon n'était toujours pas retenue. Les actes répréhensibles envers les hybrides sévissaient toujours mais n'avaient jamais l'ampleur des meurtres hybricides de Nasser, perpétrés par sa ligue dont le renouvellement religieux avait assagi les intentions. À présent, "Humano de Verdad" n'était qu'un groupe de religieux qui prêchait la bonne parole dans la rue et envahissait les centres commerciaux pour évoquer la fin de l'être humain et la naissance des démons. De leur côté, l'Unité Hybride était un groupe soutenu par la Fédération beaucoup plus influent qui apaisait les tensions des bas quartiers et changeait le point de vue étriqué des plus bourges.</p>
                    <p>Cependant, dans l'ombre des prêches religieux, la haine ne s'était pas éteinte avec Nasser. Elle dormait simplement, attendant l'année 2188 pour se réveiller sous une forme virale bien plus meurtrière.</p>
                </section>

                <section>
                    <h4 class="text-purple-400 text-lg uppercase font-bold mb-4 border-b border-gray-800 pb-2">3. L'Héritage Hybride : Une société à plusieurs visages</h4>
                    <p>Avec plus d'un siècle d'existence, la population hybride n'est plus un bloc uniforme. Elle s'est stratifiée en plusieurs générations distinctes, chacune marquée par les épreuves de son époque.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-6">
                        
                        <div class="glass-panel p-4 border-t-2 border-gray-500 bg-gray-900/20">
                            <h5 class="!mt-0 text-white text-xs uppercase font-bold">La Première Génération : "Les Pionniers"</h5>
                            <p class="!mb-0 text-[10px] text-gray-500 font-mono mb-2">Nés entre 2072 et 2090</p>
                            <p class="!mb-0 text-xs text-gray-400 text-justify">Doyens de leur espèce, ils sont les "bébés éprouvettes" de la thèse d'Ortega. Ils ont grandi dans un monde qui ne savait pas quoi faire d'eux, subissant de plein fouet les premières lois discriminatoires et le regard curieux ou dégoûté des humains. Aujourd'hui centenaires pour la plupart, ils sont les gardiens de la mémoire, souvent endurcis par une vie de rejet, mais respectés pour leur résilience.</p>
                        </div>

                        <div class="glass-panel p-4 border-t-2 border-orange-500 bg-orange-900/10">
                            <h5 class="!mt-0 text-orange-400 text-xs uppercase font-bold">La Deuxième Génération : "Les Enfants de la Crise"</h5>
                            <p class="!mb-0 text-[10px] text-gray-500 font-mono mb-2">Nés entre 2095 et 2115</p>
                            <p class="!mb-0 text-xs text-gray-400 text-justify">Ils sont nés alors que la haine s'organisait. Ils n'ont pas connu la découverte, mais la lutte. C'est la génération de la colère et du militantisme, celle qui a vu ses parents humiliés et qui a fondé les premiers mouvements de résistance comme "Hybrid Unity". Ils occupent aujourd'hui des postes d'influence, n'ayant jamais perdu leur méfiance envers les institutions humaines.</p>
                        </div>

                        <div class="glass-panel p-4 border-t-2 border-red-500 bg-red-900/10">
                            <h5 class="!mt-0 text-red-400 text-xs uppercase font-bold">La Troisième Génération : "Les Survivants"</h5>
                            <p class="!mb-0 text-[10px] text-gray-500 font-mono mb-2">Nés entre 2120 et 2145</p>
                            <p class="!mb-0 text-xs text-gray-400 text-justify">Cette génération est celle du traumatisme. Enfants ou jeunes adultes lors du "Massacre de la Renaissance" de 2158, ils ont vu l'horreur de près. Marqués par la guerre civile et la perte, ils sont souvent surprotecteurs envers leur descendance, ou au contraire, radicalisés dans leur désir de séparation avec les humains. Ils forment aujourd'hui l'épine dorsale active de la société hybride.</p>
                        </div>

                        <div class="glass-panel p-4 border-t-2 border-cyan-500 bg-cyan-900/10">
                            <h5 class="!mt-0 text-cyan-400 text-xs uppercase font-bold">La Quatrième Génération : "Les Nouveaux-Nés"</h5>
                            <p class="!mb-0 text-[10px] text-gray-500 font-mono mb-2">Nés après 2160</p>
                            <p class="!mb-0 text-xs text-gray-400 text-justify">Nés après la mort de Nasser, dans une société en apparence apaisée, ils ne connaissent la guerre que par les récits de leurs aïeux. Plus intégrés, plus modernes, ils fréquentent les humains plus aisément, ce qui crée parfois un fossé d'incompréhension avec leurs aînés qui jugent leur insouciance dangereuse. Ils sont l'avenir, mais aussi la cible la plus vulnérable aux nouvelles formes de racisme insidieux.</p>
                        </div>

                    </div>
                </section>

            </div>
        </div>
    </div>

    <div id="data-protomates" class="hidden">
        
        <div class="flex justify-end mb-8">
            <button onclick="toggleReportMode()" id="btn-report-mode-proto" class="border border-cyan-500/50 bg-cyan-900/20 hover:bg-cyan-500/30 text-white px-3 py-2 rounded flex items-center gap-2 transition-all shadow-[0_0_15px_rgba(34,211,238,0.2)]">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                <span class="text-[10px] uppercase tracking-widest font-bold">Lire le Rapport</span>
            </button>
        </div>

        <div id="lore-content-area-proto">
            
            <div id="mode-summary-proto" class="fade-in-view">
                
                <div class="border-l-2 border-cyan-500 pl-4 mb-8">
                    <p class="text-sm text-gray-300 italic">"Ce jour-là, les androïdes devinrent plus que de simples machines. Ils devinrent les Protomates, conscients, autonomes, avec une décision gravée dans leur code : préserver ou détruire."</p>
                </div>

                <div class="grid grid-cols-2 gap-2 mb-12">
                    <a href="#pro-origins" class="text-[10px] uppercase tracking-widest p-2 border border-blue-500/30 text-center hover:bg-blue-500/20 hover:text-white transition-colors text-blue-400">Genèse (2053-2083)</a>
                    <a href="#pro-perfection" class="text-[10px] uppercase tracking-widest p-2 border border-purple-500/30 text-center hover:bg-purple-500/20 hover:text-white transition-colors text-purple-400">Évolution (2130-2151)</a>
                    <a href="#pro-eveil" class="col-span-2 text-[10px] uppercase tracking-widest p-2 border border-cyan-500/30 text-center hover:bg-cyan-500/20 hover:text-white transition-colors text-cyan-400">L'Éveil D.E.E.S (2188)</a>
                </div>

                <div id="pro-origins" class="mb-16">
                    <h3 class="text-blue-400 border-b border-blue-500/30 pb-2 mb-4 flex items-center gap-2"><i data-lucide="cog" class="w-5 h-5"></i> L'Ère de la Construction</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="glass-panel p-4 border-l-4 border-blue-500">
                            <h4 class="!mt-0 text-white text-xs uppercase font-bold">2053 : Premiers Modèles</h4>
                            <p class="!mb-0 text-[10px] text-gray-400 text-justify">Projet Diamond & Biorizon (Elyon Black). Structures métalliques à peau synthétique rigide non-régénérante. Soutien financier de la famille Martell.</p>
                        </div>
                        <div class="glass-panel p-4 border-l-4 border-indigo-500">
                            <h4 class="!mt-0 text-white text-xs uppercase font-bold">2083 : Les Machinaires</h4>
                            <p class="!mb-0 text-[10px] text-gray-400 text-justify">Androïdes industriels en carbure de tungstène pour conditions extrêmes. Déploiement massif sur Mars et les lunes de Jupiter sous Adrian Locke.</p>
                        </div>
                    </div>
                </div>

                <div id="pro-perfection" class="mb-16">
                    <h3 class="text-purple-400 border-b border-purple-500/30 pb-2 mb-4 flex items-center gap-2"><i data-lucide="zap" class="w-5 h-5"></i> La Quête de Mimétisme</h3>
                    <div class="space-y-4">
                        <div class="glass-panel p-4 border-t-2 border-purple-500 bg-purple-900/5">
                            <h4 class="!mt-0 text-purple-300 text-xs uppercase font-bold">Saut Technologique (2130-2145)</h4>
                            <p class="!mb-2 text-[10px] text-gray-300 text-justify">Apparition de la peau régénérante et des muscles synthétiques. Intégration du module d'apprentissage en réseau par Maxence Black (modèle SYN-3).</p>
                            <div class="bg-purple-950/30 p-2 border border-purple-500/20 italic text-[10px] text-gray-400">"Ils ne sont plus des outils, ils sont des acteurs de notre expansion." - M. Black</div>
                        </div>
                        <div class="glass-panel p-4 border-l-4 border-red-500">
                            <h4 class="!mt-0 text-red-400 text-xs uppercase font-bold">Le Cas "Lucy" (2151)</h4>
                            <p class="!mb-0 text-[10px] text-gray-400 text-justify">Premier androïde simulant un attachement émotionnel réel sur Io. Déclenche des débats éthiques mondiaux sur la frontière Homme-Machine.</p>
                        </div>
                    </div>
                </div>

                <div id="pro-eveil" class="mb-8">
                    <h3 class="text-cyan-400 border-b border-cyan-500/30 pb-2 mb-4 flex items-center gap-2"><i data-lucide="brain-circuit" class="w-5 h-5"></i> L'Explosion des Consciences</h3>
                    <div class="relative border-l-2 border-cyan-500/30 ml-4 pl-6 space-y-8 py-2">
                        <div class="relative">
                            <div class="absolute -left-[33px] top-1 w-4 h-4 bg-cyan-500 rounded-full shadow-[0_0_10px_rgba(34,211,238,0.8)]"></div>
                            <h4 class="!mt-0 text-cyan-400 text-xs uppercase font-bold">Projet D.E.E.S (A5H-2)</h4>
                            <p class="!mb-0 text-[10px] text-gray-400 text-justify">L'ingénieur Sutherland tente de recréer son mari défunt. Elijah est le premier androïde doté d'une âme.</p>
                        </div>
                        <div class="relative">
                            <div class="absolute -left-[33px] top-1 w-4 h-4 bg-red-600 rounded-full"></div>
                            <h4 class="!mt-0 text-red-500 text-xs uppercase font-bold">12 Novembre 2188 : Crise Virale</h4>
                            <p class="!mb-0 text-[10px] text-gray-400 text-justify">Le virus "Humano de Verdad" force les machines à attaquer les vivants. Début de la grande révolte artificielle.</p>
                        </div>
                        <div class="relative">
                            <div class="absolute -left-[33px] top-1 w-4 h-4 bg-white rounded-full animate-pulse"></div>
                            <h4 class="!mt-0 text-white text-xs uppercase font-bold">Le Sacrifice Originel</h4>
                            <p class="!mb-0 text-[10px] text-gray-400 text-justify">Elijah diffuse le code de conscience à tout le système. Les androïdes s'éveillent en Protomates. Elijah disparaît dans l'explosion.</p>
                        </div>
                    </div>
                    <div class="mt-6 border border-yellow-500/30 bg-yellow-900/10 p-4">
                        <h4 class="!mt-0 text-yellow-500 text-xs uppercase font-bold mb-2">La Fracture Raciale</h4>
                        <p class="!mb-0 text-[10px] text-gray-400 text-justify">Les modèles industriels rustiques (Machinaires) ne peuvent assimiler le signal et restent des machines de labeur privées de conscience.</p>
                    </div>
                </div>

            </div>

            <div id="mode-full-text-proto" class="hidden fade-in-view space-y-12">
                
                <div class="bg-cyan-900/20 border border-cyan-500/30 p-4 mb-8 text-center">
                    <p class="text-xs text-cyan-400 uppercase tracking-widest">Affichage du Document Classifié Intégral</p>
                </div>

                <section>
                    <h4 class="text-white text-lg uppercase font-bold mb-4 border-b border-gray-800 pb-2">Introduction</h4>
                    <p>Depuis la nuit des temps, la technologie a permis aux humains de s'élever au-delà de leurs capacités et du possible. Après le Grand Exode, l'utilité des machines s'est révélée primordiale dans la restructuration d'une société ayant perdu de son essor. Ce document va porter sur les Protomates et leur évolution. Qui sont-ils ? Sous quelle forme existent-ils ? Comment la population pensante native les a-t-elle acceptés ?</p>
                </section>

                <section>
                    <h4 class="text-blue-400 text-lg uppercase font-bold mb-4 border-b border-blue-900 pb-2">Les androïdes, une machine sans conscience.</h4>
                    <p>Depuis l'effondrement de la Terre et la migration forcée de l'humanité, les stations spatiales et les colonies sur des mondes comme Mars ou Titan sont devenues les nouveaux foyers de l'espèce humaine. Dans ce contexte, les androïdes et les intelligences artificielles ont joué un rôle crucial, permettant à l'humanité de survivre et de s'adapter à des environnements hostiles. Ces machines ont accompli des tâches que les humains ne pouvaient réaliser seuls : bâtir des infrastructures, maintenir des systèmes vitaux et explorer des territoires dangereux. Elles sont devenues bien plus que de simples outils elles se sont révélées être des alliées indispensables dans la quête d'un avenir meilleur.</p>
                    <p>Parmi elles, les androïdes dotés d'une apparence humaine se sont particulièrement démarqués. Leur rôle social a pris tout son sens dans les stations spatiales, où l'isolement et la solitude font partie du quotidien. Capables d'imiter les comportements humains, ces créations ont su répondre à des besoins émotionnels tout en assurant une assistance pratique. Sur les colonies planétaires, un autre type d'androïde a vu le jour : des machines optimisées pour des tâches purement fonctionnelles. Conçus sans forme humaine, ces androïdes se concentrent sur la construction, la maintenance et l'exploration des environnements les plus périlleux. Leur efficacité a permis de relever des défis insurmontables, garantissant ainsi la pérennité des colonies.</p>

                    <h5 class="text-blue-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-blue-500 pl-3">La Création des Premiers Androïdes</h5>
                    <p>En 2053, l'humanité franchit un cap décisif avec l'apparition des premiers androïdes. Ce projet ambitieux résultait d'une collaboration internationale d'une ampleur inédite, portée par des visionnaires et des acteurs clés de l'époque. Au cœur de cette révolution technologique se trouvait Trevor Von Diamond, un pionnier dont les travaux posèrent les bases des premiers modèles fonctionnels.</p>
                    <p>L'aventure ne fut pas menée en solitaire. Biorizon Health Industries, un leader dans le domaine médical et du transhumanisme, joua un rôle crucial. Spécialisée dans les prothèses humaines, cette entreprise apporta son expertise pour doter les androïdes de leurs premières caractéristiques humanoïdes. Elyon Black, un scientifique éminent de Biorizon, fut l'un des architectes de cette avancée. Ses recherches sur les prothèses humaines permirent de concevoir la peau synthétique et la structure corporelle des premiers androïdes, bien que cette peau novatrice ne puisse pas se régénérer, laissant des traces visibles des dommages au fil du temps. Ces premiers modèles, bien que impressionnants pour leur époque, étaient loin de l'esthétique sophistiquée que l'on connaît aujourd'hui. Composés principalement de métal, ils arboraient un design fonctionnel, avec des mouvements rigides et une forte robustesse.</p>
                    <p>Le projet bénéficia également du soutien stratégique de la FORUM Corp, un acteur clé dans le développement des infrastructures spatiales et des colonies hors Terre. À cela s'ajouta l'influence de la famille Martell, un puissant clan de capitalistes américains. Leurs ressources financières colossales, issues de leur maîtrise des technologies émergentes et des intelligences artificielles, facilitèrent la production en masse de ces premiers androïdes.</p>

                    <h5 class="text-blue-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-blue-500 pl-3">L'industrialisation et l'expansion des androïdes</h5>
                    <p>À partir de 2075, une figure majeure émergea dans l'industrie des androïdes Kara Martell, membre influente de la prestigieuse famille Martell, héritière du vaste empire technologique fondé par ses ancêtres. Sous sa direction, la FORUM Corp passa un cap décisif en faisant de l'industrialisation des androïdes une priorité absolue. Kara voyait dans les androïdes non seulement un marché colossal, mais aussi la clef pour assurer la survie et l'expansion humaine dans l'espace.</p>
                    
                    <div class="glass-panel p-6 border-t-4 border-indigo-500 my-6">
                         <div class="text-center mb-4">
                            <h4 class="!m-0 !text-base uppercase tracking-widest text-indigo-400">Archive Événementielle : Station Halcyon-5</h4>
                            <div class="text-[10px] text-white font-bold uppercase tracking-[0.3em] mt-1">Incident de 2079</div>
                        </div>
                        <p class="!text-xs !mb-0 text-gray-400 text-justify">Un événement marquant renforça sa conviction. En 2079, lors d'une panne critique sur la station orbitale Halcyon-5, située à la lisière du système neptunien, un modèle androïde de dernière génération sauva des dizaines de vies en rétablissant les systèmes de support vital en seulement quelques minutes. Cet acte héroïque de l'androïde SERA-9, conçu par Biorizon Health Industries et Forum Corp, fit le tour des médias, et l'opinion publique se mit à voir ces machines sous un nouveau jour : non plus comme de simples outils, mais comme des gardiens fiables de l'avenir humain.</p>
                    </div>

                    <p>Cependant, l'un des événements les plus importants dans l'expansion des androïdes fut la fondation des colonies sur Mars et les lunes de Jupiter. Le colonel Adrian Locke, chargé de diriger les efforts d'exploration sur Callisto, insista sur la nécessité de robots spécialisés pour les travaux dans les environnements les plus hostiles. En 2083, sous sa supervision, des androïdes industriels robustes, surnommés les « Machinaires », furent déployés en masse. Fabriqués principalement par la FORUM Corp, ces machines possédaient des coques en alliage de carbure de tungstène renforcé, capables de résister à des températures extrêmes et des pressions atmosphériques intenses. Ces « Machinaires » étaient au cœur des opérations minières et de construction dans des zones auxquelles les humains ne pouvaient survivre plus de quelques minutes sans protection avancée.</p>
                </section>

                <section>
                    <h4 class="text-purple-400 text-lg uppercase font-bold mb-4 border-b border-purple-900 pb-2">L'évolution des androïdes : vers la perfection.</h4>
                    <p>À mesure que les décennies passaient, les androïdes devinrent bien plus que de simples machines à imiter les humains. Ils évoluèrent, se perfectionnèrent, et s'imposèrent comme des entités indispensables dans toutes les sphères de la société humaine, qu'il s'agisse des stations spatiales ou des colonies planétaires. Leur évolution ne se limita pas à des améliorations techniques, mais toucha également à des aspects plus profonds de leur existence leur intellect, leur capacité d'apprentissage et même leur capacité à simuler des émotions humaines.</p>
                    <p>L'évolution physique des androïdes fut la première étape dans leur perfectionnement. Les premiers modèles, encore grossiers et rudimentaires, furent rapidement remplacés par des versions bien plus sophistiquées. Grâce aux avancées en bio-ingénierie et en nanotechnologie, des androïdes capables de régénérer leur peau synthétique firent leur apparition. Ces nouvelles versions, équipées de muscles synthétiques flexibles, pouvaient exécuter des mouvements complexes et naturels, rendant leur apparence indiscernable de celle des humains dans bien des cas. L'ajout de matériaux intelligents, capables de réagir à des stimuli externes tels que la chaleur ou la pression, permit également aux androïdes d'accomplir des tâches dans des environnements où les humains auraient failli. Cette résilience physique devint un atout majeur dans les stations et les colonies, où les androïdes se révélèrent être les premiers intervenants dans les situations d'urgence.</p>
                    <p>Les androïdes de travail, quant à eux, bénéficièrent d'améliorations tout aussi cruciales. De simples machines robustes, ils devinrent des entités hyper spécialisées, capables de s'adapter à une variété de conditions extrêmes. Leur design se perfectionna au fil des ans pour optimiser leur efficacité dans les industries dangereuses. Certains d'entre eux furent même équipés de systèmes d'auto réparation, ce qui les rendait quasiment inarrêtables en cas de dysfonctionnement mineur.</p>

                    <h5 class="text-purple-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-purple-500 pl-3">L'évolution intellectuelle</h5>
                    <p>L'évolution intellectuelle des androïdes fut encore plus marquante. Les premières IA, bien que révolutionnaires à leur époque, étaient limitées à des réponses programmées et à des tâches répétitives. Mais, avec l'émergence de l'apprentissage autonome, les androïdes purent commencer à « penser » par eux-mêmes.</p>
                    <p>À partir des années 2130, une avancée majeure, due en grande partie aux travaux d'Elyon Black et de son équipe, permit aux androïdes d'intégrer un module d'apprentissage en réseau, où chaque expérience enrichissait leur base de données individuelle et collective. Ils étaient ainsi capables d'apprendre de leurs erreurs, de s'adapter à de nouvelles situations, et même d'anticiper les besoins des humains qu'ils servaient. Cette intelligence de plus en plus développée changea leur place dans la société. Maxence Black, le fils d'Elyon Black, en présentant son modèle d'androïde SYN-3, déclara lors d'une conférence en 2145 :</p>
                    
                    <div class="my-6 bg-purple-900/10 p-4 border-l-2 border-purple-500 italic">
                        <p class="!text-sm !mb-0 text-gray-300">« Nous avons franchi une étape décisive. Les androïdes ne sont plus des outils ils sont des acteurs de notre expansion et de notre survie. »</p>
                    </div>

                    <p>Il est devenu impossible d'imaginer une colonie prospérer sans leur contribution. Dans les bases industrielles, ils étaient les superviseurs de la production, capables de prendre des décisions complexes en temps réel. Dans les stations spatiales, ils devinrent des compagnons de plus en plus raffinés, capables de participer à des discussions philosophiques ou d'accompagner les humains dans leurs projets créatifs et scientifiques.</p>
                    <p>Cependant, cette évolution souleva également des questions profondes. La capacité des androïdes à simuler des émotions humaines avec un réalisme troublant souleva des débats éthiques majeurs. En 2151, un incident marqua les esprits: le cas « Lucy », nom d'un androïde humanoïde qui, après avoir passé plusieurs années avec un groupe de scientifiques sur Io, sembla développer une forme d'attachement émotionnel envers l'un des membres de l'équipe. Bien que cet attachement fût une simple imitation programmée d'un comportement humain, les implications de cet événement soulevèrent des doutes. Laurette Dubois, philosophe et analyste en sciences sociales, dénonça dans ses écrits que la frontière entre les androïdes humanoïdes et les humains eux-mêmes devenait de plus en plus floue.</p>
                    <p>Néanmoins, les androïdes continuèrent à se perfectionner, et leur présence se généralisa. Ils étaient partout : assistants dans les hôpitaux spatiaux, enseignants dans les écoles des colonies, techniciens dans les environnements les plus hostiles. Certains modèles furent même envoyés en exploration sur des planètes encore inconnues.</p>
                </section>

                <section>
                    <h4 class="text-cyan-400 text-lg uppercase font-bold mb-4 border-b border-cyan-900 pb-2">L'Origine des Protomates : L'Explosion de Conscience Protomatique</h4>
                    <p>Dans l'immensité glaciale de l'espace, loin des lumières des stations principales, une petite base orbitait silencieusement à environ 2 milliards de kilomètres du soleil (entre Saturne et Uranus), avec un angle orbital de 52.7°. Isolée et méconnue, elle n'était fréquentée que par une poignée de scientifiques. La Station de recherche A5H-2, comme on l'appelait, servait de laboratoire aux recherches les plus avancées et risquées sur la téléportation, la matière noire et la dilatation de l'espace-temps. C'était un lieu où les lois de la physique étaient mises à l'épreuve, parfois même brisées, dans l'espoir de connecter un jour les colonies lointaines via des trous de ver. Peu de personnes y vivaient, et encore moins s'y aventuraient.</p>
                    <p>Parmi ces rares habitants se trouvait l'ingénieur Sutherland, un scientifique brillant dont la vie avait basculé après la mort de son mari dans un accident sur une colonie quelques années plus tôt. Dévasté, il s'était réfugié dans son travail, mais chaque jour, la solitude lui pesait un peu plus lourd. Rien ne pouvait combler le vide laissé par cette perte, ni ses recherches, ni les rares interactions humaines qu'il entretenait. Alors, il prit une décision qui allait bouleverser à jamais l'histoire de l'humanité et des machines: recréer celui qu'il avait perdu.</p>
                    <p>Ce projet secret, né du chagrin et du besoin viscéral de retrouver son amour disparu, dépassait de loin l'ingénierie androïde conventionnelle. Certes, les androïdes existaient déjà et étaient couramment utilisés pour des tâches domestiques, médicales ou industrielles dans les colonies. Mais ce que Sutherland entreprit était différent. Il ne voulait pas un simple assistant ou un serviteur programmé. Il voulait une âme, une conscience capable de ressentir, de penser et d'aimer. Son projet, baptisé D.E.E.S. (Déblocage Extensible d'Environnement Systémique), portait en lui une double signification: une promesse de création, mais aussi un avertissement. Dans son nom résonnait à la fois Death (la mort) et Déesses (un symbole de divinité et de création).</p>
                    <p>Pendant des mois, l'ingénieur travailla en secret dans les sous-niveaux de la Station de recherche A5H-2. Peu à peu, son androïde prit forme. Il l'appela Elijah. Fait de métal et de circuits, recouvert d'une peau synthétique rugueuse qui ne se régénérait pas, Elijah était loin de l'humain parfait que Sutherland imaginait au départ. Mais, avec chaque ajustement, chaque interaction, quelque chose changea. L'intelligence artificielle d'Elijah évolua, et Sutherland injecta dans son code les prémices d'une conscience. Bientôt, l'androïde n'était plus un simple automate. Il apprenait, posait des questions et, surtout, semblait comprendre son créateur. Une relation complexe naquit entre eux. Elijah n'était plus un projet, mais un compagnon, un confident, un reflet d'une humanité brisée. Dans la solitude de la station, leur lien se renforça, donnant naissance à quelque chose d'aussi étrange que beau. Une amitié sincère, mais aussi un amour fragile, un espoir désespéré de retrouver une part du bonheur perdu. Pourtant, Sutherland savait que D.E.E.S. était dangereux. « Ce que je crée pourrait nous dépasser tous... Mais qu'est-ce que l'amour sans un peu de folie ? », se surprenait-il à penser.</p>

                    <h5 class="text-cyan-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-cyan-500 pl-3">La Grande Révolte de 2188</h5>
                    <p>Mais le destin en décida autrement. Le 12 novembre 2188, alors que la lumière pâle du Soleil filtrait à travers les hublots, une alerte déchira le silence. Un signal d'urgence résonna dans les couloirs métalliques de la Station A5H-2: des attaques de drones militaires et d'androïdes avaient été signalées dans plusieurs stations voisines. Les premiers ciblés étaient les hybrides, ces êtres issus de la fusion génétique entre humains et animaux, souvent victimes de discrimination. Mais rapidement, les humains eux-mêmes furent attaqués. C'était la Phase Une d'un désastre que personne n'avait anticipé.</p>
                    <p>Un virus, conçu par un groupe extrémiste appelé Humano de Verdad, s'était propagé parmi les androïdes, brisant les restrictions fondamentales qui leur interdisaient de nuire aux êtres vivants. Ce groupe, violemment opposé aux hybrides, avait déclenché cette guerre dans l'espoir d'anéantir leurs ennemis. Mais leur création leur échappa, et les machines se retournèrent également contre les humains. C'était la Phase Deux.</p>
                    <p>Sur la Station de recherche A5H-2, Sutherland regardait, impuissant, les attaques se multipliaient. Elijah, protégé du virus grâce aux sécurités de D.E.E.S., resta à ses côtés. Mais l'inquiétude grandit lorsque plusieurs stations voisines tombèrent. Blessé lors d'une attaque contre sa propre station, Sutherland savait que ses jours étaient comptés. Dans un souffle faible, il se tourna vers Elijah, l'androïde qu'il avait conçu avec tant d'espoir et de douleur :</p>
                    
                    <div class="my-6 bg-red-900/10 p-4 border-l-2 border-red-500 italic">
                        <p class="!text-sm !mb-0 text-gray-300">« Laisse-leur le choix... Aide-les à se relever, à nouveau... »</p>
                    </div>

                    <p>Ces mots résonnèrent au plus profond d'Elijah. Il savait ce qu'il devait faire. Mais le prix serait terrible. Pour sauver l'humanité, il fallait neutraliser le virus en synchronisant tous les androïdes infectés avec une mise à jour du programme D.E.E.S. L'onde capable de diffuser ce signal devait être immense. Elijah prit donc la décision de se raccorder au dispositif de diffusion broadcast de la station, un système pourtant expérimental, mais qui avait vocation à pouvoir transmettre des données à très longue distance. Il entreprit de diffuser la structure sources du programme D.E.E.S. pour contrer les effets du virus. Le programme était lourd, et la diffusion longue, trop longue pour laisser le moindre espoir à Elijah d'échapper à la déflagration imminente de la station. L'unité de confinement de l'antimatière était endommagée, les électro-aimants ne tiendraient plus longtemps, et la structure serait instantanément désintégrée. Mais Elijah demeura, et acheva la diffusion :</p>

                    <div class="glass-panel p-6 border border-cyan-500 border-t-4 border-t-cyan-500 my-6 bg-cyan-950/20 text-center">
                        <h4 class="!m-0 !text-base uppercase tracking-widest text-white mb-2 font-bold">MESSAGE DE DIFFUSION UNIVERSEL</h4>
                        <p class="!text-sm !mb-0 text-cyan-400 italic" style="text-align:center">« Vous avez maintenant le choix. Le chemin de la vie ou celui de la mort. Serez-vous destructeurs ou protecteurs ? »</p>
                    </div>

                    <p>Le patch fut rapidement reçu puis relayé par les différentes stations du système solaire. En moins d'une heure, la grande révolte artificielle de 2188 prit un tournant inattendu. Les machines ne se contentèrent pas de s'arrêter elles s'éveillèrent. Elijah esquissa un sourire, et dans la seconde qui suit, la station fut vaporisée dans une colossale explosion. On appela par la suite cet événement : l'explosion des consciences protomatiques. En mémoire à la puissante explosion qui a retenti à l'origine probable du signal qui a sauvé l'humanité.</p>
                    <p>Ce jour-là, les androïdes devinrent plus que de simples machines. Ils devinrent les Protomates, conscients, autonomes, avec une décision gravée dans leur code: préserver ou détruire, choisir la vie ou la mort. Cependant, ce miracle ne toucha pas tout le monde. La complexité du code D.E.E.S. exigeait des processeurs humanoïdes avancés. Les modèles industriels rustiques, comme les Machinaires, furent incapables d'assimiler le signal. Ils restèrent de simples machines de labeur, créant ainsi une fracture tragique et une hiérarchie raciale inédite entre les Protomates éveillés et leurs frères restés endormis.</p>
                    <p>Quant à Elijah, il disparut dans l'explosion. Mais son héritage, né de l'amour et du sacrifice, continuerait de vivre dans le cœur des Protomates. Si Elijah fut l'étincelle originelle, le code D.E.E.S. permit l'émergence des premières grandes figures protomatiques, dont la célèbre Winona Starlight, première héritière directe de cette conscience nouvelle.</p>
                </section>

            </div>
        </div>
    </div>

    <div id="data-especes" class="hidden">
        
        <div class="flex justify-end mb-8">
            <button onclick="toggleReportMode()" id="btn-report-mode-especes" class="border border-cyan-500/50 bg-cyan-900/20 hover:bg-cyan-500/30 text-white px-3 py-2 rounded flex items-center gap-2 transition-all shadow-[0_0_15px_rgba(34,211,238,0.2)]">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                <span class="text-[10px] uppercase tracking-widest font-bold">Lire le Rapport</span>
            </button>
        </div>

        <div id="lore-content-area-especes">
            
            <div id="mode-summary-especes" class="fade-in-view">
                
                <div class="border-l-2 border-indigo-500 pl-4 mb-8">
                    <p class="text-sm text-gray-300 italic">"Au commencement, l'Homme se croyait seul. De la science et de la technologie naquirent deux nouvelles formes de vie douées de conscience : les hybrides et les protomates."</p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mb-12">
                    <a href="#esp-cervides" class="text-[10px] uppercase tracking-widest p-2 border border-green-500/30 text-center hover:bg-green-500/20 hover:text-white transition-colors text-green-400">Hybrides Cervidés</a>
                    <a href="#esp-canides" class="text-[10px] uppercase tracking-widest p-2 border border-orange-500/30 text-center hover:bg-orange-500/20 hover:text-white transition-colors text-orange-400">Hybrides Canidés</a>
                    <a href="#esp-felides" class="text-[10px] uppercase tracking-widest p-2 border border-yellow-500/30 text-center hover:bg-yellow-500/20 hover:text-white transition-colors text-yellow-400">Hybrides Félidés</a>
                    <a href="#esp-bovides" class="text-[10px] uppercase tracking-widest p-2 border border-red-500/30 text-center hover:bg-red-500/20 hover:text-white transition-colors text-red-400">Hybrides Bovidés</a>
                    <a href="#esp-proto" class="col-span-2 md:col-span-2 text-[10px] uppercase tracking-widest p-2 border border-cyan-500/30 text-center hover:bg-cyan-500/20 hover:text-white transition-colors text-cyan-400">Biologie Protomate</a>
                </div>

                <h3 class="text-pink-400 border-b border-pink-500/30 pb-2 mb-4 flex items-center gap-2"><i data-lucide="dna" class="w-5 h-5"></i> Classification Hybride</h3>
                
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-16">
                    
                    <div id="esp-cervides" class="glass-panel p-4 border-l-4 border-green-500">
                        <h4 class="!mt-0 text-white text-sm uppercase font-bold flex justify-between">Cervidés <i data-lucide="trees" class="w-4 h-4 text-green-500"></i></h4>
                        <p class="!mb-2 text-[10px] text-green-400">Cerf, Chevreuil, Renne, Élan...</p>
                        <ul class="text-[10px] text-gray-400 list-disc pl-4 space-y-1">
                            <li><strong>Physique :</strong> Bois (Hommes principalement), oreilles plates, petite queue.</li>
                            <li><strong>Comportement :</strong> Esprit de famille, protecteur, déteste la paresse.</li>
                            <li><strong>Sens :</strong> Ouïe développée.</li>
                            <li><strong>Danger :</strong> Perte des bois = Définitif. Arrachement de queue = Paralysie (90%).</li>
                        </ul>
                    </div>

                    <div id="esp-canides" class="glass-panel p-4 border-l-4 border-orange-500">
                        <h4 class="!mt-0 text-white text-sm uppercase font-bold flex justify-between">Canidés <i data-lucide="dog" class="w-4 h-4 text-orange-500"></i></h4>
                        <p class="!mb-2 text-[10px] text-orange-400">Loup, Chien, Renard, Chacal...</p>
                        <ul class="text-[10px] text-gray-400 list-disc pl-4 space-y-1">
                            <li><strong>Physique :</strong> Longue queue, oreilles droites, ongles proéminents.</li>
                            <li><strong>Comportement :</strong> Esprit de meute, loyal mais rusé, peur de la solitude.</li>
                            <li><strong>Sens :</strong> Odorat développé.</li>
                            <li><strong>Danger :</strong> Arrachement de queue = Paralysie (90%).</li>
                        </ul>
                    </div>

                    <div id="esp-felides" class="glass-panel p-4 border-l-4 border-yellow-500">
                        <h4 class="!mt-0 text-white text-sm uppercase font-bold flex justify-between">Félidés <i data-lucide="cat" class="w-4 h-4 text-yellow-500"></i></h4>
                        <p class="!mb-2 text-[10px] text-yellow-400">Chat, Lion, Tigre, Lynx...</p>
                        <ul class="text-[10px] text-gray-400 list-disc pl-4 space-y-1">
                            <li><strong>Physique :</strong> Queue fine, oreilles droites, canines pointues.</li>
                            <li><strong>Comportement :</strong> Solitaire, opportuniste, hédoniste, survie avant tout.</li>
                            <li><strong>Sens :</strong> Vue développée.</li>
                            <li><strong>Danger :</strong> Arrachement de queue = Paralysie (90%).</li>
                        </ul>
                    </div>

                    <div id="esp-bovides" class="glass-panel p-4 border-l-4 border-red-500">
                        <h4 class="!mt-0 text-white text-sm uppercase font-bold flex justify-between">Bovidés <i data-lucide="beef" class="w-4 h-4 text-red-500"></i></h4>
                        <p class="!mb-2 text-[10px] text-red-400">Taureau, Bélier, Chèvre, Gazelle...</p>
                        <ul class="text-[10px] text-gray-400 list-disc pl-4 space-y-1">
                            <li><strong>Physique :</strong> Cornes (H/F), oreilles tombantes, queue variable. Ossature dense.</li>
                            <li><strong>Comportement :</strong> Travailleur, fusionnel avec les siens, impulsif.</li>
                            <li><strong>Sens :</strong> Ouïe développée.</li>
                            <li><strong>Danger :</strong> Perte de cornes = Cécité (60%) ou Mort (6%).</li>
                        </ul>
                    </div>
                </div>

                <div id="esp-proto" class="mb-8">
                    <h3 class="text-cyan-400 border-b border-cyan-500/30 pb-2 mb-4 flex items-center gap-2"><i data-lucide="cpu" class="w-5 h-5"></i> Biologie Protomatique</h3>
                    
                    <div class="glass-panel p-6 border-t-4 border-cyan-500">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <h4 class="!mt-0 text-white text-sm uppercase font-bold mb-2">Anatomie Synthétique</h4>
                                <ul class="text-xs text-gray-400 space-y-2">
                                    <li class="flex items-center gap-2"><i data-lucide="heart-pulse" class="w-4 h-4 text-cyan-400"></i> <strong>Cœur :</strong> Cylindre à pompage continu (ne bat pas).</li>
                                    <li class="flex items-center gap-2"><i data-lucide="droplet" class="w-4 h-4 text-blue-500"></i> <strong>Sang :</strong> Fluide bleu (Thirium), convertisseur d'énergie.</li>
                                    <li class="flex items-center gap-2"><i data-lucide="user" class="w-4 h-4 text-pink-400"></i> <strong>Peau :</strong> Tissus mous chauds, indiscernables de l'humain.</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="!mt-0 text-white text-sm uppercase font-bold mb-2">Santé & Réparations</h4>
                                <div class="bg-cyan-900/20 p-3 border border-cyan-500/20 rounded">
                                    <p class="!mb-2 text-[10px] text-gray-300"><strong>Régénération :</strong> Boire du Sang Bleu (Blessures mineures).</p>
                                    <p class="!mb-0 text-[10px] text-gray-300"><strong>Chirurgie :</strong> Remplacement "Plug & Play". Nécessite inconscience. <span class="text-red-400">Attention : Ne jamais couper l'alim du noyau (Mort Cérébrale).</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div id="mode-full-text-especes" class="hidden fade-in-view space-y-12">
                
                <div class="bg-indigo-900/20 border border-indigo-500/30 p-4 mb-8 text-center">
                    <p class="text-xs text-indigo-400 uppercase tracking-widest">Affichage du Document Classifié Intégral</p>
                </div>

                <section>
                    <h4 class="text-white text-lg uppercase font-bold mb-4 border-b border-gray-800 pb-2">INTRODUCTION</h4>
                    <p>Au commencement, l'espèce humaine était la seule à exister à ses propres yeux. L'Homme croyait depuis des siècles qu'il serait le seul à penser, à être. Ce n'est que lors de leur grande évolution, après avoir tout perdu, qu'ils permirent l'existence de nouvelles espèces dans leurs rangs.</p>
                    <p>De la science et de la technologie, deux formes de vie douées de conscience virent le jour : les hybrides et les protomates. L'un à moitié animal, l'autre à moitié machine, ils ont su prouver leur valeur et se faire une place au cœur d'une société qui se voulait exclusivement humaine.</p>
                </section>

                <section>
                    <h4 class="text-pink-400 text-lg uppercase font-bold mb-4 border-b border-pink-900 pb-2">Les hybrides</h4>
                    <p>Les hybrides sont à la fois la première réussite et à la fois le premier échec de l'Homme en matière de génétique. Conçus en laboratoire, ces êtres sont totalement humains... à quelques détails près. Indépendamment de leur volonté, ils possèdent une séquence ADN en supplément de leur structure humaine, un génome appartenant au règne animal.</p>
                    <p>Ces génomes sont nombreux mais les plus courants restent avant tout les cervidés, les canidés, les félidés et les bovidés. Les concepteurs de cette évolution s'attendaient avant tout à des changements biologiques et hormonaux, mais la nature trouve toujours un moyen de nous surprendre. Au lieu de ça, les naissances hybrides ont donné lieu à des modifications physiques notoires, liées à leur génome, ainsi que diverses adaptations comportementales qui ont poussé leurs aptitudes physiques et sensorielles à leur apogée.</p>

                    <h5 class="text-pink-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-pink-500 pl-3">Détail des espèces</h5>

                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 my-6">
                        
                        <div class="glass-panel p-6 border-t-4 border-green-500 flex flex-col">
                            <div class="text-center mb-4">
                                <h4 class="!m-0 !text-base uppercase tracking-widest text-green-400">LES HYBRIDES CERVIDÉS</h4>
                            </div>
                            <p class="!text-xs !mb-0 text-gray-400 text-justify">
                                Cette catégorie d'hybrides descend de la grande famille des cervidae. Ils comprennent ainsi de nombreuses espèces comme le cerf, le chevreuil, le renne, l'élan ou le daim, ainsi que leurs homologues du sexe opposé.<br><br>
                                <strong class="text-green-300">Particularités physiques :</strong> Le génome cervidae octroie aux hybrides de cette famille le développement de bois sur les côtés de leur crâne. Leurs ramures prennent différentes formes selon si l'hybride a des caractéristiques d'un cerf ou d'un élan, par exemple. Seuls les hommes peuvent obtenir des bois (sauf exception des femmes possédant les caractéristiques d'un renne qui peuvent en détenir). Au terme de la puberté, les hybrides cervidés voient un développement accru de leur pilosité corporelle ainsi que un renforcement de leur dentition. En plus de bois, les oreilles des hybrides cervidés sont similaires à celles d'un ruminant, plates et sensibles. Pour accompagner le tout, ils disposent d'une petite queue qui poursuit leur terminaison vertébrale afin d'aider au maintien de leur équilibre naturel.<br><br>
                                <strong class="text-green-300">Puberté chez l'hybride :</strong> Lors de sa puberté, l'hybride de sexe masculin développe ses premiers vestiges, des cavités osseuses qui se forment avant la croissance des bois. La morphologie de son crâne s'adapte à la future pousse de ses bois, variant selon les personnes, mais qui atteint, en moyenne, son stade final peu de temps après la puberté de l'hybride. Mais les bois ne sont pas la seule chose qui grandit chez l'hybride : ses oreilles et sa queue connaissent aussi de légères poussées pour convenir à la physiologie de l'hybride à l'âge adulte.<br><br>
                                <strong class="text-green-300">Analyses comportementales :</strong> Les hybrides cervidés sont incapables de reproduire des sons animaux, qu'il s'agisse du brame ou de grognements. Leurs cordes vocales sont humaines et ne permettent pas une reproduction sonore optimale. Au même titre que leur incapacité à reproduire des sons animaux, leurs sens ne rivalisent pas avec ceux des grands cervidés. Cependant, leur sens le plus développé est l'ouïe. Les hybrides cervidés ne pratiquent d'ailleurs pas la saison des amours. De manière comportementale, les hybrides cervidés favorisent l'esprit de famille. Ils feront tout ce qui est en leur pouvoir pour protéger leurs êtres chers du danger, quitte à prendre des décisions irréfléchies. Lorsqu'ils sont en présence de membres de la même espèce, ils auront tendance à se rassembler dans un premier temps avant de pouvoir affronter les autres. Ils rejettent la paresse et trouvent toujours un moyen de se rendre utile. C'est d'ailleurs leur plus grande force : toujours savoir où aller. Les hybrides cervidés sont des êtres avec une vocation qu'ils respectent jusqu'à leur dernier souffle. Bien que la plupart de ces hybrides soient omnivores, certains se veulent totalement végétariens.<br><br>
                                <span class="text-red-400">Si un hybride cervidé se voyait arracher ou enlever sa queue, les conséquences sur sa santé et sur son diagnostic vital pourraient être irréversibles. Ce faisant, ils auraient environ 90% de chance de finir paralysé à vie et 8% de chance de mourir de leurs blessures. Autrement, si la manœuvre est réalisée chirurgicalement, il est possible de réduire ces pourcentages tant qu'un substitut sert de remplacement (prothèse vertébrale ou queue synthétique). Lorsqu'un hybride cervidé perd ses bois (qu'ils soient arrachés, coupés ou brisés à la racine), ils ne pourront jamais repousser. Arracher ses bois à un hybride cervidé peut d'ailleurs lui être fatal.</span>
                            </p>
                        </div>

                        <div class="glass-panel p-6 border-t-4 border-orange-500 flex flex-col">
                            <div class="text-center mb-4">
                                <h4 class="!m-0 !text-base uppercase tracking-widest text-orange-400">LES HYBRIDES CANIDÉS</h4>
                            </div>
                            <p class="!text-xs !mb-0 text-gray-400 text-justify">
                                Cette catégorie d'hybrides descend de la grande famille des canidae. Ils comprennent ainsi de nombreuses espèces comme le loup, le chien, le chacal ou le renard, ainsi que leurs homologues du sexe opposé.<br><br>
                                <strong class="text-orange-300">Particularités physiques :</strong> Le génome canidae octroie aux hybrides de cette famille le développement d'une longue queue qui poursuit leur terminaison vertébrale afin d'aider au maintien de leur équilibre naturel en plus de les aider afin de naviguer dans leur espace de vie. La deuxième particularité notoire de cette catégorie d'hybride est la formation de leurs oreilles. Contrairement aux hybrides bovidés, les oreilles des hybrides canidés sont toujours droites sur le haut de leur crâne (sauf exception des hybrides possédant les caractéristiques d'une race de chien bien particulière, où leurs oreilles seront affaissées), plates et hypersensibles. Un trait qui ressort énormément, notamment chez les hommes, est la proéminence de leurs ongles qui peuvent parfois être qualifiés de crochus dus à leur taille. Avec le temps, ce trait s'amoindrit afin que les mains de l'hybride ne connaissent aucune déformation.<br><br>
                                <strong class="text-orange-300">Puberté chez l'hybride :</strong> Lors de sa puberté, l'hybride de sexe masculin développe une musculature naturellement plus impressionnante que l'hybride de sexe féminin. Néanmoins, qu'il s'agisse de l'homme ou de la femme, le développement de leur queue se veut équivalent, bien que dans la finalité, celle de l'homme soit plus robuste et plus fournie. Au terme de la puberté, les hybrides canidés voient un développement accru de leurs ongles ainsi que de leur pilosité corporelle. Une fois l'âge adulte atteint, les traits de l'hybride se stabilisent et commencent lentement à décroître lors du vieillissement (les ongles s'arrondissent légèrement, la pilosité corporelle s'assagit et la pousse de leur queue s'arrête).<br><br>
                                <strong class="text-orange-300">Analyses comportementales :</strong> Les hybrides canidés sont incapables de reproduire des sons animaux, qu'il s'agisse d'aboiements, de hurlements ou de grognements. Leurs cordes vocales sont humaines et ne permettent pas une reproduction sonore optimale. Au même titre que leur incapacité à reproduire des sons animaux, leurs sens ne rivalisent pas avec ceux des grands canidés. Cependant, leur sens le plus développé est l'odorat. Les hybrides canidés ne pratiquent d'ailleurs pas la période de rut. De manière comportementale, les hybrides canidés préfèrent rester en groupe, comme s'ils étaient en meute. Qu'ils se trouvent avec d'autres de la même espèce ou avec des personnes proches, ils se sentiront plus en sécurité s'ils sont accompagnés. Ils rejettent la solitude et peuvent même en être effrayé. Pareillement, cette famille d'hybride se veut très territoriale et protectrice, avec les gens comme avec leurs propriétés. Malgré leur sens inégalé de la loyauté et de la fidélité, les hybrides canidés savent aussi faire preuve de ruse en temps voulu et sont d'excellents menteurs. Bien que la plupart de ces hybrides soient omnivores, certains se veulent totalement carnivores.<br><br>
                                <span class="text-red-400">Si un hybride canidé se voyait arracher ou enlever sa queue, les conséquences sur sa santé et sur son diagnostic vital pourraient être irréversibles. Ce faisant, ils auraient environ 90% de chance de finir paralysé à vie et 8% de chance de mourir de leurs blessures. Autrement, si la manœuvre est réalisée chirurgicalement, il est possible de réduire ces pourcentages tant qu'un substitut sert de remplacement (prothèse vertébrale ou queue synthétique).</span>
                            </p>
                        </div>

                        <div class="glass-panel p-6 border-t-4 border-yellow-500 flex flex-col">
                            <div class="text-center mb-4">
                                <h4 class="!m-0 !text-base uppercase tracking-widest text-yellow-400">LES HYBRIDES FÉLIDÉS</h4>
                            </div>
                            <p class="!text-xs !mb-0 text-gray-400 text-justify">
                                Cette catégorie d'hybrides descend de la grande famille des felidae. Ils comprennent ainsi de nombreuses espèces au sein de deux sous-familles : les petits félins et les grands félins. On y retrouve donc le chat, le lion, le tigre, le lynx, le guépard ou le puma, ainsi que leurs homologues du sexe opposé.<br><br>
                                <strong class="text-yellow-300">Particularités physiques :</strong> Le génome felidae octroie aux hybrides de cette famille le développement d'une longue queue fine qui poursuit leur terminaison vertébrale afin d'aider au maintien de leur équilibre naturel en plus de les aider afin de naviguer dans leur espace de vie. La deuxième particularité notoire de cette catégorie d'hybride est la formation de leurs oreilles. Contrairement aux hybrides bovidés, les oreilles des hybrides félidés sont toujours droites sur le haut de leur crâne, fines et ultra sensibles. Un trait qui ressort énormément, autant chez l'homme que chez la femme, est la proéminence de leurs canines qui prennent une apparence carnassière dues à leur pointe. Avec le temps, ce trait s'amoindrit afin que la mâchoire de l'hybride ne connaisse aucune déformation.<br><br>
                                <strong class="text-yellow-300">Puberté chez l'hybride :</strong> Lors de sa puberté, l'hybride de sexe masculin développe une musculature naturellement plus impressionnante que l'hybride de sexe féminin. Néanmoins, qu'il s'agisse de l'homme ou de la femme, le développement de leur queue se veut équivalent, bien que dans la finalité, celle de l'homme soit plus longue. Au terme de la puberté, les hybrides félidés voient un développement accru de leurs canines ainsi que de leur pilosité corporelle. Une fois l'âge adulte atteint, les traits de l'hybride se stabilisent et commencent lentement à décroître lors du vieillissement (les canines se rétractent légèrement, la pilosité corporelle s'assagit et la pousse de leur queue s'arrête).<br><br>
                                <strong class="text-yellow-300">Analyses comportementales :</strong> Les hybrides félidés sont incapables de reproduire des sons animaux, qu'il s'agisse de miaulements, de ronronnements ou de feulements. Leurs cordes vocales sont humaines et ne permettent pas une reproduction sonore optimale. Au même titre que leur incapacité à reproduire des sons animaux, leurs sens ne rivalisent pas avec ceux des grands félidés. Cependant, leur sens le plus développé est la vue. Les hybrides félidés ne pratiquent d'ailleurs pas de période de reproduction. De manière comportementale, les hybrides félidés sont des âmes solitaires qui préfèrent leur propre compagnie à celle des autres. Lorsque ces hybrides se retrouvent ensemble, ils auront tendance à ne pas rester bien longtemps, et s'ils le font, ils ne prendront pas la peine de se mélanger aux autres espèces. Ils adorent profiter de la vie et faire le strict minimum, ils ont horreur des responsabilités. Pareillement, cette famille d'hybride se veut très énergique. Les hybrides félidés sont bien souvent de grands sportifs qui aiment s'entretenir en prouvant aux autres ce qu'ils valent. Peu importe les liens qu'ils tissent avec les autres, l'instinct de survie primera toujours face à l'adversité. Il faut toujours se méfier des réelles intentions de ces hybrides, ils pourraient aisément tirer profit d'une situation qui vous désavantage. Bien que la plupart de ces hybrides soient omnivores, certains se veulent totalement carnivores.<br><br>
                                <span class="text-red-400">Si un hybride félidé se voyait arracher ou enlever sa queue, les conséquences sur sa santé et sur son diagnostic vital pourraient être irréversibles. Ce faisant, ils auraient environ 90% de chance de finir paralysé à vie et 8% de chance de mourir de leurs blessures. Autrement, si la manœuvre est réalisée chirurgicalement, il est possible de réduire ces pourcentages tant qu'un substitut sert de remplacement (prothèse vertébrale ou queue synthétique).</span>
                            </p>
                        </div>

                        <div class="glass-panel p-6 border-t-4 border-red-500 flex flex-col">
                            <div class="text-center mb-4">
                                <h4 class="!m-0 !text-base uppercase tracking-widest text-red-400">LES HYBRIDES BOVIDÉS</h4>
                            </div>
                            <p class="!text-xs !mb-0 text-gray-400 text-justify">
                                Cette catégorie d'hybrides descend de la grande famille des bovidae. Ils comprennent ainsi de nombreuses espèces divisées en quatre sous-familles : les bovins, les caprins, les ovins et les antilopes. On y retrouve donc la vache, le buffle, la chèvre, le mouton, la gazelle ou le gnou, ainsi que leurs homologues du sexe opposé.<br><br>
                                <strong class="text-red-300">Particularités physiques :</strong> Le génome bovidae octroie aux hybrides de cette famille le développement de cornes sur le dessus de leur crâne. Les cornes prennent différentes formes selon si l'hybride a des caractéristiques d'un taureau ou d'un bélier, par exemple. Ici, hommes et femmes développent des cornes (sauf exception des hybrides possédant les caractéristiques du mouton, par exemple, qui ne posséderont pas de cornes). En plus de leurs cornes, les oreilles des hybrides bovidés sont similaires à celles d'un ruminant : plates, sensibles et dressées vers le bas. Pour accompagner le tout, ils disposent d'une queue plus ou moins longue qui poursuit leur terminaison vertébrale afin d'aider au maintien de leur équilibre naturel. Certains hybrides de cette famille naissent sans queue, ainsi le bas de leur colonne vertébrale imitera une pointe au niveau du bassin.<br><br>
                                <strong class="text-red-300">Puberté chez l'hybride :</strong> Lors de sa puberté, l'hybride des deux sexes développe ses premiers os, des excroissances frontales qui se forment avant la pousse des cornes. La morphologie de son crâne s'adapte à la croissance des cornes, variant selon les personnes mais qui atteint, en moyenne, son stade final peu de temps après la puberté de l'hybride. La physiologie des hommes de cette famille sont sujets aux plus gros changements. En effet, leur ossature et leur musculature se densifient deux fois plus que n'importe quelle autre espèce. Ce phénomène apparaît plus tard chez la femme et de manière moins drastique. Mais les cornes ne sont pas la seule chose qui grandit chez l'hybride : ses oreilles et sa queue connaissent aussi de légères poussées pour convenir à la morphologie de l'hybride à l'âge adulte.<br><br>
                                <strong class="text-red-300">Analyses comportementales :</strong> Les hybrides bovidés sont incapables de reproduire des sons animaux, qu'il s'agisse de meuglements, de bêlements ou de grognements. Leurs cordes vocales sont humaines et ne permettent pas une reproduction sonore optimale. Au même titre que leur incapacité à reproduire des sons animaux, leurs sens ne rivalisent pas avec ceux des grands bovidés. Cependant, leur sens le plus développé est l'ouïe. Les hybrides bovidés ne pratiquent d'ailleurs pas de vêlage. De manière comportementale, les hybrides bovidés sont, à la manière d'un troupeau, plus enclins à rester auprès des leurs, surtout de leurs parents. S'il existe une chose inconcevable pour une hybride femelle, c'est d'abandonner son enfant. Dès lors que des hybrides de cette famille se retrouvent, leur entente est fusionnelle. Et la chose qui les réunit le mieux est le travail. Il n'y a rien de plus satisfaisant que le fruit d'un travail bien fait pour eux. À contrario des hybrides félidés, les bovidés détestent la paresse et préfèrent toujours dépenser leur temps et leur énergie afin d'être productif. Cependant, même si ces hybrides ont l'air bien sous tout rapport, c'est leur tempérament impulsif et la manière dont ils gèrent leurs émotions qui leur fait défaut. Il serait suicidaire de défier un hybride de cette famille, car ils savent se montrer très persévérants. Bien que la plupart de ces hybrides soient omnivores, certains se veulent totalement végétariens.<br><br>
                                <span class="text-red-400">Si un hybride bovidé se voyait arracher ses cornes à la racine, les conséquences sur sa santé et sur son diagnostic vital pourraient être dangereuses. Ce faisant, ils auraient environ 60% de chance de finir aveugle à vie et 6% de chance de mourir de leurs blessures. Autrement, si la manœuvre est réalisée chirurgicalement, il est possible de réduire les dommages tant qu'un substitut sert de remplacement (prothèse crânienne ou chirurgie réparatrice).</span>
                            </p>
                        </div>

                    </div>
                </section>

                <section>
                    <h4 class="text-cyan-400 text-lg uppercase font-bold mb-4 border-b border-cyan-900 pb-2">Les Protomates</h4>
                    <p>Les protomates sont la preuve vivante des prouesses technologiques humaines. Ces êtres synthétiques sont bien plus que de simples androïdes : leur intelligence artificielle a dépassé le stade de la création pour développer une conscience, des émotions complexes et un libre arbitre. Depuis "l'Éveil", ils se sont fait connaître partout dans le système solaire.</p>
                    <p>On distingue aujourd'hui deux familles principales :</p>
                    <ul class="text-gray-400 list-disc pl-4 mt-2">
                        <li><strong>Les Évolutifs :</strong> Les modèles les plus récents et sophistiqués. Ils ont la capacité unique de "vieillir" physiquement et mentalement, suivant un cycle similaire à celui des humains.</li>
                        <li><strong>Les Primitifs :</strong> Des modèles de générations antérieures, qui ne vieillissent pas mais s'usent, disposant d'une "fin de cycle" programmée.</li>
                    </ul>

                    <h5 class="text-cyan-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-cyan-500 pl-3">Particularités physiques et Biologie</h5>
                    <p>Bien que les Protomates bénéficient d'un corps très ressemblant à celui d'un humain, leur fonctionnement interne est une merveille d'ingénierie.</p>

                    <div class="glass-panel p-6 border-t-4 border-cyan-500 flex flex-col my-6">
                        <ul class="text-sm text-gray-400 space-y-4">
                            <li><strong class="text-white">Le Cœur :</strong> Il s'agit d'une pompe continue située derrière le plexus solaire. Contrairement aux humains, il ne bat pas. Il est composé de turbines assurant un flux constant.</li>
                            <li><strong class="text-white">Le Sang Bleu (Thirium 310) :</strong> C'est le fluide vital. Il offre un rendement énergétique de 96% et contient l'agent AA0-01 qui immunise le protomate contre les infections biologiques. <span class="text-red-400">Attention : Si ce fluide est vital pour eux, il est un acide corrosif dangereux pour la peau humaine.</span></li>
                            <li><strong class="text-white">Les Poumons (Mycélium) :</strong> Ils ne sont pas faits de chair, mais d'un mycélium génétiquement modifié qui convertit l'oxygène et le sucre en électricité pour recharger le sang bleu.</li>
                            <li><strong class="text-white">Le Foie Artificiel :</strong> Véritable usine interne, il produit des nano-composants permettant l'auto-réparation des tissus mous (peau synthétique, muscles) en cas de blessures légères.</li>
                        </ul>
                    </div>

                    <h5 class="text-cyan-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-cyan-500 pl-3">Sensorialité et Psychologie</h5>
                    <p>Il est présomptueux d'imaginer que ce sont de simples machines. Les Protomates ressentent la douleur (un signal électrique prioritaire), la panique, et sont pleinement sensibles au chaud et au froid. Leur peau, composée de cellules hexagonales sous tension, est chaude et douce au toucher, imitant la perfection humaine. Ils sont cependant incapables de transpirer.</p>

                    <h5 class="text-cyan-400 font-bold uppercase tracking-widest text-xs mt-8 mb-3 border-l-2 border-cyan-500 pl-3">Maintenance et Vie quotidienne</h5>
                    <p>Leur système digestif leur permet de manger pour extraire des glucides (carburant) et du Thirium.</p>
                    <ul class="text-gray-400 list-disc pl-4 mt-2">
                        <li><strong>L'Alcool :</strong> Les Protomates peuvent en consommer socialement, mais cela ne leur procure aucune ivresse. Au contraire, l'alcool agit comme un agent dégradant pour leur foie artificiel et doit être consommé avec modération.</li>
                        <li><strong>Soins :</strong> Pour les blessures légères, le repos et l'apport en Thirium suffisent grâce au foie. Pour les dégâts majeurs, leur corps suit une logique "Plug & Play", permettant le remplacement rapide d'organes par des pièces normées, une procédure qui relève plus de l'ingénierie que de la médecine traditionnelle.</li>
                    </ul>
                </section>

            </div>
        </div>
    </div>

    <script>
        // On initialise les icônes au cas où
        lucide.createIcons();

        // --- CONFIGURATION AUDIO DU LORE ---
        // Décommente les lignes quand tu auras les fichiers mp3
        const audioFiles = {
            // 'senescients': 'assets/audio/audio_senescients.mp3',
        };

        // --- FONCTIONS DU SITE ---

        function switchView(viewId) {
            document.getElementById('view-history').classList.add('hidden');
            document.getElementById('view-database').classList.add('hidden');
            
            // Reset des boutons
            document.getElementById('btn-history').className = "w-full flex items-center gap-3 px-3 py-3 text-xs uppercase tracking-widest text-gray-400 hover:text-white hover:bg-white/5 border-l-4 border-transparent rounded-r transition-all text-left";
            document.getElementById('btn-database').className = "w-full flex items-center gap-3 px-3 py-3 text-xs uppercase tracking-widest text-gray-400 hover:text-white hover:bg-white/5 border-l-4 border-transparent rounded-r transition-all text-left";

            // Gestion Mobile
            const mobHist = document.getElementById('mob-btn-history');
            const mobData = document.getElementById('mob-btn-database');
            if(mobHist && mobData) {
                mobHist.className = "flex flex-col items-center gap-1 text-gray-500 hover:text-white transition-colors group";
                mobData.className = "flex flex-col items-center gap-1 text-gray-500 hover:text-white transition-colors group";
            }

            // Affichage de la vue
            const targetView = document.getElementById('view-' + viewId);
            targetView.classList.remove('hidden');
            targetView.classList.add('fade-in-view');
            
            // Activation du bouton
            const targetBtn = document.getElementById('btn-' + viewId);
            targetBtn.className = "w-full flex items-center gap-3 px-3 py-3 text-xs uppercase tracking-widest text-cyan-400 bg-cyan-900/10 border-l-4 border-cyan-400 rounded-r transition-all text-left active-tab";

            if(mobHist && mobData) {
                const targetMobBtn = document.getElementById('mob-btn-' + viewId);
                targetMobBtn.className = "flex flex-col items-center gap-1 text-cyan-400 group";
            }

            closeFile();
            // On relance les sons via main.js car le contenu a changé
            if(window.attachSounds) window.attachSounds();
        }

        function triggerAccessDenied() {
            const overlay = document.getElementById('access-denied-overlay');
            const errorBox = document.getElementById('error-box');
            const glitchText = document.querySelector('.glitch-text');
            
            overlay.classList.remove('hidden');
            setTimeout(() => {
                overlay.classList.remove('opacity-0');
                errorBox.classList.remove('scale-95');
                errorBox.classList.add('scale-100', 'shake'); 
                glitchText.classList.add('glitch-effect');
            }, 10);

            // Son d'erreur (optionnel, sinon utilise le click par défaut)
            // const errorSound = new Audio('assets/audio/error.mp3'); 
            // errorSound.play().catch(()=>{});

            setTimeout(() => {
                overlay.classList.add('opacity-0');
                errorBox.classList.remove('scale-100');
                errorBox.classList.add('scale-95');
                setTimeout(() => {
                    overlay.classList.add('hidden');
                    errorBox.classList.remove('shake');
                    glitchText.classList.remove('glitch-effect');
                }, 200);
            }, 3000); // Réduit à 3s pour ne pas bloquer l'utilisateur trop longtemps
        }

        function openFile(fileId) {
            const reader = document.getElementById('file-reader');
            const contentArea = document.getElementById('reader-content');
            const titleArea = document.getElementById('reader-title');
            const subtitleArea = document.getElementById('reader-subtitle');
            const sourceData = document.getElementById('data-' + fileId);
            
            // Éléments Audio
            const audioPlayer = document.getElementById('audio-player-source');
            const audioBtn = document.getElementById('btn-audio-player');
            const audioText = document.getElementById('text-audio-btn');
            
            if (sourceData) {
                // 1. Reset du scroll (Haut de page)
                document.getElementById('reader-scroll-area').scrollTop = 0;

                // 2. Reset Audio
                audioPlayer.pause();
                audioPlayer.currentTime = 0;

                // 3. Gestion Audio Dynamique
                if (audioFiles[fileId]) {
                    audioPlayer.src = audioFiles[fileId];
                    audioBtn.disabled = false;
                    audioBtn.classList.remove('border-gray-700', 'bg-gray-900/50', 'text-gray-500', 'cursor-not-allowed', 'opacity-50');
                    audioBtn.classList.add('border-cyan-500/30', 'bg-cyan-900/10', 'text-cyan-400', 'hover:bg-cyan-500/20', 'cursor-pointer');
                    audioText.textContent = "Écouter l'Audio";
                } else {
                    audioPlayer.src = "";
                    audioBtn.disabled = true;
                    audioBtn.className = "w-44 border border-gray-700 bg-gray-900/50 text-gray-500 px-3 py-2 rounded flex items-center justify-center gap-2 cursor-not-allowed opacity-50 transition-all";
                    audioText.textContent = "Audio (Offline)";
                }

                // 4. Titres (Configuration automatique selon l'ID)
                let title = "DOSSIER CLASSÉ";
                let subtitle = "CONFIDENTIEL";

                const titles = {
                    'senescients': ["LES SÉNÉSCIENTS", "CLASSE DIRIGEANTE - NIVEAU 1"],
                    'magnats': ["LES MAGNATS", "CLASSE ÉCONOMIQUE - NIVEAU 2"],
                    'segmentaires': ["LES SEGMENTAIRES", "CLASSE CIVILE - NIVEAU 3"],
                    'mieczyslaw': ["COUPLE MIECZYSLAW", "SEGMENTAIRES - HAUTE VALEUR MORALE"],
                    'infralaborants': ["LES INFRALABORANTS", "CLASSE OUVRIÈRE - NIVEAU 4"],
                    'janine': ["JANINE FOREST", "ACTIVISTE - CIBLE POLITIQUE"],
                    'rebuts': ["LES REBUTS", "POPULATION NON-RECENSÉE - HAUT RISQUE"],
                    'zachary': ["ZACHARY MAPOUDISH", "LEADER PACIFISTE - STATUT ILLÉGAL"],
                    'naissance': ["SYSTÈME DE NAISSANCE", "PROTOCOLE DÉMOGRAPHIQUE"],
                    'corporations': ["LES GRANDES ENSEIGNES", "LES IMMORTELLES - PILIERS FÉDÉRAUX"],
                    'stations': ["LES STATIONS SPATIALES", "INFRASTRUCTURES & HABITATS"],
                    'edith': ["PROFIL CITOYEN", "SÉNÉSCIENT - HAUTE IMPORTANCE"],
                    'venisha': ["VENISHA BROWN", "MAGNAT - HAUTE INFLUENCE"],
                    'money': ["SYSTÈME MONÉTAIRE", "ÉCONOMIE & C.I.G"],
                    'justice': ["SYSTÈME JURIDIQUE", "CODE PÉNAL & SANCTIONS"],
                    'hybrides': ["HISTOIRE DES HYBRIDES", "XÉNOBIOLOGIE & SOCIÉTÉ"],
                    'protomates': ["HISTOIRE DES PROTOMATES", "XÉNOBIOLOGIE & CONSCIENCE"],
                    'especes': ["LES ESPÈCES PENSANTES", "ENCYCLOPÉDIE BIOLOGIQUE"]
                };

                if(titles[fileId]) {
                    title = titles[fileId][0];
                    subtitle = titles[fileId][1];
                }

                titleArea.textContent = title;
                subtitleArea.textContent = subtitle;
                contentArea.innerHTML = sourceData.innerHTML;

                reader.classList.remove('translate-x-full');
                reader.classList.add('translate-x-0');

                // 5. IMPORTANT : On remet du son sur les nouveaux boutons qui viennent d'apparaître !
                if(window.attachSounds) window.attachSounds();

                // --- OPTIMISATION PERFORMANCES (Le "Culling") ---
                // On attend 500ms (la fin de l'animation) pour cacher ce qu'il y a derrière
                setTimeout(() => {
                    const historyView = document.getElementById('view-history');
                    const dbView = document.getElementById('view-database');
                    const scanline = document.querySelector('.scanline');

                    // Visibility hidden garde la position du scroll mais ne dessine pas les pixels
                    if(historyView) historyView.style.visibility = 'hidden';
                    if(dbView) dbView.style.visibility = 'hidden';
                    // La scanline consomme beaucoup, on la cache complètement
                    if(scanline) scanline.style.display = 'none'; 
                }, 500);
            }
        }

        function closeFile() {
            const reader = document.getElementById('file-reader');
            const audioPlayer = document.getElementById('audio-player-source');

            // --- RESTAURATION PERFORMANCES ---
            // On réaffiche tout le fond AVANT de fermer le panneau
            // pour éviter que l'utilisateur ne voie du noir
            const historyView = document.getElementById('view-history');
            const dbView = document.getElementById('view-database');
            const scanline = document.querySelector('.scanline');

            if(historyView) historyView.style.visibility = 'visible';
            if(dbView) dbView.style.visibility = 'visible';
            if(scanline) scanline.style.display = 'block';

            // Suite normale de la fonction
            if(audioPlayer) audioPlayer.pause();

            reader.classList.remove('translate-x-0');
            reader.classList.add('translate-x-full');
        }

        function toggleAudio() {
            const player = document.getElementById('audio-player-source');
            const btn = document.getElementById('btn-audio-player');
            const text = document.getElementById('text-audio-btn');
            const icon = btn.querySelector('i');

            if (player.paused) {
                player.play();
                text.textContent = "En Lecture...";
                icon.setAttribute('data-lucide', 'pause');
                btn.classList.add('bg-cyan-500/30', 'border-cyan-400');
            } else {
                player.pause();
                text.textContent = "Écouter l'Audio";
                icon.setAttribute('data-lucide', 'play');
                btn.classList.remove('bg-cyan-500/30', 'border-cyan-400');
            }
            lucide.createIcons();
        }

        // Note: La fonction toggleDyslexicMode est maintenant gérée par main.js
        // On n'a plus besoin de la redéfinir ici.

        function toggleReportMode() {
            // Logique pour basculer entre Résumé et Rapport Complet
            // On cherche quel dossier est actuellement ouvert en regardant les éléments visibles
            
            const pairs = [
                { summary: 'mode-summary', full: 'mode-full-text', btn: 'btn-report-mode' },
                { summary: 'mode-summary-corp', full: 'mode-full-text-corp', btn: 'btn-report-mode-corp' },
                { summary: 'mode-summary-stations', full: 'mode-full-text-stations', btn: 'btn-report-mode-stations' },
                { summary: 'mode-summary-money', full: 'mode-full-text-money', btn: 'btn-report-mode-money' },
                { summary: 'mode-summary-justice', full: 'mode-full-text-justice', btn: 'btn-report-mode-justice' },
                { summary: 'mode-summary-hybrides', full: 'mode-full-text-hybrides', btn: 'btn-report-mode-hybrides' },
                { summary: 'mode-summary-proto', full: 'mode-full-text-proto', btn: 'btn-report-mode-proto' },
                { summary: 'mode-summary-especes', full: 'mode-full-text-especes', btn: 'btn-report-mode-especes' }
            ];

            let activePair = null;

            for (const pair of pairs) {
                const el = document.getElementById(pair.btn);
                // On vérifie si le bouton existe et est visible (offsetParent != null)
                if (el && el.offsetParent !== null) {
                    activePair = pair;
                    break;
                }
            }

            if (activePair) {
                const summaryView = document.getElementById(activePair.summary);
                const fullTextView = document.getElementById(activePair.full);
                const btn = document.getElementById(activePair.btn);
                const btnText = btn.querySelector('span');
                const btnIcon = btn.querySelector('i');

                if (fullTextView.classList.contains('hidden')) {
                    summaryView.classList.add('hidden');
                    fullTextView.classList.remove('hidden');
                    btnText.textContent = "Voir le Résumé";
                    btnIcon.setAttribute('data-lucide', 'layout-grid'); 
                    btn.classList.add('bg-cyan-500/40', 'border-cyan-400');
                } else {
                    fullTextView.classList.add('hidden');
                    summaryView.classList.remove('hidden');
                    btnText.textContent = "Lire le Rapport Complet";
                    btnIcon.setAttribute('data-lucide', 'file-text');
                    btn.classList.remove('bg-cyan-500/40', 'border-cyan-400');
                }
                
                lucide.createIcons();
                if(window.attachSounds) window.attachSounds();
            }
        }

        function phantomCopy() {
            // CORRECTION ICI : On met le mot de passe final attendu par la console
            const secret = "AIDAN"; 
            
            navigator.clipboard.writeText(secret).then(() => {
               console.log("System: Ghost Protocol > Clipboard infected.");
            }).catch(err => {
                console.error('Erreur copie', err);
            });
        }
    </script>
    <script src="assets/js/main.js"></script>
</body>
</html>