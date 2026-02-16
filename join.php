<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://unpkg.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src https://fonts.gstatic.com; img-src 'self' data: https:; frame-src https://www.youtube-nocookie.com; connect-src 'self';">

    <title>CANDIDATURE - ORIGIN</title>

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
        }

        .scanline {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(to bottom, transparent 50%, rgba(0,0,0,0.3) 51%);
            background-size: 100% 4px; pointer-events: none; z-index: 40; opacity: 0.3;
        }
    </style>
</head>
<body class="bg-black min-h-screen flex flex-col pb-24 md:pb-0" id="page-body">

    <div class="scanline"></div>

    <?php include './assets/elements/header.php'; ?>

    <header class="pt-32 pb-12 px-6 text-center relative z-10">
        <div class="inline-block mb-4 border border-cyan-500/30 px-4 py-1 rounded-full bg-cyan-900/10">
            <span class="text-cyan-400 text-xs uppercase tracking-[0.3em]">Protocole d'Admission</span>
        </div>
        <h1 class="text-4xl md:text-6xl font-bold text-white tracking-tighter mb-6">REJOINDRE <span class="text-cyan-400">L'AVENTURE</span></h1>
        <p class="text-gray-400 max-w-2xl mx-auto text-sm leading-relaxed">
            Le processus de recrutement est strict mais ouvert à tous. Suivez ce guide pas à pas pour intégrer la simulation Star Maze.
        </p>
    </header>

    <main class="max-w-5xl mx-auto px-6 relative z-10 mb-24">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-16">
            <a href="https://discord.gg/YmVxZCqEYs" target="_blank" class="glass-panel p-4 flex items-center justify-center gap-3 hover:bg-cyan-900/20 transition-colors group border-l-4 border-l-indigo-500">
                <i data-lucide="message-circle" class="w-5 h-5 text-indigo-400 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-bold uppercase tracking-wider">Rejoindre le Discord</span>
            </a>
            
            <button onclick="toggleDyslexicMode()" class="glass-panel p-4 flex items-center justify-center gap-3 hover:bg-cyan-900/20 transition-colors group border-l-4 border-l-cyan-500 cursor-pointer w-full">
                <i data-lucide="eye" class="w-5 h-5 text-cyan-400 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-bold uppercase tracking-wider">Mode Dyslexique</span>
            </button>

            <a href="https://docs.google.com/document/d/1aIMRrDGG6UiTVuZOJLTYfV0bnT2qBOyKumnNablnVnc/edit?usp=sharing" target="_blank" class="glass-panel p-4 flex items-center justify-center gap-3 hover:bg-cyan-900/20 transition-colors group border-l-4 border-l-green-500">
                <i data-lucide="shirt" class="w-5 h-5 text-green-400 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-bold uppercase tracking-wider">Guide du Skin</span>
            </a>
        </div>
        <div class="border border-red-500/50 bg-red-950/30 p-6 rounded mb-16 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full bg-[repeating-linear-gradient(45deg,transparent,transparent_10px,rgba(239,68,68,0.05)_10px,rgba(239,68,68,0.05)_20px)] pointer-events-none"></div>
            
            <div class="flex flex-col md:flex-row items-start md:items-center gap-6 relative z-10">
                <div class="p-4 bg-red-500/20 rounded-full border border-red-500 shrink-0">
                    <i data-lucide="lock" class="w-8 h-8 text-red-500 animate-pulse"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-red-500 uppercase tracking-widest mb-2">ACCÈS RESTREINT : SUR INVITATION</h3>
                    <p class="text-sm text-gray-300 leading-relaxed">
                        Le serveur <strong class="text-white">Origin</strong> est actuellement en mode privé. L'accès est réservé uniquement aux personnes sélectionnées en amont et invitées par l'équipe d'Origin.
                        <br><br>
                        <span class="text-red-400 font-bold underline">IL EST INUTILE DE CANDIDATER SI VOUS N'AVEZ PAS REÇU D'INVITATION OFFICIELLE.</span>
                        <br>Toute candidature non sollicitée sera automatiquement rejetée par le système.
                    </p>
                </div>
            </div>
        </div>
        <div class="relative border-l border-cyan-900/50 ml-4 md:ml-0 md:pl-12 space-y-16">
            
            <div class="relative">
                <div class="absolute -left-[21px] md:-left-[53px] top-0 w-10 h-10 bg-black border border-cyan-500 rounded-full flex items-center justify-center z-20">
                    <span class="text-cyan-400 font-bold">01</span>
                </div>
                <div class="glass-panel p-8">
                    <h3 class="text-2xl font-bold text-white mb-2 uppercase flex items-center gap-3">
                        <i data-lucide="book-open" class="w-6 h-6 text-cyan-400"></i> Conception du Personnage
                    </h3>
                    <p class="text-sm text-gray-400 mb-6 border-b border-gray-800 pb-4">
                        Avant de remplir le formulaire, vous devez construire votre identité. Nous sommes en <strong>Février 2203</strong>.
                    </p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h4 class="text-cyan-400 text-xs uppercase tracking-widest font-bold mb-3">Identité de base</h4>
                            <ul class="space-y-2 text-sm text-gray-300">
                                <li class="flex gap-2"><span class="text-cyan-600">▹</span> <strong>Nom & Prénom :</strong> Évitez les jeux de mots ou noms célèbres.</li>
                                <li class="flex gap-2"><span class="text-cyan-600">▹</span> <strong>Âge :</strong> Minimum 16 ans.</li>
                                <li class="flex gap-2"><span class="text-cyan-600">▹</span> <strong>Espèce :</strong> Humain, Hybride ou Protomate.</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="text-cyan-400 text-xs uppercase tracking-widest font-bold mb-3">Les Indispensables</h4>
                            <ul class="space-y-2 text-sm text-gray-300">
                                <li class="flex gap-2"><span class="text-cyan-600">▹</span> <strong>Physique :</strong> Min. 5 lignes (Taille, poids, signes distinctifs...).</li>
                                <li class="flex gap-2"><span class="text-cyan-600">▹</span> <strong>Mental :</strong> Min. 5 lignes (Qualités, défauts, peurs, buts).</li>
                                <li class="flex gap-2"><span class="text-cyan-600">▹</span> <strong>Histoire :</strong> Min. 20 lignes. De la naissance à l'arrivée dans le Star Maze.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-800">
                        <h4 class="text-white text-sm font-bold mb-6 uppercase">Comment votre personnage est-il arrivé ici ?</h4>
                        
                        <div class="space-y-6">
                            
                            <div class="bg-blue-900/10 border border-blue-500/20 p-6 rounded relative overflow-hidden">
                                <div class="absolute top-0 right-0 p-4 opacity-10">
                                    <i data-lucide="file-signature" class="w-16 h-16 text-blue-400"></i>
                                </div>
                                <h5 class="text-blue-400 font-bold text-sm uppercase mb-2">L'Accord</h5>
                                <p class="text-xs text-gray-300 leading-relaxed text-justify mb-3">
                                    Des publicités et de nombreuses annonces holographiques vous sautent aux yeux à propos d'un programme révolutionnaire, capable de changer la face de l'univers. Le seul élément manquant, c'est vous. Sceptique ? Peut-être que la somme astronomique à la clé vous fera changer d'avis. Vous décidez ainsi de signer un accord avec des pages à n'en plus finir. Une fois votre signature déposée, plus question de faire machine arrière. Vous êtes endormis. À votre réveil, vous n'avez plus aucune notion du temps. Vous vous retrouvez enfermé dans une cuve, en route pour le programme Star Maze.
                                </p>
                                <div class="inline-block bg-blue-900/30 px-2 py-1 rounded border border-blue-500/30">
                                    <span class="text-[10px] text-blue-300 uppercase tracking-widest">Pour : Segmentaires & Infralaborants</span>
                                </div>
                            </div>

                            <div class="bg-orange-900/10 border border-orange-500/20 p-6 rounded relative overflow-hidden">
                                <div class="absolute top-0 right-0 p-4 opacity-10">
                                    <i data-lucide="help-circle" class="w-16 h-16 text-orange-400"></i>
                                </div>
                                <h5 class="text-orange-400 font-bold text-sm uppercase mb-2">Le Choix</h5>
                                <p class="text-xs text-gray-300 leading-relaxed text-justify mb-3">
                                    La vie dans les stations est mouvementée, surtout lorsque l'on gagne durement sa vie. Cependant, un événement particulier vient chambouler votre quotidien. Des sentinels vous tombent dessus, accompagnés d'une personne visiblement amicale. Il vous est donné un choix simple : la possibilité de réussir votre vie. En échange, vous acceptez de participer au programme cité dans votre contrat. Sans lire les petits caractères, vous êtes endormis à bord d'une navette. À votre réveil, vous n'avez plus aucune notion du temps. Vous vous retrouvez enfermé dans une cuve, attendant le début du programme Star Maze.
                                </p>
                                <div class="inline-block bg-orange-900/30 px-2 py-1 rounded border border-orange-500/30">
                                    <span class="text-[10px] text-orange-300 uppercase tracking-widest">Pour : Rebuts</span>
                                </div>
                            </div>

                            <div class="bg-red-900/10 border border-red-500/20 p-6 rounded relative overflow-hidden">
                                <div class="absolute top-0 right-0 p-4 opacity-10">
                                    <i data-lucide="gavel" class="w-16 h-16 text-red-400"></i>
                                </div>
                                <h5 class="text-red-400 font-bold text-sm uppercase mb-2">La Grâce</h5>
                                <p class="text-xs text-gray-300 leading-relaxed text-justify mb-3">
                                    Condamné à la peine capitale, vous croupissez dans une cellule hautement gardée d'une station pénitentiaire. Tout espoir semblait vain de revoir un jour les lueurs artificielles des lieux que vous fréquentiez... jusqu'à ce jour. Un entretien vous est proposé en compagnie d'un agent fédéral. Le marché est simple. En échange d'une grâce de la part de la Fédération, vous serez assigné en tant que matériel scientifique pour le programme Star Maze. Le choix vous revient... même si l'autre option reste la mort. Vous choisissez naturellement le premier choix. Des sentinels pénètrent la salle dans laquelle vous vous trouvez afin de vous endormir. À votre réveil, vous n'avez plus aucune notion du temps. Vous vous retrouvez enfermé dans une cuve, ne sachant pas ce qu'il adviendra de vous pour le programme Star Maze.
                                </p>
                                <div class="inline-block bg-red-900/30 px-2 py-1 rounded border border-red-500/30">
                                    <span class="text-[10px] text-red-300 uppercase tracking-widest">Pour : Condamnés à mort</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="relative">
                <div class="absolute -left-[21px] md:-left-[53px] top-0 w-10 h-10 bg-black border border-cyan-500 rounded-full flex items-center justify-center z-20">
                    <span class="text-cyan-400 font-bold">02</span>
                </div>
                <div class="glass-panel p-8">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                        <h3 class="text-2xl font-bold text-white uppercase flex items-center gap-3">
                            <i data-lucide="send" class="w-6 h-6 text-cyan-400"></i> Envoi de Candidature
                        </h3>
                        <a href="https://forms.gle/iPgeb3HqQje2PNZ88" target="_blank" class="bg-cyan-500 hover:bg-cyan-400 text-black font-bold px-6 py-3 rounded uppercase tracking-widest text-xs transition-colors flex items-center gap-2">
                            Accéder au Formulaire <i data-lucide="external-link" class="w-4 h-4"></i>
                        </a>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-start gap-4 p-4 bg-green-900/10 border border-green-500/20 rounded">
                            <i data-lucide="check-circle" class="w-6 h-6 text-green-400 flex-shrink-0 mt-1"></i>
                            <div>
                                <strong class="text-green-400 text-sm uppercase">Candidature Acceptée</strong>
                                <p class="text-sm text-gray-400 mt-1">Un recruteur vous contactera en MP Discord pour organiser votre entretien.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 p-4 bg-red-900/10 border border-red-500/20 rounded">
                            <i data-lucide="x-circle" class="w-6 h-6 text-red-400 flex-shrink-0 mt-1"></i>
                            <div>
                                <strong class="text-red-400 text-sm uppercase">Candidature Refusée</strong>
                                <p class="text-sm text-gray-400 mt-1">Pas de panique. Vous recevrez un message détaillé des points à corriger. Vous pourrez modifier et renvoyer votre fiche autant de fois que nécessaire.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative">
                <div class="absolute -left-[21px] md:-left-[53px] top-0 w-10 h-10 bg-black border border-cyan-500 rounded-full flex items-center justify-center z-20">
                    <span class="text-cyan-400 font-bold">03</span>
                </div>
                <div class="glass-panel p-8">
                    <h3 class="text-2xl font-bold text-white mb-4 uppercase flex items-center gap-3">
                        <i data-lucide="mic" class="w-6 h-6 text-cyan-400"></i> L'Entretien Oral
                    </h3>
                    <p class="text-sm text-gray-400 mb-4">
                        Une discussion vocale pour vérifier que vous maîtrisez les règles et le lore. C'est aussi le moment de vérifier votre configuration technique.
                    </p>
                    <div class="bg-gray-900/50 p-4 border-l-2 border-cyan-500 text-xs text-gray-300 italic">
                        "Conseil : Lancez votre jeu et votre launcher avant l'entretien pour détecter d'éventuels problèmes de RAM ou de connexion."
                    </div>
                </div>
            </div>

        </div>

        <div class="mt-24 border border-red-500/50 bg-red-950/20 p-8 rounded relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-[repeating-linear-gradient(45deg,transparent,transparent_10px,#ef4444_10px,#ef4444_20px)] opacity-50"></div>
            
            <div class="flex items-center gap-4 mb-6">
                <div class="p-3 bg-red-500/20 rounded-full border border-red-500">
                    <i data-lucide="alert-triangle" class="w-8 h-8 text-red-500"></i>
                </div>
                <h3 class="text-2xl font-bold text-red-500 uppercase tracking-widest">Critères de Refus Immédiat</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-4 text-sm text-gray-300">
                <ul class="list-disc pl-5 space-y-2 marker:text-red-500">
                    <li>Candidature "Troll" ou manque total de sérieux.</li>
                    <li>Histoire tragique excessive ou sujets sensibles sans retenue.</li>
                    <li>Absence d'histoire ou explications insuffisantes.</li>
                    <li>Identité célèbre (ex: Obiwan Kenobi) ou jeux de mots.</li>
                    <li>Nombre de lignes insuffisant (moins de 20).</li>
                </ul>
                <ul class="list-disc pl-5 space-y-2 marker:text-red-500">
                    <li>Personnage "Overpowered" (QI de 500, expert en tout à 14 ans).</li>
                    <li>Invention de Lore (Création d'organisations non-officielles).</li>
                    <li>Maladies rares, incurables ou albinisme.</li>
                    <li>Physique incohérent (ex: Hybride félidé roux aux oreilles blondes).</li>
                    <li>Psychopathie / Sociopathie (Interdiction formelle).</li>
                </ul>
            </div>
        </div>

    </main>
    <footer id="page-footer" class="reveal w-full bg-black/95 border-t border-cyan-900/50 z-50 py-3 px-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-center items-center text-gray-500 text-[10px] space-y-1 md:space-y-0 md:gap-6 tracking-wider">
            <div class="flex items-center gap-1 text-cyan-400">
                <i data-lucide="copyright" class="w-3 h-3"></i>
                <span class="font-bold">2025 par L'équipe d'Origin</span>
            </div>
            <div class="hidden md:block text-gray-700">|</div>
            <p class="text-xs text-gray-400 text-center">© OriginRp, 2024. Tous droits réservés.</p>
            <div class="hidden md:block text-gray-700">|</div>
            <p class="text-[9px] text-red-500/70 uppercase font-bold text-center">PAS DE MINECRAFT OFFICIEL.</p>
        </div>
        <div class="text-black select-text text-[1px]">PASSWORD COMPLETE: LIBERTY_2203</div>
    </footer>
    <script src="assets/js/main.js"></script>
</body>
</html>