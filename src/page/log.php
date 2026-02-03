<?php
session_start(); // Avvio sessione
require_once 'db.php'; // Importa database

// Gestione redirect sicuro
if (isset($_GET['redirect']) && !empty($_GET['redirect'])) {
    $page = basename($_GET['redirect']); // Estrae il nome della pagina di provenienza senza directory

    // Array contentente tutte le pagine del sito
    $allowed_pages = ['home.php', 'map.php', 'autosalone.php', 'profile.php', 'community.php', 'admin.php'];
    
    // Verifica pagina di provenienza e la salva in sessione
    if (in_array($page, $allowed_pages)) {
        $_SESSION['redirect_url'] = $page;
    } else {
        $_SESSION['redirect_url'] = 'home.php';
    }
}

if (!isset($_SESSION['redirect_url'])) {
    $_SESSION['redirect_url'] = 'home.php';
}

if (!isset($db) || !$db) {
    die("Errore: Connessione al database fallita.");
}

// Se l'utente è già loggato, via da qui
if (isset($_SESSION['user_id']) || isset($_SESSION['username'])) {
    header("Location: home.php");
    exit;
}

$error_msg = '';
$success_msg = '';
$auth_mode = 'login'; // Default
$sticky_login_user = '';
$sticky_reg_user = '';
$sticky_reg_email = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $action = $_POST['action'] ?? ''; // Register o Login

    // --- REGISTRAZIONE ---
    if ($action === 'register') {
        $auth_mode = 'register';
        
        $username = trim($_POST['reg-user']);
        $email = trim($_POST['reg-email']);
        $password = $_POST['reg-pass'];
        $password_conf = $_POST['reg-pass-conf'];

        $sticky_reg_user = htmlspecialchars($username);
        $sticky_reg_email = htmlspecialchars($email);

        // --- VALIDAZIONE LATO SERVER (PHP) ---
        if (empty($username) || empty($email) || empty($password)) {
            $error_msg = "Tutti i campi sono obbligatori.";
        } elseif ($password !== $password_conf) {
            $error_msg = "Le password non coincidono.";
        } elseif (strlen($password) < 6) {
            $error_msg = "La password deve essere di almeno 6 caratteri.";
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $error_msg = "La password deve contenere almeno una lettera maiuscola.";
        } elseif (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            $error_msg = "La password deve contenere almeno un carattere speciale.";
        } else {
            // Se tutto ok, inseriamo nel DB
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (username, email, password_hash) VALUES ($1, $2, $3)";
            
            // Usiamo @ per silenziare errori PHP nativi e gestire pg_last_error
            $result = @pg_query_params($db, $query, [$username, $email, $password_hash]);

            if ($result) {
                $auth_mode = 'login'; 
                $sticky_login_user = $username;
                $success_msg = "Registrazione completata! Ora puoi accedere.";
                // Resettiamo i campi sticky della registrazione
                $sticky_reg_user = '';
                $sticky_reg_email = '';
            } else {
                $pg_err = pg_last_error($db);
                // Cerchiamo errori di duplicazione (Unique constraint violation)
                if (preg_match('/(duplicate|unique|viola|violazione|23505)/i', $pg_err)) {
                    $error_msg = "Attenzione: Username o Email già utilizzati da un altro utente.";
                } else {
                    $error_msg = "Errore generico nel database. Riprova più tardi.";
                }
            }
        }
    }

    // --- LOGIN ---
    elseif ($action === 'login') {
        $auth_mode = 'login';
        
        $username = trim($_POST['login-user']);
        $password = $_POST['login-pass'];
        
        $sticky_login_user = htmlspecialchars($username);

        if (empty($username) || empty($password)) {
            $error_msg = "Inserisci username e password.";
        } else {
            $query = "SELECT id, username, password_hash, role FROM users WHERE username = $1";
            $result = pg_query_params($db, $query, array($username));
            
            if ($result) {
                $user_row = pg_fetch_assoc($result);

                if ($user_row && password_verify($password, $user_row['password_hash'])) {
                    // Login Success
                    $_SESSION['user_id'] = $user_row['id'];
                    $_SESSION['username'] = $user_row['username'];
                    $_SESSION['role'] = $user_row['role'];

                    header("Location: home.php");
                    exit;
                } else {
                    $error_msg = "Credenziali non valide.";
                }
            } else {
                $error_msg = "Errore di connessione al sistema.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <title> Accedi | beGreen</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Web App progettata per rispondere alle sfide emergenti dell'energia rinnovabile nel settore delle auto elettriche">
        <link rel="icon" type="image/png" href="../src_image/favicon/favicon-96x96.png" sizes="96x96" />
        <link rel="icon" type="image/svg+xml" href="../src_image/favicon/favicon.svg" />
        <link rel="shortcut icon" href="../src_image/favicon/favicon.ico"/>
        <link rel="manifest" href="../src_image/favicon/site.webmanifest"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="../css/style.css">
        <script src="../js/log.js"></script>
    </head>

<body>
<section id="auth-section">
    <div class="auth-wrapper">
            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i> <?php echo $success_msg; ?>
            </div>
            <?php endif; ?>
        <div id="login-container" style="display: <?php echo $auth_mode === 'login' ? 'block' : 'none'; ?>;">
            <div class="auth-header">
                <h2>Accedi</h2>
                <p>Bentornato in beGreen</p>
            </div>
        
                <form id="login-form" action="log.php" method="POST" novalidate>
                <input type="hidden" name="action" value="login">
                
                <div class="form-group">
                    <label for="l-user">Username</label>
                    <input type="text" id="l-user" name="login-user" 
                           placeholder="Inserisci il tuo username" 
                           value="<?php echo $sticky_login_user; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="l-pass">Password</label>
                    <div class="password-container">
                        <input type="password" id="l-pass" name="login-pass" placeholder="Password" required>
                        <i class="fa-solid fa-eye toggle-eye" onclick="togglePassword('l-pass', this)"></i>
                    </div>
                </div>
                
                <button type="submit" class="auth-btn">Entra</button>
            </form>
            
            <div class="auth-toggle">
                Non hai un account? <a onclick="toggleMode('register')">Registrati ora</a>
            </div>
        </div>

        <div id="register-container" style="display: <?php echo $auth_mode === 'register' ? 'block' : 'none'; ?>;">
            <div class="auth-header">
                <h2>Crea Account</h2>
                <p>Benvenuto in beGreen</p>
            </div>
            
            <form id="register-form" action="log.php" method="POST" novalidate>
                <input type="hidden" name="action" value="register">

                <div class="form-group">
                    <label for="r-user">Username</label>
                    <input type="text" id="r-user" name="reg-user" 
                           placeholder="Scegli un username" 
                           value="<?php echo $sticky_reg_user; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="r-email">Email</label>
                    <input type="email" id="r-email" name="reg-email" 
                           placeholder="esempio@email.com" 
                           value="<?php echo $sticky_reg_email; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="r-pass">Password</label>
                    <div class="password-container">
                        <input type="password" id="r-pass" name="reg-pass" placeholder="Minimo 6 caratteri" required>
                        <i class="fa-solid fa-eye toggle-eye" onclick="togglePassword('r-pass', this)"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="r-conf">Conferma Password</label>
                    <div class="password-container">
                        <input type="password" id="r-conf" name="reg-pass-conf" placeholder="Ripeti password" required>
                        <i class="fa-solid fa-eye toggle-eye" onclick="togglePassword('r-conf', this)"></i>
                    </div>
                </div>
                
                <button type="submit" class="auth-btn">Registrati</button>
            </form>
            
            <div class="auth-toggle">
                Hai già un account? <a onclick="toggleMode('login')">Accedi</a>
            </div>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; gap: 15px; margin-top: 20px;">
            <img src="../src_image/images/beGreen_cyan.png" alt="Logo beGreen" style="height: 35px; width: auto;">
            <a href="<?php echo htmlspecialchars($_SESSION['redirect_url']); ?>" class="back-home-btn">
                <i class="fa-regular fa-circle-xmark"></i>  Torna Indietro
            </a>
        </div>

    </div>
</section>
</body>
</html>