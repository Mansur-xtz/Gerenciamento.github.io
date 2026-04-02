<?php
require_once __DIR__ . '/config.php';
iniciarSessao();
session_destroy();
header('Location: login.php');
exit;
