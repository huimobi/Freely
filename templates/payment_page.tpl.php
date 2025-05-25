<?php declare(strict_types=1); ?>

<?php function drawPaymentPage(Service $Service,User $Buyer): void
{ 
    $Seller= $Service->seller ?? null;
    ?>

<div class="payment-page">
        <h1>Payment </h1>
        <div class="payment-summary">
            <p><strong>Service:</strong> <?= htmlspecialchars($Service->title) ?></p>
            <p><strong>Price:</strong> <?= htmlspecialchars($Service->currency  . number_format($Service->basePrice, 2)) ?></p>
            <p><strong>Buyer:</strong> <?= htmlspecialchars($Buyer->userName." (".$Buyer->firstName." ".$Buyer->lastName.")")?></p>
            <p><strong>Seller:</strong> <?= htmlspecialchars($Seller->userName." (".$Seller->firstName." ".$Seller->lastName.")")  ?><p>
        </div>
        <form action="../actions/action_service_payment.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::getInstance()->getCsrfToken(), ENT_QUOTES) ?>">
            <label for="card_number">Card Number</label>
            <input type="text" id="card_number" name="card_number" maxlength="19" placeholder="0000 0000 0000 0000">

            <label for="holder_name">Card Name</label>
            <input type="text" id="holder_name" name="holder_name" placeholder="John Doe">

            <label for="expiry_date">Expiration date</label>
            <input type="month" id="expiry_date" name="expiry_date" >

            <label for="cvv">CVV</label>
            <input type="password" id="cvv" name="cvv" maxlength="4" placeholder="123" >

            <input type="hidden" name="service_id" value="<?=$Service->id?>">
            <button type="submit">Send <?= htmlspecialchars($Service->currency.number_format($Service->basePrice, 2)) ?></button>
        </form>
    </div>

<?php } ?>