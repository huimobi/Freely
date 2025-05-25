<?php declare(strict_types=1);
require_once __DIR__ . "/comment_list.php";
?>

<?php function drawFreelancerPage(array $freelancerInfo, array $services = []): void
{ ?>
    <main class="freelancer-page">
        <section class="freelancer-header">
            <div class="freelancer-profile">
                <img src="<?= $freelancerInfo['profilePic'] ?>" class="profile-picture" alt="Freelancer">
                <div class="freelancer-info">
                    <div class="freelancer-details">
                        <h1><?= htmlspecialchars($freelancerInfo['freelancer']->userName) ?></h1>
                        <h2 class="headline">
                            <?= htmlspecialchars($freelancerInfo['freelancer']->headline ?? 'Freelancer') ?>
                        </h2>
                        <dl class="freelancer-list-info">
                            <div class="info-group left">
                                <dt>Name</dt>
                                <dd><?= htmlspecialchars($freelancerInfo['freelancer']->name()) ?></dd>
                                <dt>Rating</dt>
                                <dd>⭐ <?= $freelancerInfo['freelancer']->rating ?? '0.0' ?>
                                    (<?= $freelancerInfo['freelancer']->numReviews ?? '0' ?> comments)</dd>
                                <dt>Member Since</dt>
                                <dd><?= $freelancerInfo['freelancer']->getCreationDate() ?></dd>
                            </div>
                            <div class="info-group right">
                                <dt>Total Orders</dt>
                                <dd><?= $freelancerInfo['totalOrders'] ?></dd>
                                <dt>Total Services</dt>
                                <dd><?= $freelancerInfo['totalServices'] ?></dd>
                            </div>
                        </dl>
                    </div>
                </div>
                <div class="contact-wrapper">
                    <button class="contact-btn"
                        onclick="window.location.href='/pages/messages.php?user=<?= $freelancerInfo['freelancer']->id ?>'">
                        <i class="fa fa-handshake-o"></i> Contact Freelancer
                    </button>
                </div>

            </div>
        </section>

        <section class="freelancer-description">
            <h2>About <?= htmlspecialchars($freelancerInfo['freelancer']->firstName) ?></h2>
            <p><?= htmlspecialchars($freelancerInfo['freelancer']->description ?? 'No description') ?></p>
        </section>

        <?php if (!empty($services)): ?>
            <section class="freelancer-services">
                <h2>Offered Services</h2>
                <div class="service-list">
                    <?php foreach ($services as $service): ?>
                        <?php drawServiceCard($service); ?>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <section class="freelancer-reviews">
            <h2>Some comments about this Freelancer</h2>
            <?php drawCommentList($freelancerInfo['comments']); ?>
        </section>
    </main>
<?php } ?>