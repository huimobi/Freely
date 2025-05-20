<?php
    declare(strict_types=1);

    require_once(__DIR__ . '/../database/scripts/tag.class.php');

    $query = $_GET['q'] ?? '';
    $tags = [];

    if (!empty($query)) {
        $tags = Tag::getTagsByPartial($query, 10);
    }

    echo json_encode($tags);
?>
