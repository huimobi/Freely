<?php
declare(strict_types=1);
require_once __DIR__ . '/service_card.tpl.php';

function drawBrowseServicesPage(string $title, array $services, ?string $categoryDescription = null, int $page = 1, int $totalPages = 1, ?string $baseUrl = null): void { ?>
  <section class="category-header">
    <h2><?= htmlspecialchars($title) ?></h2>
    <?php if ($categoryDescription): ?>
      <p><?= htmlspecialchars($categoryDescription) ?></p>
    <?php endif; ?>
  </section>

  <section class="service-list">
    <?php if (empty($services)): ?>
      <p>No services found.</p>
    <?php else: ?>
      <?php foreach ($services as $svc): ?>
        <?php drawServiceCard($svc); ?>
      <?php endforeach; ?>
    <?php endif; ?>
  </section>

  <?php if ($baseUrl && $totalPages > 1): ?>
    <nav class="pagination">
      <?php if ($page > 1): ?>
        <a href="<?= $baseUrl ?>&page=<?= $page-1 ?>">&laquo; Prev</a>
      <?php endif; ?>
      <?php for ($p = 1; $p <= $totalPages; $p++): ?>
        <a href="<?= $baseUrl ?>&page=<?= $p ?>" class="<?= $p === $page ? 'active' : '' ?>"><?= $p ?></a>
      <?php endfor; ?>
      <?php if ($page < $totalPages): ?>
        <a href="<?= $baseUrl ?>&page=<?= $page+1 ?>">Next &raquo;</a>
      <?php endif; ?>
    </nav>
  <?php endif;
}
?>
