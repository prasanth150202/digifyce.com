<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) { header('Location: page_technology.php'); exit; }
require_once __DIR__ . '/../../config/database.php';

Database::getInstance()->prepare("DELETE FROM technology_panels WHERE id=?")->execute([(int)$_POST['id']]);
header('Location: page_technology.php?deleted=1#tab-panels');
exit;
