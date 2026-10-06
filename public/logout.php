<?php

require_once __DIR__ . '/../app/services/Auth.php';

Auth::logout();

header("Location: login.php");

exit;
