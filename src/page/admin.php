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

// Gestione eliminazione elementi da db
if (isset($_GET['delete_user'])) {
    $id = (int)$_GET['delete_user'];
    pg_query_params($db, "DELETE FROM users WHERE id = $1 AND role != 'admin'", array($id));
    header("Location: admin.php?status=deleted");
    exit();
}

if (isset($_GET['delete_post'])) {
    $id = (int)$_GET['delete_post'];
    pg_query_params($db, "DELETE FROM forum_db WHERE id = $1", array($id));
    header("Location: admin.php?status=deleted");
    exit();
}

if (isset($_GET['delete_vehicle'])) {
    $id = (int)$_GET['delete_vehicle'];
    pg_query_params($db, "DELETE FROM vehicles WHERE id = $1", array($id));
    header("Location: admin.php?status=deleted");
    exit();
}

// Gestione aggiunta auto al db
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_car'])) {
    $brand = htmlspecialchars($_POST['brand']);
    $model = htmlspecialchars($_POST['model']);
    $battery = (float)$_POST['battery_capacity'];
    $charge = (float)$_POST['max_charge_power'];
    $category = htmlspecialchars($_POST['category']);
    $price = (float)$_POST['price'];
    $image_url = htmlspecialchars($_POST['image_url']); // Nuovo campo

    $query = "INSERT INTO vehicles (brand, model, battery_capacity, max_charge_power, category, price, image_url) 
              VALUES ($1, $2, $3, $4, $5, $6, $7)";
    
    $result = pg_query_params($db, $query, array($brand, $model, $battery, $charge, $category, $price, $image_url));
    
    if ($result) {
        header("Location: admin.php?status=added");
    } else {
        $error = pg_last_error($db);
        header("Location: admin.php?status=error&msg=" . urlencode($error));
    }
    exit();
}

