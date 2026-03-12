<aside class="origin-nav">
    <div class="origin-nav--header">
        <h5 class="font-bold tracking-tighter">ORIGIN <span class="text-cyan-400">SUPPORT</span></h5>
    </div>
    <div class="origin-nav--header">
        <div class="badge-glass badge-glass--danger" style="margin-bottom: 10px;">
            <span class="badge-glass--text">Administrateur</span>
        </div>
        <h6>Mathis150</h6>
        <div class="flex items-center gap-2 mt-2">
            <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
            <span class="text-[10px] text-green-500 uppercase tracking-widest">Connecté</span>
        </div>
        <button id="btn-database" onclick="switchView('database')" class="origin-nav--categorie-button" data-sound-attached="true" style="margin-bottom: 0; margin-top: 1.5rem;">
            <i data-lucide="settings" class="lucide lucide-history w-4 h-4"></i>
            Paramètres
        </button>
        <a href="#" class="origin-nav--categorie-link group" style="margin-bottom: 0;" data-sound-attached="true">
            <i data-lucide="power" class="lucide lucide-power w-4 h-4 group-hover:text-red-400 transition-colors"></i>  
            Déconnexion
        </a>
    </div>
    <nav class="origin-nav--menu">
        <div class="origin-nav--categorie-title">Général</div>
        <button id="btn-database" class="origin-nav--categorie-button origin-nav--categorie-button--active" data-sound-attached="true">
            <i data-lucide="house" class="lucide lucide-history w-4 h-4"></i>
            Dashboard
        </button>
        <button id="btn-database" class="origin-nav--categorie-button" data-sound-attached="true">
            <i data-lucide="contact-round" class="lucide lucide-history w-4 h-4"></i>
            Votre profile
        </button>
        <button id="btn-database" class="origin-nav--categorie-button" data-sound-attached="true">
            <i data-lucide="pen" class="lucide lucide-history w-4 h-4"></i>
            Candidature RP
        </button>

        <div class="origin-nav--categorie-separator"></div>

        <div class="origin-nav--categorie-title">Support</div>
        <button id="btn-database" class="origin-nav--categorie-button" data-sound-attached="true">
            <i data-lucide="ticket-plus" class="lucide lucide-history w-4 h-4"></i>
            Créer un ticket
        </button>
        <button id="btn-database" class="origin-nav--categorie-button" data-sound-attached="true">
            <i data-lucide="tickets" class="lucide lucide-history w-4 h-4"></i>
            Gérer vos tickets
        </button>

        <div class="origin-nav--categorie-separator"></div>

        <div class="origin-nav--categorie-title">Administration</div>
        <button id="btn-database" class="origin-nav--categorie-button" data-sound-attached="true">
            <i data-lucide="tv-minimal" class="lucide lucide-history w-4 h-4"></i>
            Monitoring du site
        </button>
        <button id="btn-database" class="origin-nav--categorie-button" data-sound-attached="true">
            <i data-lucide="file-clock" class="lucide lucide-history w-4 h-4"></i>
            Gestion des logs
        </button>
        <button id="btn-database" class="origin-nav--categorie-button" data-sound-attached="true">
            <i data-lucide="users" class="lucide lucide-history w-4 h-4"></i>
            Gest. utilisateurs
        </button>
        <button id="btn-database" class="origin-nav--categorie-button" data-sound-attached="true">
            <i data-lucide="user-key" class="lucide lucide-history w-4 h-4"></i>
            Gest. permissions
        </button>

        <div class="origin-nav--categorie-separator"></div>

        <div class="origin-nav--categorie-title">Management</div>
        <button id="btn-database" class="origin-nav--categorie-button" data-sound-attached="true">
            <i data-lucide="scroll-text" class="lucide lucide-history w-4 h-4"></i>
            Gestion des tickets
        </button>
        <button id="btn-database" class="origin-nav--categorie-button" data-sound-attached="true">
            <i data-lucide="notebook-text" class="lucide lucide-history w-4 h-4"></i>
            Gest. candidatures
        </button>
        <button id="btn-database" class="origin-nav--categorie-button" data-sound-attached="true">
            <i data-lucide="notebook-text" class="lucide lucide-history w-5 h-5"></i>
            Visualisateur des profils
        </button>
    </nav>
    <div class="origin-nav--footer">
        TERMINAL V.3.0.4
        <span onclick="phantomCopy()" class="cursor-default opacity-0 px-2 py-1 select-none absolute" title="ERR_BUFFER_OVERFLOW">.</span>
    </div>
</aside>
<nav class="origin-nav--responsive">
    <button onclick="switchView('history')" id="mob-btn-history" class="origin-nav--responsive--button origin-nav--responsive--button-active group">
        <i data-lucide="house" class="w-6 h-6 group-hover:scale-110 transition-transform"></i>
        <span class="text-[10px] uppercase tracking-widest">Dashboard</span>
    </button>

    <div class="w-px h-8 bg-gray-800"></div>

    <button onclick="switchView('database')" id="mob-btn-database" class="origin-nav--responsive--button group">
        <i data-lucide="tickets" class="w-6 h-6 group-hover:scale-110 transition-transform"></i>
        <span class="text-[10px] uppercase tracking-widest">Tickets</span>
    </button>
    
    <div class="w-px h-8 bg-gray-800"></div>

    <a href="index.html" class="origin-nav--responsive--button origin-nav--responsive--button-important group">
        <i data-lucide="power" class="w-6 h-6 group-hover:scale-110 transition-transform"></i>
        <span class="text-[10px] uppercase tracking-widest">Déconnexion</span>
    </a>
</nav>