<?php
/* -------------------------------------------------------------------------- */
/* LATO SERVER                                  */
/* -------------------------------------------------------------------------- */
session_start();
require_once 'db.php';

// Variabili per messaggi e dati Sticky
$error_msg = "";
$success_msg = "";
$sticky_login_user = "";
$sticky_reg_user = "";
$sticky_reg_email = "";

// Default: mostriamo il login. Se però c'è un errore in registrazione, cambieremo questo valore.
$auth_mode = 'login'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ==============================
       LOGICA DI LOGIN
       ============================== */
    if (isset($_POST['action']) && $_POST['action'] === 'login') {
        $auth_mode = 'login'; // Restiamo sul login
        
        $login_user = trim($_POST['login-user'] ?? '');
        $login_pass = $_POST['login-pass'] ?? '';

        // Sticky Login
        $sticky_login_user = htmlspecialchars($login_user);

        if (!empty($login_user) && !empty($login_pass)) {
            $query = "SELECT id, username, password_hash, role FROM users WHERE username = $1";
            $result = pg_query_params($db, $query, array($login_user));

            if ($result && pg_num_rows($result) > 0) {
                $user = pg_fetch_assoc($result);
                if (password_verify($login_pass, $user['password_hash'])) {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    header("Location: home.php");
                    exit;
                } else {
                    $error_msg = "Password errata.";
                }
            } else {
                $error_msg = "Utente non trovato.";
            }
        } else {
            $error_msg = "Inserisci username e password.";
        }
    }

    /* ==============================
       LOGICA DI REGISTRAZIONE
       ============================== */
    if (isset($_POST['action']) && $_POST['action'] === 'register') {
        
        // IMPORTANTE: Se siamo qui, l'utente ha provato a registrarsi.
        // Impostiamo la modalità su 'register' così se c'è un errore il form rimane aperto.
        $auth_mode = 'register'; 

        $reg_user = trim($_POST['reg-user'] ?? '');
        $reg_email = trim($_POST['reg-email'] ?? '');
        $reg_pass = $_POST['reg-pass'] ?? '';
        $reg_pass_conf = $_POST['reg-pass-conf'] ?? '';

        // Sticky Registration: Salviamo i dati per ristamparli
        $sticky_reg_user = htmlspecialchars($reg_user);
        $sticky_reg_email = htmlspecialchars($reg_email);

        // Validazione
        if (strlen($reg_user) < 3) {
            $error_msg = "L'username deve avere almeno 3 caratteri.";
        } elseif (!filter_var($reg_email, FILTER_VALIDATE_EMAIL)) {
            $error_msg = "Email non valida.";
        } elseif (strlen($reg_pass) < 6) {
            $error_msg = "La password deve avere almeno 6 caratteri.";
        } elseif ($reg_pass !== $reg_pass_conf) {
            $error_msg = "Le password non coincidono.";
        } else {
            // Controllo duplicati
            $check_query = "SELECT id FROM users WHERE username = $1 OR email = $2";
            $check_result = pg_query_params($db, $check_query, array($reg_user, $reg_email));

            if (pg_num_rows($check_result) > 0) {
                $error_msg = "Username o Email già esistenti.";
            } else {
                // Inserimento
                $hashed_password = password_hash($reg_pass, PASSWORD_DEFAULT);
                $insert_query = "INSERT INTO users (username, email, password_hash) VALUES ($1, $2, $3) RETURNING id, role";
                $insert_result = pg_query_params($db, $insert_query, array($reg_user, $reg_email, $hashed_password));

                if ($insert_result) {
                    $new_user = pg_fetch_assoc($insert_result);
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $new_user['id'];
                    $_SESSION['username'] = $reg_user;
                    $_SESSION['role'] = $new_user['role'];
                    header("Location: home.php");
                    exit;
                } else {
                    $error_msg = "Errore DB: " . pg_last_error($db);
                }
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
                    <input type="password" id="l-pass" name="login-pass" placeholder="Password" required>
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
                    <input type="password" id="r-pass" name="reg-pass" 
                           placeholder="Minimo 6 caratteri" required>
                </div>
                
                <div class="form-group">
                    <label for="r-conf">Conferma Password</label>
                    <input type="password" id="r-conf" name="reg-pass-conf" 
                           placeholder="Ripeti password" required>
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
</script>

</body>
</html>