<?php
session_start();

// Pad naar UserManager.php. Van MBOcinema/login.php naar MBOcinema/classes/UserManager.php
require_once 'classes/UserManager.php';

// Instantieer de UserManager
$userManager = new UserManager();

$login_error = '';
$signup_error = '';

// Login afhandeling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_submit'])) { // Gebruik een specifieke submit knop naam
    $name_login = trim($_POST['username_login']);
    $password_login = trim($_POST['password_login']);

    $user = $userManager->verifyUser($name_login, $password_login);

    if ($user) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user['ID'];
        $_SESSION['user_role'] = $user['role']; // Sla ook de rol op

        header("Location: admin.php"); // Of waar de gebruiker naartoe moet
        exit();
    } else {
        $login_error = 'De gebruikersnaam en wachtwoord combinatie is incorrect!';
    }
}

// Registratie afhandeling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup_submit'])) { // Gebruik een specifieke submit knop naam
    $name = trim($_POST['username_signup']);
    $password = trim($_POST['password_signup']);
    if ($userManager->addUser($name, $password)) {
        // Registratie succesvol, toon eventueel een bericht of redirect naar login
        $login_error = 'Account succesvol aangemaakt! Log hier in.'; // Dit toont het bericht in de login sectie
    } else {
        $signup_error = 'Deze gebruikersnaam is al in gebruik of er is een fout opgetreden tijdens de registratie.';
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Welcome back!</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet">
</head>

<body id="login" class="loginn">
    <section id="form">
        <input type="checkbox" id="chk" aria-hidden="true">

        <article class="login">
            <form method="POST" action="">
                <label for="chk" aria-hidden="true">Log in</label>
                <input type="text" name="username_login" placeholder="username" required>
                <input type="password" name="password_login" placeholder="password" required>
                <button type="submit" name="login_submit">log in</button>
                <?php if ($login_error): ?>
                    <p class="error-message"><span class="error-icon">&#x26A0;</span> <?php echo $login_error; ?></p>
                <?php endif; ?>
            </form>
        </article>

        <article class="signup">
            <form method="POST" action="">
                <label for="chk" aria-hidden="true">sign up</label>
                <input type="text" name="username_signup" placeholder="username" required>
                <input type="password" name="password_signup" placeholder="password" required>
                <input type="password" name="Email_signup" placeholder="Email" required>
                <button type="submit" name="signup_submit">Create account</button>
                <?php if ($signup_error): ?>
                    <p class="error-message"><span class="error-icon">&#x26A0;</span> <?php echo $signup_error; ?></p>
                <?php endif; ?>
            </form>
        </article>
    </section>
</body>

</html>