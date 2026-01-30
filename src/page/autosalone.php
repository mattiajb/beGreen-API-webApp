<?php
session_start();
require_once 'db.php';

// --- GESTIONE SESSIONE E RUOLI (Logica standard di home.php) ---
$is_logged = false;
$username = "Ospite";
$user_role = "guest"; 
$user_label = "Visitatore";

// Variabili per lo stile dinamico del badge
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

// --- PERMESSI ---
$can_access_plus = ($user_role === 'plus' || $user_role === 'admin');
$is_admin = ($user_role === 'admin');

// *** FIX ERRORE: Definizione esplicita della variabile mancante ***
// La logica è: se sei loggato (qualsiasi ruolo), puoi richiedere il preventivo.
$can_request_quote = $is_logged;

// --- LOGICA AUTOSALONE (Database e Filtri) ---

// Recupero categoria dal filtro GET
$filter_category = isset($_GET['category']) ? $_GET['category'] : 'all';
$valid_categories = ['economy', 'normal', 'luxury'];

// Costruzione Query Dinamica
$sql = "SELECT * FROM vehicles";
$params = [];

if (in_array($filter_category, $valid_categories)) {
    $sql .= " WHERE category = $1";
    $params[] = $filter_category;
}

$sql .= " ORDER BY price ASC";

// Esecuzione Query PostgreSQL
$result = pg_query_params($db, $sql, $params);
$vehicles = pg_fetch_all($result);
if (!$vehicles) $vehicles = []; // Evita errori se array vuoto
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
        <style>
            /* Layout Generale a Griglia: 2/3 Catalogo, 1/3 Carrello */
.autosalone-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    padding: 20px;
    max-width: 1400px;
    margin: 0 auto;
    min-height: 80vh;
}

/* --- SEZIONE SINISTRA: CATALOGO (2/3) --- */
.catalog-section {
    flex: 3; /* Prende 2 parti dello spazio */
    min-width: 600px;
}

/* Barra Filtri */
.filter-bar {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    background: #1e1e1e;
    padding: 15px;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.3);
}

.filter-btn {
    text-decoration: none;
    color: #fff;
    padding: 10px 20px;
    border-radius: 20px;
    border: 1px solid #00E5FF;
    transition: all 0.3s ease;
    font-weight: bold;
}

.filter-btn:hover, .filter-btn.active {
    background: #00E5FF;
    color: #000;
    box-shadow: 0 0 10px #00E5FF;
}

/* Griglia delle Auto */
.cars-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
}

.car-card {
    background: #2a2a2a;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0,0,0,0.4);
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: grab;
    border: 1px solid #444;
}

.car-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 229, 255, 0.2);
    border-color: #00E5FF;
}

.car-card:active {
    cursor: grabbing;
}

.car-img {
    width: 100%;
    height: 160px;
    object-fit: cover;
}

.car-info {
    padding: 15px;
}

.car-info h3 {
    margin: 0 0 5px 0;
    color: #fff;
    font-size: 1.2rem;
}

.car-info p {
    color: #ccc;
    font-size: 0.9rem;
    margin: 5px 0;
}

.car-price {
    color: #00E5FF;
    font-weight: bold;
    font-size: 1.1rem;
    margin-top: 10px;
}

/* --- SEZIONE DESTRA: CARRELLO (1/3) --- */
.cart-section {
    flex: 1; /* Prende 1 parte dello spazio */
    min-width: 300px;
    position: relative;
}

.sticky-cart {
    background: #1e1e1e;
    border: 2px dashed #444;
    border-radius: 12px;
    padding: 20px;
    position: sticky;
    top: 100px; /* Rimane fisso mentre scrolli */
    min-height: 400px;
    display: flex;
    flex-direction: column;
    transition: border-color 0.3s, background-color 0.3s;
}

.sticky-cart.drag-over {
    border-color: #00E5FF;
    background-color: rgba(0, 229, 255, 0.05);
}

.cart-header h2 {
    color: #fff;
    border-bottom: 1px solid #444;
    padding-bottom: 10px;
    margin-top: 0;
}

.cart-items {
    flex-grow: 1;
    list-style: none;
    padding: 0;
    margin: 20px 0;
    overflow-y: auto;
    max-height: 500px;
}

.cart-item {
    background: #333;
    margin-bottom: 10px;
    padding: 10px;
    border-radius: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    animation: fadeIn 0.3s ease;
}

.cart-item img {
    width: 50px;
    height: 35px;
    object-fit: cover;
    border-radius: 4px;
    margin-right: 10px;
}

.cart-item-details {
    flex-grow: 1;
    color: #fff;
    font-size: 0.9rem;
}

.remove-btn {
    background: transparent;
    border: none;
    color: #ff4444;
    cursor: pointer;
    font-size: 1.2rem;
}

.remove-btn:hover {
    color: #ff0000;
}

