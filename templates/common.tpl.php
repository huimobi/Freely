<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/session.php';

function drawHeader() { 
    $session = Session::getInstance();
    $user    = $session->getUser();
    ?>

<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Freely</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" 
            integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" 
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="../javascript/script.js" defer></script>
    </head>
    <body>
        <header>
            <h1><a href="../index.php">Freely</a></h1>

            <form action="../search.php" method="get" class="search-bar">
                <input id="q" name="q" type="search" placeholder="Search freelancers…" required>
                <button type="submit" aria-label="Search"><i class="fa fa-search"></i></button>
            </form>

            <?php if ($user): ?>
                <form action="../actions/action_logout.php" method="post" class="login">
                    <?= htmlspecialchars($user->name()) ?>
                    <button class="btn btn--primary" type="submit">Logout</button>
                </form>
            
            <?php else: ?>
                <nav class="actions">
                    <button class="btn btn--link" type="button" data-modal-open="roleModal">Register</button>
                    <button class="btn btn--primary" type="button" data-modal-open="loginModal">Login</button>
                </nav>

            <?php endif; ?>


        </header>
        <main>
        <?php drawModal('roleModal', 'drawRoleSelectForm'); ?>
        <?php drawModal('loginModal','drawLoginForm'); ?>
        <?php drawModal('registerModal','drawRegisterForm');?>


<?php } function drawLoginForm() { ?>
    <form action="../actions/action_login.php" method="post" class="login">
        <input type="email" name="email" placeholder="you@example.com" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <p>
        Don't have an account?
        <button class="btn btn--link" type="button" data-modal-open="roleModal">Register</button>
    </p>


<?php } function drawRegisterForm() { ?>
    <form action="../actions/action_register.php" method="POST">
        <input type="text" name="first_name" placeholder="First Name" required>
        <input type="text" name="last_name" placeholder="Last Name"  required>
        <input type="text" name="username" placeholder="Username"   required>
        <input type="email" name="email" placeholder="you@example.com" required>
        <input type="password" name="password" placeholder="Password"   required>
        <button type="submit">Register</button>
    </form>


<?php } function drawRoleSelectForm() { ?>

    <h2 class="role-title">Join as a client or freelancer</h2>

    <p class="role-pair">
        <button class="role-btn btn--primary"  type="button" data-modal-open="registerModal">
        <i class="fa fa-user"></i>
        I’m a client,<br> hiring for a project
        </button>

        <button class="role-btn btn--primary" type="button"  onclick="window.location.href='/pages/form_register.php'">
        <i class="fa fa-laptop-code"></i>
        I’m a freelancer,<br> looking for work
        </button>
    </p>

    <p>
        Already have an account?
        <button class="btn--link" type="button" data-modal-open="loginModal"> Login </button>
    </p>


<?php } function drawModal(string $modal, callable $formName) { ?>
    <dialog id="<?= $modal ?>">
      <?php $formName(); ?>
      <menu>
        <button data-modal-close type="button">Close</button>
      </menu>
    </dialog>


<?php } function drawFooter() { ?>
    <footer>
        <ul class="footer-team">
            <li>Nuno Gomes (up202306826)</li>
            <li>Francisco Antunes (up202307639)</li>
            <li>Pedro Coelho (up202306714)</li>
        </ul>  
        <p class="footer-note">
            LTW Freelancer website &copy; 2025
        </p>
    </footer>
    </main>    
    </body>
    </html>

<?php } ?>