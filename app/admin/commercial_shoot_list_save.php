<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: page_commercial_shoot.php'); exit; }
require_once __DIR__ . '/../../config/database.php';

$allowed = ['commercial_shoot_challenges','commercial_shoot_services','commercial_shoot_steps','commercial_shoot_impacts','cs_hero','cs_hero_features','cs_section_headers','cs_approach_panels','cs_why_bullets','cs_cta'];
$table = $_POST['table_name'] ?? '';
if (!in_array($table, $allowed)) { header('Location: page_commercial_shoot.php'); exit; }

$pdo    = Database::getInstance();
$id     = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
$sort   = (int)($_POST['sort_order'] ?? 0);
$active = (int)($_POST['is_active'] ?? 1);

switch ($table) {
    case 'commercial_shoot_challenges':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'text'=>$_POST['text']??''];
        break;
    case 'commercial_shoot_services':
        $chipsRaw = $_POST['chips_json'] ?? '[]';
        json_decode($chipsRaw);
        if (json_last_error() !== JSON_ERROR_NONE) $chipsRaw = '[]';
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'eyebrow'=>$_POST['eyebrow']??'','heading'=>$_POST['heading']??'','description'=>$_POST['description']??'','chips_json'=>$chipsRaw,'img_src'=>$_POST['img_src']??''];
        break;
    case 'commercial_shoot_steps':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'step_number'=>$_POST['step_number']??'','title'=>$_POST['title']??'','description'=>$_POST['description']??''];
        break;
    case 'commercial_shoot_impacts':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'title'=>$_POST['title']??'','description'=>$_POST['description']??''];
        break;
    case 'cs_hero':
        $pdo->prepare("UPDATE cs_hero SET eyebrow=?,h1_line1=?,h1_line2_accent=?,hero_copy=?,btn1_label=?,btn1_url=?,btn2_label=?,btn2_url=? WHERE id=1")
            ->execute([$_POST['eyebrow']??'',$_POST['h1_line1']??'',$_POST['h1_line2_accent']??'',$_POST['hero_copy']??'',$_POST['btn1_label']??'',$_POST['btn1_url']??'',$_POST['btn2_label']??'',$_POST['btn2_url']??'']);
        header('Location: page_commercial_shoot.php?saved=1'); exit;
    case 'cs_hero_features':
        $cj = $_POST['chips_json']??'[]'; json_decode($cj); if(json_last_error()!==JSON_ERROR_NONE) $cj='[]';
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'label'=>$_POST['label']??'','title'=>$_POST['title']??'','copy'=>$_POST['copy']??'','footer_text'=>$_POST['footer_text']??'','chips_json'=>$cj];
        break;
    case 'cs_section_headers':
        $slug = preg_replace('/[^a-z0-9_]/', '', $_POST['slug'] ?? '');
        if (!$slug) { header('Location: page_commercial_shoot.php'); exit; }
        $pdo->prepare("UPDATE cs_section_headers SET eyebrow=?,heading=?,sub_text=?,extra_text=?,btn_label=?,btn_url=? WHERE slug=?")
            ->execute([$_POST['eyebrow']??'',$_POST['heading']??'',$_POST['sub_text']??'',$_POST['extra_text']??'',$_POST['btn_label']??'',$_POST['btn_url']??'',$slug]);
        header('Location: page_commercial_shoot.php?saved=1'); exit;
    case 'cs_approach_panels':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'step_label'=>$_POST['step_label']??'','heading'=>$_POST['heading']??'','description'=>$_POST['description']??'','is_full_width'=>(int)($_POST['is_full_width']??0)];
        break;
    case 'cs_why_bullets':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'text'=>$_POST['text']??''];
        break;
    case 'cs_cta':
        $pdo->prepare("UPDATE cs_cta SET bg_text=?,heading=?,description=?,btn1_label=?,btn1_url=?,btn2_label=?,btn2_url=? WHERE id=1")
            ->execute([$_POST['bg_text']??'',$_POST['heading']??'',$_POST['description']??'',$_POST['btn1_label']??'',$_POST['btn1_url']??'',$_POST['btn2_label']??'',$_POST['btn2_url']??'']);
        header('Location: page_commercial_shoot.php?saved=1'); exit;
    default:
        header('Location: page_commercial_shoot.php'); exit;
}

if ($id > 0) {
    $sets = implode(', ', array_map(fn($k) => "`$k`=?", array_keys($fields)));
    $pdo->prepare("UPDATE `$table` SET $sets WHERE id=?")->execute([...array_values($fields), $id]);
} else {
    $cols = implode(', ', array_map(fn($k) => "`$k`", array_keys($fields)));
    $phs  = implode(', ', array_fill(0, count($fields), '?'));
    $pdo->prepare("INSERT INTO `$table` ($cols) VALUES ($phs)")->execute(array_values($fields));
}
header('Location: page_commercial_shoot.php?saved=1');
exit;
