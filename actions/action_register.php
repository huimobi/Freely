    <?php

    require_once __DIR__ . '/../includes/session.php';

    $username = trim($_POST['username'] ?? '');
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';

    $role = $_POST['role']?? 'client';

    $headline = trim($_POST['headline']     ?? '');
    $description = trim($_POST['description']  ?? '');
    $hourlyRaw = $_POST['hourly_rate'] ?? '';
    $currency = trim($_POST['currency_rate']?? '');

    $errors = [];
    if (User::emailExists($email)) $errors[] = 'That email is already in use.'; 
    if (User::usernameExists($username)) $errors[] = 'That username is already taken.'; 
    if (strlen($firstName) > 30) $errors[] = 'First name cannot exceed 30 characters.'; 
    if (strlen($lastName) > 30) $errors[] = 'Last name cannot exceed 30 characters.'; 
    if (strlen($username) > 30) $errors[] = 'Username cannot exceed 30 characters.'; 
    if (strlen($email) > 30) $errors[] = 'Email cannot exceed 30 characters.'; 
    if (strlen($headline) > 200) $errors[] = 'Headline cannot exceed 200 characters.'; 
    if (strlen($description) > 1000) $errors[] = 'Description cannot exceed 1000 characters.';
    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
    if (strlen($currency) > 3) $errors[] = 'Currency code must be 3 letters.';
    
    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid e-mail address.';

    if ($hourlyRaw === '' || !is_numeric($hourlyRaw)) {
        $errors[] = 'Hourly rate must be a number.';
    } else {
        $hourlyRate = (float)$hourlyRaw;
        if ($hourlyRate < 0) $errors[] = 'Hourly rate cannot be negative.';
        if ($hourlyRate > 1000) $errors[] = 'Hourly rate seems too large.';
    }

    if (!empty($errors)) {
        $_SESSION['register_errors'] = $errors;
        if ($role === 'freelancer') { header('Location: /pages/form_register.php');
        } else { header('Location: /');}
        exit;
    }

        
    $user = User::register( $username, $firstName, $lastName, $email, $password);
    if ($user !== null){
        if ($role === 'freelancer'){
            if(! $user->registerFreelancer($headline, $description, $hourlyRate, $currency)){
                $_SESSION['register_errors'] = ['Could not create freelancer profile.'];
                header('Location: /pages/form_register.php');
                exit;
            }
        }
        Session::getInstance()->login($user);
    }
    

    header('Location: /');
    exit;
