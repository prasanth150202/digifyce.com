<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: page_creative_development.php'); exit; }
require_once __DIR__ . '/../../config/database.php';

$allowed = ['creative_pains','creative_pillars','creative_services','creative_steps','creative_metrics','creative_why_cards','creative_hero','creative_stat_chips','creative_cta','cd_section_headers','cd_video_cards'];
$table = $_POST['table_name'] ?? '';
if (!in_array($table, $allowed)) { header('Location: page_creative_development.php'); exit; }

$pdo    = Database::getInstance();
$id     = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
$sort   = (int)($_POST['sort_order'] ?? 0);
$active = (int)($_POST['is_active'] ?? 1);

switch ($table) {
    case 'creative_pains':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'text'=>$_POST['text']??''];
        break;
    case 'creative_pillars':
        $tagsRaw = $_POST['tags_json'] ?? '[]';
        json_decode($tagsRaw);
        if (json_last_error() !== JSON_ERROR_NONE) $tagsRaw = '[]';
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'number'=>$_POST['number']??'','name'=>$_POST['name']??'','description'=>$_POST['description']??'','tags_json'=>$tagsRaw,'svg_path'=>$_POST['svg_path']??''];
        break;
    case 'creative_services':
        $bulletsRaw = $_POST['bullets_json'] ?? '[]';
        json_decode($bulletsRaw);
        if (json_last_error() !== JSON_ERROR_NONE) $bulletsRaw = '[]';
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'name'=>$_POST['name']??'','subtitle'=>$_POST['subtitle']??'','description'=>$_POST['description']??'','bullets_json'=>$bulletsRaw];
        break;
    case 'creative_steps':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'step_number'=>$_POST['step_number']??'','title'=>$_POST['title']??'','description'=>$_POST['description']??''];
        break;
    case 'creative_metrics':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'value'=>$_POST['value']??'','unit'=>$_POST['unit']??'','label'=>$_POST['label']??''];
        break;
    case 'creative_why_cards':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'number'=>$_POST['number']??'','title'=>$_POST['title']??'','description'=>$_POST['description']??''];
        break;
    case 'creative_hero':
        $pdo->prepare("UPDATE creative_hero SET kicker=?,h1_line1=?,h1_line2_accent=?,hero_sub=?,btn1_label=?,btn1_url=?,btn2_label=?,btn2_url=? WHERE id=1")
            ->execute([$_POST['kicker']??'',$_POST['h1_line1']??'',$_POST['h1_line2_accent']??'',$_POST['hero_sub']??'',$_POST['btn1_label']??'',$_POST['btn1_url']??'',$_POST['btn2_label']??'',$_POST['btn2_url']??'']);
        header('Location: page_creative_development.php?saved=1'); exit;
    case 'creative_stat_chips':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'chip_num'=>$_POST['chip_num']??'','chip_lbl'=>$_POST['chip_lbl']??''];
        break;
    case 'creative_cta':
        $pdo->prepare("UPDATE creative_cta SET bg_text=?,heading=?,description=?,btn_label=?,btn_url=? WHERE id=1")
            ->execute([$_POST['bg_text']??'',$_POST['heading']??'',$_POST['description']??'',$_POST['btn_label']??'',$_POST['btn_url']??'']);
        header('Location: page_creative_development.php?saved=1'); exit;
    case 'cd_section_headers':
        $slug = preg_replace('/[^a-z0-9_]/', '', $_POST['slug'] ?? '');
        if (!$slug) { header('Location: page_creative_development.php'); exit; }
        $pdo->prepare("UPDATE cd_section_headers SET eyebrow=?,heading=?,sub_text=?,extra_text=?,btn_label=?,btn_url=? WHERE slug=?")
            ->execute([$_POST['eyebrow']??'',$_POST['heading']??'',$_POST['sub_text']??'',$_POST['extra_text']??'',$_POST['btn_label']??'',$_POST['btn_url']??'',$slug]);
        header('Location: page_creative_development.php?saved=1'); exit;
    case 'cd_video_cards':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'track'=>(int)($_POST['track']??1),'title'=>$_POST['title']??''];
        break;
    default:
        header('Location: page_creative_development.php'); exit;
}

if ($id > 0) {
    $sets = implode(', ', array_map(fn($k) => "`$k`=?", array_keys($fields)));
    $pdo->prepare("UPDATE `$table` SET $sets WHERE id=?")->execute([...array_values($fields), $id]);
} else {
    $cols = implode(', ', array_map(fn($k) => "`$k`", array_keys($fields)));
    $phs  = implode(', ', array_fill(0, count($fields), '?'));
    $pdo->prepare("INSERT INTO `$table` ($cols) VALUES ($phs)")->execute(array_values($fields));
}
header('Location: page_creative_development.php?saved=1');
exit;
