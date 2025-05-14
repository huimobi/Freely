<?php
declare(strict_types=1);
?>

<?php function drawServiceHeader(Service $service, User $user): void { ?>
    <section class="service-header">
        <h1><?= htmlspecialchars($service->title) ?></h1>
        <section class= "seller-overview">
            <img src="https://picsum.photos/300/200?b" alt="Profile Picture" class="profile-picture">
            <section class="seller-text">
                <h2><a href="/pages/user.php?id=<?=$user->id?>"><?= htmlspecialchars($user->username) ?></h2></a>
                <p> <?= htmlspecialchars($user->headline) ?></p>
            </section>
        </section> 
    </section>
<?php } ?>
