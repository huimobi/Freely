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

        <link rel="stylesheet" href="../css/cat_style.css">
        <link rel="stylesheet" href="../css/drop_down_style.css">
        <link rel="stylesheet" href="../css/style.css">
        <link rel="stylesheet" href="../css/admin_panel.css">


        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" 
            integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" 
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="../javascript/script.js" defer></script>
    </head>
    <body>
        <header>
            <h1><a href="../index.php">Freely</a></h1>

            <form action="../pages/search.php" method="get" class="search-bar">
                <input id="q" name="q" type="search" placeholder="Search by tags…" autocomplete="off" required>
                <button type="submit" aria-label="Search"><i class="fa fa-search"></i></button>
            </form>

            <?php if ($user): ?>
                <nav class="actions">
                    <?php if ($user && $user->isAdmin($user->id)): ?> <button class="btn btn--link" type="button" onclick="window.location.href='/pages/admin_panel.php'">Admin Panel</button> <?php endif; ?>
                    <button class="btn btn--link" type="button" onclick="window.location.href='/pages/create_service.php'"> Create Service </button>
                
                    <nav class="actions profile-nav">
                        <button id="profileBtn" class="btn btn--link profile-btn" type="button">
                        Profile <i class="fa fa-chevron-down profile-caret"></i>
                        </button>
                        <ul id="profileMenu" class="dropdown-menu" aria-hidden="true">
                        <li><a href="/../pages/my_services.php">My Services</a></li>
                        <li><a href="/../pages/my_buys.php">My Buys</a></li>
                        <li><a href="/../pages/messages.php">Messages</a></li>
                        <li><a href="/../pages/edit_profile.php">Edit Profile</a></li>
                        </ul>

                    </nav>
                </nav>

                <form action="../actions/action_logout.php" method="post" class="login">
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
    <?php if (!empty($_SESSION['login_errors'])): ?>
        <?php foreach($_SESSION['login_errors'] as $err): ?>
            <p class="form-error"><?= htmlspecialchars($err) ?></p>
        <?php endforeach; ?>

        <script>
            document.addEventListener('DOMContentLoaded', () => { document.getElementById('<?= "loginModal" ?>').showModal();});
        </script>

        <?php unset($_SESSION['login_errors']); ?>
    <?php endif; ?>

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
    <?php if (!empty($_SESSION['register_errors'])): ?>
        <?php foreach($_SESSION['register_errors'] as $err): ?>
            <p class="form-error"><?= htmlspecialchars($err) ?></p>
        <?php endforeach; ?>

        <script>
            document.addEventListener('DOMContentLoaded', () => { document.getElementById('<?= "registerModal" ?>').showModal();});
        </script>

        <?php unset($_SESSION['register_errors']); ?>
    <?php endif; ?>

    <form action="../actions/action_register.php" method="POST">
        <input type="hidden" name="role" value="client">
        <input type="text" name="first_name" placeholder="First Name" maxlength="30" required>
        <input type="text" name="last_name" placeholder="Last Name" maxlength="30" required>
        <input type="text" name="username" placeholder="Username" maxlength="30" required>
        <input type="email" name="email" placeholder="you@example.com" maxlength="30" required>
        <input type="password" name="password" placeholder="Password"  minlength="8" required>
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
        <button class="btn btn--link" type="button" data-modal-open="loginModal"> Login </button>
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

<?php } function drawSimpleHeader() { ?>
  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Freely</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/form_style.css">
  </head>
  <body>
    <header class="minimal-header">
      <h1><a href="/index.php">Freely</a></h1>
    </header>
<?php } ?>