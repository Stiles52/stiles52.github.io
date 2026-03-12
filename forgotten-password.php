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
                <h4 class="font-bold text-white uppercase">Avez-vous oubliés <span class="text-cyan-400">votre mot de passe ?</span></h4>
                <div style="display: flex; justify-content: center; flex-direction: column; align-items: center; padding-top: 45px;">
                    <div style="width: 100%; display: flex; justify-content: center; flex-direction: column; align-items: center; padding-bottom: 25px;">
                        <label style="text-align: left; width: 340px;">
                            <i data-lucide="user" class="lucide w-5 h-5" style="margin-right: 10px;"></i>
                            Adresse E-Mail du compte
                        </label>
                        <input type="text" placeholder="+@example.xyz">
                    </div>
                </div>
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 15px;">
                <a href="./login" class="origin-btn btn--graphic btn--danger" data-sound-attached="true">
                    <i data-lucide="arrow-left" class="lucide lucide-x btn--icon"></i> 
                    <span>Retour en arrière</span>
                </a>
                <a href="#" class="origin-btn btn--graphic btn--primary" data-sound-attached="true" style="font-size: 16px;">
                    <span>Récupérer</span>
                    <i data-lucide="arrow-right" class="lucide lucide-x btn--icon"></i> 
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