<?php
declare(strict_types=1);

require_once __DIR__ . '/service_card.tpl.php';

function drawBrowseAllPage(array $services): void { ?>
  <section class="category-header"> <h2>All Services</h2> </section>

  <section class="service-list">
    <?php if (empty($services)): ?>
      <p>No services found.</p>
    <?php else: ?>
      <?php foreach ($services as $svc): ?>
        <?php drawServiceCard($svc); ?>
      <?php endforeach; ?>
    <?php endif; ?>
  </section>

<?php }