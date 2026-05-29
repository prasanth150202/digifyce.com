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
