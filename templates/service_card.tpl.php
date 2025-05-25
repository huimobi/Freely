<?php
declare(strict_types=1);
require_once __DIR__ ."/../includes/photo.php";

function drawServiceCard($svc): void { 
    $servicePhoto=photo::getServiceMainPhoto($svc->id);
    $sellerProfilePic=Photo::getUserProfilePic($svc->sellerId);
  ?>
  <a href="/pages/service.php?id=<?= $svc->id ?>" class="service-card-link">
    <article class="service-card">
      <img src="<?= $servicePhoto ?>" class="service-img" alt="Service">

      <div class="service-info">
        <h3 class="service-title"><?= htmlspecialchars($svc->title) ?></h1>
        <p class="service-desc"><?= htmlspecialchars($svc->description) ?></p>
        <div class="service-meta">
          <span><i class="fa fa-clock"></i> <?= $svc->deliveryDays ?> day<?= $svc->deliveryDays > 1 ? 's' : '' ?></span>
          <span>From <strong><?= $svc->currency ?><?= number_format($svc->basePrice, 2) ?></strong></span>
        </div>
      </div>

      <div class="service-seller">
        <img src="<?= $sellerProfilePic ?>" class="seller-img"  alt="Seller">
        <div class="seller-details">
          <span class="seller-name"><?= htmlspecialchars($svc->seller->firstName . ' ' . $svc->seller->lastName) ?></span>
          <span class="seller-rating"> ⭐ <?= $svc->rating ?? '4.9' ?> (<?= $svc->numRatings ?? '100' ?>) </span>
        </div>
      </div>
    </article>
  </a>

<?php } ?>
