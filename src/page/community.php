<?php
session_start();
// require_once 'db.php'; // Decommentare se il file database è presente
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

// Opzionale: Reindirizza se non si hanno i permessi (visto che è una feature Plus)
// if (!$can_access_plus) { header("Location: home.php"); exit(); }
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <title>Community | beGreen</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Community Forum per utenti beGreen">
        <link rel="icon" type="image/png" href="../src_image/favicon/favicon-96x96.png" sizes="96x96" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="../css/style.css">
        
        <!-- Stili Inline aggiunti per la sezione Forum (adattati da GreenSpark) -->
        <style>
            :root {
                --primary-cyan: #00f2ff;
                --text-muted: #94a3b8;
                --card-bg-dark: rgba(30, 41, 59, 0.8);
            }
            
            main {
                background-color: #0b1120; /* Sfondo scuro come GreenSpark */
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
                    <li><a href="home.php" class="nav-item active">Home</a></li>
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
                        <a href="log.php" class="log-btn">
                            <img src="../src_image/images/white_user.png" alt="Logo user" class="logo-user"> Accedi 
                        </a>
                    <?php else: ?>
                        <div class="user-display">
                            <a href="profile.html" class="user-info"> 
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
                <h2 style="text-align: center; color:  #00f2ff; margin-bottom: 2rem; padding-top: 1rem;">Community & Discussioni</h2>
                
                <!-- Controls per Nuovo Topic -->
                <div class="forum-controls">
                    <?php if ($is_logged): ?>
                        <button id="btn-new-topic" class="auth-btn-custom" onclick="toggleTopicForm()">
                            <i class="fa-solid fa-plus"></i> Inizia Nuova Discussione
                        </button>
                    <?php else: ?>
                        <p style="color: #94a3b8;">Accedi per partecipare alle discussioni.</p>
                    <?php endif; ?>
                </div>

                <!-- Form Nuovo Topic (Sticky) -->
                <div id="new-topic-form-container">
                    <div class="new-topic-header">
                        <h3 style="color:var(--primary-cyan)">Crea Nuova Discussione</h3>
                        <p style="color:var(--text-muted); font-size:0.9rem;">Condividi la tua esperienza con la community.</p>
                    </div>
                    
                    <form id="new-topic-form" onsubmit="event.preventDefault(); submitNewTopic();">
                        <div class="form-group">
                            <label>Titolo Discussione</label>
                            <input type="text" id="topic-title" placeholder="Es: Problema ricarica Ionity..." required>
                            <div class="error-msg" id="err-topic-title">Titolo troppo breve (min 5 caratteri)</div>
                        </div>

                        <div class="form-group">
                            <label>Categoria</label>
                            <select id="topic-category">
                                <option value="Generale">Generale</option>
                                <option value="Ricarica">Ricarica & Colonnine</option>
                                <option value="Veicoli">Veicoli & Recensioni</option>
                                <option value="News">News & Eventi</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Messaggio</label>
                            <textarea id="topic-body" placeholder="Scrivi qui il tuo messaggio..." required rows="4"></textarea>
                            <div class="error-msg" id="err-topic-body">Il messaggio deve essere di almeno 10 caratteri</div>
                        </div>

                        <div style="display:flex; justify-content:flex-end; gap:10px;">
                            <button type="button" class="auth-btn-custom" style="background:transparent; border:1px solid white;" onclick="toggleTopicForm()">Annulla</button>
                            <button type="submit" class="auth-btn-custom">Pubblica</button>
                        </div>
                    </form>
                </div>

                <!-- Griglia Forum: Qui simuliamo il rendering PHP -->
                <div class="forum-grid" id="forum-container">
                    <?php
                        // Simulazione dati dal DB poi da togliere eventualmente
                        $posts = [
                            [
                                'title' => "Opinioni su Tesla Model 3", 
                                'author' => "MarioRossi", 
                                'date' => "12/10/2025", 
                                'category' => "Veicoli", 
                                'body' => "Ciao a tutti, volevo condividere la mia esperienza..."
                            ],
                            [
                                'title' => "Colonnine Enel X vs Be Charge", 
                                'author' => "ElettroFan", 
                                'date' => "15/10/2025", 
                                'category' => "Ricarica", 
                                'body' => "Quale abbonamento usate per risparmiare?"
                            ]
                        ];

                        foreach($posts as $post) {
                            echo '<div class="forum-card">';
                            echo '<h3 style="color:#00f2ff; margin-bottom:5px;">' . htmlspecialchars($post['title']) . '</h3>';
                            echo '<div style="font-size:0.85rem; color:#94a3b8; margin-bottom:10px;">';
                            echo '<span style="background:rgba(255,255,255,0.1); padding:2px 8px; border-radius:4px;">' . htmlspecialchars($post['category']) . '</span>';
                            echo ' • di ' . htmlspecialchars($post['author']) . ' • ' . $post['date'];
                            echo '</div>';
                            echo '<p>' . htmlspecialchars($post['body']) . '</p>';
                            echo '</div>';
                        }
                    ?>
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

        <!-- Script JS per la gestione interattiva (Simulazione) -->
        <script>
            function toggleTopicForm() {
                const form = document.getElementById('new-topic-form-container');
                form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
            }

            function submitNewTopic() {
                const title = document.getElementById('topic-title').value;
                const body = document.getElementById('topic-body').value;
                
                // Semplice validazione lato client
                if(title.length < 5) {
                    document.getElementById('err-topic-title').style.display = 'block';
                    return;
                }
                if(body.length < 10) {
                    document.getElementById('err-topic-body').style.display = 'block';
                    return;
                }

                alert("Discussione pubblicata con successo!");
                toggleTopicForm();
                
                // Aggiunta visiva al DOM (Simulazione senza refresh)
                const container = document.getElementById('forum-container');
                const div = document.createElement('div');
                div.className = 'forum-card';
                div.innerHTML = `
                    <h3 style="color:#00f2ff; margin-bottom:5px;">${title}</h3>
                    <div style="font-size:0.85rem; color:#94a3b8; margin-bottom:10px;">
                        <span style="background:rgba(255,255,255,0.1); padding:2px 8px; border-radius:4px;">Nuovo</span> • di <?php echo $username; ?> • Adesso
                    </div>
                    <p>${body}</p>
                `;
                container.prepend(div);
                
                // Reset form
                document.getElementById('new-topic-form').reset();
            }
        </script>
    </body>
</html>