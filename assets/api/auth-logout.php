<?php
require_once __DIR__ . '/../../assets/import/init.php';

$auth->logout();

header('Location: ../../login');
exit;
