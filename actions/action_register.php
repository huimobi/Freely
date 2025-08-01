    <?php

    require_once __DIR__ . '/../includes/session.php';

    $session = Session::getInstance();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {http_response_code(405); exit('Method Not Allowed');}
    $submitted = $_POST['csrf_token'] ?? '';
    if (! $session->validateCsrfToken($submitted)) {http_response_code(403); exit('Invalid CSRF token');}

    $username = trim($_POST['username'] ?? '');
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';

    $role = $_POST['role']?? 'client';

    $headline = trim($_POST['headline']     ?? '');
    $description = trim($_POST['description']  ?? '');

    $errors = [];
    
    if (User::emailExists($email)) $errors[] = 'That email is already in use.'; 
    if (User::usernameExists($username)) $errors[] = 'That username is already taken.'; 
    if (strlen($firstName) > 30) $errors[] = 'First name cannot exceed 30 characters.'; 
    if (strlen($lastName) > 30) $errors[] = 'Last name cannot exceed 30 characters.'; 
    if (strlen($username) > 30) $errors[] = 'Username cannot exceed 30 characters.'; 
    if (strlen($email) > 30) $errors[] = 'Email cannot exceed 30 characters.'; 
    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid e-mail address.';
    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';

    if ($role === 'freelancer') {
        if (strlen($headline) > 200) $errors[] = 'Headline cannot exceed 200 characters.'; 
        if (strlen($description) > 1000) $errors[] = 'Description cannot exceed 1000 characters.';
    }

    if (!empty($errors)) {
        $_SESSION['register_errors'] = $errors;
        if ($role === 'freelancer') { header('Location: /pages/form_register.php');
        } else { header('Location: /');}
        exit;
    }
    
    if ($role === 'freelancer') { $user = User::register( $username, $firstName, $lastName, $email, $password, $headline, $description);
    } else { $user = User::register( $username, $firstName, $lastName, $email, $password);}
      
    if ($user !== null){
        Session::getInstance()->login($user);
    }
    

    header('Location: /');
    exit;
