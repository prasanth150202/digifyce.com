<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: page_content_marketing.php'); exit; }
require_once __DIR__ . '/../../config/database.php';

$allowed = ['content_solutions','content_hero','content_hero_stats','content_challenges','content_pillars','content_metrics','content_why_points','content_cta','content_section_headers','content_signal_points'];
$table = $_POST['table_name'] ?? '';
if (!in_array($table, $allowed)) { header('Location: page_content_marketing.php'); exit; }

$pdo    = Database::getInstance();
$id     = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
$sort   = (int)($_POST['sort_order'] ?? 0);
$active = (int)($_POST['is_active'] ?? 1);

switch ($table) {
    case 'content_solutions':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'number'=>$_POST['number']??'','title'=>$_POST['title']??'','description'=>$_POST['description']??'','bg_color'=>$_POST['bg_color']??'#0f172a'];
        break;
    case 'content_hero':
        $pdo->prepare("UPDATE content_hero SET kicker=?,h1_line1=?,h1_line2_gradient=?,hero_sub=?,btn1_label=?,btn1_url=?,btn2_label=?,btn2_url=? WHERE id=1")
            ->execute([$_POST['kicker']??'',$_POST['h1_line1']??'',$_POST['h1_line2_gradient']??'',$_POST['hero_sub']??'',$_POST['btn1_label']??'',$_POST['btn1_url']??'',$_POST['btn2_label']??'',$_POST['btn2_url']??'']);
        header('Location: page_content_marketing.php?saved=1'); exit;
    case 'content_hero_stats':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'label'=>$_POST['label']??'','value'=>$_POST['value']??'','description'=>$_POST['description']??''];
        break;
    case 'content_challenges':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'number_label'=>$_POST['number_label']??'','title'=>$_POST['title']??'','description'=>$_POST['description']??'','progress_width'=>$_POST['progress_width']??'w-1/2'];
        break;
    case 'content_pillars':
        $bj = $_POST['bullets_json']??'[]'; json_decode($bj); if(json_last_error()!==JSON_ERROR_NONE) $bj='[]';
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'number_label'=>$_POST['number_label']??'','name'=>$_POST['name']??'','panel_title'=>$_POST['panel_title']??'','panel_description'=>$_POST['panel_description']??'','bullets_json'=>$bj];
        break;
    case 'content_metrics':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'target_num'=>(int)($_POST['target_num']??0),'label'=>$_POST['label']??''];
        break;
    case 'content_why_points':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'icon'=>$_POST['icon']??'','title'=>$_POST['title']??'','description'=>$_POST['description']??''];
        break;
    case 'content_cta':
        $pdo->prepare("UPDATE content_cta SET bg_text=?,heading=?,description=?,btn_label=?,btn_url=? WHERE id=1")
            ->execute([$_POST['bg_text']??'',$_POST['heading']??'',$_POST['description']??'',$_POST['btn_label']??'',$_POST['btn_url']??'']);
        header('Location: page_content_marketing.php?saved=1'); exit;
    case 'content_section_headers':
        $slug = preg_replace('/[^a-z0-9_]/', '', $_POST['slug'] ?? '');
        if (!$slug) { header('Location: page_content_marketing.php'); exit; }
        $pdo->prepare("UPDATE content_section_headers SET eyebrow=?,heading=?,sub_text=?,extra_text=?,btn_label=?,btn_url=? WHERE slug=?")
            ->execute([$_POST['eyebrow']??'',$_POST['heading']??'',$_POST['sub_text']??'',$_POST['extra_text']??'',$_POST['btn_label']??'',$_POST['btn_url']??'',$slug]);
        header('Location: page_content_marketing.php?saved=1'); exit;
    case 'content_signal_points':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'title'=>$_POST['title']??'','description'=>$_POST['description']??''];
        break;
    default:
        header('Location: page_content_marketing.php'); exit;
}

if ($id > 0) {
    $sets = implode(', ', array_map(fn($k) => "`$k`=?", array_keys($fields)));
    $pdo->prepare("UPDATE `$table` SET $sets WHERE id=?")->execute([...array_values($fields), $id]);
} else {
    $cols = implode(', ', array_map(fn($k) => "`$k`", array_keys($fields)));
    $phs  = implode(', ', array_fill(0, count($fields), '?'));
    $pdo->prepare("INSERT INTO `$table` ($cols) VALUES ($phs)")->execute(array_values($fields));
}
header('Location: page_content_marketing.php?saved=1');
exit;
