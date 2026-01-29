<?php
session_start();
require_once 'db.php';
$is_logged = false;
$username = "Ospite";
$user_role = "guest"; // Ruoli possibili: guest, user, plus, admin
$user_label = "Visitatore";

if (isset($_SESSION['user_id'])) {
    $is_logged = true;
    $username = htmlspecialchars($_SESSION['username']); // Protezione XSS
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
}
$can_access_plus = ($user_role === 'plus' || $user_role === 'admin');
$is_admin = ($user_role === 'admin');
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <title>Autosalone | beGreen</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/svg+xml" href="../src_image/favicon/favicon.svg" />
        <link rel="shortcut icon" href="../src_image/favicon/favicon.ico"/>
        <link rel="manifest" href="../src_image/favicon/site.webmanifest"/>
        <link rel="stylesheet" href="../css/style.css"> 
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </head>
    <body>
        <!-- NAVBAR -->
        <header class="site-header">
            <nav class="navbar">
                <a href="home.php" class="logo"><img src="../src_image/images/beGreen_cyan.png" alt="Logo beGreen" class="logo-img"> beGreen </a>
                <ul class="nav-links">
                    <li><a href="home.php" class="nav-item">Home</a></li>
                    <li><a href="map.php" class="nav-item">Charge Map</a></li>
                    <li><a href="autosalone.php" class="nav-item active">Autosalone</a></li>

                    <?php if ($can_access_plus): ?>
                        <li><a href="community.php" class="nav-plus"> Community+ </a></li>
                    <?php endif; ?>

                    <?php if ($is_admin): ?>
                        <li><a href="admin.html" class="nav-admin"> Admin Panel</a></li>
                    <?php endif; ?>
                </ul>

                <div class="log-container">
                    <?php if (!$is_logged): ?>
                        <a href="log.php" class="log-btn">
                            <img src="../src_image/images/white_user.png" alt="Logo user" class="logo-user"> Accedi 
                        </a>
                    <?php else: ?>
                        <div class="user-display">
                            <a href="profile.php" class="user-info"> 
                                <span class="user-name"><?php echo $username; ?></span>
                                <span class="user-type"><?php echo $user_label; ?></span>
                            </a>
                            <a href="logout.php" class="logout-btn-link">
                                <button class="logout-btn"> Esci </button>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </nav>
        </header>

        <!-- AUTOSALONE -->

        <footer>
            <div class="footer-container">
                <div class="footer-col">
                    <h3><img src="../src_image/images/beGreen_cyan.png" alt="Logo beGreen" style="height: 40px; width: auto; margin-bottom: -12px;"> beGreen </h3>
                    <p style="margin-bottom: 1rem; font-size: 0.9rem; line-height: 1.6;"> Il punto di riferimento per la mobilità elettrica. </p>
                    <div class="social-icons">
                        <a href="https://github.com/mattiajb/beGreen-API-webApp.git" class="social-btn"><i class="fa-brands fa-github"></i></a>
                        <a href="https://www.linkedin.com/top-content/" class="social-btn"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="https://www.unisa.it/" class="social-btn"><i class="fa-solid fa-building-columns"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h3>Link Rapidi</h3>
                    <ul class="footer-links">
                        <li><a href="home.php">Home</a></li>
                        <li><a href="map.php">Charge Map</a></li>
                        <li><a href="autosalone.php">Autosalone</a></li>
                        <?php if ($can_access_plus): ?>
                            <li><a href="community.php">Community Forum</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Il Team</h3>
                    <ul class="footer-links">
                        <li class="founder">Mattia Gerardo Bavaro</li>
                        <li class="founder">Mario Mele</li>
                        <li class="founder">Cosimo Rivellini</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                &copy; 2026 Gruppo beGreen 04 - Progetto Tecnologie Web Unisa 2025/26
            </div>
        </footer>
    </body>
</html>