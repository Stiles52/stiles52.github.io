<?php
require_once __DIR__ . '/../import/init.php';

$base = rtrim($_ENV['APP_URL'] ?? '', '/');

$auth->logout();

header("Location: {$base}/login");
exit;
