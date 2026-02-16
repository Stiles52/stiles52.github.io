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
            <a href="support" class="origin-nav-list-link origin-nav-list-special group <?php if($currentPage == 'support') { echo 'origin-nav-list-active'; } ?>">
                Support
                <?php 
                    if($currentPage == 'support') {
                        echo '<div class="active-pin"></div>';
                    }
                    else {
                        echo '<span class="special-pin group-hover:w-4"></span>';
                    }
                ?>
            </a>
        </li>
    </ul>
</nav>