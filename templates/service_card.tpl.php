<?php
declare(strict_types=1);

function drawServiceCard($svc): void { 
    $serviceImgRel  = "/images/services/{$svc->id}/0.jpg";
    $serviceImgAbs  = $_SERVER['DOCUMENT_ROOT'] . $serviceImgRel;
    $sellerImgRel   = "/images/users/{$svc->seller->id}.jpg";
    $sellerImgAbs   = $_SERVER['DOCUMENT_ROOT'] . $sellerImgRel;
    if (!file_exists($serviceImgAbs)) {$serviceImgRel = "/images/services/default.jpg";}
    if (!file_exists($sellerImgAbs)) {$sellerImgRel = "/images/users/default.jpg";}
  ?>
  <a href="/pages/service.php?id=<?= $svc->id ?>" class="service-card-link">
    <article class="service-card">
      <img src="<?= $serviceImgRel ?>" class="service-img" alt="Service">

      <div class="service-info">
        <h3 class="service-title"><?= htmlspecialchars($svc->title) ?></h1>
        <p class="service-desc"><?= htmlspecialchars($svc->description) ?></p>
        <div class="service-meta">
          <span><i class="fa fa-clock"></i> <?= $svc->deliveryDays ?> day<?= $svc->deliveryDays > 1 ? 's' : '' ?></span>
          <span>From <strong><?= $svc->currency ?><?= number_format($svc->basePrice, 2) ?></strong></span>
        </div>
      </div>

      <div class="service-seller">
        <img src="<?= $sellerImgRel ?>" class="seller-img"  alt="Seller">
        <div class="seller-details">
          <span class="seller-name"><?= htmlspecialchars($svc->seller->firstName . ' ' . $svc->seller->lastName) ?></span>
          <span class="seller-rating"> ⭐ <?= $svc->rating ?? '4.9' ?> (<?= $svc->numRatings ?? '100' ?>) </span>
        </div>
      </div>
    </article>
  </a>

<?php } ?>