$res_users = pg_query($db, "SELECT * FROM users ORDER BY id ASC");
$res_forum = pg_query($db, "SELECT f.*, u.username as author FROM forum_db f JOIN users u ON f.user_id = u.id ORDER BY created_at DESC");
$res_cars = pg_query($db, "SELECT * FROM vehicles ORDER BY brand ASC");
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <title> Admin | beGreen</title>
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
                    <li><a href="home.php" class="nav-item">Home</a></li>
                    <li><a href="map.php" class="nav-item">Charge Map</a></li>
                    <li><a href="autosalone.php" class="nav-item">Autosalone</a></li>

                    <?php if ($can_access_plus): ?>
                        <li><a href="community.php" class="nav-plus"> Community+ </a></li>
                    <?php endif; ?>

                    <?php if ($is_admin): ?>
                        <li><a href="admin.php" class="nav-admin active"> Admin Panel</a></li>
                    <?php endif; ?>
                </ul>

                <div class="log-container">
                    <?php if (!$is_logged): ?>
                        <a href="log.php?redirect=admin.php" class="log-btn">
                            <img src="../src_image/images/white_user.png" alt="Logo user" class="logo-user"> Accedi 
                        </a>
                    <?php else: ?>
                        <div class="user-display">
                            <a href="profile.php?redirect=admin.php" class="user-info">
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

    <div class="admin-container">
        <h1 class="dashboard-title"><i class="fa-solid fa-gears"></i> Pannello di Controllo</h1>
        
        <div class="admin-card">
            <h2><i class="fa-solid fa-users"></i> Gestione Database Utenti</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Ruolo</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($u = pg_fetch_assoc($res_users)): ?>
                        <tr>
                            <td><?php echo $u['id']; ?></td>
                            <td><?php echo htmlspecialchars($u['username']); ?></td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td>
                                <span class="role-badge <?php echo $u['role']; ?>">
                                    <?php echo strtoupper($u['role']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($u['role'] !== 'admin'): ?>
                                    <a href="?delete_user=<?php echo $u['id']; ?>" 
                                       class="btn-delete-action"
                                       onclick="return confirm('Eliminare l\'utente?')">
                                        <i class="fa-solid fa-user-minus"></i> ELIMINA
                                    </a>
                                <?php else: ?>
                                    <small style="color:#64748b; font-style:italic;">Protetto</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-card">
            <h2><i class="fa-solid fa-plus-circle"></i> Inserimento veicolo nel Database</h2>
            <form id="add-vehicle-form" method="POST" novalidate>
                <div class="table-responsive">
                    <table class="form-table">
                        <thead>
                            <tr>
                                <th>Brand</th>
                                <th>Modello</th>
                                <th>kWh</th>
                                <th>kW (DC)</th>
                                <th>Categoria</th> <th>Prezzo (€)</th>
                                <th>URL Immagine</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" name="brand" class="admin-input" required placeholder="es. Tesla"></td>
                                <td><input type="text" name="model" class="admin-input" required placeholder="es. Model 3"></td>
                                <td><input type="number" step="0.1" name="battery_capacity" class="admin-input" placeholder="es. 50.0" required></td>
                                <td><input type="number" step="0.1" name="max_charge_power" class="admin-input" placeholder="es. 150.0" required></td>
                                
                                <td>
                                    <select name="category" class="admin-input" required>
                                        <option value="economy">Economy</option>
                                        <option value="normal">Normal</option>
                                        <option value="luxury">Luxury</option>
                                    </select>
                                </td>
                                
                                <td><input type="number" step="0.01" name="price" class="admin-input" placeholder="es. 35000.00" required></td>
                                <td><input type="url" name="image_url" class="admin-input" placeholder="https://..." required></td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div style="text-align: right; margin-top: 15px;">
                        <button type="submit" name="add_car" class="log-btn">
                            <i class="fa-solid fa-plus"></i> Inserisci veicolo
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="admin-card">
            <h2><i class="fa-solid fa-car-side"></i> Catalogo Veicoli</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Brand</th>
                            <th>Modello</th>
                            <th>Batteria</th>
                            <th>Ricarica</th>
                            <th>Categoria</th> <th>Prezzo</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($car = pg_fetch_assoc($res_cars)): ?>
                        <tr>
                            <td><?php echo $car['id']; ?></td>
                            <td><?php echo htmlspecialchars($car['brand']); ?></td>
                            <td><?php echo htmlspecialchars($car['model']); ?></td>
                            <td><?php echo $car['battery_capacity']; ?> kWh</td>
                            <td><?php echo $car['max_charge_power']; ?> kW</td>
                            
                            <td>
                                <span class="role-badge" style="background-color: #e2e8f0; color: #475569;">
                                    <?php echo ucfirst(htmlspecialchars($car['category'])); ?>
                                </span>
                            </td>

                            <td>€ <?php echo number_format($car['price'], 2, ',', '.'); ?></td>
                            <td>
                                <a href="?delete_vehicle=<?php echo $car['id']; ?>" 
                                   class="btn-delete-action" 
                                   onclick="return confirm('Eliminare veicolo?')">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-card">
            <h2><i class="fa-solid fa-comments"></i> Gestione Community+</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titolo Discussione</th>
                            <th>Categoria</th>
                            <th>Autore</th>
                            <th>Data</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($p = pg_fetch_assoc($res_forum)): ?>
                        <tr>
                            <td><?php echo $p['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($p['title']); ?></strong></td>
                            <td><?php echo htmlspecialchars($p['category']); ?></td>
                            <td><?php echo htmlspecialchars($p['author']); ?></td>
                            <td><?php echo date("d/m/y H:i", strtotime($p['created_at'])); ?></td>
                            <td style="text-align: center;">
                                <a href="?delete_post=<?php echo $p['id']; ?>" 
                                   class="btn-delete-action"
                                   onclick="return confirm('Rimuovere il post?')">
                                   <i class="fa-solid fa-trash-can"></i> RIMUOVI
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const vehicleForm = document.getElementById('add-vehicle-form');

        if (vehicleForm) {
            vehicleForm.addEventListener("submit", function(e) {
                const brand = vehicleForm.querySelector('input[name="brand"]').value.trim();
                const model = vehicleForm.querySelector('input[name="model"]').value.trim();
                const battery = parseFloat(vehicleForm.querySelector('input[name="battery_capacity"]').value);
                const power = parseFloat(vehicleForm.querySelector('input[name="max_charge_power"]').value);
                const price = parseFloat(vehicleForm.querySelector('input[name="price"]').value);
                const imageUrl = vehicleForm.querySelector('input[name="image_url"]').value.trim();
                let errors = [];
                if (brand === "" || model === "") {
                    errors.push("Marca e Modello sono obbligatori.");
                }
                if (isNaN(battery) || battery <= 0) {
                    errors.push("La capacità della batteria (kWh) deve essere maggiore di 0.");
                }
                if (isNaN(power) || power <= 0) {
                    errors.push("La potenza di ricarica (kW) deve essere maggiore di 0.");
                }
                if (isNaN(price) || price <= 0) {
                    errors.push("Il prezzo deve essere maggiore di 0.");
                }
                const urlPattern = /^(https?:\/\/[^\s]+)/;
                if (!urlPattern.test(imageUrl)) {
                    errors.push("Inserisci un URL valido per l'immagine (deve iniziare con http:// o https://).");
                }
                if (errors.length > 0) {
                    e.preventDefault();
                    alert("Impossibile inserire il veicolo:\n- " + errors.join("\n- "));
                }
            });
        }
    });
    </script>
</body>
</html>