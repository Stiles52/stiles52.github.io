<?php
require_once __DIR__ . '/assets/import/init.php';

$token = trim($_GET['token'] ?? '');
$valid = !empty($token) && $auth->isResetTokenValid($token);
$csrf  = Auth::generateCsrfToken();
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://unpkg.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self';">
    <title>Nouveau mot de passe - ORIGIN</title>
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    <link href="./assets/css/style.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .auth-card    { width: 600px; }
        .auth-actions { width: 600px; gap: 1.5rem; }
        button.origin-btn { background: none; border: none; cursor: pointer; font-family: inherit; padding: 0; }
        @media (max-width: 640px) {
            body { display: flex; flex-direction: column; }
            section { position: relative !important; height: auto !important; flex: 1; padding-top: 2rem; padding-bottom: 2rem; }
            .login--footer { position: relative !important; bottom: auto !important; left: auto !important; }
            .auth-wrapper { width: 100%; padding: 0 1rem; box-sizing: border-box; }
            .auth-card { width: 100% !important; box-sizing: border-box; }
            .auth-actions { width: 100% !important; box-sizing: border-box; flex-direction: column; gap: 0.5rem; }
            .auth-actions a, .auth-actions button { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body class="bg-black min-h-screen relative">

    <audio id="sfx-boot"   src="assets/audio/boot.mp3"   preload="auto"></audio>
    <audio id="sfx-anthem" src="assets/audio/anthem.mp3" preload="auto"></audio>

    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute w-full h-full bg-black"></div>
        <div id="particles-container"></div>
        <div class="fixed bottom-[-150px] left-1/2 -translate-x-1/2 w-[600px] h-[400px] bg-cyan-500/15 blur-[120px] rounded-full pointer-events-none z-0"></div>
    </div>

    <section style="width:100%;height:calc(100vh - 53px);display:flex;align-items:center;justify-content:center;color:#fff;z-index:50;position:absolute;">
        <div class="auth-wrapper">
            <?php if ($valid): ?>

            <form id="reset-form" method="POST" action="assets/handlers/auth-reset-password">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES) ?>">
                <input type="hidden" name="token"      value="<?= htmlspecialchars($token, ENT_QUOTES) ?>">

                <div class="auth-card" style="padding:15px;border:1px solid #20d3ee33;">
                    <h4 class="font-bold text-white uppercase">Nouveau <span class="text-cyan-400">mot de passe</span></h4>
                    <div style="display:flex;justify-content:center;flex-direction:column;align-items:center;padding-top:45px;">

                        <div style="width:100%;display:flex;justify-content:center;flex-direction:column;align-items:center;padding-bottom:25px;">
                            <label style="text-align:left;width:340px;">
                                <i data-lucide="key-round" class="lucide w-5 h-5" style="margin-right:10px;"></i>
                                Nouveau mot de passe
                            </label>
                            <input type="password" name="password" placeholder="Min. 8 caractères" required>
                        </div>

                        <div style="width:100%;display:flex;justify-content:center;flex-direction:column;align-items:center;padding-bottom:25px;">
                            <label style="text-align:left;width:340px;">
                                <i data-lucide="key-round" class="lucide w-5 h-5" style="margin-right:10px;"></i>
                                Confirmer le mot de passe
                            </label>
                            <input type="password" name="password2" placeholder="Confirmer" required>
                        </div>

                        <?php if ($flash): ?>
                        <p style="text-align:center;padding:10px 0;font-size:13px;color:<?= $flash['type'] === 'success' ? '#22d3ee' : '#ef4444' ?>;">
                            <?= htmlspecialchars($flash['message']) ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </form>

            <div class="auth-actions" style="display:flex;justify-content:space-between;margin-top:15px;">
                <a href="./login" class="origin-btn btn--graphic btn--danger" data-sound-attached="true">
                    <i data-lucide="arrow-left" class="lucide btn--icon"></i>
                    <span>Annuler</span>
                </a>
                <button type="submit" form="reset-form" class="origin-btn btn--graphic btn--primary" data-sound-attached="true" style="font-size:16px;">
                    <span>Enregistrer</span>
                    <i data-lucide="arrow-right" class="lucide btn--icon"></i>
                </button>
            </div>

            <?php else: ?>

            <div class="auth-card" style="padding:40px;border:1px solid rgba(239,68,68,0.3);text-align:center;">
                <i data-lucide="circle-x" class="w-12 h-12 text-red-500 mx-auto mb-4"></i>
                <h4 class="font-bold text-white uppercase mb-3">Lien invalide</h4>
                <p class="text-gray-400 text-sm mb-8">Ce lien de réinitialisation est invalide ou a expiré.</p>
                <a href="./forgotten-password" class="origin-btn btn--graphic btn--primary">
                    <span>Faire une nouvelle demande</span>
                    <i data-lucide="arrow-right" class="btn--icon"></i>
                </a>
            </div>

            <?php endif; ?>
        </div>
    </section>

    <div class="login--footer">
        <?php include './assets/elements/footer.html'; ?>
    </div>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/intro.js"></script>
    <script src="assets/js/matinal.js"></script>
    <script>lucide.createIcons();</script>
</body>
</html>
