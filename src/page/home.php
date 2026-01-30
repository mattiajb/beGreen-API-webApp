<?php
session_start();
require_once 'db.php';

$is_logged = false;
$username = "Ospite";
$user_role = "guest"; 
$user_label = "Visitatore";

// Variabili per lo stile dinamico
$badge_class = ""; 

if (isset($_SESSION['user_id'])) {
    $is_logged = true;
    $username = htmlspecialchars($_SESSION['username']); 
    $user_role = $_SESSION['role']; 
    
    switch ($user_role) {
        case 'admin':
            $user_label = "ADMIN";
            $badge_class = "type-admin"; // Classe CSS per Admin
            break;
            
        case 'plus':
            $user_label = "UTENTE PLUS+";
            $badge_class = "type-plus"; // Classe CSS per Plus
        break;
            
        default: // User standard
            $user_label = "STANDARD";
            $badge_class = "type-standard"; // Classe CSS per Standard
            break;
    }
}
$can_access_plus = ($user_role === 'plus' || $user_role === 'admin');
$is_admin = ($user_role === 'admin');
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <title>Home | beGreen</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Web App progettata per rispondere alle sfide emergenti dell'energia rinnovabile nel settore delle auto elettriche">
        <link rel="icon" type="image/png" href="../src_image/favicon/favicon-96x96.png" sizes="96x96" />
        <link rel="icon" type="image/svg+xml" href="../src_image/favicon/favicon.svg" />
        <link rel="shortcut icon" href="../src_image/favicon/favicon.ico"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="manifest" href="../src_image/favicon/site.webmanifest"/>
        <link rel="stylesheet" href="../css/style.css">
    </head>
    <body>
        <header class="site-header">
            <nav class="navbar">
                <a href="home.php" class="logo"><img src="../src_image/images/beGreen_cyan.png" alt="Logo beGreen" class="logo-img"> beGreen </a>
                <ul class="nav-links">
                    <li><a href="home.php" class="nav-item active">Home</a></li>
                    <li><a href="map.php" class="nav-item">Charge Map</a></li>
                    <li><a href="autosalone.php" class="nav-item">Autosalone</a></li>

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
                                <span class="user-name">
                                    <i class="fa-solid fa-circle-user"></i> 
                                    <?php echo $username; ?>
                                </span>
                                
                                <span class="user-type <?php echo $badge_class; ?>">
                                    <?php echo $user_label; ?>
                                </span>
                            </a>
                            <a href="logout.php" class="logout-btn">
                                Esci
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </nav>
        </header>

        <main>
            <section class="theme-section">
            <div class="main-theme">
                <div>
                        <h1>Il futuro della mobilità è <span class="highlight">Green</span></h1>
                    <p class="theme">
                        Benvenuto in <strong>beGreen</strong>, la piattaforma definitiva per la gestione della tua auto elettrica. 
                        Monitora i consumi, trova le colonnine di ricarica più vicine e unisciti a una community 
                        di guidatori consapevoli. Il pianeta ti ringrazia, il tuo portafoglio anche.
                    </p>
                </div>
            </div>
            <div class="iframe-class">
                <div class="relation-iframe">
                    <iframe src="../external_file/19_routing.pdf" title="Relazione Tecnologie Web"><p>Relazione Tecnologie Web</p></iframe>
                </div>
            </div>
            </section>

            <section class="team-container">
                <h2 class="team-title">beGreen <span class="highlight">Team</span></h2>
                <div class="team-grid">
                    <div class="team-card">
                    <a href="https://github.com/mattiajb">
                        <div class="avatar-glow">
                            <img src="../src_image/images/beGreen_cyan.png" alt="Mattia Bavaro">
                        </div>
                        <h3>Mattia Gerardo Bavaro</h3>
                    </a>
                    </div>
                    <div class="team-card">
                    <a href="https://github.com/melem878">
                        <div class="avatar-glow">
                            <img src="../src_image/images/beGreen_cyan.png" alt="Mario Mele">
                        </div>
                        <h3>Mario Mele</h3>
                    </a>
                    </div>
                    <div class="team-card">
                    <a href="https://github.com/crivellini">
                        <div class="avatar-glow">
                            <img src="../src_image/images/beGreen_cyan.png" alt="Cosimo Rivellini">
                        </div>
                        <h3>Cosimo Rivellini</h3>
                    </a>
                    </div>
                </div>
            </section>
        </main>

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
                        <li><a href="autosalone.html">Autosalone</a></li>
                        <?php if ($can_access_plus): ?>
                            <li><a href="community.html">Community Forum</a></li>
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