.cart-footer {
    border-top: 1px solid #444;
    padding-top: 15px;
    text-align: center;
}

.cart-total {
    color: #fff;
    font-size: 1.2rem;
    margin-bottom: 15px;
    display: block;
}

.action-btn {
    display: inline-block;
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 6px;
    font-size: 1rem;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s;
    text-decoration: none;
}

.btn-login {
    background: #444;
    color: #fff;
}
.btn-login:hover { background: #555; }

.btn-quote {
    background: #00E5FF;
    color: #000;
}
.btn-quote:hover { background: #00b8cc; }

.empty-msg {
    color: #777;
    text-align: center;
    margin-top: 50px;
    font-style: italic;
}

/* --- MODAL / POPUP --- */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    backdrop-filter: blur(5px);
}

.modal-overlay.active {
    opacity: 1;
    visibility: visible;
}

.modal-content {
    background: #1e1e1e;
    padding: 30px;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 0 20px rgba(0, 229, 255, 0.3);
    position: relative;
    border: 1px solid #00E5FF;
    color: #fff;
}

.close-modal {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 1.5rem;
    color: #ccc;
    cursor: pointer;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    color: #00E5FF;
}

.form-group input, .form-group textarea {
    width: 100%;
    padding: 10px;
    background: #2a2a2a;
    border: 1px solid #444;
    color: #fff;
    border-radius: 4px;
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Responsive Mobile */
@media (max-width: 900px) {
    .autosalone-container {
        flex-direction: column;
    }
    
    .catalog-section, .cart-section {
        min-width: 100%;
        flex: auto;
    }
    
    .sticky-cart {
        position: static;
        min-height: auto;
    }
}
        </style>
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

        <!-- AUTOSALONE -->
         <main>
        <div class="autosalone-container">
            
            <!-- COLONNA SINISTRA: CATALOGO (2/3) -->
            <section class="catalog-section">
                <h1>Il nostro <span class="highlight">Autosalone</span></h1>
                
                <!-- Barra Filtri -->
                <div class="filter-bar">
                    <a href="autosalone.php" class="filter-btn <?php echo $filter_category == 'all' ? 'active' : ''; ?>">Tutte</a>
                    <a href="autosalone.php?category=economy" class="filter-btn <?php echo $filter_category == 'economy' ? 'active' : ''; ?>">Economy</a>
                    <a href="autosalone.php?category=normal" class="filter-btn <?php echo $filter_category == 'normal' ? 'active' : ''; ?>">Normal</a>
                    <a href="autosalone.php?category=luxury" class="filter-btn <?php echo $filter_category == 'luxury' ? 'active' : ''; ?>">Luxury</a>
                </div>

                <!-- Griglia Auto -->
                <div class="cars-grid">
                    <?php if (count($vehicles) > 0): ?>
                        <?php foreach ($vehicles as $car): ?>
                            <?php 
                                // Preparazione dati JSON per Drag & Drop
                                $carData = json_encode([
                                    'id' => $car['id'],
                                    'brand' => $car['brand'],
                                    'model' => $car['model'],
                                    'price' => $car['price'],
                                    'image' => $car['image_url']
                                ]);
                            ?>
                            <div class="car-card" draggable="true" data-car='<?php echo htmlspecialchars($carData, ENT_QUOTES, 'UTF-8'); ?>'>
                                <img src="<?php echo htmlspecialchars($car['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($car['model']); ?>" 
                                     class="car-img"
                                     onerror="this.src='https://placehold.co/600x400/1e1e1e/FFF?text=Auto+Elettrica'">
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
                        <p style="color: #fff;">Nessuna auto trovata in questa categoria.</p>
                    <?php endif; ?>
                </div>
            </section>

            <!-- COLONNA DESTRA: CARRELLO (1/3) -->
            <section class="cart-section">
                <div class="sticky-cart" id="cart-zone">
                    <div class="cart-header">
                        <h2><i class="fa-solid fa-cart-shopping"></i> Preventivo</h2>
                        <p style="color:#aaa; font-size:0.9rem;">Trascina qui le auto</p>
                    </div>

                    <ul class="cart-items" id="cart-items-list">
                        <!-- Qui vengono inseriti gli elementi via JS -->
                        <li id="empty-msg" class="empty-msg">Il carrello è vuoto</li>
                    </ul>

                    <div class="cart-footer">
                        <span class="cart-total">Totale Stimato: <span id="total-price" class="highlight">€ 0,00</span></span>
                        
                        <?php if ($can_request_quote): ?>
                            <button id="btn-request-quote" class="action-btn btn-quote">
                                Richiedi Preventivo
                            </button>
                        <?php else: ?>
                            <a href="log.php" class="action-btn btn-login">
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
            <h2>Richiesta Preventivo</h2>
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

    <!-- JS Logic -->
    <script src="../js/autosalone.js"></script>

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