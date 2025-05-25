<?php
declare(strict_types=1);

require_once __DIR__ . "/../database/scripts/user.class.php";

function drawCreateCommentForm(int $serviceId, int $jobOrderId): void
{
    $user = SESSION::getInstance()->getUser();
    ?>
    <main class="form-page">
        <section class="service-add-comment-section">
            <h2>Leave a Comment</h2>
            <form action="/actions/action_submit_comment.php" method="post" class="add-comment-form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::getInstance()->getCsrfToken(), ENT_QUOTES) ?>">
                <input type="hidden" name="service_id" value="<?= $serviceId ?>">
                <input type="hidden" name="job_order_id" value="<?= $jobOrderId ?>">
                <input type="hidden" name="buyer_user_id" value="<?= $user->id ?>">

                <div class="star-rating" id="star-rating">

                    <?php for ($i = 5; $i >= 1; $i--): ?>

                        <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" required>
                        <label for="star<?= $i ?>" title="<?= $i ?> stars">&#9733;</label>

                    <?php endfor; ?>

                </div>
                <textarea id="comment-text" name="description" rows="3" maxlength="500" required
                    placeholder="Write your comment here..."></textarea>
                <button type="submit" class="submit-comment-btn">Submit Comment</button>
            </form>
        </section>
    </main>
    <?php
}
?>