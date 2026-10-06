<?php

require_once __DIR__ . '/../app/bootstrap.php';

$newMode = toggle_mode();

$back = $_SERVER['HTTP_REFERER'] ?? 'index.php';

header("Location: " . $back);
exit;
