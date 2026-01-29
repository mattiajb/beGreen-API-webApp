<?php
session_start();

// Simulazione di database utenti (in un caso reale useresti PDO o mysqli)
$mock_users = [
    ['username' => 'admin', 'password' => 'admin', 'role' => 'administrator'],
    ['username' => 'user', 'password' => 'password123', 'role' => 'user']
];

$message = "";
$error = "";
$mode = isset($_POST['auth_mode']) ? $_POST['auth_mode'] : 'login';

// LOGICA DI LOGIN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $user_input = $_POST['login-user'] ?? '';
    $pass_input = $_POST['login-pass'] ?? '';

    $authenticated = false;
    foreach ($mock_users as $u) {
        if ($u['username'] === $user_input && $u['password'] === $pass_input) {
            $_SESSION['user'] = $u['username'];
            $_SESSION['role'] = $u['role'];
            $authenticated = true;
            break;
        }
    }

    if ($authenticated) {
        $message = "Login effettuato con successo! Benvenuto, " . htmlspecialchars($_SESSION['user']);
    } else {
        $error = "Username o password errati.";
        $mode = 'login';
    }
}

// LOGICA DI REGISTRAZIONE (Simulata)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $reg_user = $_POST['reg-user'] ?? '';
    $reg_pass = $_POST['reg-pass'] ?? '';
    $reg_pass_conf = $_POST['reg-pass-conf'] ?? '';

    if (strlen($reg_user) < 3) {
        $error = "L'username deve avere almeno 3 caratteri.";
    } elseif (strlen($reg_pass) < 6) {
        $error = "La password deve avere almeno 6 caratteri.";
    } elseif ($reg_pass !== $reg_pass_conf) {
        $error = "Le password non coincidono.";
    } else {
        $message = "Registrazione completata! Ora puoi accedere.";
        $mode = 'login';
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../src_image/favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="../src_image/favicon/favicon.svg" />
    <link rel="shortcut icon" href="../src_image/favicon/favicon.ico"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Accedi | beGreen</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<section id="auth-section" class="section">
    <div class="auth-wrapper">
        
        <?php if ($message): ?>
            <div class="success-msg"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- CONTAINER LOGIN -->
        <div id="login-container" style="display: <?php echo $mode === 'login' ? 'block' : 'none'; ?>;">
            <div class="auth-header">
                <h2>Accedi</h2>
                <p style="color: var(--text-muted)">Bentornato in beGreen</p>
                <p style="font-size: 0.8rem; color: var(--secondary); margin-top:10px;">
                    (Tip: Usa <b>admin</b> / <b>admin</b> per accesso amministratore)
                </p>
            </div>
            
            <form action="auth.php" method="POST">
                <input type="hidden" name="action" value="login">
                <input type="hidden" name="auth_mode" value="login">
                
                <div class="form-group">
                    <label>Email o Username</label>
                    <input type="text" name="login-user" placeholder="Inserisci username" required value="<?php echo isset($_POST['login-user']) ? htmlspecialchars($_POST['login-user']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="login-pass" placeholder="Password" required>
                </div>
                
                <button type="submit" class="auth-btn" style="width: 100%">Entra</button>
            </form>
            
            <div class="auth-toggle">Non hai un account? <a onclick="toggleMode('register')">Registrati ora</a></div>
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 15px; margin-top: 20px;">
                <img src="../src_image/images/beGreen_cyan.png" alt="Logo beGreen" style="height: 35px; width: auto;">
                
                <a href="home.php" class="back-home-btn">
                    <i class="fa-regular fa-circle-xmark"></i>  Torna alla Home
                </a>
            </div>

        </div>

        <!-- CONTAINER REGISTRAZIONE -->
        <div id="register-container" style="display: <?php echo $mode === 'register' ? 'block' : 'none'; ?>;">
            <div class="auth-header">
                <h2>Crea Account</h2>
                <p style="color: var(--text-muted)">Benvenuto in beGreen</p>
                <p style="font-size: 0.8rem; color: var(--secondary); margin-top:10px;">
                    Inserisci le credenziali e registrati.
                </p>
            </div>
            
            <form action="auth.php" method="POST">
                <input type="hidden" name="action" value="register">
                <input type="hidden" name="auth_mode" value="register">

                <div class="form-group">
                    <label>Nome Utente</label>
                    <input type="text" name="reg-user" placeholder="Scegli un username" required>
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="reg-email" placeholder="latuamail@esempio.com" required>
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="reg-pass" placeholder="Minimo 6 caratteri" required>
                </div>
                
                <div class="form-group">
                    <label>Conferma Password</label>
                    <input type="password" name="reg-pass-conf" placeholder="Ripeti password" required>
                </div>
                
                <button type="submit" class="auth-btn" style="width: 100%">Registrati</button>
            </form>
            
            <div class="auth-toggle">Hai già un account? <a onclick="toggleMode('login')">Accedi</a></div>
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 15px; margin-top: 20px;">
                <img src="../src_image/images/beGreen_cyan.png" alt="Logo beGreen" style="height: 35px; width: auto;">
                
                <a href="home.php" class="back-home-btn">
                    <i class="fa-regular fa-circle-xmark"></i>  Torna alla Home
                </a>
            </div>
        </div>
    </div>
</section>

<script>
    // Gestione visualizzazione lato client senza ricaricare per il toggle semplice
    function toggleMode(mode) {
        const loginCont = document.getElementById('login-container');
        const regCont = document.getElementById('register-container');
        
        if (mode === 'register') {
            loginCont.style.display = 'none';
            regCont.style.display = 'block';
        } else {
            loginCont.style.display = 'block';
            regCont.style.display = 'none';
        }
    }
</script>

</body>
</html>