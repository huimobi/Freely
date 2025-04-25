<?php function drawHeader() { ?>

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

            <button type="button" data-modal-open="loginModal">Login</button>
            <button type="button" data-modal-open="registerModal">Register</button>

        </header>
        <?php drawModal('loginModal','drawLoginForm');?>
        <?php drawModal('registerModal','drawRegisterForm');?>


<?php } function drawLoginForm() { ?>
    <form action="../actions/action_login.php" method="post" class="login">
        <input type="text" name="username" placeholder="username">
        <input type="password" name="password" placeholder="password">
        <button type="submit">Login</button>
    </form>


<?php } function drawRegisterForm() { ?>
    <form action="../actions/action_register.php" method="POST">
        <input type="text" name="first_name" placeholder="First Name">
        <input type="text" name="last_name" placeholder="Last Name">
        <input type="text" name="username" placeholder="Username">
        <input type="password" name="password" placeholder="Password">
        <button type="submit">Register</button>
    </form>

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
        LTW Freelancer website &copy; 2025
    </footer>    
    </body>
    </html>

<?php } ?>