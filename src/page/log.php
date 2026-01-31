<?php
// 1. Avvio Sessione e Inclusione DB
session_start();
require_once 'db.php'; 

// Debug: Se $db non esiste, blocca tutto
if (!isset($db) || !$db) {
    die("Errore: Connessione al database fallita.");
}

// Se l'utente è già loggato, via alla home
if (isset($_SESSION['user_id']) || isset($_SESSION['username'])) {
    header("Location: home.php");
    exit;
}

// 2. Inizializzazione Variabili
$error_msg = '';
$success_msg = '';
$auth_mode = 'login'; // Default view
$sticky_login_user = '';
$sticky_reg_user = '';
$sticky_reg_email = '';

// 3. Gestione del Form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $action = $_POST['action'] ?? '';

    // --- REGISTRAZIONE ---
    if ($action === 'register') {
        $auth_mode = 'register';
        
        $username = trim($_POST['reg-user']);
        $email = trim($_POST['reg-email']);
        $password = $_POST['reg-pass'];
        $password_conf = $_POST['reg-pass-conf'];

        // Mantieni i valori nei campi in caso di errore
        $sticky_reg_user = htmlspecialchars($username);
        $sticky_reg_email = htmlspecialchars($email);

        // Validazione Input
        if (empty($username) || empty($email) || empty($password)) {
            $error_msg = "Tutti i campi sono obbligatori.";
        } elseif ($password !== $password_conf) {
            $error_msg = "Le password non coincidono.";
        } elseif (strlen($password) < 6) {
            $error_msg = "La password deve essere di almeno 6 caratteri.";
        } else {
            // Hashing della password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Query Parametrica per Inserimento
            $query = "INSERT INTO users (username, email, password_hash) VALUES ($1, $2, $3)";
            
            // Usiamo @ per sopprimere il warning PHP in caso di duplicato (gestiamo noi l'errore)
            $result = @pg_query_params($db, $query, [$username, $email, $password_hash]);

            if ($result) {
                // Successo
                $auth_mode = 'login'; 
                $sticky_login_user = $username; // Precompila il login
                $success_msg = "Registrazione completata! Ora puoi accedere.";
                // Reset dei campi di registrazione
                $sticky_reg_user = '';
                $sticky_reg_email = '';
            } else {
                // Fallimento: Controlliamo se è un errore di duplicato
                $pg_err = pg_last_error($db);
                // CORREZIONE QUI: Usiamo una regex per catturare 'duplicate', 'unique' o il codice errore '23505'
                // Questo funziona sia se il DB è in inglese, sia in italiano.
                    if (preg_match('/(duplicate|unique|viola|violazione|23505)/i', $pg_err)) {
                    $error_msg = "Attenzione: Username o Email già utilizzati da un altro utente.";
                } else {
                    // Errore tecnico
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
            // Query diretta con pg_query_params (più semplice e sicura di prepare/execute per query singole)
            $query = "SELECT id, username, password_hash, role FROM users WHERE username = $1";
            $result = pg_query_params($db, $query, array($username));
            
            if ($result) {
                $user_row = pg_fetch_assoc($result);

                if ($user_row && password_verify($password, $user_row['password_hash'])) {
                    // Login corretto: Salva sessione
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
    </head>

<body>
<section id="auth-section">
    <div class="auth-wrapper">
                    <?php if (!empty($error_msg)): ?>
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>
        <div id="login-container" style="display: <?php echo $auth_mode === 'login' ? 'block' : 'none'; ?>;">
            <div class="auth-header">
                <h2>Accedi</h2>
                <p>Bentornato in beGreen</p>
            </div>
            <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i> <?php echo $success_msg; ?>
            </div>
            <?php endif; ?>
            
            <form action="log.php" method="POST">
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
            
            <form action="log.php" method="POST">
                <input type="hidden" name="action" value="register">

                <div class="form-group">
                    <label for="r-user">Nome Utente</label>
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
            <a href="home.php" class="back-home-btn">
                <i class="fa-regular fa-circle-xmark"></i>  Torna alla Home
            </a>
        </div>

    </div>
</section>

<script>
    function toggleMode(mode) {
        const loginCont = document.getElementById('login-container');
        const regCont = document.getElementById('register-container');
        const alerts = document.querySelectorAll('.alert');

        alerts.forEach(el => el.style.display = 'none');
        
        if (mode === 'register') {
            loginCont.style.display = 'none';
            regCont.style.display = 'block';
        } else {
            loginCont.style.display = 'block';
            regCont.style.display = 'none';
        }
    }
    
    function togglePassword(inputId, icon) {
        const input = document.getElementById(inputId);
        
        if (input.type === "password") {
            input.type = "text"; // Mostra password
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash"); // Cambia icona in occhio sbarrato
        } else {
            input.type = "password"; // Nascondi password
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye"); // Torna icona occhio normale
        }
    }
</script>

</body>
</html>