<?php

function load_page_seo(PDO $pdo, string $identifier): array {
    try {
        $stmt = $pdo->prepare(
            "SELECT meta_title, meta_description, slug FROM page_seo WHERE page_identifier = ?"
        );
        $stmt->execute([$identifier]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Build a Schema.org Service JSON-LD block for a service page. Reuses the
 * page's own $pageTitle/$pageDescription (already flowing through
 * load_page_seo() and any CMS override) rather than separate hardcoded
 * text, so the schema can never drift out of sync with the visible meta
 * tags. Links to the sitewide Organization entity declared in header.php
 * via matching @id rather than repeating Digifyce's details.
 */
function service_schema(string $appUrl, string $pageSlug, string $serviceType, string $name, string $description): string {
    $appUrl = rtrim($appUrl, '/');
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'serviceType' => $serviceType,
        'name' => $name,
        'description' => $description,
        'url' => $appUrl . '/' . ltrim($pageSlug, '/'),
        'provider' => ['@id' => $appUrl . '/#organization'],
        'areaServed' => 'IN',
    ];
    return '<script type="application/ld+json">'
        . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        . '</script>';
}
