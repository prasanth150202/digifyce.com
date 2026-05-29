<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: page_d2c_branding.php'); exit; }
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getInstance();
$section = $_POST['section'] ?? '';

switch ($section) {
    case 'hero':
        $pdo->prepare("UPDATE d2c_hero SET badge_text=?,headline_main=?,headline_accent=?,sub_description=?,btn1_label=?,btn1_url=?,btn2_label=?,btn2_url=? WHERE id=1")
            ->execute([$_POST['badge_text']??'',$_POST['headline_main']??'',$_POST['headline_accent']??'',$_POST['sub_description']??'',$_POST['btn1_label']??'',$_POST['btn1_url']??'',$_POST['btn2_label']??'',$_POST['btn2_url']??'']);
        break;
    case 'cta':
        $pdo->prepare("UPDATE d2c_cta SET heading=?,description=?,btn_label=?,btn_url=? WHERE id=1")
            ->execute([$_POST['heading']??'',$_POST['description']??'',$_POST['btn_label']??'',$_POST['btn_url']??'']);
        break;
    case 'd2c_section_headers':
        $slug = preg_replace('/[^a-z0-9_]/', '', $_POST['slug'] ?? '');
        if (!$slug) { header('Location: page_d2c_branding.php'); exit; }
        $pdo->prepare("UPDATE d2c_section_headers SET eyebrow=?,heading=?,sub_text=?,extra_text=?,btn_label=?,btn_url=? WHERE slug=?")
            ->execute([$_POST['eyebrow']??'',$_POST['heading']??'',$_POST['sub_text']??'',$_POST['extra_text']??'',$_POST['btn_label']??'',$_POST['btn_url']??'',$slug]);
        break;
}
header('Location: page_d2c_branding.php?saved=1');
exit;
