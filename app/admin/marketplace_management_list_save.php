<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: page_marketplace_management.php'); exit; }
require_once __DIR__ . '/../../config/database.php';

$allowed = ['mktplace_challenges','mktplace_steps','mktplace_hero','mktplace_approach_cards','mktplace_impacts','mktplace_why_bullets','mktplace_cta','mktplace_section_headers','mktplace_hero_icons','mktplace_service_blocks','mktplace_service_block_cards'];
$table = $_POST['table_name'] ?? '';
if (!in_array($table, $allowed)) { header('Location: page_marketplace_management.php'); exit; }

$pdo    = Database::getInstance();
$id     = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
$sort   = (int)($_POST['sort_order'] ?? 0);
$active = (int)($_POST['is_active'] ?? 1);

switch ($table) {
    case 'mktplace_challenges':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'icon'=>$_POST['icon']??'','title'=>$_POST['title']??'','description'=>$_POST['description']??''];
        break;
    case 'mktplace_steps':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'step_number'=>$_POST['step_number']??'','title'=>$_POST['title']??'','description'=>$_POST['description']??'','icon'=>$_POST['icon']??''];
        break;
    case 'mktplace_hero':
        $pdo->prepare("UPDATE mktplace_hero SET badge=?,h1_line1=?,h1_line2_accent=?,hero_sub=?,btn_label=?,btn_url=? WHERE id=1")
            ->execute([$_POST['badge']??'',$_POST['h1_line1']??'',$_POST['h1_line2_accent']??'',$_POST['hero_sub']??'',$_POST['btn_label']??'',$_POST['btn_url']??'']);
        header('Location: page_marketplace_management.php?saved=1'); exit;
    case 'mktplace_approach_cards':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'number_label'=>$_POST['number_label']??'','title'=>$_POST['title']??'','description'=>$_POST['description']??'','icon'=>$_POST['icon']??''];
        break;
    case 'mktplace_impacts':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'icon'=>$_POST['icon']??'','title'=>$_POST['title']??'','description'=>$_POST['description']??''];
        break;
    case 'mktplace_why_bullets':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'text'=>$_POST['text']??''];
        break;
    case 'mktplace_cta':
        $pdo->prepare("UPDATE mktplace_cta SET bg_text=?,heading=?,description=?,btn1_label=?,btn1_url=?,btn2_label=?,btn2_url=? WHERE id=1")
            ->execute([$_POST['bg_text']??'',$_POST['heading']??'',$_POST['description']??'',$_POST['btn1_label']??'',$_POST['btn1_url']??'',$_POST['btn2_label']??'',$_POST['btn2_url']??'']);
        header('Location: page_marketplace_management.php?saved=1'); exit;
    case 'mktplace_section_headers':
        $slug = preg_replace('/[^a-z0-9_]/', '', $_POST['slug'] ?? '');
        if (!$slug) { header('Location: page_marketplace_management.php'); exit; }
        $pdo->prepare("UPDATE mktplace_section_headers SET eyebrow=?,heading=?,sub_text=?,extra_text=?,btn_label=?,btn_url=? WHERE slug=?")
            ->execute([$_POST['eyebrow']??'',$_POST['heading']??'',$_POST['sub_text']??'',$_POST['extra_text']??'',$_POST['btn_label']??'',$_POST['btn_url']??'',$slug]);
        header('Location: page_marketplace_management.php?saved=1'); exit;
    case 'mktplace_hero_icons':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'title'=>$_POST['title']??'','svg_file'=>$_POST['svg_file']??''];
        break;
    case 'mktplace_service_blocks':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'title'=>$_POST['title']??'','description'=>$_POST['description']??''];
        break;
    case 'mktplace_service_block_cards':
        $bj = $_POST['bullets_json']??'[]'; json_decode($bj); if(json_last_error()!==JSON_ERROR_NONE) $bj='[]';
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'service_block_id'=>(int)($_POST['service_block_id']??0),'icon'=>$_POST['icon']??'','title'=>$_POST['title']??'','description'=>$_POST['description']??'','bullets_json'=>$bj,'is_wide'=>(int)($_POST['is_wide']??0)];
        break;
    default:
        header('Location: page_marketplace_management.php'); exit;
}

if ($id > 0) {
    $sets = implode(', ', array_map(fn($k) => "`$k`=?", array_keys($fields)));
    $pdo->prepare("UPDATE `$table` SET $sets WHERE id=?")->execute([...array_values($fields), $id]);
} else {
    $cols = implode(', ', array_map(fn($k) => "`$k`", array_keys($fields)));
    $phs  = implode(', ', array_fill(0, count($fields), '?'));
    $pdo->prepare("INSERT INTO `$table` ($cols) VALUES ($phs)")->execute(array_values($fields));
}
header('Location: page_marketplace_management.php?saved=1');
exit;
