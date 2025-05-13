<?php
declare(strict_types=1);

function drawServiceCard($svc): void {
  $desc = htmlspecialchars($svc->description);
  printf(
    '<article class="service-card">
       <h3>%s</h3>
       <p class="service-desc"><a href="/pages/service.php?id=%d">%s</a></p>
       <p class="service-meta"><strong>%s%.2f</strong> • %d day%s</p>
     </article>',
    htmlspecialchars($svc->title),
    $svc->id,
    $desc,
    htmlspecialchars($svc->currency),
    $svc->basePrice,
    $svc->deliveryDays,
    $svc->deliveryDays > 1 ? 's' : ''
  );
}
