<?php
require_once __DIR__ . '/assets/import/init.php';

$token  = trim($_GET['token'] ?? '');
$result = !empty($token)
    ? $auth->verifyEmail($token)
    : ['success' => false, 'message' => 'Token manquant.'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://unpkg.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self';">
    <title>Vérification e-mail - ORIGIN</title>
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    <link href="./assets/css/style.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-black min-h-screen flex items-center justify-center">

    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute w-full h-full bg-black"></div>
        <div class="fixed bottom-[-150px] left-1/2 -translate-x-1/2 w-[600px] h-[400px] bg-cyan-500/15 blur-[120px] rounded-full pointer-events-none z-0"></div>
    </div>

    <div class="relative z-10 text-center px-6" style="max-width: 480px;">
        <?php if ($result['success']): ?>
            <div style="border:1px solid rgba(34,211,238,0.3); padding:40px; background:rgba(0,0,0,0.8);">
                <i data-lucide="circle-check" class="w-16 h-16 text-cyan-400 mx-auto mb-6"></i>
                <h3 class="text-white font-bold uppercase mb-3">E-mail vérifié !</h3>
                <p class="text-gray-400 text-sm mb-8"><?= htmlspecialchars($result['message']) ?></p>
                <a href="./login" class="origin-btn btn--graphic btn--primary">
                    <span>Se connecter</span>
                    <i data-lucide="arrow-right" class="btn--icon"></i>
                </a>
            </div>
        <?php else: ?>
            <div style="border:1px solid rgba(239,68,68,0.3); padding:40px; background:rgba(0,0,0,0.8);">
                <i data-lucide="circle-x" class="w-16 h-16 text-red-500 mx-auto mb-6"></i>
                <h3 class="text-white font-bold uppercase mb-3">Lien invalide</h3>
                <p class="text-gray-400 text-sm mb-8"><?= htmlspecialchars($result['message']) ?></p>
                <a href="./register" class="origin-btn btn--graphic btn--danger">
                    <i data-lucide="arrow-left" class="btn--icon"></i>
                    <span>Retour à l'inscription</span>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script src="assets/js/main.js"></script>
    <script>lucide.createIcons();</script>
</body>
</html>
