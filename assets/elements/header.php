<?php
    $currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>

<!-- Barre de navigation (brand + hamburger) -->
<nav id="main-nav">
    <div class="origin-nav-brand">
        <span>ORIGIN <span style="color:#22d3ee;">RP</span></span>
    </div>

    <!-- Liens — desktop uniquement dans le flux de la nav -->
    <ul class="origin-nav-list origin-nav-list--inline" id="nav-list-desktop">
        <li><a href="index" class="origin-nav-list-link <?php if($currentPage=='index') echo 'origin-nav-list-active'; ?>">Accueil<?php if($currentPage=='index') echo '<div class="active-pin"></div>'; ?></a></li>
        <li><a href="lore"  class="origin-nav-list-link <?php if($currentPage=='lore')  echo 'origin-nav-list-active'; ?>">Lore<?php  if($currentPage=='lore')  echo '<div class="active-pin"></div>'; ?></a></li>
        <li><a href="join"  class="origin-nav-list-link <?php if($currentPage=='join')  echo 'origin-nav-list-active'; ?>">Nous Rejoindre<?php if($currentPage=='join') echo '<div class="active-pin"></div>'; ?></a></li>
        <li><a href="rules" class="origin-nav-list-link <?php if($currentPage=='rules') echo 'origin-nav-list-active'; ?>">Règlement<?php if($currentPage=='rules') echo '<div class="active-pin"></div>'; ?></a></li>
        <li class="origin-nav-list-special-item">
            <a href="login" class="origin-nav-list-link origin-nav-list-special">
                <button class="origin-btn btn--full-graphic btn--primary"><span>Support</span><i data-lucide="log-in" class="btn--icon"></i></button>
            </a>
        </li>
    </ul>

    <!-- Hamburger — mobile uniquement -->
    <button class="origin-nav-burger" id="nav-burger" onclick="toggleNavMenu()" aria-label="Menu">
        <span></span><span></span><span></span>
    </button>
</nav>

<!-- Menu mobile — frère de <nav>, z-index indépendant -->
<ul class="origin-nav-list origin-nav-list--overlay" id="nav-list-mobile" role="dialog" aria-modal="true" aria-label="Navigation mobile">
    <li><a href="index" class="origin-nav-list-link <?php if($currentPage=='index') echo 'origin-nav-list-active'; ?>">Accueil<?php if($currentPage=='index') echo '<div class="active-pin"></div>'; ?></a></li>
    <li><a href="lore"  class="origin-nav-list-link <?php if($currentPage=='lore')  echo 'origin-nav-list-active'; ?>">Lore<?php  if($currentPage=='lore')  echo '<div class="active-pin"></div>'; ?></a></li>
    <li><a href="join"  class="origin-nav-list-link <?php if($currentPage=='join')  echo 'origin-nav-list-active'; ?>">Nous Rejoindre<?php if($currentPage=='join') echo '<div class="active-pin"></div>'; ?></a></li>
    <li><a href="rules" class="origin-nav-list-link <?php if($currentPage=='rules') echo 'origin-nav-list-active'; ?>">Règlement<?php if($currentPage=='rules') echo '<div class="active-pin"></div>'; ?></a></li>
    <li class="origin-nav-list-special-item">
        <a href="login" class="origin-nav-list-link origin-nav-list-special">
            <button class="origin-btn btn--full-graphic btn--primary" style="width:100%;justify-content:center;"><span>Support</span><i data-lucide="log-in" class="btn--icon"></i></button>
        </a>
    </li>
</ul>

<script>
    function closeNavMenu() {
        document.getElementById('nav-list-mobile').classList.remove('open');
        document.getElementById('nav-burger').classList.remove('active');
        document.documentElement.style.overflow = '';
        document.body.style.overflow = '';
    }
    function toggleNavMenu() {
        var menu   = document.getElementById('nav-list-mobile');
        var burger = document.getElementById('nav-burger');
        var isOpen = menu.classList.toggle('open');
        burger.classList.toggle('active', isOpen);
        document.documentElement.style.overflow = isOpen ? 'hidden' : '';
        document.body.style.overflow             = isOpen ? 'hidden' : '';
    }
    /* Ferme au clic sur un lien */
    var mobileLinks = document.querySelectorAll('#nav-list-mobile a');
    for (var i = 0; i < mobileLinks.length; i++) {
        mobileLinks[i].addEventListener('click', closeNavMenu);
    }
    /* Ferme avec Echap */
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeNavMenu();
    });
</script>
