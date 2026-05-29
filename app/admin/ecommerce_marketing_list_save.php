<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: page_ecommerce_marketing.php'); exit; }
require_once __DIR__ . '/../../config/database.php';

$allowed = ['ecom_challenges','ecom_approaches','ecom_steps','ecom_hero','ecom_solutions','ecom_why_points','ecom_cta','ecom_section_headers'];
$table = $_POST['table_name'] ?? '';
if (!in_array($table, $allowed)) { header('Location: page_ecommerce_marketing.php'); exit; }

$pdo    = Database::getInstance();
$id     = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
$sort   = (int)($_POST['sort_order'] ?? 0);
$active = (int)($_POST['is_active'] ?? 1);

switch ($table) {
    case 'ecom_challenges':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'icon'=>$_POST['icon']??'','title'=>$_POST['title']??'','description'=>$_POST['description']??''];
        break;
    case 'ecom_approaches':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'number'=>$_POST['number']??'','title'=>$_POST['title']??'','description'=>$_POST['description']??'','tag'=>$_POST['tag']??''];
        break;
    case 'ecom_steps':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'step_number'=>$_POST['step_number']??'','title'=>$_POST['title']??'','description'=>$_POST['description']??''];
        break;
    case 'ecom_hero':
        $fields = ['badge'=>$_POST['badge']??'','h1_line1'=>$_POST['h1_line1']??'','h1_line2_accent'=>$_POST['h1_line2_accent']??'','hero_sub'=>$_POST['hero_sub']??'','btn_label'=>$_POST['btn_label']??'','btn_url'=>$_POST['btn_url']??''];
        $pdo->prepare("UPDATE ecom_hero SET badge=?,h1_line1=?,h1_line2_accent=?,hero_sub=?,btn_label=?,btn_url=? WHERE id=1")->execute(array_values($fields));
        header('Location: page_ecommerce_marketing.php?saved=1'); exit;
    case 'ecom_solutions':
        $bj = $_POST['bullets_json']??'[]'; json_decode($bj); if(json_last_error()!==JSON_ERROR_NONE) $bj='[]';
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'tag_label'=>$_POST['tag_label']??'','tag_color'=>$_POST['tag_color']??'blue','title'=>$_POST['title']??'','description'=>$_POST['description']??'','bullets_json'=>$bj,'bg_image'=>$_POST['bg_image']??''];
        break;
    case 'ecom_why_points':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'icon'=>$_POST['icon']??'','title'=>$_POST['title']??'','description'=>$_POST['description']??''];
        break;
    case 'ecom_cta':
        $fields = ['bg_text'=>$_POST['bg_text']??'','heading'=>$_POST['heading']??'','description'=>$_POST['description']??'','btn_label'=>$_POST['btn_label']??'','btn_url'=>$_POST['btn_url']??''];
        $pdo->prepare("UPDATE ecom_cta SET bg_text=?,heading=?,description=?,btn_label=?,btn_url=? WHERE id=1")->execute(array_values($fields));
        header('Location: page_ecommerce_marketing.php?saved=1'); exit;
    case 'ecom_section_headers':
        $slug = preg_replace('/[^a-z0-9_]/', '', $_POST['slug'] ?? '');
        if (!$slug) { header('Location: page_ecommerce_marketing.php'); exit; }
        $pdo->prepare("UPDATE ecom_section_headers SET eyebrow=?,heading=?,sub_text=?,extra_text=?,btn_label=?,btn_url=? WHERE slug=?")
            ->execute([$_POST['eyebrow']??'',$_POST['heading']??'',$_POST['sub_text']??'',$_POST['extra_text']??'',$_POST['btn_label']??'',$_POST['btn_url']??'',$slug]);
        header('Location: page_ecommerce_marketing.php?saved=1'); exit;
    default:
        header('Location: page_ecommerce_marketing.php'); exit;
}

if ($id > 0) {
    $sets = implode(', ', array_map(fn($k) => "`$k`=?", array_keys($fields)));
    $pdo->prepare("UPDATE `$table` SET $sets WHERE id=?")->execute([...array_values($fields), $id]);
} else {
    $cols = implode(', ', array_map(fn($k) => "`$k`", array_keys($fields)));
    $phs  = implode(', ', array_fill(0, count($fields), '?'));
    $pdo->prepare("INSERT INTO `$table` ($cols) VALUES ($phs)")->execute(array_values($fields));
}
header('Location: page_ecommerce_marketing.php?saved=1');
exit;
