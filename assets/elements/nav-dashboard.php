<?php
    if(!isset($_GET['page'])){$_GET['page'] = "";}
?>
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
        <a href="./dashboard?page=parameters" class="origin-nav--categorie-button" data-sound-attached="true" style="margin-bottom: 0; margin-top: 1.5rem;">
            <i data-lucide="settings" class="lucide lucide-history w-4 h-4"></i>
            Paramètres
        </a>
        <a href="./dashboard?actions=disconnect" class="origin-nav--categorie-link group" style="margin-bottom: 0;" data-sound-attached="true">
            <i data-lucide="power" class="lucide lucide-power w-4 h-4 group-hover:text-red-400 transition-colors"></i>  
            Déconnexion
        </a>
    </div>
    <nav class="origin-nav--menu">
        <div class="origin-nav--categorie-title">Général</div>
        <a href="./dashboard" class="origin-nav--categorie-button <?php if(empty($_GET['page']) || $_GET['page'] == 'dashboard') { echo 'origin-nav--categorie-button--active'; } ?>" data-sound-attached="true">
            <i data-lucide="house" class="lucide lucide-history w-4 h-4"></i>
            Dashboard
        </a>
        <!--<a href="./dashboard?page=profil" class="origin-nav--categorie-button <?php if($_GET['page'] == 'profil') { echo 'origin-nav--categorie-button--active'; } ?>" data-sound-attached="true">
            <i data-lucide="contact-round" class="lucide lucide-history w-4 h-4"></i>
            Votre profile
        </a>
        <a href="./dashboard?page=candidature" class="origin-nav--categorie-button <?php if($_GET['page'] == 'candidature') { echo 'origin-nav--categorie-button--active'; } ?>" data-sound-attached="true">
            <i data-lucide="pen" class="lucide lucide-history w-4 h-4"></i>
            Candidature RP
        </a>-->

        <div class="origin-nav--categorie-separator"></div>

        <div class="origin-nav--categorie-title">Support</div>
        <a href="./dashboard?page=ticket-actions" class="origin-nav--categorie-button <?php if($_GET['page'] == 'ticket-actions') { echo 'origin-nav--categorie-button--active'; } ?>" data-sound-attached="true">
            <i data-lucide="ticket-plus" class="lucide lucide-history w-4 h-4"></i>
            Créer un ticket
        </a>
        <a href="./dashboard?page=tickets" class="origin-nav--categorie-button <?php if($_GET['page'] == 'tickets') { echo 'origin-nav--categorie-button--active'; } ?>" data-sound-attached="true">
            <i data-lucide="tickets" class="lucide lucide-history w-4 h-4"></i>
            Gérer vos tickets
        </a>
        <a href="./dashboard?page=personnages" class="origin-nav--categorie-button <?php if($_GET['page'] == 'personnages') { echo 'origin-nav--categorie-button--active'; } ?>" data-sound-attached="true">
            <i data-lucide="theater" class="lucide lucide-history w-4 h-4"></i>
            Gestion des personnages
        </a>

        <div class="origin-nav--categorie-separator"></div>

        <div class="origin-nav--categorie-title">Administration</div>
        <a href="./dashboard?page=monitoring" class="origin-nav--categorie-button <?php if($_GET['page'] == 'monitoring') { echo 'origin-nav--categorie-button--active'; } ?>" data-sound-attached="true">
            <i data-lucide="tv-minimal" class="lucide lucide-history w-4 h-4"></i>
            Monitoring du site
        </a>
        <a href="./dashboard?page=logs" class="origin-nav--categorie-button <?php if($_GET['page'] == 'logs') { echo 'origin-nav--categorie-button--active'; } ?>" data-sound-attached="true">
            <i data-lucide="file-clock" class="lucide lucide-history w-4 h-4"></i>
            Gestion des logs
        </a>
        <a href="./dashboard?page=users" class="origin-nav--categorie-button <?php if($_GET['page'] == 'users') { echo 'origin-nav--categorie-button--active'; } ?>" data-sound-attached="true">
            <i data-lucide="users" class="lucide lucide-history w-4 h-4"></i>
            Gest. utilisateurs
        </a>
        <a href="./dashboard?page=groups" class="origin-nav--categorie-button <?php if($_GET['page'] == 'groups') { echo 'origin-nav--categorie-button--active'; } ?>" data-sound-attached="true">
            <i data-lucide="user-key" class="lucide lucide-history w-4 h-4"></i>
            Gest. groupes
        </a>
        <a href="./dashboard?page=permissions" class="origin-nav--categorie-button <?php if($_GET['page'] == 'permissions') { echo 'origin-nav--categorie-button--active'; } ?>" data-sound-attached="true">
            <i data-lucide="user-key" class="lucide lucide-history w-4 h-4"></i>
            Gest. permissions
        </a>

        <div class="origin-nav--categorie-separator"></div>

        <div class="origin-nav--categorie-title">Management</div>
        <a href="./dashboard?page=manage-tickets" class="origin-nav--categorie-button <?php if($_GET['page'] == 'manage-tickets') { echo 'origin-nav--categorie-button--active'; } ?>" data-sound-attached="true">
            <i data-lucide="scroll-text" class="lucide lucide-history w-4 h-4"></i>
            Gestion des tickets
        </a>
        <a href="./dashboard?page=candidatures" class="origin-nav--categorie-button <?php if($_GET['page'] == 'candidatures') { echo 'origin-nav--categorie-button--active'; } ?>" data-sound-attached="true">
            <i data-lucide="notebook-text" class="lucide lucide-history w-4 h-4"></i>
            Gest. candidatures
        </a>
        <a href="./dashboard?page=profil-visual" class="origin-nav--categorie-button <?php if($_GET['page'] == 'profil-visual') { echo 'origin-nav--categorie-button--active'; } ?>" data-sound-attached="true">
            <i data-lucide="notebook-text" class="lucide lucide-history w-5 h-5"></i>
            Visualisateur des profils
        </a>
    </nav>
    <div class="origin-nav--footer">
        TERMINAL V.3.0.4
        <span onclick="phantomCopy()" class="cursor-default opacity-0 px-2 py-1 select-none absolute" title="ERR_BUFFER_OVERFLOW">.</span>
    </div>
