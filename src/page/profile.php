<?php
session_start();
require_once 'db.php';

// CONTROLLO ACCESSO
if (!isset($_SESSION['user_id'])) {
    header("Location: log.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$error = "";

// 2. RECUPERO DATI UTENTE (SPOSTATO QUI IN ALTO)
// È fondamentale recuperare la password hash PRIMA di provare a cambiarla
$query_user = "SELECT username, email, role, password_hash FROM users WHERE id = $1";
$result_user = pg_query_params($db, $query_user, array($user_id));

if ($user_data = pg_fetch_assoc($result_user)) {
    $email_reale = $user_data['email'];
    $username_reale = $user_data['username'];
    $role_reale = $user_data['role']; 
    $current_hash_db = $user_data['password_hash']; // Ora la variabile è definita!
} else {
    // Se l'utente non esiste nel DB
    session_destroy();
    header("Location: log.php");
    exit();
}

// 3. LOGICA CAMBIO PASSWORD
if (isset($_POST['update_password'])) {
    $current_pass_input = $_POST['current_pass'];
    $new_pass_input = $_POST['new_pass'];

    // Ora $current_hash_db esiste ed è popolata
    if (!password_verify($current_pass_input, $current_hash_db)) {
        $error = "La password attuale non è corretta.";
    } 
    elseif (strlen($new_pass_input) < 6) {
        $error = "La nuova password deve avere almeno 6 caratteri.";
    }
    else {
        // Aggiornamento password
        $new_hash = password_hash($new_pass_input, PASSWORD_DEFAULT);
        
        $query_update_pass = "UPDATE users SET password_hash = $1 WHERE id = $2";
        $result_update = pg_query_params($db, $query_update_pass, array($new_hash, $user_id));

        if ($result_update) {
            $message = "Password aggiornata con successo!";
            $current_hash_db = $new_hash; // Aggiorniamo la variabile locale per evitare errori
        } else {
            $error = "Errore durante l'aggiornamento della password.";
        }
    }
}

// "DIVENTA PLUS" (Aggiornamento nel Database)
if (isset($_POST['go_plus'])) {
    // Aggiornanemdo del ruolo nel DB
    $query_plus = "UPDATE users SET role = 'plus' WHERE id = $1";
    $result_plus = pg_query_params($db, $query_plus, array($user_id));

    if ($result_plus) {
        $_SESSION['role'] = 'plus';
        header("Location: profile.php?success=1");
        exit();
    } else {
        $error = "Errore durante l'aggiornamento del profilo.";
    }
}

// GESTIONE ETICHETTE E STILI IN BASE AL RUOLO
$user_label = "STANDARD";
$badge_style = ""; 

switch ($role_reale) {
    case 'admin':
        $user_label = "ADMIN";
        $badge_style = "color: #ff2d55; border-color: #ff2d55; font-weight: bold;";
        break;
    case 'plus':
        $user_label = "UTENTE PLUS+";
        $badge_style = "color: #ffd700; border-color: #ffd700; font-weight: bold; text-shadow: 0 0 10px rgba(255, 215, 0, 0.5); box-shadow: inset 0 0 10px rgba(255, 215, 0, 0.1);";
        break;
    default: // user standard
        $user_label = "STANDARD";
        $badge_style = "color: var(--text-muted); border-color: rgba(255,255,255,0.2);";
        break;
}

if (isset($_GET['success'])) {
    $message = "Complimenti! Ora sei un <b style='color:#ffd700;'>Utente Plus+</b>";
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Profilo | beGreen</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../src_image/favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Stile specifico per i campi non modificabili */
        input[readonly] {
            background: rgba(255, 255, 255, 0.03);
            color: var(--text-muted);
            cursor: not-allowed;
            border: 1px dashed rgba(255, 255, 255, 0.1);
        }
        /* Highlight per il ruolo Plus */
        .status-plus {
            border-color: var(--primary) !important;
            color: var(--primary) !important;
            font-weight: bold;
        }
    </style>
</head>
<body>

<section id="auth-section">
    <div class="auth-wrapper" style="max-width: 500px;">
        
        <div class="auth-header">
            <h2>Il Tuo Profilo</h2>
            <p>Gestisci il tuo account beGreen</p>
        </div>

        <?php if ($message): ?> <div class="success-msg"><?php echo $message; ?></div> <?php endif; ?>
        <?php if ($error): ?> <div class="error-msg"><?php echo $error; ?></div> <?php endif; ?>

        <div class="user-info-section">
            <div class="form-group">
                <label>Username</label>
                <input type="text" value="<?php echo htmlspecialchars($username_reale); ?>" readonly>
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" value="<?php echo htmlspecialchars($email_reale); ?>" readonly>
            </div>
            
            <div class="form-group">
                <label>Tipologia Utente</label>
                <input type="text" value="<?php echo $user_label; ?>" readonly style="<?php echo $badge_style; ?>">
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 25px 0;">

        <?php if ($role_reale !== 'admin'): ?>
            <form method="POST">
                <h3 style="color: var(--primary); font-size: 1rem; margin-bottom: 15px;">Cambia Password</h3>
                <div class="form-group">
                    <input type="password" name="current_pass" placeholder="Password attuale" required>
                </div>
                <div class="form-group">
                    <input type="password" name="new_pass" placeholder="Nuova password" required>
                </div>
                <button type="submit" name="update_password" class="auth-btn" style="background: transparent; border: 1px solid var(--primary); color: var(--primary);">
                    Aggiorna Password
                </button>
            </form>

        <?php else: ?>
            <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 25px 0;">
            <div style="padding: 15px; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; text-align: center; color: var(--text-muted); font-size: 0.85rem;">
                <i class="fa-solid fa-lock"></i> Le impostazioni di sicurezza dell'Admin sono gestite internamente.
            </div>
        <?php endif; ?>


        <?php if ($role_reale === 'user'): ?>
                    <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 25px 0;">
        <div style="margin-top: 30px; padding: 20px; border: 1px solid #ffd700; border-radius: 12px; text-align: center; background: rgba(255, 215, 0, 0.05);">
            <p style="font-size: 0.9rem; color: #ffd700; margin-bottom: 15px;">
                Sblocca funzionalità esclusive
            </p>
            <form method="POST">
                <button type="submit" name="go_plus" class="plus-btn">
                    <i class="fa-solid fa-crown"></i> Diventa Utente Plus+
                </button>
            </form>
        </div>
        <?php endif; ?>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 30px;">
            <img src="../src_image/images/beGreen_cyan.png" alt="Logo beGreen" style="height: 35px; width: auto;">

            <div class="bottom-actions">
                <a href="home.php" class="bottom-link home">
                    <i class="fa-solid fa-house"></i> Home
                </a>
                <a href="logout.php" class="bottom-link logout">
                    Esci <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>

        </div>

    </div>
</section>

</body>
</html>