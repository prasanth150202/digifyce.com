<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: page_about_us.php'); exit; }
require_once __DIR__ . '/../../config/database.php';

$allowed = ['about_hero','about_hero_stats','about_section_headers','about_why_cards','about_who_sub_cards','about_what_we_do','about_approach_pillars','about_why_digi_cards','about_mission_vision','about_cta'];
$table = $_POST['table_name'] ?? '';
if (!in_array($table, $allowed)) { header('Location: page_about_us.php'); exit; }

$pdo    = Database::getInstance();
$id     = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
$sort   = (int)($_POST['sort_order'] ?? 0);
$active = (int)($_POST['is_active'] ?? 1);

switch ($table) {
    case 'about_hero':
        $pdo->prepare("UPDATE about_hero SET h1_line1=?,h1_line2_accent=?,h1_line3=?,subtext=?,btn1_label=?,btn1_url=?,btn2_label=?,btn2_url=? WHERE id=1")
            ->execute([$_POST['h1_line1']??'',$_POST['h1_line2_accent']??'',$_POST['h1_line3']??'',$_POST['subtext']??'',$_POST['btn1_label']??'',$_POST['btn1_url']??'',$_POST['btn2_label']??'',$_POST['btn2_url']??'']);
        header('Location: page_about_us.php?saved=1'); exit;
    case 'about_hero_stats':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'badge'=>$_POST['badge']??'','number'=>$_POST['number']??'','description'=>$_POST['description']??''];
        break;
    case 'about_section_headers':
        $slug = preg_replace('/[^a-z0-9_]/', '', $_POST['slug'] ?? '');
        if (!$slug) { header('Location: page_about_us.php'); exit; }
        $pdo->prepare("UPDATE about_section_headers SET eyebrow=?,heading=?,sub_text=?,extra_text=?,btn_label=?,btn_url=? WHERE slug=?")
            ->execute([$_POST['eyebrow']??'',$_POST['heading']??'',$_POST['sub_text']??'',$_POST['extra_text']??'',$_POST['btn_label']??'',$_POST['btn_url']??'',$slug]);
        header('Location: page_about_us.php?saved=1'); exit;
    case 'about_why_cards':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'card_number'=>$_POST['card_number']??'','body_text'=>$_POST['body_text']??''];
        break;
    case 'about_who_sub_cards':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'badge'=>$_POST['badge']??'','text'=>$_POST['text']??''];
        break;
    case 'about_what_we_do':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'number'=>$_POST['number']??'','title'=>$_POST['title']??'','description'=>$_POST['description']??''];
        break;
    case 'about_approach_pillars':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'number'=>$_POST['number']??'','badge'=>$_POST['badge']??'','title'=>$_POST['title']??'','description'=>$_POST['description']??''];
        break;
    case 'about_why_digi_cards':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'badge'=>$_POST['badge']??'','title'=>$_POST['title']??'','description'=>$_POST['description']??''];
        break;
    case 'about_mission_vision':
        $pdo->prepare("UPDATE about_mission_vision SET mission_badge=?,mission_title=?,mission_text=?,vision_badge=?,vision_title=?,vision_text=? WHERE id=1")
            ->execute([$_POST['mission_badge']??'',$_POST['mission_title']??'',$_POST['mission_text']??'',$_POST['vision_badge']??'',$_POST['vision_title']??'',$_POST['vision_text']??'']);
        header('Location: page_about_us.php?saved=1'); exit;
    case 'about_cta':
        $pdo->prepare("UPDATE about_cta SET badge=?,heading=?,description=?,btn_label=?,btn_url=? WHERE id=1")
            ->execute([$_POST['badge']??'',$_POST['heading']??'',$_POST['description']??'',$_POST['btn_label']??'',$_POST['btn_url']??'']);
        header('Location: page_about_us.php?saved=1'); exit;
    default:
        header('Location: page_about_us.php'); exit;
}

if ($id > 0) {
    $sets = implode(', ', array_map(fn($k) => "`$k`=?", array_keys($fields)));
    $pdo->prepare("UPDATE `$table` SET $sets WHERE id=?")->execute([...array_values($fields), $id]);
} else {
    $cols = implode(', ', array_map(fn($k) => "`$k`", array_keys($fields)));
    $phs  = implode(', ', array_fill(0, count($fields), '?'));
    $pdo->prepare("INSERT INTO `$table` ($cols) VALUES ($phs)")->execute(array_values($fields));
}
header('Location: page_about_us.php?saved=1');
exit;
