<?php declare(strict_types=1); ?>

<?php function drawServicePage(Service $SERVICE): void
{
    $Photos = $SERVICE->photos ?? null;
    $totalPhotos = $SERVICE->totalPhotos ?? 0;
    ?>
    <main class="service-page">
        <a href="/pages/freelancer.php?id=<?= $SERVICE->sellerId ?>">
            <article class="seller-overview">
                <img src="<?= htmlspecialchars($SERVICE->seller->profilePic ?? 'error') ?>" class="profile-picture"
                    onerror="this.src='/images/users/default.jpg'" alt="Seller">
                <section class="seller-text">
                    <div class="seller-rating">
                        <h2><?= htmlspecialchars($SERVICE->seller->userName ?? 'error') ?></h2>
                        <span class="rating">⭐ <?= $SERVICE->rating ?? '4.9' ?>
                            (<?= $SERVICE->numRatings ?? '100' ?>)</span>
                    </div>

                    <p><?= htmlspecialchars($SERVICE->seller->headline ?? ($SERVICE->sellerId == $_SESSION['user_id'] ? 'Tip: Add a catch phrase in your profile to show here!' : ' ')) ?>
                    </p>
                </section>
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
        <!----------TODO: test and add style to this-------->
        <section class="service-comment-section">
            <h2>Some comments about this service</h2>
            <ul class="comments-list" id="comments-list">
                <?php if ($SERVICE->totalComments > 0): ?>
                    <?php foreach (array_slice($SERVICE->comments, 0, $SERVICE->commentsToShow) as $index => $comment): ?>
                        <li class="comment" data-index="<?= $index ?>">
                            <article class="comment-user-info">
                                <img src="/images/users/<?= htmlspecialchars($comment->user->id) ?>.jpg" class="profile-picture"
                                    onerror="this.src='/images/users/default.jpg'" alt="User">
                                <span class="comment-username"><?= htmlspecialchars($comment->user->username) ?></span>
                                <span class="comment-rating">⭐ <?= htmlspecialchars($comment->rating) ?></span>
                            </article>
                            <p><?= htmlspecialchars($comment->text) ?></p>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No comments yet.</p>
                <?php endif; ?>
            </ul>
            <?php if ($SERVICE->totalComments > $SERVICE->commentsToShow): ?>
                <button id="show-more-comments" type="button">Show more comments</button>
            <?php endif; ?>
        </section>

        <aside class="service-aside-menu">
            <div class="service-aside-menu-info">
                <p><?= htmlspecialchars($SERVICE->category->name ?? '') ?></p>
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