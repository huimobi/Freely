<?php
declare(strict_types=1);

function drawServiceCard($svc): void { ?>
  <a href="/pages/service.php?id=<?= $svc->id ?>" class="service-card-link">
    <article class="service-card">
      <img src="/images/services/<?= $svc->id ?>.jpg" class="service-img"
          onerror="this.src='/images/services/default.jpg'" alt="Service">

      <div class="service-info">
        <h3 class="service-title"><?= htmlspecialchars($svc->title) ?></h1>
        <p class="service-desc"><?= htmlspecialchars($svc->description) ?></p>
        <div class="service-meta">
          <span><i class="fa fa-clock"></i> <?= $svc->deliveryDays ?> day<?= $svc->deliveryDays > 1 ? 's' : '' ?></span>
          <span>From <strong><?= $svc->currency ?><?= number_format($svc->basePrice, 2) ?></strong></span>
        </div>
      </div>

      <div class="service-seller">
        <img src="/images/users/<?= $svc->seller->id ?>.jpg" class="seller-img" onerror="this.src='/images/users/default.jpg'" alt="Seller">
        <div class="seller-details">
          <span class="seller-name"><?= htmlspecialchars($svc->seller->firstName . ' ' . $svc->seller->lastName) ?></span>
          <span class="seller-rating"> ⭐ <?= $svc->rating ?? '4.9' ?> (<?= $svc->numRatings ?? '100' ?>) </span>
        </div>
      </div>
    </article>
  </a>

<?php } ?>
