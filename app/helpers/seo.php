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

/**
 * Returns a ' width="W" height="H"' attribute string for an <img>, read from
 * the real local file so declared dimensions can never drift from what's
 * actually on disk. $docRoot is the absolute filesystem path the $srcPath is
 * relative to; $srcPath is skipped (returns '') if empty or an external
 * http(s) URL, since resolving those would mean a blocking network fetch on
 * every page render.
 */
function image_dims_attr(string $docRoot, string $srcPath): string {
    if ($srcPath === '' || preg_match('#^https?://#i', $srcPath)) return '';
    $abs = rtrim($docRoot, '/') . '/' . ltrim($srcPath, '/');
    $size = @getimagesize($abs);
    if (!$size) return '';
    return ' width="' . (int)$size[0] . '" height="' . (int)$size[1] . '"';
}

/**
 * Build a Schema.org FAQPage JSON-LD block from the same $faqs array a page
 * renders as visible FAQ markup, so schema and on-page content can never
 * drift apart. Pass an array of ['q' => ..., 'a' => ...] pairs.
 */
function faq_schema(array $faqs): string {
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => array_map(function ($faq) {
            return [
                '@type' => 'Question',
                'name' => $faq['q'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['a'],
                ],
            ];
        }, $faqs),
    ];
    return '<script type="application/ld+json">'
        . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        . '</script>';
}

/**
 * Build a Schema.org BlogPosting JSON-LD block for a blog post. Pass the
 * raw $blog row (as fetched by blog.php, PDO::FETCH_ASSOC) plus the
 * description already computed for the page's meta tag.
 *
 * If the post's author_name is empty, or is just the org name reused as a
 * byline (seen live as "Digifyce" with a "Contributor" label -- not a real
 * named person), attribute authorship to the Organization instead of
 * emitting a Person entity that would misrepresent the org as an
 * individual. A genuinely different author_name is used as a real Person.
 */
function blog_posting_schema(string $appUrl, array $blog, string $description): string {
    $appUrl = rtrim($appUrl, '/');
    $url = $appUrl . '/blog/' . $blog['slug'];
    $published = $blog['published_at'] ?: $blog['created_at'];
    $orgId = ['@id' => $appUrl . '/#organization'];

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => $blog['title'],
        'description' => $description,
        'url' => $url,
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $url],
        'datePublished' => date('Y-m-d', strtotime($published)),
        'publisher' => $orgId,
    ];

    if (!empty($blog['updated_at'])) {
        $schema['dateModified'] = date('Y-m-d', strtotime($blog['updated_at']));
    }
    if (!empty($blog['featured_image'])) {
        $schema['image'] = $appUrl . '/storage/uploads/' . $blog['featured_image'];
    }

    $authorName = trim((string) ($blog['author_name'] ?? ''));
    $isRealPerson = $authorName !== '' && strcasecmp($authorName, 'Digifyce') !== 0;
    if ($isRealPerson) {
        $author = ['@type' => 'Person', 'name' => $authorName];
        if (!empty($blog['author_avatar'])) {
            $author['image'] = $appUrl . '/storage/uploads/' . $blog['author_avatar'];
        }
        if (!empty($blog['author_bio'])) {
            $author['description'] = $blog['author_bio'];
        }
        $schema['author'] = $author;
    } else {
        $schema['author'] = $orgId;
    }

    return '<script type="application/ld+json">'
        . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        . '</script>';
}
