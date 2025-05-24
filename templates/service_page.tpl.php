<?php declare(strict_types=1); ?>

<?php function drawServicePage(Service $SERVICE, User $SELLER): void
{
  $Photos = $SERVICE->photos ?? null;
  $totalPhotos = $SERVICE->totalPhotos ?? 0;
  ?>
  <main class="service-page">
    <a href="/pages/freelancer.php?id=<?= $SELLER->id ?>">
      <article class="seller-overview">
        <img src="<?= htmlspecialchars($SELLER->profilePic) ?>" class="profile-picture"
          onerror="this.src='/images/users/default.jpg'" alt="Seller">
        <div class="seller-text">
          <h2><?= htmlspecialchars($SELLER->userName ?? 'error') ?></h2>
          <p>
            <?= htmlspecialchars($SELLER->headline ?? ($SERVICE->sellerId == $_SESSION['user_id'] ? 'Tip: Add a headline in your profile to show here!' : '')) ?>
          </p>
        </div>
        <span class="rating">Service Rating ⭐ <?= $SERVICE->rating ?? '0' ?>
          (<?= $SERVICE->numRatings ?? '0' ?>)</span>
      </article>
    </a>
    <section class="service-description">
      <h1><?= htmlspecialchars($SERVICE->title) ?></h1>
      <div class="service-photo-description">
        <?php if ($totalPhotos > 0): ?>
          <div class="photo-displayer">
            <div class="main-photo">
              <img id="selected-photo" src="<?= htmlspecialchars($Photos[0]) ?>" alt="Selected Photo">
            </div>
            <div class="thumbnail-photos">
              <?php foreach ($Photos as $index => $photo): ?>
                <img class="thumbnail" src="<?= htmlspecialchars($photo) ?>" alt="Thumbnail" <?= $index === 0 ? 'data-selected="true"' : '' ?>>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
        <section class="service-text-description">
          <h2>About this service</h2>
          <p><?= (htmlspecialchars($SERVICE->description)) ?></p>
        </section>

      </div>
    </section>
    </section>
    <section class="service-comment-section">

      <?php if(true): ?>

      <section class="service-add-comment-section">
        <h2>Leave a Comment</h2>
        <form action="/actions/action_submit_comment.php" method="post" class="add-comment-form">
          <input type="hidden" name="service_id" value="<?= $SERVICE->id ?>">
          <div class="star-rating" id="star-rating">

            <?php for ($i = 5; $i >= 1; $i--): ?>

              <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" required>
              <label for="star<?= $i ?>" title="<?= $i ?> stars">&#9733;</label>

            <?php endfor; ?>

          </div>
          <textarea id="comment-text" name="text" rows="3" maxlength="500" required
            placeholder="Write your comment here..."></textarea>
          <button type="submit" class="submit-comment-btn">Submit Comment</button>
        </form>
      </section>

      <?php endif;?>

      <h2>Some comments about this service</h2>

      <ul class="comments-list" id="comments-list">

        <?php if ($SERVICE->totalComments > 0): 
          foreach ($SERVICE->comments as $comment): ?>

        <li class="comment">
          <article class="comment-user-info">
            <img src="/images/users/<?=$comment->buyerUserId ?>.jpg" class="profile-picture"
          onerror="this.src='/images/users/default.jpg'" alt="User">
            <span class="comment-username"><?= htmlspecialchars($comment->user->userName) ?></span>
            <span class="comment-rating">⭐ <?= $comment->rating ?></span>
          </article>
          <p><?= htmlspecialchars($comment->description)?></p>
        </li>

          <?php endforeach;else: ?>
          <p id='no_comments'>No comments yet.</p>
        <?php endif; ?>
      </ul>
      <?php if ($SERVICE->totalComments > $SERVICE->commentsToShow): ?>
        <button id="show-more-comments" type="button">Show more comments</button>
      <?php endif; ?>
    </section>
    <?php
    ?>
    <aside class="service-aside-menu">
      <div class="service-aside-menu-info">
        <p><?= htmlspecialchars($SERVICE->category->name) ?></p>
        <div class="price"><?= $SERVICE->currency ?><?= number_format($SERVICE->basePrice, 2) ?></div>
        <span class="service-delivery-text"><i class="fa fa-clock"> </i> Delivered in
          <?= $SERVICE->deliveryDays ?>
          day<?= $SERVICE->deliveryDays > 1 ? 's' : '' ?></span>
        <span class="service-revisions-text"><i class="fa fa-refresh"> </i> <?= $SERVICE->revisions ?>
          revision<?= $SERVICE->revisions > 1 ? 's' : '' ?></span>
      </div>
      <?php if ($SERVICE->sellerId != $_SESSION['user_id']): ?>
        <div class="service-actions">
          <form action="../pages/payment.php" method="get">
            <input type="hidden" name="service_id" value="<?= $SERVICE->id ?>">
            <button type="submit" class="pay-btn">Proceed to Payment</button>
          </form>
          <button class="message-btn"
            onclick="window.location.href='/pages/messages.php?user=<?= $SERVICE->sellerId ?>'">Message
            Provider</button>
        </div>
      <?php else: ?>
        <a href="/pages/edit_service.php?id=<?= $SERVICE->id ?>" class="service-aside-edit">Edit</a>
      <?php endif; ?>
    </aside>
  </main>

<?php } ?>