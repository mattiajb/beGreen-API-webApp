<?php
session_start();
require_once 'db.php';

$is_logged = false;
$username = "Ospite";
$user_role = "guest"; 
$user_label = "Visitatore";

$badge_class = ""; 

if (isset($_SESSION['user_id'])) {
    $is_logged = true;
    $username = htmlspecialchars($_SESSION['username']); 
    $user_role = $_SESSION['role']; 
    
    switch ($user_role) {
        case 'admin':
            $user_label = "ADMIN";
            $badge_class = "type-admin";
            break;
            
        case 'plus':
            $user_label = "UTENTE PLUS+";
            $badge_class = "type-plus";
        break;
            
        default: 
            $user_label = "STANDARD";
            $badge_class = "type-standard";
            break;
    }
}

$can_access_plus = ($user_role === 'plus' || $user_role === 'admin');
$is_admin = ($user_role === 'admin');

$can_request_quote = $is_logged;

$filter_category = isset($_GET['category']) ? $_GET['category'] : 'all';
$valid_categories = ['economy', 'normal', 'luxury'];

$sql = "SELECT * FROM vehicles";
$params = [];

if (in_array($filter_category, $valid_categories)) {
    $sql .= " WHERE category = $1";
    $params[] = $filter_category;
}

$sql .= " ORDER BY price ASC";

$result = pg_query_params($db, $sql, $params);
$vehicles = pg_fetch_all($result);
if (!$vehicles) $vehicles = [];
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <title> Autosalone | beGreen</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Web App progettata per rispondere alle sfide emergenti dell'energia rinnovabile nel settore delle auto elettriche">
        <link rel="icon" type="image/png" href="../src_image/favicon/favicon-96x96.png" sizes="96x96" />
        <link rel="icon" type="image/svg+xml" href="../src_image/favicon/favicon.svg" />
        <link rel="shortcut icon" href="../src_image/favicon/favicon.ico"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="manifest" href="../src_image/favicon/site.webmanifest"/>
        <link rel="stylesheet" href="../css/style.css">
        <script src="../js/autosalone.js"></script>
    </head>
    <body>
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
                        <li><a href="admin.php" class="nav-admin"> Admin Panel</a></li>
                    <?php endif; ?>
                </ul>

                <div class="log-container">
                    <?php if (!$is_logged): ?>
                        <a href="log.php?redirect=autosalone.php" class="log-btn">
                            <img src="../src_image/images/white_user.png" alt="Logo user" class="logo-user"> Accedi 
                        </a>
                    <?php else: ?>
                        <div class="user-display">
                            <a href="profile.php?redirect=autosalone.php" class="user-info">
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
        <div class="autosalone-container">
            <section class="catalog-section">
                <h1>Il nostro <span class="highlight">Autosalone</span></h1>
                <div class="filter-bar">
                    <a href="autosalone.php" class="filter-btn <?php echo $filter_category == 'all' ? 'active' : ''; ?>">Tutte</a>
                    <a href="autosalone.php?category=economy" class="filter-btn <?php echo $filter_category == 'economy' ? 'active' : ''; ?>">Economy</a>
                    <a href="autosalone.php?category=normal" class="filter-btn <?php echo $filter_category == 'normal' ? 'active' : ''; ?>">Normal</a>
                    <a href="autosalone.php?category=luxury" class="filter-btn <?php echo $filter_category == 'luxury' ? 'active' : ''; ?>">Luxury</a>
                </div>
                <!-- Griglia Auto -->
                <div class="cars-grid">
                            <?php if (!empty($vehicles)): ?>
                            <?php foreach ($vehicles as $car): ?>
                                <div class="car-card" 
                                    draggable="true" 
                                    data-id="<?php echo $car['id']; ?>"
                                    data-brand="<?php echo htmlspecialchars($car['brand']); ?>"
                                    data-model="<?php echo htmlspecialchars($car['model']); ?>"
                                    data-price="<?php echo $car['price']; ?>"
                                    data-image="<?php echo htmlspecialchars($car['image_url']); ?>">
                                    <img src="<?php echo htmlspecialchars($car['image_url']); ?>" 
                                        alt="<?php echo htmlspecialchars($car['model']); ?>" 
                                        class="car-img">
                                    <div class="car-info">
                                        <h3><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h3>
                                        <p><i class="fa-solid fa-battery-full"></i> <?php echo $car['battery_capacity']; ?> kWh</p>
                                        <p><i class="fa-solid fa-bolt"></i> <?php echo $car['max_charge_power']; ?> kW Max</p>
                                        <div class="car-price">
                                            € <?php echo number_format($car['price'], 2, ',', '.'); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="color: var(--text-main);">Nessuna auto trovata in questa categoria.</p>
                        <?php endif; ?>
                    </div>
            </section>

            <section class="cart-section">
                <div class="sticky-cart" id="cart-zone" data-logged="<?php echo $is_logged ? 'true' : 'false'; ?>">
                    
                    <div class="cart-header">
                        <h2><i class="fa-solid fa-cart-shopping"></i> Carrello</h2>
                        <p style="color:#aaa; font-size:0.9rem;">
                            <?php echo $is_logged ? 'Trascina qui le auto' : 'Accedi per richiedere informazioni'; ?>
                        </p>
                    </div>

                    <ul class="cart-items" id="cart-items-list">
                        <li id="empty-msg" class="empty-msg">Il carrello è vuoto</li>
                    </ul>

                    <div class="cart-footer">
                        <span class="cart-total">Totale Stimato: <span id="total-price">€ 0,00</span></span>
                        
                        <?php if ($can_request_quote): ?>
                            <button id="btn-request-quote" class="action-btn btn-quote">
                                Richiedi Informazioni
                            </button>
                        <?php else: ?>
                            <a href="log.php?redirect=autosalone.php" class="action-btn btn-quote">
                                Accedi per richiedere
                            </a>
                        <?php endif; ?>
                    </div>
                    
                </div>
            </section>
        </div>
    </main>
    <div class="modal-overlay" id="quote-modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2>Contattaci</h2>
            <p style="margin-bottom:20px; color:#ccc;">Compila il modulo per ricevere una risposta ufficiale via email.</p>
            
            <form id="quote-form">
                <div class="form-group">
                    <label for="email">La tua Email</label>
                    <input type="email" id="email" name="email" required placeholder="esempio@email.com">
                </div>
                
                <div class="form-group">
                    <label for="message">Note aggiuntive (Opzionale)</label>
                    <textarea id="message" name="message" placeholder="Vorrei informazioni su finanziamenti o permute..."></textarea>
                </div>

                <button type="submit" class="action-btn btn-quote">Conferma Invio</button>
            </form>
        </div>
    </div>
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
                <div class="footer-col">
                <h3>Contattaci</h3>
                <ul class="footer-links">
                    <li><i class="fa-solid fa-envelope" style="color:var(--primary); width:20px;"></i> info@begreen.it</li>
                    <li><i class="fa-solid fa-location-dot" style="color:var(--primary); width:20px;"></i> Università di Salerno</li>
                </ul>
                </div>
            </div>
            <div class="footer-bottom">
                &copy; 2026 Gruppo beGreen 04 - Progetto Tecnologie Web Unisa 2025/26
            </div>
        </footer>
    </body>
</html>