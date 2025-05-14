<?php
declare(strict_types=1);

require_once __DIR__ . '/../database/scripts/service.class.php';
require_once __DIR__ . '/../database/scripts/comment.class.php';
require_once __DIR__ . '/../database/scripts/user.class.php';
require_once __DIR__ . '/service_card.tpl.php';

function drawTopRatedBlock(): void {
  $topServices = Service::getTopRated(10);

  foreach ($topServices as $svc) {
    $svc->seller = User::getUser($svc->sellerId);
    $svc->rating = Comment::averageForService($svc->id);
    $svc->numRatings = Comment::countForService($svc->id);
  }

  drawTopRatedServices($topServices);
}


function drawTopRatedServices(array $services): void { ?>
  <section class="top-rated">
    <h2>Our Top Rated Services</h2>
    <section class="service-list">
      <?php foreach ($services as $svc): ?>
        <?php drawServiceCard($svc); ?>
      <?php endforeach; ?>
    </section>
  </section>

  <section class="view-more"> <a href="/pages/browse_services.php" class="btn btn--link">View All Services</a> </section>

<?php }