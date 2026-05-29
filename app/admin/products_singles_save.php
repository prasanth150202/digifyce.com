<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: page_products.php'); exit; }
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getInstance();
$section = $_POST['section'] ?? '';

switch ($section) {
    case 'hero':
        $pdo->prepare("UPDATE products_hero SET headline_main=?,headline_highlight=?,description=?,crm_btn_label=?,zingbot_btn_label=? WHERE id=1")
            ->execute([$_POST['headline_main']??'',$_POST['headline_highlight']??'',$_POST['description']??'',$_POST['crm_btn_label']??'',$_POST['zingbot_btn_label']??'']);
        break;
    case 'crm':
        $pdo->prepare("UPDATE products_crm_section SET label=?,heading=?,sub_desc=?,cta_label=?,cta_url=? WHERE id=1")
            ->execute([$_POST['label']??'',$_POST['heading']??'',$_POST['sub_desc']??'',$_POST['cta_label']??'',$_POST['cta_url']??'']);
        break;
    case 'zingbot':
        $pdo->prepare("UPDATE products_zingbot_section SET label=?,heading=?,sub_desc=?,cta_label=?,cta_url=? WHERE id=1")
            ->execute([$_POST['label']??'',$_POST['heading']??'',$_POST['sub_desc']??'',$_POST['cta_label']??'',$_POST['cta_url']??'']);
        break;
    case 'cta':
        $pdo->prepare("UPDATE products_cta SET heading=?,description=?,btn_label=?,btn_url=? WHERE id=1")
            ->execute([$_POST['heading']??'',$_POST['description']??'',$_POST['btn_label']??'',$_POST['btn_url']??'']);
        break;
}
header('Location: page_products.php?saved=1');
exit;
