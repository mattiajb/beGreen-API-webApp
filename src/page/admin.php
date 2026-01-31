<?php
session_start();
require_once 'db.php';

// --- 1. CONTROLLO ACCESSO ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: home.php");
    exit("Accesso Negato.");
}

$is_logged = true;
$username = $_SESSION['username'];
$user_role = $_SESSION['role'];
$user_label = "ADMIN";
$is_admin = true;
$can_access_plus = true;

// --- 2. LOGICA CRUD (POST/GET) ---
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

// --- 3. RECUPERO DATI ---
$res_users = pg_query($db, "SELECT * FROM users ORDER BY id ASC");
$res_forum = pg_query($db, "SELECT f.*, u.username as author FROM forum_db f JOIN users u ON f.user_id = u.id ORDER BY created_at DESC");
$res_cars = pg_query($db, "SELECT * FROM vehicles ORDER BY brand ASC");
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel | beGreen</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../src_image/favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 100px auto 40px;
            padding: 0 20px;
        }

        .dashboard-title {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 2rem;
            text-shadow: 0 0 15px rgba(0, 242, 255, 0.3);
        }

        .admin-card {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .admin-card h2 {
            color: var(--primary);
            font-size: 1.5rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Stile Tabelle */
        .table-responsive {
            overflow-x: auto;
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            color: var(--text-main);
        }

        th {
            background: rgba(0, 242, 255, 0.1);
            color: var(--primary);
            text-align: left;
            padding: 12px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 0.95rem;
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        /* Pulsanti Azione */
        .btn-delete {
            color: var(--accent);
            padding: 5px 10px;
            border: 1px solid var(--accent);
            border-radius: 4px;
            transition: all 0.3s;
        }

        .btn-delete:hover {
            background: var(--accent);
            color: white;
            box-shadow: 0 0 10px var(--accent);
        }

        /* Input specifici per Admin */
        .admin-input {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            padding: 8px;
            border-radius: 4px;
            width: 100%;
        }

        .admin-input:focus {
            border-color: var(--primary);
            outline: none;
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
                        <a href="log.php?redirect=home.php" class="log-btn">
                            <img src="../src_image/images/white_user.png" alt="Logo user" class="logo-user"> Accedi 
                        </a>
                    <?php else: ?>
                        <div class="user-display">
                            <a href="log.php?redirect=home.php" class="user-info">
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
        <h1 class="dashboard-title"><i class="fa-solid fa-gears"></i> Dashboard Amministratore</h1>

        <div class="admin-card">
            <h2 ><i class="fa-solid fa-users"></i> Gestione Database Utenti</h2>
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
                            <td><span <?php echo $u['role']; ?>><?php echo strtoupper($u['role']); ?></span></td>
                            <td>
                                <?php if ($u['role'] !== 'admin'): ?>
                                    <a href="?delete_user=<?php echo $u['id']; ?>" 
                                        style="color: #ff4d4d; border: 1px solid #ff4d4d; padding: 4px 12px; border-radius: 6px; text-decoration: none; font-size: 0.8rem; font-weight: bold; transition: all 0.3s;"
                                        onmouseover="this.style.background='#ff4d4d'; this.style.color='white';"
                                        onmouseout="this.style.background='transparent'; this.style.color='#ff4d4d';"
                                        onclick="return confirm('Eliminare l\'utente?')">
                                        <i class="fa-solid fa-user-minus"></i> ELIMINA
                                    </a>
                                <?php else: ?>
                                    <small style="color:#64748b">Protetto</small>
                                <?php endif; ?>
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
                                style="color: #ff4d4d; border: 1px solid #ff4d4d; padding: 4px 12px; border-radius: 6px; text-decoration: none; font-size: 0.8rem; font-weight: bold; transition: all 0.3s;"
                                onmouseover="this.style.background='#ff4d4d'; this.style.color='white';"
                                onmouseout="this.style.background='transparent'; this.style.color='#ff4d4d';"
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

        <div class="admin-card">
            <h2><i class="fa-solid fa-car-side"></i> Catalogo Veicoli</h2>
            <div style="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Brand</th>
                            <th>Modello</th>
                            <th>Batteria</th>
                            <th>Ricarica</th>
                            <th>Prezzo</th>
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
                            <td>€ <?php echo number_format($car['price'], 2, ',', '.'); ?></td>
                            <td>
                                <a href="?delete_vehicle=<?php echo $car['id']; ?>" class="btn-danger" onclick="return confirm('Eliminare veicolo?')">
                                    <i class="fa-solid fa-trash" style="color: #ff4d4d;"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-card">
            <h2><i class="fa-solid fa-plus-circle"></i> Inserimento veicolo nel Database</h2>
            <form method="POST">
                <div style="table-responsive">
                    <table class="form-table">
                        <thead>
                            <tr>
                                <th>Brand</th>
                                <th>Modello</th>
                                <th>kWh</th>
                                <th>kW (DC)</th>
                                <th>Prezzo (€)</th>
                                <th>URL Immagine</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" name="brand" required placeholder="es. Tesla"></td>
                                <td><input type="text" name="model" required placeholder="es. Model 3"></td>
                                <td><input type="number" step="0.1" name="battery_capacity" required></td>
                                <td><input type="number" step="0.1" name="max_charge_power" required></td>
                                <td><input type="number" step="0.01" name="price" required></td>
                                <td><input type="url" name="image_url" placeholder="https://..." required></td>
                            </tr>
                        </tbody>
                    </table>
                    <input type="hidden" name="category" value="economy">
                    <div style="text-align: right; margin-top: 15px;">
                        <button type="submit" name="add_car" class="log-btn">
                            <i class="fa-solid fa-plus"></i> Inserisci veicolo
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>