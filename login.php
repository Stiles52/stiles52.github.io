<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://unpkg.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src https://fonts.gstatic.com; img-src 'self' data: https:; frame-src https://www.youtube-nocookie.com; connect-src 'self';">

    <title>CONNEXION - ORIGIN</title>

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

    <!-- BACKGROUND & PARTICLES -->
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute w-full h-full bg-black"></div>
        <div id="particles-container"></div>
        <div class="fixed bottom-[-150px] left-1/2 -translate-x-1/2 w-[600px] h-[400px] bg-cyan-500/15 blur-[120px] rounded-full pointer-events-none z-0"></div>
    </div>

    <!-- SECTION PRÉSENTATION -->
    <section style="width: 100%; height: calc(100vh - 53px); display: flex; align-items: center; justify-content: center; color: #FFFFFF; z-index: 50; position: absolute;">
        <div>
            <div style="padding: 15px; border: 1px solid #20d3ee33; width: 600px;">
                <h3 class="font-bold text-white uppercase">Connexion <span class="text-cyan-400">au support</span></h3>
                <div style="display: flex; justify-content: center; flex-direction: column; align-items: center; padding-top: 45px;">
                    <div style="width: 100%; display: flex; justify-content: center; flex-direction: column; align-items: center; padding-bottom: 25px;">
                        <label style="text-align: left; width: 340px;">
                            <i data-lucide="user" class="lucide w-5 h-5" style="margin-right: 10px;"></i>
                            E-Mail ou Pseudonyme
                        </label>
                        <input type="text" placeholder="Identifiant">
                    </div>
                    <div style="width: 100%; display: flex; justify-content: center; flex-direction: column; align-items: center; padding-bottom: 25px;">
                        <label style="text-align: left; width: 340px;">
                            <i data-lucide="key-round" class="lucide w-5 h-5" style="margin-right: 10px;"></i>
                            Mot de passe
                        </label>
                        <input type="password" placeholder="Clés">
                    </div>
                    <div style="display: flex; width: 100%; justify-content: space-between; flex-direction: row; align-items: center; padding-top: 25px;">
                        <div style="display: flex; justify-content: center; gap: 10px;">
                            <label class="checkbox">
                                <input type="checkbox" name="checkbox-content" class="checkbox-content">
                                <span class="checkbox-mark"></span>
                                <span class="checkbox-label-text">Se souvenir de moi</span>
                            </label>
                        </div>
                        <div>
                            <a href="./forgotten-password">Mot de passe oubliés ?</a>
                        </div>
                    </div>
                    <p style="text-align: center; padding-top: 15px;">Vous n'avez pas de compte ? <a href="./register">Inscrivez-vous !</a></p>
                </div>
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 15px;">
                <a href="./" class="origin-btn btn--graphic btn--danger" data-sound-attached="true">
                    <i data-lucide="arrow-left" class="lucide lucide-x btn--icon"></i> 
                    <span>Retour en arrière</span>
                </a>
                <a href="#" class="origin-btn btn--graphic btn--primary" data-sound-attached="true" style="font-size: 16px;">
                    <span>Connexion</span>
                    <i data-lucide="arrow-right" class="lucide lucide-x btn--icon"></i> 
                </a>
            </div>
            <div style="display: flex; justify-content: flex-start; margin-top: 60px; flex-direction: column; gap: 15px; align-items: stretch;">
                <h6 style="text-align: center;">Ou connectez-vous par :</h6>
                <a href="lore.html" class="origin-btn btn--glass btn--danger" data-sound-attached="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 640 640" fill="#ef4444" class="lucide lucide-message-circle btn--icon">
                        <path d="M96 96L310.6 96L310.6 310.6L96 310.6L96 96zM329.4 96L544 96L544 310.6L329.4 310.6L329.4 96zM96 329.4L310.6 329.4L310.6 544L96 544L96 329.4zM329.4 329.4L544 329.4L544 544L329.4 544L329.4 329.4z"/>
                    </svg>
                    <span>Microsoft</span>
                </a>
                <a href="lore.html" class="origin-btn btn--glass btn--discord" data-sound-attached="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="message-circle" aria-hidden="true" class="lucide lucide-message-circle btn--icon"><path d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719"></path></svg>
                    <span>Discord</span>
                </a>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <div class="login--footer">
        <?php include './assets/elements/footer.html'; ?>
    </div>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/intro.js"></script>
    <script src="assets/js/matinal.js"></script>
    <script src="assets/js/statut-system.js"></script>
</body>
</html>