</aside>
<nav class="origin-nav--responsive">
    <a href="./dashboard" class="origin-nav--responsive--button <?php if(empty($_GET['page']) || $_GET['page'] == 'dashboard') echo 'origin-nav--responsive--button-active'; ?> group">
        <i data-lucide="house" class="w-6 h-6 group-hover:scale-110 transition-transform"></i>
        <span class="text-[10px] uppercase tracking-widest">Accueil</span>
    </a>

    <div class="w-px h-8 bg-gray-800"></div>

    <a href="./dashboard?page=tickets" class="origin-nav--responsive--button <?php if($_GET['page'] == 'tickets') echo 'origin-nav--responsive--button-active'; ?> group">
        <i data-lucide="tickets" class="w-6 h-6 group-hover:scale-110 transition-transform"></i>
        <span class="text-[10px] uppercase tracking-widest">Tickets</span>
    </a>

    <div class="w-px h-8 bg-gray-800"></div>

    <button onclick="toggleMobileMenu()" class="origin-nav--responsive--button group" id="mob-menu-btn">
        <i data-lucide="menu" class="w-6 h-6 group-hover:scale-110 transition-transform"></i>
        <span class="text-[10px] uppercase tracking-widest">Menu</span>
    </button>

    <div class="w-px h-8 bg-gray-800"></div>

    <a href="./dashboard?page=parameters" class="origin-nav--responsive--button <?php if($_GET['page'] == 'parameters') echo 'origin-nav--responsive--button-active'; ?> group">
        <i data-lucide="settings" class="w-6 h-6 group-hover:scale-110 transition-transform"></i>
        <span class="text-[10px] uppercase tracking-widest">Compte</span>
    </a>

    <div class="w-px h-8 bg-gray-800"></div>

    <a href="./dashboard?actions=disconnect" class="origin-nav--responsive--button origin-nav--responsive--button-important group">
        <i data-lucide="power" class="w-6 h-6 group-hover:scale-110 transition-transform"></i>
        <span class="text-[10px] uppercase tracking-widest">Déco.</span>
    </a>
</nav>

