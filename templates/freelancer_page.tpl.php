<?php declare(strict_types=1); ?>

<?php function drawFreelancerPage(User $freelancer, array $services = []): void 
{ ?>
    <main class="freelancer-page">
        <section class="freelancer-header">
            <div class="freelancer-profile">
                <img src="/images/users/<?= $freelancer->id ?>.jpg" class="profile-picture"
                    onerror="this.src='/images/users/default.jpg'" alt="Freelancer">
                <div class="freelancer-info">
                    <div class="freelancer-details">
                        <h1><?= htmlspecialchars($freelancer->name()) ?></h1>
                        <h2 class="headline"><?= htmlspecialchars($freelancer->headline ?? 'Freelancer') ?></h2>
                        <div class="rating">
                            ⭐ <?= $freelancer->rating ?? '0.0' ?> (<?= $freelancer->numReviews ?? '0' ?> avaliações)
                        </div>
                    </div>
                    <div class="contact-wrapper">
                        <button class="contact-btn" onclick="window.location.href='/pages/message.php?user=<?= $freelancer->id ?>'">
                            <i class="fa fa-handshake-o"></i> Contratar Freelancer
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section class="freelancer-description">
            <h2>Sobre <?= htmlspecialchars($freelancer->firstName) ?></h2>
            <p><?= htmlspecialchars($freelancer->description ?? 'Nenhuma descrição fornecida.') ?></p>
        </section>

        <?php if (!empty($services)): ?>
            <section class="freelancer-services">
                <h2>Serviços Oferecidos</h2>
                <div class="service-list">
                    <?php foreach ($services as $service): ?>
                        <?php drawServiceCard($service); ?>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($freelancer->reviews)): ?>
            <section class="freelancer-reviews">
                <h2>Avaliações</h2>
                <ul class="reviews-list">
                    <?php foreach ($freelancer->reviews as $review): ?>
                        <li class="review">
                            <div class="review-header">
                                <img src="/images/users/<?= $review->buyerId ?>.jpg" class="reviewer-img"
                                     onerror="this.src='/images/users/default.jpg'" alt="Reviewer">
                                <span class="reviewer-name"><?= htmlspecialchars($review->buyerName) ?></span>
                                <span class="review-rating">⭐ <?= $review->rating ?></span>
                            </div>
                            <p><?= htmlspecialchars($review->text) ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>
        
        <!-- Removemos a seção freelancer-contact pois já movemos o botão -->
    </main>
<?php } ?>