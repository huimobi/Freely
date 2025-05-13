<?php
declare(strict_types=1);


function drawBrowsePage($category, array $services, int $page, int $totalPages, int $catId, string $priceFilter, string $deliveryFilter): void { ?>
  <section class="category-header">
    <h2><?= htmlspecialchars($category->name) ?></h2>
    <?php if ($category->description): ?>
      <p><?= htmlspecialchars($category->description) ?></p>
    <?php endif; ?>
  </section>

  <section class="filter-bar">
    <label>
      Price
      <select name="price_filter"
              onchange="location.search='?cat=<?= $catId ?>&price='+this.value+'&delivery=<?= $deliveryFilter ?>'">
        <option value=""  <?= $priceFilter===''   ? 'selected' : '' ?>>Any</option>
        <option value="low"  <?= $priceFilter==='low'  ? 'selected' : '' ?>>Low → High</option>
        <option value="high" <?= $priceFilter==='high' ? 'selected' : '' ?>>High → Low</option>
      </select>
    </label>

    <label>
      Delivery Days
      <select name="delivery_filter"
              onchange="location.search='?cat=<?= $catId ?>&delivery='+this.value+'&price=<?= $priceFilter ?>'">
        <option value=""  <?= $deliveryFilter===''   ? 'selected' : '' ?>>Any</option>
        <option value="1" <?= $deliveryFilter==='1'   ? 'selected' : '' ?>>1–3 days</option>
        <option value="7" <?= $deliveryFilter==='7'   ? 'selected' : '' ?>>1 week+</option>
      </select>
    </label>
  </section>

  <div class="service-list-container">
    <section class="service-list">
      <?php if (empty($services)): ?>
        <p>No services found in this category.</p>
      <?php else: ?>
        <?php foreach ($services as $svc): ?>
          <?php drawServiceCard($svc); ?>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>
  </div>

    <?php if ($totalPages > 1): ?>
      <nav class="pagination">
        <?php if ($page > 1): ?>
          <a href="/pages/browse_cat.php?cat=<?= $catId ?>&page=<?= $page-1 ?>">&laquo; Prev</a>
        <?php endif; ?>
        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
          <a href="/pages/browse_cat.php?cat=<?= $catId ?>&page=<?= $p ?> "class="<?= $p === $page ? 'active' : '' ?>" > <?= $p ?></a>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
          <a href="/pages/browse_cat.php?cat=<?= $catId ?>&page=<?= $page+1 ?>"> Next &raquo; </a>
        <?php endif; ?>
      </nav>
    <?php endif; ?>
  </div>
<?php }
