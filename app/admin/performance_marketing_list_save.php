<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: ' . admin_login_url()); exit; }

require_once __DIR__ . '/../../config/database.php';
$pdo = Database::getInstance();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$allowed = ['pm_challenges','pm_approaches','pm_services','pm_leadgen_tabs','pm_seo_panels','pm_steps','pm_impacts','pm_hero','pm_hero_metrics','pm_benchmark_groups','pm_section_headers'];
$table = $_POST['table_name'] ?? '';
if (!in_array($table, $allowed, true)) {
    http_response_code(400); exit('Invalid table');
}

$id         = (int)($_POST['id'] ?? 0);
$sort_order = (int)($_POST['sort_order'] ?? 0);
$is_active  = isset($_POST['is_active']) ? 1 : 0;

switch ($table) {
    case 'pm_challenges':
        $fields = [
            'icon'       => trim($_POST['icon'] ?? ''),
            'text'       => trim($_POST['text'] ?? ''),
            'sort_order' => $sort_order,
            'is_active'  => $is_active,
        ];
        break;

    case 'pm_approaches':
        $fields = [
            'step_label'  => trim($_POST['step_label'] ?? ''),
            'heading'     => trim($_POST['heading'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'sort_order'  => $sort_order,
            'is_active'   => $is_active,
        ];
        break;

    case 'pm_services':
        $chips_raw = trim($_POST['chips_json'] ?? '[]');
        json_decode($chips_raw);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $chips_raw = '[]';
        }
        $fields = [
            'tag'         => trim($_POST['tag'] ?? ''),
            'heading'     => trim($_POST['heading'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'chips_json'  => $chips_raw,
            'sort_order'  => $sort_order,
            'is_active'   => $is_active,
        ];
        break;

    case 'pm_leadgen_tabs':
        $bullets_raw = trim($_POST['bullets_json'] ?? '[]');
        json_decode($bullets_raw);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $bullets_raw = '[]';
        }
        $fields = [
            'tab_icon'   => trim($_POST['tab_icon'] ?? ''),
            'title'      => trim($_POST['title'] ?? ''),
            'intro_text' => trim($_POST['intro_text'] ?? ''),
            'bullets_json' => $bullets_raw,
            'conclusion' => trim($_POST['conclusion'] ?? ''),
            'sort_order' => $sort_order,
            'is_active'  => $is_active,
        ];
        break;

    case 'pm_seo_panels':
        $bullets_raw = trim($_POST['bullets_json'] ?? '[]');
        json_decode($bullets_raw);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $bullets_raw = '[]';
        }
        $fields = [
            'panel_icon'  => trim($_POST['panel_icon'] ?? ''),
            'title'       => trim($_POST['title'] ?? ''),
            'intro_text'  => trim($_POST['intro_text'] ?? ''),
            'bullets_json'=> $bullets_raw,
            'conclusion'  => trim($_POST['conclusion'] ?? ''),
            'sort_order'  => $sort_order,
            'is_active'   => $is_active,
        ];
        break;

    case 'pm_steps':
        $fields = [
            'icon'        => trim($_POST['icon'] ?? ''),
            'heading'     => trim($_POST['heading'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'sort_order'  => $sort_order,
            'is_active'   => $is_active,
        ];
        break;

    case 'pm_impacts':
        $fields = [
            'icon'        => trim($_POST['icon'] ?? ''),
            'icon_color'  => trim($_POST['icon_color'] ?? ''),
            'heading'     => trim($_POST['heading'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'sort_order'  => $sort_order,
            'is_active'   => $is_active,
        ];
        break;

    case 'pm_hero':
        $fields = [
            'kicker'          => trim($_POST['kicker'] ?? ''),
            'headline_main'   => trim($_POST['headline_main'] ?? ''),
            'headline_accent' => trim($_POST['headline_accent'] ?? ''),
            'sub_text'        => trim($_POST['sub_text'] ?? ''),
            'btn1_label'      => trim($_POST['btn1_label'] ?? ''),
            'btn1_url'        => trim($_POST['btn1_url'] ?? ''),
            'btn2_label'      => trim($_POST['btn2_label'] ?? ''),
            'btn2_url'        => trim($_POST['btn2_url'] ?? ''),
            'card_heading'    => trim($_POST['card_heading'] ?? ''),
            'card_body'       => trim($_POST['card_body'] ?? ''),
            'card_body2'      => trim($_POST['card_body2'] ?? ''),
        ];
        $sets = implode(', ', array_map(fn($k) => "`$k`=:$k", array_keys($fields)));
        $pdo->prepare("UPDATE pm_hero SET $sets WHERE id=1")->execute($fields);
        header('Location: page_performance_marketing.php?saved=1');
        exit;

    case 'pm_hero_metrics':
        $fields = [
            'value'      => (int)($_POST['value'] ?? 0),
            'text'       => trim($_POST['text'] ?? ''),
            'sort_order' => $sort_order,
            'is_active'  => $is_active,
        ];
        break;

    case 'pm_benchmark_groups':
        $rows_raw = trim($_POST['rows_json'] ?? '[]');
        json_decode($rows_raw);
        if (json_last_error() !== JSON_ERROR_NONE) { $rows_raw = '[]'; }
        $fields = [
            'industry_icon'  => trim($_POST['industry_icon'] ?? ''),
            'industry_label' => trim($_POST['industry_label'] ?? ''),
            'rows_json'      => $rows_raw,
            'sort_order'     => $sort_order,
            'is_active'      => $is_active,
        ];
        break;

    case 'pm_section_headers':
        $allowed_slugs = ['problem','approach','services','leadgen','seo','process','impact','cta'];
        $slug = trim($_POST['slug'] ?? '');
        if (!in_array($slug, $allowed_slugs, true)) { http_response_code(400); exit('Invalid slug'); }
        $f = [
            'slug'       => $slug,
            'kicker'     => trim($_POST['kicker'] ?? ''),
            'heading'    => trim($_POST['heading'] ?? ''),
            'sub_text'   => trim($_POST['sub_text'] ?? ''),
            'extra_text' => trim($_POST['extra_text'] ?? ''),
            'btn_label'  => trim($_POST['btn_label'] ?? ''),
            'btn_url'    => trim($_POST['btn_url'] ?? ''),
            'btn2_label' => trim($_POST['btn2_label'] ?? ''),
            'btn2_url'   => trim($_POST['btn2_url'] ?? ''),
        ];
        $pdo->prepare("INSERT INTO pm_section_headers (slug,kicker,heading,sub_text,extra_text,btn_label,btn_url,btn2_label,btn2_url) VALUES (:slug,:kicker,:heading,:sub_text,:extra_text,:btn_label,:btn_url,:btn2_label,:btn2_url) ON DUPLICATE KEY UPDATE kicker=VALUES(kicker),heading=VALUES(heading),sub_text=VALUES(sub_text),extra_text=VALUES(extra_text),btn_label=VALUES(btn_label),btn_url=VALUES(btn_url),btn2_label=VALUES(btn2_label),btn2_url=VALUES(btn2_url)")->execute($f);
        header('Location: page_performance_marketing.php?saved=1');
        exit;

    default:
        exit('Unhandled table');
}

if ($id > 0) {
    $sets = implode(', ', array_map(fn($k) => "`$k`=:$k", array_keys($fields)));
    $stmt = $pdo->prepare("UPDATE `$table` SET $sets WHERE id=:id");
    $stmt->execute(array_merge($fields, ['id' => $id]));
} else {
    $cols = implode(', ', array_map(fn($k) => "`$k`", array_keys($fields)));
    $pls  = implode(', ', array_map(fn($k) => ":$k", array_keys($fields)));
    $stmt = $pdo->prepare("INSERT INTO `$table` ($cols) VALUES ($pls)");
    $stmt->execute($fields);
}

header('Location: page_performance_marketing.php?saved=1');
exit;
