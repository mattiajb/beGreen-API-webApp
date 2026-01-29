<?php
session_start();
require_once 'db.php';

// 1. LOGICA DI ACCESSO E RUOLI (Sincronizzata con Home)
$is_logged = false;
$username = "Ospite";
$user_role = "guest"; 
$user_label = "Visitatore";

if (isset($_SESSION['user_id'])) {
    $is_logged = true;
    $username = htmlspecialchars($_SESSION['username']); 
    $user_role = $_SESSION['role']; 
    
    switch ($user_role) {
        case 'admin':
            $user_label = "Amministratore";
            break;
        case 'plus':
            $user_label = "Utente Plus";
            break;
        default:
            $user_label = "Utente Standard";
            break;
    }
} else {
    // Se non è loggato, non può stare qui
    header("Location: log.php");
    exit();
}

// Variabili di supporto per la UI
$email = $_SESSION['email'] ?? ($username . "@example.com");
$message = "";
$error = "";

// 2. LOGICA CAMBIO PASSWORD (Esempio)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    // Qui andrebbe la logica reale con password_verify e UPDATE sul DB
    $message = "Funzionalità cambio password pronta per il database!";
}

// 3. LOGICA DIVENTA PLUS
if (isset($_POST['go_plus']) && $user_role !== 'plus' && $user_role !== 'admin') {
    // In un caso reale: UPDATE users SET role='plus' WHERE id=...
    $_SESSION['role'] = 'plus';
    header("Location: profile.php?success=1");
    exit();
}
if (isset($_GET['success'])) $message = "Complimenti! Ora sei un Utente Plus 🌿";
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
                <input type="text" value="<?php echo $username; ?>" readonly>
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" value="<?php echo htmlspecialchars($email); ?>" readonly>
            </div>
            
            <div class="form-group">
                <label>Tipologia Utente</label>
                <input type="text" value="<?php echo $user_label; ?>" readonly 
                       class="<?php echo ($user_role === 'plus') ? 'status-plus' : ''; ?>">
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 25px 0;">

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

        <?php if ($user_role === 'user'): ?>
        <div style="margin-top: 30px; padding: 20px; border: 1px dashed #ffd700; border-radius: 12px; text-align: center;">
            <p style="font-size: 0.8rem; color: #ffd700; margin-bottom: 10px;">Ottieni l'accesso alla Community+</p>
            <form method="POST">
                <button type="submit" name="go_plus" class="auth-btn" style="background: linear-gradient(135deg, #ffd700, #b8860b); color: #020617;">
                    <i class="fa-solid fa-crown"></i> Diventa Utente+
                </button>
            </form>
        </div>
        <?php endif; ?>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 30px;">
            <img src="../src_image/images/beGreen_cyan.png" alt="Logo beGreen" style="height: 35px; width: auto;">
            <div style="display: flex; gap: 15px;">
                <a href="home.php" style="color: var(--text-muted); text-decoration: none; font-size: 0.85rem;">
                    <i class="fa-solid fa-house"></i> Home
                </a>
                <a href="logout.php" style="color: #ff2d55; text-decoration: none; font-size: 0.85rem;">
                    Esci <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>
        </div>

    </div>
</section>

</body>
</html>