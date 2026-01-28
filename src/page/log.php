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
    <title>GreenSpark Auth</title>


<link rel="stylesheet" href="../acss/style.css">
<!-- style temporaneo-->
<style>
:root {
    --primary: #00f2ff;       /* Ciano Elettrico */
    --secondary: #4361ee;     /* Blu profondo */
    --accent: #f72585;        /* Magenta (errori) */
    --gold: #ffd700;          /* Oro */
    --dark-bg: #0b1120;       /* Sfondo scuro */
    --card-bg: rgba(30, 41, 59, 0.7); /* Vetro */
    --text-main: #f8fafc;
    --text-muted: #94a3b8;
    --success: #00b894;       /* Verde Eco */
    --warning: #f1c40f;
}

/* BASE */
body {
    font-family: 'Segoe UI', sans-serif;
    background: radial-gradient(circle at top, #111827, var(--dark-bg));
    color: var(--text-main);
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
}

/* CARD */
.auth-wrapper {
    background: var(--card-bg);
    backdrop-filter: blur(14px);
    border-radius: 16px;
    padding: 2.2rem;
    width: 100%;
    max-width: 420px;
    box-shadow: 0 0 40px rgba(0, 242, 255, 0.15);
    border: 1px solid rgba(255,255,255,0.08);
}

/* HEADER */
.auth-header {
    text-align: center;
    margin-bottom: 2rem;
}

.auth-header h2 {
    color: var(--primary);
    margin-bottom: 0.4rem;
}

.auth-header p {
    color: var(--text-muted);
}

/* FORM */
.form-group {
    margin-bottom: 1.3rem;
}

label {
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-bottom: 0.4rem;
    display: block;
}

input {
    width: 100%;
    padding: 12px;
    background: rgba(15, 23, 42, 0.9);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px;
    color: var(--text-main);
    outline: none;
}

input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 8px rgba(0,242,255,0.6);
}

/* BUTTON */
.auth-btn {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 10px;
    font-weight: bold;
    cursor: pointer;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: #020617;
    transition: transform 0.2s, box-shadow 0.2s;
}

.auth-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 0 20px rgba(0,242,255,0.6);
}

/* TOGGLE */
.auth-toggle {
    margin-top: 1.6rem;
    text-align: center;
    font-size: 0.9rem;
    color: var(--text-muted);
}

.auth-toggle a {
    color: var(--primary);
    font-weight: bold;
    cursor: pointer;
    text-decoration: none;
}

.auth-toggle a:hover {
    text-decoration: underline;
}

/* MESSAGGI */
.success-msg {
    background: rgba(0, 184, 148, 0.15);
    border: 1px solid var(--success);
    color: var(--success);
    padding: 12px;
    border-radius: 10px;
    margin-bottom: 15px;
    text-align: center;
}

.error-msg {
    background: rgba(247, 37, 133, 0.15);
    border: 1px solid var(--accent);
    color: var(--accent);
    padding: 12px;
    border-radius: 10px;
    margin-bottom: 15px;
    text-align: center;
}

</style>

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
                <p style="color: var(--text-muted)">Benvenuto in GreenSpark</p>
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
            
            <div class="auth-toggle">
                Non hai un account? <a onclick="toggleMode('register')">Registrati ora</a>
            </div>
        </div>

        <!-- CONTAINER REGISTRAZIONE -->
        <div id="register-container" style="display: <?php echo $mode === 'register' ? 'block' : 'none'; ?>;">
            <div class="auth-header"><h2>Crea Account</h2></div>
            
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