<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: page_d2c_branding.php'); exit; }
require_once __DIR__ . '/../../config/database.php';

$allowed = ['d2c_intro_tags','d2c_challenges','d2c_pillars','d2c_solutions','d2c_steps','d2c_metrics','d2c_why_features'];
$table = $_POST['table_name'] ?? '';
if (!in_array($table, $allowed)) { header('Location: page_d2c_branding.php'); exit; }

$pdo = Database::getInstance();
$id     = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
$sort   = (int)($_POST['sort_order'] ?? 0);
$active = (int)($_POST['is_active'] ?? 1);

switch ($table) {
    case 'd2c_intro_tags':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'tag_name'=>$_POST['tag_name']??''];
        break;
    case 'd2c_challenges':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'title'=>$_POST['title']??'','description'=>$_POST['description']??''];
        break;
    case 'd2c_pillars':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'number'=>$_POST['number']??'','name'=>$_POST['name']??'','text'=>$_POST['text']??'','dots_json'=>$_POST['dots_json']??'[]'];
        break;
    case 'd2c_solutions':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'name'=>$_POST['name']??'','description'=>$_POST['description']??''];
        break;
    case 'd2c_steps':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'step_number'=>$_POST['step_number']??'','title'=>$_POST['title']??'','description'=>$_POST['description']??''];
        break;
    case 'd2c_metrics':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'value'=>$_POST['value']??'','unit'=>$_POST['unit']??'','label'=>$_POST['label']??'','bar_pct'=>(int)($_POST['bar_pct']??100)];
        break;
    case 'd2c_why_features':
        $fields = ['sort_order'=>$sort,'is_active'=>$active,'title'=>$_POST['title']??'','description'=>$_POST['description']??''];
        break;
    default:
        header('Location: page_d2c_branding.php'); exit;
}

if ($id > 0) {
    $sets = implode(', ', array_map(fn($k) => "`$k`=?", array_keys($fields)));
    $pdo->prepare("UPDATE `$table` SET $sets WHERE id=?")->execute([...array_values($fields), $id]);
} else {
    $cols = implode(', ', array_map(fn($k) => "`$k`", array_keys($fields)));
    $phs  = implode(', ', array_fill(0, count($fields), '?'));
    $pdo->prepare("INSERT INTO `$table` ($cols) VALUES ($phs)")->execute(array_values($fields));
}
header('Location: page_d2c_branding.php?saved=1');
exit;
