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
    $user_id = $_SESSION['user_id'];
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

    if (isset($_POST['submit_topic'])) {
        $title = htmlspecialchars($_POST['topic-title']);
        $category = $_POST['topic-category'];
        $body = htmlspecialchars($_POST['topic-body']);

        $query_insert = "INSERT INTO forum_db (user_id, title, category, body) VALUES ($1, $2, $3, $4)";
        pg_query_params($db, $query_insert, array($user_id, $title, $category, $body));
        
        header("Location: community.php");
        exit();
    }

    $query_posts = "SELECT t.*, u.username AS author 
                    FROM forum_db t 
                    JOIN users u ON t.user_id = u.id 
                    ORDER BY t.created_at DESC";

    $result_posts = pg_query($db, $query_posts);

    // Se la query fallisce, stampa l'errore per capire cosa non va
    if (!$result_posts) {
        die("Errore nel database: " . pg_last_error($db));
    }
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <title>Community+ | beGreen</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Community Forum per utenti beGreen">
        <link rel="icon" type="image/png" href="../src_image/favicon/favicon-96x96.png" sizes="96x96" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="../css/style.css">
        
        <style>
            :root {
                --primary-cyan: #00f2ff;
                --text-muted: #94a3b8;
                --card-bg-dark: rgba(30, 41, 59, 0.8);
            }
            
            main {
                background-color: #0b1120;
                color: #f8fafc;
                min-height: 80vh;
                padding-top: 2rem;
            }

            .forum-container-custom {
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 2rem;
            }

            .forum-controls {
                text-align: right;
                margin-bottom: 2rem;
            }

            .auth-btn-custom {
                background: linear-gradient(135deg, #4361ee, var(--primary-cyan));
                border: none;
                padding: 0.5rem 1.5rem;
                border-radius: 50px;
                color: white;
                font-weight: bold;
                cursor: pointer;
                transition: transform 0.2s;
            }
            .auth-btn-custom:hover { transform: translateY(-2px); }

            /* New Topic Form */
            #new-topic-form-container {
                max-width: 900px;
                margin: 0 auto 2rem auto;
                background: var(--card-bg-dark);
                border: 1px solid var(--primary-cyan);
                border-radius: 12px;
                padding: 2rem;
                display: none; /* Nascosto di default */
            }

            .form-group { margin-bottom: 1.2rem; }
            .form-group label { display: block; margin-bottom: 0.5rem; color: #ccc; }
            
            .form-group input, .form-group select, .form-group textarea {
                width: 100%;
                padding: 12px;
                background: rgba(0, 0, 0, 0.3);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 8px;
                color: white;
                font-family: inherit;
            }

            /* Forum Cards */
            .forum-grid {
                display: grid;
                gap: 1.5rem;
                max-width: 900px;
                margin: 0 auto;
            }

            .forum-card {
                background: var(--card-bg-dark);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 12px;
                padding: 1.5rem;
                transition: border-color 0.3s;
            }
            .forum-card:hover { border-color: var(--primary-cyan); }
            
            .error-msg { color: #f72585; font-size: 0.85rem; display: none; margin-top: 5px; }
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
                        <li><a href="community.php" class="nav-plus active"> Community+ </a></li>
                    <?php endif; ?>

                    <?php if ($is_admin): ?>
                        <li><a href="admin.html" class="nav-admin"> Admin Panel</a></li>
                    <?php endif; ?>
                </ul>

                <div class="log-container">
                    <?php if (!$is_logged): ?>
                        <a href="log.php?redirect=community.php" class="log-btn">
                            <img src="../src_image/images/white_user.png" alt="Logo user" class="logo-user"> Accedi 
                        </a>
                    <?php else: ?>
                        <div class="user-display">
                            <a href="log.php?redirect=community.php" class="user-info"> 
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

        <main>
            <section class="forum-container-custom">
                <h2 class="plus-text">Community+</h2>
                
                <div class="forum-controls">
                    <?php if ($is_logged): ?>
                        <button id="btn-new-topic" class="auth-btn-custom" onclick="toggleTopicForm()">
                            <i class="fa-solid fa-plus"></i> Inizia Nuova Discussione
                        </button>
                    <?php endif; ?> </div>

                <div id="new-topic-form-container">
                    <div class="new-topic-header">
                        <h3 style="color:var(--primary-cyan)">Crea Nuova Discussione</h3>
                        <p style="color:var(--text-muted); font-size:0.9rem;">Condividi la tua esperienza con la <b style='color:#ffd700;'>Community+</b>.</p>
                    </div>
                    
                    <form id="new-topic-form" method="POST" action="community.php">
                        <div class="form-group">
                            <label>Titolo Discussione</label>
                            <input type="text" name="topic-title" id="topic-title" placeholder="Es: Problema ricarica Ionity..." required minlength="5">
                        </div>

                        <div class="form-group">
                            <label>Categoria</label>
                            <select name="topic-category" id="topic-category">
                                <option value="Generale">Generale</option>
                                <option value="Ricarica">Ricarica & Colonnine</option>
                                <option value="Veicoli">Veicoli & Recensioni</option>
                                <option value="News">News & Eventi</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Messaggio</label>
                            <textarea name="topic-body" id="topic-body" placeholder="Scrivi qui il tuo messaggio..." required rows="4" minlength="10"></textarea>
                        </div>

                        <div style="display:flex; justify-content:flex-end; gap:10px;">
                            <button type="button" class="auth-btn-custom" style="background:transparent; border:1px solid white;" onclick="toggleTopicForm()">Annulla</button>
                            <button type="submit" name="submit_topic" class="auth-btn-custom">Pubblica</button>
                        </div>
                    </form>
                </div>

                <!-- Visualizzazione dei post dal database -->
                <div class="forum-grid" id="forum-container">
                    <?php while ($post = pg_fetch_assoc($result_posts)): ?>
                        <?php $date_formatted = date("d/m/Y H:i", strtotime($post['created_at'])); ?>
                        
                        <div class="forum-card">
                            <h3 style="color:#00f2ff; margin-bottom:5px;">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </h3>
                            <div style="font-size:0.85rem; color:#94a3b8; margin-bottom:10px;">
                                <span style="background:rgba(255,255,255,0.1); padding:2px 8px; border-radius:4px;">
                                    <?php echo htmlspecialchars($post['category']); ?>
                                </span>
                                • di <?php echo htmlspecialchars($post['author']); ?> • <?php echo $date_formatted; ?>
                            </div>
                            <p><?php echo nl2br(htmlspecialchars($post['body'])); ?></p>
                        </div>
                    <?php endwhile; ?>

                    <?php if (pg_num_rows($result_posts) == 0): ?>
                        <p style="text-align:center; color:var(--text-muted);">Ancora nessuna discussione. Inizia tu!</p>
                    <?php endif; ?>
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

        <script>
            function toggleTopicForm() {
                const form = document.getElementById('new-topic-form-container');
                form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
            }
        </script>
    </body>
</html>