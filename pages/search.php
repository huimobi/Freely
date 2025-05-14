<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/scripts/tag.class.php');
require_once(__DIR__ . '/../database/scripts/service.class.php');
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/service_card.tpl.php');

$searchTerm = $_GET['q'] ?? '';
$services = [];

if (!empty($searchTerm)) {
    $services = Tag::getServicesByPartialTag($searchTerm);
}

drawHeader("Search Results");

if (empty($services)) {
    echo "<p>No services found for '<strong>" . htmlspecialchars($searchTerm) . "</strong>'.</p>";
} else {
    echo "<section class='category-header'> <h1>Results for '<strong>" . htmlspecialchars($searchTerm) . "</strong>':</h1> </section>";
    echo "<section class='service-list'>";
    foreach ($services as $service) {
        drawServiceCard($service);
    }
    echo "</section>";
}

drawFooter();
?>
