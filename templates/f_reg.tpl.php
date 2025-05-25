<?php
declare(strict_types=1);
?>

<?php function drawRegisterPage() { ?>
    <main class="form-page">
        <h2>Freelancer Sign Up</h2>

        <?php if (!empty($_SESSION['register_errors'])): ?>
            <?php foreach ($_SESSION['register_errors'] as $err): ?>
                <p class="form-error"><?= htmlspecialchars($err) ?></p>
            <?php endforeach; ?>
            <?php unset($_SESSION['register_errors']); ?>
        <?php endif; ?>

        <form action="/actions/action_register.php" method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::getInstance()->getCsrfToken(), ENT_QUOTES) ?>">

            <input type="hidden" name="role" value="freelancer">

            <label> First Name <input type="text" name="first_name" placeholder="First Name" maxlength="30" required> </label>
            <label> Last Name <input type="text" name="last_name" placeholder="Last Name" maxlength="30" required> </label>
            <label> Username <input type="text" name="username" placeholder="Username" maxlength="30" required> </label>
            <label> Email <input type="email" name="email" placeholder="you@example.com" maxlength="30" required> </label>
            <label> Password <input type="password" name="password" placeholder="Password" minlength="8" required> </label>

            <label> Headline <textarea name="headline" placeholder="Expert PHP Developer" maxlength="200"> </textarea>  </label>
            <label> Description <textarea name="description" placeholder="Tell clients what you do…" maxlength="1000"> </textarea> </label>
            <button type="submit">Create Freelancer Account</button>
        </form>
    </main>

<?php } ?>