<!-- Overlay menu mobile -->
<div id="mobile-menu-overlay" class="mobile-menu-overlay" style="display:none;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; padding-bottom:1rem; border-bottom:1px solid rgba(34,211,238,0.2);">
        <h5 class="font-bold tracking-tighter" style="margin:0;">ORIGIN <span class="text-cyan-400">SUPPORT</span></h5>
        <button onclick="toggleMobileMenu()" style="background:none; border:none; color:#6b7280; cursor:pointer; font-size:1.4rem; line-height:1; padding:0.25rem;">✕</button>
    </div>

    <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1.5rem; padding-bottom:1.25rem; border-bottom:1px solid #111;">
        <div style="width:40px;height:40px;background:rgba(34,211,238,0.1);border:1px solid rgba(34,211,238,0.25);display:flex;align-items:center;justify-content:center;font-weight:bold;color:#22d3ee;font-family:'JetBrains Mono',monospace;">M</div>
        <div>
            <div class="font-bold text-sm text-white">Mathis150</div>
            <div class="badge-glass badge-glass--danger" style="margin-top:0.25rem; display:inline-flex;">
                <span class="badge-glass--text">Administrateur</span>
            </div>
        </div>
        <div style="margin-left:auto; display:flex; align-items:center; gap:0.4rem;">
            <div class="w-2 h-2 rounded-full bg-green-500"></div>
            <span style="font-size:9px; color:#22c55e; text-transform:uppercase; letter-spacing:.1em;">Connecté</span>
        </div>
    </div>

    <div class="origin-nav--categorie-title">Général</div>
    <a href="./dashboard" onclick="toggleMobileMenu()" class="origin-nav--categorie-button <?php if(empty($_GET['page']) || $_GET['page'] == 'dashboard') echo 'origin-nav--categorie-button--active'; ?>">
        <i data-lucide="house" class="w-4 h-4"></i>Dashboard
    </a>

    <div class="origin-nav--categorie-separator"></div>

    <div class="origin-nav--categorie-title">Support</div>
    <a href="./dashboard?page=ticket-actions" onclick="toggleMobileMenu()" class="origin-nav--categorie-button <?php if($_GET['page'] == 'ticket-actions') echo 'origin-nav--categorie-button--active'; ?>">
        <i data-lucide="ticket-plus" class="w-4 h-4"></i>Créer un ticket
    </a>
    <a href="./dashboard?page=tickets" onclick="toggleMobileMenu()" class="origin-nav--categorie-button <?php if($_GET['page'] == 'tickets') echo 'origin-nav--categorie-button--active'; ?>">
        <i data-lucide="tickets" class="w-4 h-4"></i>Gérer vos tickets
    </a>
    <a href="./dashboard?page=personnages" onclick="toggleMobileMenu()" class="origin-nav--categorie-button <?php if($_GET['page'] == 'personnages') echo 'origin-nav--categorie-button--active'; ?>">
        <i data-lucide="theater" class="w-4 h-4"></i>Gestion des personnages
    </a>

    <div class="origin-nav--categorie-separator"></div>

    <div class="origin-nav--categorie-title">Administration</div>
    <a href="./dashboard?page=monitoring" onclick="toggleMobileMenu()" class="origin-nav--categorie-button <?php if($_GET['page'] == 'monitoring') echo 'origin-nav--categorie-button--active'; ?>">
        <i data-lucide="tv-minimal" class="w-4 h-4"></i>Monitoring du site
    </a>
    <a href="./dashboard?page=logs" onclick="toggleMobileMenu()" class="origin-nav--categorie-button <?php if($_GET['page'] == 'logs') echo 'origin-nav--categorie-button--active'; ?>">
        <i data-lucide="file-clock" class="w-4 h-4"></i>Gestion des logs
    </a>
    <a href="./dashboard?page=users" onclick="toggleMobileMenu()" class="origin-nav--categorie-button <?php if($_GET['page'] == 'users') echo 'origin-nav--categorie-button--active'; ?>">
        <i data-lucide="users" class="w-4 h-4"></i>Gest. utilisateurs
    </a>
    <a href="./dashboard?page=groups" onclick="toggleMobileMenu()" class="origin-nav--categorie-button <?php if($_GET['page'] == 'groups') echo 'origin-nav--categorie-button--active'; ?>">
        <i data-lucide="user-key" class="w-4 h-4"></i>Gest. groupes
    </a>
    <a href="./dashboard?page=permissions" onclick="toggleMobileMenu()" class="origin-nav--categorie-button <?php if($_GET['page'] == 'permissions') echo 'origin-nav--categorie-button--active'; ?>">
        <i data-lucide="shield-check" class="w-4 h-4"></i>Gest. permissions
    </a>

    <div class="origin-nav--categorie-separator"></div>

    <div class="origin-nav--categorie-title">Management</div>
    <a href="./dashboard?page=manage-tickets" onclick="toggleMobileMenu()" class="origin-nav--categorie-button <?php if($_GET['page'] == 'manage-tickets') echo 'origin-nav--categorie-button--active'; ?>">
        <i data-lucide="scroll-text" class="w-4 h-4"></i>Gestion des tickets
    </a>
    <a href="./dashboard?page=candidatures" onclick="toggleMobileMenu()" class="origin-nav--categorie-button <?php if($_GET['page'] == 'candidatures') echo 'origin-nav--categorie-button--active'; ?>">
        <i data-lucide="notebook-text" class="w-4 h-4"></i>Gest. candidatures
    </a>
    <a href="./dashboard?page=profil-visual" onclick="toggleMobileMenu()" class="origin-nav--categorie-button <?php if($_GET['page'] == 'profil-visual') echo 'origin-nav--categorie-button--active'; ?>">
        <i data-lucide="contact-round" class="w-4 h-4"></i>Visualisateur des profils
    </a>

    <div class="origin-nav--categorie-separator"></div>

    <a href="./dashboard?page=parameters" onclick="toggleMobileMenu()" class="origin-nav--categorie-button <?php if($_GET['page'] == 'parameters') echo 'origin-nav--categorie-button--active'; ?>">
        <i data-lucide="settings" class="w-4 h-4"></i>Paramètres
    </a>
    <a href="./dashboard?actions=disconnect" class="origin-nav--categorie-link group" style="color:#ef4444b3;">
        <i data-lucide="power" class="w-4 h-4"></i>Déconnexion
    </a>
</div>

<script>
    function toggleMobileMenu() {
        const overlay = document.getElementById('mobile-menu-overlay');
        const isOpen = overlay.style.display !== 'none';
        overlay.style.display = isOpen ? 'none' : 'block';
        document.getElementById('mob-menu-btn').classList.toggle('origin-nav--responsive--button-active', !isOpen);
        document.body.style.overflow = isOpen ? '' : 'hidden';
        if (!isOpen) lucide.createIcons();
    }
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            const overlay = document.getElementById('mobile-menu-overlay');
            if (overlay.style.display !== 'none') toggleMobileMenu();
        }
    });
</script>