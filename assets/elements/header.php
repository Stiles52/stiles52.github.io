<?php

    $currentPage = basename($_SERVER['PHP_SELF'], '.php');

?>
<nav class="origin-nav">
    <ul class="origin-nav-list">
        <li>
            <a href="index" class="origin-nav-list-link <?php if($currentPage == 'index') { echo 'origin-nav-list-active'; } ?>">
                Accueil
                <?php 
                    if($currentPage == 'index') {
                        echo '<div class="active-pin"></div>';
                    }
                ?>
            </a>
        </li>
        <li>
            <a href="lore" class="origin-nav-list-link <?php if($currentPage == 'lore') { echo 'origin-nav-list-active'; } ?>">
                Lore
                <?php 
                    if($currentPage == 'lore') {
                        echo '<div class="active-pin"></div>';
                    }
                ?>
            </a>
        </li>
        <li>
            <a href="join" class="origin-nav-list-link <?php if($currentPage == 'join') { echo 'origin-nav-list-active'; } ?>">
                Nous Rejoindre
                <?php 
                    if($currentPage == 'join') {
                        echo '<div class="active-pin"></div>';
                    }
                ?>
            </a>
        </li>
        <li>
            <a href="rules" class="origin-nav-list-link <?php if($currentPage == 'join') { echo 'origin-nav-list-active'; } ?>">
                Règlement
                <?php 
                    if($currentPage == 'join') {
                        echo '<div class="active-pin"></div>';
                    }
                ?>
            </a>
        </li>
        <li>
            <a href="login" class="origin-nav-list-link origin-nav-list-special group <?php if($currentPage == 'support') { echo 'origin-nav-list-active'; } ?>">
                <button class="origin-btn btn--full-graphic btn--primary">
                    <span>Support</span>
                    <i data-lucide="log-in" class="btn--icon"></i>
                </button>
            </a>
        </li>
    </ul>
</nav>