<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://unpkg.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src https://fonts.gstatic.com; img-src 'self' data: https:; frame-src https://www.youtube-nocookie.com; connect-src 'self';">
    <title>DASHBOARD - ORIGIN</title>

    <link rel="icon" type="image/png" href="assets/images/favicon.png">

    <link href="./assets/css/style.css" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-black h-screen w-full flex overflow-hidden">
    <?php include "./assets/elements/nav-dashboard.php"; ?>

    <?php
    if(empty($_GET['page']) || $_GET['page'] == "dashboard") {
        include "./assets/dashboard/main.php";
    } else {
        if (file_exists("./assets/dashboard/".$_GET['page'].".php")) {
            include "./assets/dashboard/".$_GET['page'].".php";
        } else {
            include "./assets/dashboard/error-404.php";
        }
    }
    ?>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/dashboard.js"></script>
</body>
</html>