<?php declare(strict_types=1); 
require_once __DIR__ ."/comment_list.php";
function drawServicePage(array $SERVICE_INFO, array $SELLER_INFO): void
{
  ?>
  <main class="service-page">
    <a href="/pages/freelancer.php?id=<?=$SELLER_INFO['seller']->id ?>">
      <article class="seller-overview">
        <img src="<?= htmlspecialchars($SELLER_INFO['profilePic']) ?>" class="profile-picture" alt="Seller">
        <div class="seller-text">
          <h2><?= htmlspecialchars($SELLER_INFO['seller']->userName ?? 'error') ?></h2>
          <p>
            <?= htmlspecialchars($SELLER_INFO['seller']->headline ?? ($SERVICE_INFO['service']->sellerId == $_SESSION['user_id'] ? 'Tip: Add a headline in your profile to show here!' : '')) ?>
          </p>
        </div>
        <span class="rating"><strong>Service Rating</strong>⭐ <?= $SERVICE_INFO['service']->rating ?? '0' ?>
          (<?= count($SERVICE_INFO['comments']) ?? '0' ?>)</span>
      </article>
    </a>
    <section class="service-description">
      <h1><?= htmlspecialchars($SERVICE_INFO['service']->title) ?></h1>
      <div class="service-photo-description">
        <?php if (count($SERVICE_INFO['photos'])>0): ?>
          <div class="photo-displayer">
            <div class="main-photo">
              <img id="selected-photo" src="<?= htmlspecialchars($SERVICE_INFO['photos'][0]) ?>" alt="Selected Photo">
            </div>
            <div class="thumbnail-photos">
              <?php foreach ($SERVICE_INFO['photos'] as $index => $photo): ?>
                <img class="thumbnail" src="<?= htmlspecialchars($photo) ?>" alt="Thumbnail" <?= $index === 0 ? 'data-selected="true"' : '' ?> >
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
        <section class="service-text-description">
          <h2>About this service</h2>
          <p><?= (htmlspecialchars($SERVICE_INFO['service']->description)) ?></p>
        </section>
      </div>
    </section>
    </section>
    <section class="service-comment-section">

      <h2>Some comments about this service</h2>
      <?php drawCommentList($SERVICE_INFO['comments']); ?>
    </section>
    <?php
    ?>
    <aside class="service-aside-menu">
      <div class="service-aside-menu-info">
        <p><?= htmlspecialchars($SERVICE_INFO['category']->name) ?></p>
        <div class="price"><?= $SERVICE_INFO['service']->currency ?><?= number_format($SERVICE_INFO['service']->basePrice, 2) ?></div>
        <span class="service-delivery-text"><i class="fa fa-clock"> </i> Delivered in
          <?= $SERVICE_INFO['service']->deliveryDays ?>
          day<?= $SERVICE_INFO['service']->deliveryDays > 1 ? 's' : '' ?></span>
        <span class="service-revisions-text"><i class="fa fa-refresh"> </i> <?= $SERVICE_INFO['service']->revisions ?>
          revision<?= $SERVICE_INFO['service']->revisions > 1 ? 's' : '' ?></span>
      </div>
      <?php if ( $SERVICE_INFO['service']->sellerId != $_SESSION['user_id'] ): ?>
        <div class="service-actions">
          <form action="../pages/payment.php" method="post">
            <input type="hidden" name="service_id" value="<?= $SERVICE_INFO['service']->id ?>">
            <button type="submit" class="pay-btn">Proceed to Payment</button>
          </form>
          <button class="message-btn"
            onclick="window.location.href='/pages/messages.php?user=<?= $SERVICE_INFO['service']->sellerId ?>'">Message
            Provider</button>
        </div>
      <?php else: ?>
        <button class="edit-btn"
            onclick="window.location.href='/pages/edit_service.php?id=<?= $SERVICE_INFO['service']->id ?>'">Edit</button>
      <?php endif; ?>
    </aside>
  </main>

<?php } ?>