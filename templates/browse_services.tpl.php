<?php
declare(strict_types=1);
require_once __DIR__ . '/service_card.tpl.php';

function drawBrowseServicesPage(string  $title, array   $services, ?string $categoryDescription = null, int $page = 1, int $totalPages = 1, ?string $baseUrl = null): void {?>
  <section class="category-header">
    <h2><?= htmlspecialchars($title) ?></h2>
    <?php if ($categoryDescription): ?>
      <p><?= htmlspecialchars($categoryDescription) ?></p>
    <?php endif; ?>
  </section>

  <section class="filters-bar">
    <h3>Filters</h3>
    <form method="get" class="filter-form">

      <div class="filter-group">
        <label>Price</label>
        <div class="price-range">
          <input type="number" name="price_min" placeholder="from" value="<?= htmlspecialchars($_GET['price_min'] ?? '') ?>">
          <input type="number" name="price_max" placeholder="to" value="<?= htmlspecialchars($_GET['price_max'] ?? '') ?>">
        </div>
      </div>

      <div class="filter-group">
        <label>Rating</label>
        <div class="price-range">
          <input type="number" name="rating_min" placeholder="from" step="0.1" min="0" max="5" value="<?= htmlspecialchars($_GET['rating_min'] ?? '') ?>">
          <input type="number" name="rating_max" placeholder="to" step="0.1" min="1" max="5" value="<?= htmlspecialchars($_GET['rating_max'] ?? '') ?>">
        </div>
      </div>

      <div class="filter-group">
        <label for="sort">Ordenar by</label>
        <select name="sort" id="sort">
          <option value="">Show all</option>
          <option value="price_asc" <?= ($_GET['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>>Price: des</option>
          <option value="price_desc" <?= ($_GET['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>>Price: asc</option>
          <option value="rating_asc" <?= ($_GET['sort'] ?? '') === 'rating_asc' ? 'selected' : '' ?>>Rating: des</option>
          <option value="rating_desc" <?= ($_GET['sort'] ?? '') === 'rating_desc' ? 'selected' : '' ?>>Rating: asc</option>
        </select>
      </div>

      <?php if (isset($_GET['cat'])): ?>
        <input type="hidden" name="cat" value="<?= htmlspecialchars($_GET['cat']) ?>">
      <?php endif; ?>

      <?php if (isset($_GET['q'])): ?>
        <input type="hidden" name="q" value="<?= htmlspecialchars($_GET['q']) ?>">
      <?php endif; ?>

      <button class="btn--primary" type="submit">Filter</button>
    </form>
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
