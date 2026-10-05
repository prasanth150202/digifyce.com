<?php
// Loads the store list for the "Shopify Stores We've Built" section. Shared by
// shopify-development.php and tools/capture_showcase.php so both agree on each
// store's slug and screenshot path.

const SHOPIFY_SHOWCASE_DIR = 'public/assets/img/shopify-showcase';

function shopify_showcase_items(string $root): array {
    $file = $root . '/app/webContent/shopify-showcase.php';
    $list = is_file($file) ? require $file : [];

    $items = [];
    foreach ((array) $list as $store) {
        $name = trim((string) ($store['name'] ?? ''));
        $url  = trim((string) ($store['url'] ?? ''));
        if ($name === '' || !preg_match('#^https?://#i', $url)) continue;

        $slug = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($name)), '-');
        $items[] = [
            'name'         => $name,
            'url'          => $url,
            'domain'       => preg_replace('/^www\./i', '', (string) parse_url($url, PHP_URL_HOST)),
            'tags'         => array_values(array_filter((array) ($store['tags'] ?? []))),
            'slug'         => $slug,
            'image'        => ltrim((string) ($store['image'] ?? SHOPIFY_SHOWCASE_DIR . '/' . $slug . '.webp'), '/'),
            'custom_image' => isset($store['image']),
        ];
    }
    return $items;
}
