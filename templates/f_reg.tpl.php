<?php
declare(strict_types=1);
?>

<?php function drawRegisterPage() { ?>
    <?php if (!empty($_SESSION['register_errors'])): ?>
        <?php foreach ($_SESSION['register_errors'] as $err): ?>
            <p class="form-error"><?= htmlspecialchars($err) ?></p>
        <?php endforeach; ?>
        <?php unset($_SESSION['register_errors']); ?>
    <?php endif; ?>

    <main class="register-form">
        <h2>Freelancer Sign Up</h2>
        <form action="/actions/action_register.php" method="post">

            <input type="hidden" name="role" value="freelancer">

            <label> First Name <input type="text" name="first_name" placeholder="First Name" maxlength="30" required> </label>
            <label> Last Name <input type="text" name="last_name" placeholder="Last Name" maxlength="30" required> </label>
            <label> Username <input type="text" name="username" placeholder="Username" maxlength="30" required> </label>
            <label> Email <input type="email" name="email" placeholder="you@example.com" maxlength="30" required> </label>
            <label> Password <input type="password" name="password" placeholder="Password" minlength="8" required> </label>

            <label> Headline <textarea name="headline" placeholder="Expert PHP Developer" maxlength="200"> </textarea>  </label>
            <label> Description <textarea name="description" placeholder="Tell clients what you do…" maxlength="1000"> </textarea> </label>
            <label> Hourly Rate (€) <input type="number" name="hourly_rate" step="0.01" placeholder="e.g. 25.00" min="0" inputmode="decimal"> </label>
            <label> Currency (3-letter code) <input type="text" name="currency_rate"  placeholder="EUR" maxlength="3"> </label>
            <button type="submit">Create Freelancer Account</button>
        </form>
    </main>

<?php } ?>