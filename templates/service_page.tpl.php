<?php declare(strict_types=1); ?>

<?php function drawServicePage(Service $SERVICE): void
{ ?>
    <main class="service-page">
        <div class="service-left">
            <section class="service-header">
                <h1><?= htmlspecialchars($SERVICE->title) ?></h1>
                <section class="seller-overview">
                    <img src="/images/users/<?= $SERVICE->sellerId ?>.jpg" class="profile-picture"
                        onerror="this.src='/images/users/default.jpg'" alt="Seller">
                    <section class="seller-text">
                        <div class="seller-rating">
                            <h2><a href="/pages/freelancer.php?id=<?= $SERVICE->sellerId ?>"><?= htmlspecialchars($SERVICE->seller->username) ?>
                            </h2></a>
                            <span class="rating">⭐ <?= $SERVICE->rating ?? '4.9' ?>
                                (<?= $SERVICE->numRatings ?? '100' ?>)</span>
                        </div>

                        <?= htmlspecialchars($SERVICE->seller->headline) ?></p>
                    </section>
                </section>
            </section>
            <section class="service-description">
                <div class="service-photo-description">
                    <div class="photo-displayer">
                        <div class="main-photo">
                            <img id="selected-photo"
                                src="<?= htmlspecialchars($SERVICE->photos[0] ?? '/images/services/default.jpg') ?>"
                                alt="Selected Photo">
                        </div>
                        <div class="thumbnail-photos">
                            <?php foreach ($SERVICE->photos ?? ['/images/services/default.jpg'] as $index => $photo): ?>
                                <img class="thumbnail" src="<?= htmlspecialchars($photo) ?>" alt="Thumbnail" <?= $index === 0 ? 'data-selected="true"' : '' ?>>
                            <?php endforeach; ?>
                        </div>
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
                </section>
            <?php endif; ?>
        </div>

        <aside class="service-aside-menu">
            <div class="service-aside-menu-info">
                <p><?= htmlspecialchars($SERVICE->category->name) ?></p>
                <p><?= $SERVICE->currency ?><?= number_format($SERVICE->basePrice, 2) ?></p>
                <span class="service-delivery-text"><i class="fa fa-clock"> </i> Delivered in <?= $SERVICE->deliveryDays ?>
                    day<?= $SERVICE->deliveryDays > 1 ? 's' : '' ?></span>

            </div>
            <div class="service-actions">
                <form action="/payment.php" method="get">
                    <input type="hidden" name="service_id" value="<?= htmlspecialchars((string) $SERVICE->id) ?>">
                    <button type="submit" class="pay-btn">Proceed to Payment</button>
                </form>
                <button class="message-btn"
                    onclick="window.location.href='/message.php?service_id=<?= urlencode((string) $SERVICE->id) ?>'">Message
                    Provider</button>
            </div>
        </aside>
    </main>

<?php } ?>