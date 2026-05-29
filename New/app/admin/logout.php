<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
session_destroy();
header('Location: ' . admin_login_url());
exit;
