<?php
session_start();
require_once 'db.php';

if ($db) {
    $query = "SELECT id, brand, model, battery_capacity, max_charge_power FROM vehicles ORDER BY brand ASC";
    $result = pg_query($db, $query);
    $cars = pg_fetch_all($result);
    if (!$cars) $cars = []; // Se vuoto
} else {
    $cars = [];
    $error_db = "Errore connessione database";
}

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
    <title>Charge Map | beGreen</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Web App progettata per rispondere alle sfide emergenti dell'energia rinnovabile nel settore delle auto elettriche">
    <link rel="icon" type="image/png" href="../src_image/favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="../src_image/favicon/favicon.svg" />
    <link rel="shortcut icon" href="../src_image/favicon/favicon.ico"/>
    <link rel="manifest" href="../src_image/favicon/site.webmanifest"/>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="site-header">
            <nav class="navbar">
                <a href="home.php" class="logo"><img src="../src_image/images/beGreen_cyan.png" alt="Logo beGreen" class="logo-img"> beGreen </a>
                <ul class="nav-links">
                    <li><a href="home.php" class="nav-item">Home</a></li>
                    <li><a href="map.php" class="nav-item active">Charge Map</a></li>
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

    <main class="map-layout">
        <section class="map-section">
            <div id="map"></div>
            <div class="info-panel" id="status">In attesa di posizione...</div>
        </section>

        <aside class="calc-section">
            <h2>Calcolo Ricarica</h2>
            <p>Seleziona una colonnina dalla mappa.</p>

            <div class="calc-placeholder">
                <form id="calc-form">
                    <div class="form-group">
                        <label>Seleziona il tuo veicolo:</label>
                        
                        <select id="car-select" required onchange="updateCarSpecs()">
                            <option value="">-- Scegli Auto --</option>
                            <?php foreach($cars as $car): ?>
                                <option 
                                    value="<?php echo $car['id']; ?>" 
                                    data-battery="<?php echo $car['battery_capacity']; ?>"
                                    data-power="<?php echo $car['max_charge_power']; ?>">
                                    <?php echo $car['brand'] . " " . $car['model']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div id="car-specs-display" style="font-size:0.85rem; color:#00aaa0; margin-top:5px; min-height:20px;"></div>
                    </div>

                    <div class="form-group">
                        <label>Batteria Attuale (%):</label>
                        <input type="number" id="battery-current" min="1" max="100" placeholder="Es: 20" required>
                    </div>

                    <div class="form-group">
                        <label>Potenza Colonnina (kW):</label>
                        <input type="number" id="power-input" step="0.1" placeholder="Seleziona dalla mappa" required>
                    </div>

                    <button type="submit" class="calc-btn">Calcola Tempo</button>
                </form>

                <div id="result-box" class="result-box" style="display:none;">
                    <h3>Risultato Stima</h3>
                    <div class="result-content">
                        <div class="text-data">
                            <p>Energia necessaria: <strong id="res-kwh">-</strong> kWh</p>
                            <p>Potenza effettiva: <strong id="res-power">-</strong> kW</p>
                            <p class="time-big" style="font-size: 1.5rem; margin-top: 15px; color: #00aaa0;">
                                Tempo Totale: <strong id="res-time">-</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </main>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="../js/map.js"></script>

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
