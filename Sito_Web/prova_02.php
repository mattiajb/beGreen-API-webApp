<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Mobility Hub | Il Futuro della Ricarica</title>
    <!-- Icone FontAwesome per abbellire -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* --- 1. CSS VARIABILI E RESET --- */
        :root {
            --primary: #00f2ff;       /* Ciano Elettrico */
            --secondary: #4361ee;     /* Blu profondo */
            --accent: #f72585;        /* Magenta per errori */
            --gold: #ffd700;          /* Oro per Utente Plus */
            --dark-bg: #0b1120;       /* Sfondo molto scuro */
            --card-bg: rgba(30, 41, 59, 0.7); /* Effetto vetro */
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --success: #00b894;       /* Verde Eco */
            --warning: #f1c40f;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Inter, system-ui, sans-serif;
        }

        body {
            background-color: var(--dark-bg);
            color: var(--text-main);
            /* Sfondo geometrico tech */
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(67, 97, 238, 0.2) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(0, 242, 255, 0.15) 0%, transparent 20%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* --- 2. NAVBAR MODERNA (GLASSMORPHISM) --- */
        header {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            background: rgba(11, 17, 32, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        nav {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-main);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo i { color: var(--primary); }

        .nav-links {
            display: flex;
            gap: 1.5rem;
            list-style: none;
        }

        .nav-item {
            cursor: pointer;
            font-weight: 500;
            color: var(--text-muted);
            transition: color 0.3s, text-shadow 0.3s;
            position: relative;
            font-size: 0.95rem;
        }

        .nav-item:hover, .nav-item.active {
            color: var(--primary);
            text-shadow: 0 0 10px rgba(0, 242, 255, 0.5);
        }

        .nav-item.active::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--primary);
            box-shadow: 0 0 10px var(--primary);
        }

        /* Link Admin specifico */
        #nav-admin {
            color: var(--accent);
            display: none; /* Nascosto di default */
        }
        #nav-admin:hover, #nav-admin.active {
            color: #ff80ab;
            text-shadow: 0 0 10px rgba(247, 37, 133, 0.5);
        }
        #nav-admin.active::after {
            background: var(--accent);
            box-shadow: 0 0 10px var(--accent);
        }

        /* Link Plus specifico */
        #nav-plus {
            color: var(--gold);
            display: none;
            font-weight: bold;
        }
        #nav-plus:hover, #nav-plus.active {
            color: #fffacd;
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.6);
        }
        #nav-plus.active::after {
            background: var(--gold);
            box-shadow: 0 0 10px var(--gold);
        }

        /* Pulsanti Auth Navbar */
        .auth-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-display {
            display: none; /* Nascosto se non loggato */
            align-items: center;
            gap: 10px;
            color: var(--primary);
            font-weight: bold;
        }

        .role-badge {
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: 10px;
            background: rgba(255,255,255,0.1);
            margin-left: 5px;
            text-transform: uppercase;
        }

        .auth-btn {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .auth-btn.logout {
            background: transparent;
            border: 1px solid var(--accent);
            color: var(--accent);
        }

        .auth-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.4);
        }

        /* --- 3. CONTENUTO GENERALE --- */
        main {
            margin-top: 80px; /* Spazio per navbar */
            flex: 1;
            padding: 2rem;
            max-width: 1200px;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
        }

        .section {
            display: none; /* Nascondiamo tutto di base */
            animation: fadeIn 0.5s ease-in-out;
        }

        .section.active {
            display: block; /* Mostriamo solo la sezione attiva */
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* --- 4. HOMEPAGE STYLES --- */
        .hero {
            text-align: center;
            padding: 4rem 0;
        }

        .hero h1 {
            font-size: 3.5rem;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            background: linear-gradient(to right, #fff, var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            font-size: 1.2rem;
            color: var(--text-muted);
            max-width: 700px;
            margin: 0 auto 2rem;
        }

        .cta-btn {
            display: inline-block;
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
            padding: 1rem 2rem;
            font-size: 1.1rem;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
        }

        .cta-btn:hover {
            background: var(--primary);
            color: var(--dark-bg);
            box-shadow: 0 0 20px rgba(0, 242, 255, 0.4);
        }

        /* Team Section */
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 4rem;
        }

        .team-card {
            background: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 2rem;
            border-radius: 16px;
            text-align: center;
            transition: transform 0.3s;
        }

        .team-card:hover {
            transform: translateY(-10px);
            border-color: var(--primary);
        }

        .team-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--secondary), var(--primary));
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
        }

        /* --- 5. APP SECTION (CALCOLATORE & MAPPA) --- */
        .app-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            height: calc(100vh - 150px);
        }

        .panel {
            background: var(--card-bg);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 2rem;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .panel h2 {
            border-bottom: 2px solid var(--primary);
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Mappa Simulata */
        .map-wrapper {
            flex: 1;
            background: #1e293b;
            border-radius: 12px;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .map-bg {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            opacity: 0.3;
            background-image: radial-gradient(#4361ee 1px, transparent 1px);
            background-size: 20px 20px;
        }

        .map-pin {
            position: absolute;
            color: var(--primary);
            font-size: 2rem;
            cursor: pointer;
            transition: transform 0.2s;
            text-shadow: 0 0 10px var(--primary);
        }

        .map-pin:hover { transform: scale(1.2); color: white; }

        /* Form Controls */
        .form-group { margin-bottom: 1.2rem; }
        
        label { display: block; margin-bottom: 0.5rem; font-size: 0.9rem; color: var(--text-muted); }
        
        select, input, textarea {
            width: 100%;
            padding: 12px;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        select:focus, input:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 10px rgba(0, 242, 255, 0.2);
        }

        textarea { resize: vertical; min-height: 100px; }

        .calc-btn {
            width: 100%;
            padding: 1rem;
            background: var(--success);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1.1rem;
            cursor: pointer;
            margin-top: 1rem;
            transition: background 0.3s;
        }
        .calc-btn:hover { background: #00a383; }

        /* Risultati e Guest Blocker */
        .result-box {
            margin-top: 2rem;
            padding: 1.5rem;
            background: rgba(0, 242, 255, 0.05);
            border-radius: 12px;
            border-left: 4px solid var(--primary);
            display: none; /* Nascosto di default */
            animation: slideUp 0.3s ease-out;
        }

        .canvas-container {
            position: relative;
            margin: 0 auto;
            width: 120px;
            height: 120px;
        }

        .guest-lock {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(11, 17, 32, 0.6);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: var(--white);
            font-size: 1.5rem;
            opacity: 0; /* Invisibile se loggato */
            transition: opacity 0.3s;
            pointer-events: none;
            z-index: 10;
        }

        .is-guest .guest-lock {
            opacity: 1;
            pointer-events: all;
            cursor: not-allowed;
        }
        
        .guest-msg {
            font-size: 0.8rem;
            text-align: center;
            margin-top: 5px;
            color: var(--accent);
            display: none;
        }

        .is-guest .guest-msg { display: block; }


        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        #batteryCanvas {
            width: 100px;
            height: 100px;
        }

        /* --- 6. AUTH SECTION STYLES --- */
        .auth-wrapper {
            max-width: 500px;
            margin: 2rem auto;
            background: var(--card-bg);
            padding: 3rem;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-header h2 {
            font-size: 2rem;
            color: var(--white);
            margin-bottom: 0.5rem;
        }

        .auth-toggle {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .auth-toggle a {
            color: var(--primary);
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
        }

        .auth-toggle a:hover {
            text-decoration: underline;
        }

        .error-msg {
            color: var(--accent);
            font-size: 0.85rem;
            margin-top: 5px;
            display: none; /* Nascosto di base */
            font-weight: bold;
        }
        
        input.error, textarea.error, select.error {
            border-color: var(--accent) !important;
            background: rgba(247, 37, 133, 0.1) !important;
        }

        .success-msg {
            background: rgba(0, 184, 148, 0.2);
            color: var(--success);
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 15px;
            display: none;
        }

        /* --- 7. ADMIN SECTION --- */
        .admin-dashboard {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 20px;
            border: 1px solid var(--accent);
        }

        .admin-section-block {
            margin-bottom: 3rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .admin-table th, .admin-table td {
            text-align: left;
            padding: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .admin-table th { color: var(--primary); }

        .btn-delete {
            background: transparent;
            border: 1px solid var(--accent);
            color: var(--accent);
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-delete:hover { background: var(--accent); color: white; }

        /* --- 8. FORUM SECTION & NEW TOPIC --- */
        .forum-grid {
            display: grid;
            gap: 1.5rem;
            max-width: 900px;
            margin: 0 auto;
        }

        .forum-controls {
            max-width: 900px;
            margin: 0 auto 2rem auto;
            text-align: right;
        }

        /* Form Nuova Discussione */
        #new-topic-form-container {
            max-width: 900px;
            margin: 0 auto 2rem auto;
            background: var(--card-bg);
            border: 1px solid var(--primary);
            border-radius: 12px;
            padding: 2rem;
            display: none; /* Nascosto di default */
            animation: slideUp 0.3s;
        }

        .new-topic-header {
            margin-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 1rem;
        }

        .new-topic-header h3 { color: var(--primary); }

        .forum-card {
            background: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.2s;
        }
        
        .forum-card:hover { border-color: var(--primary); }

        .forum-header {
            padding: 1.5rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(0,0,0,0.2);
        }

        .forum-title { font-size: 1.2rem; font-weight: bold; color: var(--text-main); }
        .forum-meta { font-size: 0.85rem; color: var(--text-muted); display:flex; gap:10px; align-items:center; }
        .badge-cat { padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); }

        .forum-body {
            padding: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            display: none; /* Nascosto di default (Expandable) */
            animation: fadeIn 0.3s;
        }
        
        .forum-card.active .forum-body { display: block; }

        .forum-locked-msg {
            padding: 1rem;
            text-align: center;
            color: var(--accent);
            background: rgba(247, 37, 133, 0.1);
            font-size: 0.9rem;
            display: none;
        }

        .comments-section {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255,255,255,0.05);
        }

        .comment {
            background: rgba(0,0,0,0.2);
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 10px;
            font-size: 0.9rem;
            display: flex;
            justify-content: space-between;
        }

        .comment-user { color: var(--primary); font-weight: bold; margin-right: 5px; }

        .comment-form {
            display: flex;
            gap: 10px;
            margin-top: 1rem;
        }
        
        .comment-input {
            flex: 1;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid rgba(255,255,255,0.2);
            background: rgba(0,0,0,0.3);
            color: white;
        }

        /* --- 9. PROFILE SECTION --- */
        .profile-container {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 2rem;
        }
        
        .profile-sidebar {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            height: fit-content;
        }

        .profile-pic-large {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
        }

        .profile-content {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 2rem;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .history-table th { text-align: left; border-bottom: 2px solid var(--primary); padding: 10px; color: var(--primary); }
        .history-table td { padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .history-table tr:hover { background: rgba(255,255,255,0.05); }

        /* Styles per il "Plus" Box nel profilo */
        .plus-offer-box {
            margin-top: 2rem;
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.1), rgba(0, 0, 0, 0.3));
            border: 1px solid var(--gold);
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
        }
        
        .btn-plus {
            background: linear-gradient(135deg, #ffd700, #ffa500);
            color: #000;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            transition: 0.3s;
        }
        
        .btn-plus:hover { transform: scale(1.05); box-shadow: 0 0 15px var(--gold); }

        /* --- 10. GARAGE SECTION --- */
        .garage-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            min-height: 60vh;
        }

        .car-list-panel, .my-garage-panel {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 1.5rem;
            border: 1px solid rgba(255,255,255,0.05);
            display: flex;
            flex-direction: column;
        }

        .draggable-car {
            background: rgba(255,255,255,0.1);
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            cursor: grab;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid transparent;
            transition: all 0.2s;
        }
        
        .draggable-car:hover {
            border-color: var(--primary);
            background: rgba(255,255,255,0.15);
        }
        
        .draggable-car:active { cursor: grabbing; }

        .drop-zone {
            flex: 1;
            min-height: 300px;
            border: 2px dashed rgba(255,255,255,0.2);
            border-radius: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 20px;
            transition: background 0.3s, border-color 0.3s;
            overflow-y: auto;
        }

        .drop-zone.drag-over {
            background: rgba(0, 242, 255, 0.1);
            border-color: var(--primary);
        }
        
        .drop-zone .draggable-car {
            width: 100%;
            cursor: default;
        }

        .garage-stats {
            margin-top: 20px;
            padding: 15px;
            background: rgba(0,0,0,0.3);
            border-radius: 10px;
            text-align: center;
        }

        .garage-action-btn {
            background: transparent;
            border: 1px solid var(--success);
            color: var(--success);
            padding: 2px 8px;
            border-radius: 5px;
            font-size: 0.75rem;
            cursor: pointer;
            margin-left: 10px;
            transition: 0.2s;
        }
        
        .garage-action-btn:hover { background: var(--success); color: white; }

        /* --- 11. ECO-HUB SECTION --- */
        .eco-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-top: 2rem; }
        
        .energy-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .range-wrap { margin-bottom: 2rem; }
        .range-wrap label { font-size: 1rem; display: flex; justify-content: space-between; color: var(--text-main); }
        
        input[type=range] {
            -webkit-appearance: none; width: 100%; height: 8px; background: rgba(255,255,255,0.1); border-radius: 5px; outline: none; margin-top: 10px; border: none;
        }
        input[type=range]::-webkit-slider-thumb {
            -webkit-appearance: none; appearance: none; width: 20px; height: 20px; border-radius: 50%; background: var(--success); cursor: pointer; box-shadow: 0 0 10px var(--success);
        }

        .info-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 2rem; }
        .i-card { background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; text-align: center; border: 1px solid transparent; transition: 0.3s; }
        .i-card:hover { border-color: var(--success); transform: translateY(-5px); background: rgba(46, 204, 113, 0.1); }
        .i-card i { font-size: 2rem; margin-bottom: 10px; color: var(--success); }
        
        #energyChart { width: 100%; height: 300px; display: block; margin-top: 20px; }
        .eco-stat-box { background: rgba(46, 204, 113, 0.1); padding: 15px; border-radius: 10px; margin-top: 20px; border-left: 4px solid var(--success); }

        /* --- 12. PLUS PAGE (Route Planner) --- */
        .route-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .route-form-card {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.05), var(--card-bg));
            border: 1px solid var(--gold);
            border-radius: 16px;
            padding: 2rem;
        }

        .route-results {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .stop-card {
            background: rgba(255,255,255,0.05);
            margin-bottom: 10px;
            padding: 15px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-left: 3px solid var(--gold);
        }

        /* --- 13. FOOTER --- */
        footer {
            background-color: rgba(11, 17, 32, 0.95);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding: 3rem 2rem 1rem;
            margin-top: 4rem;
            color: var(--text-muted);
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-col h3 {
            color: var(--primary);
            margin-bottom: 1.2rem;
            font-size: 1.1rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .footer-col h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background: var(--secondary);
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.8rem;
        }

        .footer-links a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.3s;
            font-size: 0.95rem;
        }

        .footer-links a:hover {
            color: var(--primary);
            padding-left: 5px;
        }

        .social-icons {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .social-btn {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-main);
            text-decoration: none;
            transition: all 0.3s;
        }

        .social-btn:hover {
            background: var(--primary);
            color: var(--dark-bg);
            transform: translateY(-3px);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 0.85rem;
        }

        /* Responsive Footer */
        @media (max-width: 900px) {
            .app-container, .profile-container, .garage-container, .eco-grid, .info-cards, .route-grid { grid-template-columns: 1fr; height: auto; }
            .map-wrapper { height: 400px; }
            .hero h1 { font-size: 2.5rem; }
            .footer-container { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 600px) {
            .footer-container { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <header>
        <nav>
            <a href="#" class="logo" onclick="switchTab('home')">
                <i class="fa-solid fa-bolt"></i> GreenSpark
            </a>
            <ul class="nav-links">
                <li id="nav-home" class="nav-item active" onclick="switchTab('home')">Home & Team</li>
                <li id="nav-app" class="nav-item" onclick="switchTab('app')">Mappa & Calcolatore</li>
                <li id="nav-garage" class="nav-item" onclick="switchTab('garage')">Garage</li> 
                <li id="nav-eco" class="nav-item" onclick="switchTab('eco')">Eco-Hub</li>
                <li id="nav-forum" class="nav-item" onclick="switchTab('forum')">Community</li>
                
                <!-- Link Esclusivo PLUS -->
                <li id="nav-plus" class="nav-item" onclick="switchTab('plus')"><i class="fa-solid fa-route"></i> Viaggi Pro</li>

                <!-- Visibile solo all'ADMIN -->
                <li id="nav-admin" class="nav-item" onclick="switchTab('admin')"><i class="fa-solid fa-lock"></i> Admin Panel</li>
            </ul>
            
            <div class="auth-container">
                <!-- Sezione visualizzata quando NON loggato (Ospite) -->
                <button id="btn-login-nav" class="auth-btn" onclick="switchTab('auth')">
                    <i class="fa-solid fa-user"></i> Accedi
                </button>
                
                <!-- Sezione visualizzata quando LOGGATO (User/Admin) -->
                <div id="user-logged-nav" class="user-display">
                    <span style="display:flex; flex-direction:column; align-items:end; line-height:1.2; cursor:pointer;" onclick="switchTab('profile')">
                        <span style="font-size:0.9rem;">
                            <i id="user-icon-display" class="fa-solid fa-circle-user"></i> 
                            <span id="display-username">User</span>
                        </span>
                        <span id="role-badge" class="role-badge">Utente</span>
                    </span>
                    <button class="auth-btn logout" onclick="logout()">Esci</button>
                </div>
            </div>
        </nav>
    </header>

    <!-- MAIN CONTENT AREA -->
    <main>

        <!-- SEZIONE 1: HOMEPAGE -->
        <section id="home-section" class="section active">
            <div class="hero">
                <h1>Ricarica Smart per<br>il tuo Veicolo Elettrico</h1>
                <p>La piattaforma definitiva per trovare colonnine e pianificare i tuoi viaggi senza ansia da ricarica. Sviluppato dal team GreenSpark.</p>
                <div style="display:flex; gap:15px; justify-content:center;">
                    <div onclick="switchTab('app')" class="cta-btn">Vai all'App <i class="fa-solid fa-arrow-right"></i></div>
                    <div onclick="switchTab('eco')" class="cta-btn" style="border-color:var(--success); color:var(--success)">Scopri Eco-Hub <i class="fa-solid fa-leaf"></i></div>
                </div>
            </div>

            <!-- Team / Chi Siamo -->
            <h2 style="text-align: center; color: var(--secondary);">Il Team GreenSpark</h2>
            <div class="team-grid">
                <div class="team-card">
                    <div class="team-avatar"><i class="fa-solid fa-code"></i></div>
                    <h3>Mario Rossi</h3>
                    <p style="color:var(--text-muted)">Full Stack Dev</p>
                </div>
                <div class="team-card">
                    <div class="team-avatar"><i class="fa-solid fa-palette"></i></div>
                    <h3>Giulia Bianchi</h3>
                    <p style="color:var(--text-muted)">UI/UX Designer</p>
                </div>
                <div class="team-card">
                    <div class="team-avatar"><i class="fa-solid fa-database"></i></div>
                    <h3>Luca Verdi</h3>
                    <p style="color:var(--text-muted)">Backend & DB</p>
                </div>
                <div class="team-card">
                    <div class="team-avatar"><i class="fa-solid fa-magnifying-glass-chart"></i></div>
                    <h3>Sara Neri</h3>
                    <p style="color:var(--text-muted)">Data Analyst</p>
                </div>
            </div>
        </section>

        <!-- SEZIONE 2: APP (MAPPA + CALCOLATORE) -->
        <section id="app-section" class="section">
            <div class="app-container">
                
                <!-- Colonna Sinistra: MAPPA -->
                <div class="panel">
                    <h2><i class="fa-solid fa-map-location-dot"></i> Mappa Punti Ricarica</h2>
                    <p style="margin-bottom: 1rem; color: var(--text-muted);">Clicca sui pin per selezionare automaticamente la potenza della colonnina.</p>
                    
                    <div class="map-wrapper" id="map-area">
                        <div class="map-bg"></div>
                        <!-- Simulazione Pin Colonnine -->
                        <div class="map-pin" style="top: 30%; left: 40%;" onclick="selectStation('Enel X Way', 22)" title="Enel X (22kW)">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div class="map-pin" style="top: 60%; left: 70%; color: #ff6b6b;" onclick="selectStation('Tesla Supercharger', 150)" title="Supercharger (150kW)">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <div class="map-pin" style="top: 20%; left: 80%; color: #ffd93d;" onclick="selectStation('Ionity Fast', 350)" title="Ionity (350kW)">
                            <i class="fa-solid fa-charging-station"></i>
                        </div>
                        <div class="map-pin" style="top: 70%; left: 20%;" onclick="selectStation('Be Charge', 11)" title="Be Charge (11kW)">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        
                        <!-- Messaggio API (Simulato) -->
                        <div style="position:absolute; bottom:10px; left:10px; background:rgba(0,0,0,0.6); padding:5px; border-radius:5px; font-size:0.7rem;">
                            API: OpenChargeMap (Simulated)
                        </div>
                    </div>

                    <div id="station-info" style="margin-top:15px; padding:10px; background:rgba(255,255,255,0.05); border-radius:8px; display:none;">
                        <strong>Colonnina Selezionata:</strong> <span id="st-name" style="color:var(--primary)">-</span><br>
                        <strong>Potenza Max:</strong> <span id="st-power" style="color:var(--primary)">-</span> kW
                    </div>
                </div>

                <!-- Colonna Destra: CALCOLATORE -->
                <div class="panel">
                    <h2><i class="fa-solid fa-calculator"></i> Stima Tempo Ricarica</h2>
                    
                    <form id="calc-form" onsubmit="event.preventDefault(); calcola();">
                        <div class="form-group">
                            <label>Seleziona il tuo veicolo (Da Database):</label>
                            <select id="car-select" onchange="updateCarSpecs()">
                                <option value="">-- Scegli Auto --</option>
                                <!-- Popolato via JS dall'array simulato -->
                            </select>
                            <div id="car-specs" style="font-size:0.8rem; color:var(--primary); margin-top:5px; height:20px;"></div>
                        </div>

                        <div class="form-group">
                            <label>Percentuale Batteria Attuale (%):</label>
                            <input type="number" id="battery-current" min="0" max="99" placeholder="Es: 20">
                        </div>

                        <div class="form-group">
                            <label>Potenza Colonnina (kW):</label>
                            <input type="number" id="power-input" placeholder="Seleziona dalla mappa o scrivi qui">
                        </div>

                        <button type="submit" class="calc-btn">Calcola Tempo</button>
                    </form>

                    <!-- Area Risultati con Canvas -->
                    <div id="result-box" class="result-box">
                        <h3 style="margin-bottom:10px;">Risultato Stima</h3>
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <p>Energia necessaria: <strong id="res-kwh">-</strong> kWh</p>
                                <p>Potenza effettiva: <strong id="res-power">-</strong> kW</p>
                                <p style="font-size:1.2rem; margin-top:10px;">Tempo Totale: <strong id="res-time" style="color:var(--primary)">-</strong></p>
                                <p class="guest-msg"><i class="fa-solid fa-lock"></i> Registrati per vedere il grafico</p>
                            </div>
                            <!-- Elemento Canvas con Lock Overlay per Guest -->
                            <div id="canvas-wrapper" class="canvas-container is-guest">
                                <div class="guest-lock"><i class="fa-solid fa-lock"></i></div>
                                <canvas id="batteryCanvas" width="100" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
        
        <!-- SEZIONE 3: GARAGE (DRAG & DROP) -->
        <section id="garage-section" class="section">
            <h2 style="text-align:center; margin-bottom:1rem;">Il Tuo Garage Virtuale</h2>
            <p style="text-align:center; color:var(--text-muted); margin-bottom:2rem;">Gestisci la tua flotta: trascina le auto per salvarle e accedere rapidamente al calcolo ricarica.</p>
            
            <div class="garage-container">
                <!-- LISTA AUTO (DRAGGABLE) -->
                <div class="car-list-panel">
                    <h3><i class="fa-solid fa-car"></i> Concessionaria</h3>
                    <p style="font-size:0.85rem; color:#aaa; margin-bottom:15px;">Trascina l'auto a destra per aggiungerla.</p>
                    <div id="draggable-list">
                        <!-- Popolato via JS -->
                    </div>
                </div>

                <!-- DROP ZONE -->
                <div class="my-garage-panel">
                    <h3><i class="fa-solid fa-warehouse"></i> Il Mio Garage</h3>
                    <div id="garage-drop-zone" class="drop-zone" ondrop="drop(event)" ondragover="allowDrop(event)" ondragleave="leaveDrop(event)">
                        <p id="empty-garage-msg" style="color:#666; margin-top:50%; pointer-events:none;">Trascina qui le auto...</p>
                    </div>
                    
                    <div class="garage-stats">
                        <div style="display:flex; justify-content:space-around;">
                            <div>
                                <p style="font-size:0.8rem; color:#aaa">Capacità Accumulo</p>
                                <strong id="garage-kwh" style="color:var(--primary); font-size:1.2rem;">0</strong> kWh
                            </div>
                            <div>
                                <p style="font-size:0.8rem; color:#aaa">Potenza Picco AC</p>
                                <strong id="garage-kw-peak" style="color:var(--warning); font-size:1.2rem;">0</strong> kW
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SEZIONE 4: ECO-HUB -->
        <section id="eco-section" class="section">
            <h2 style="text-align:center; color:var(--success); margin-bottom:2rem;"><i class="fa-solid fa-solar-panel"></i> Centro Energia Rinnovabile</h2>
            
            <!-- Educational Cards -->
            <div class="info-cards">
                <div class="i-card">
                    <i class="fa-regular fa-sun"></i>
                    <h3>Fotovoltaico</h3>
                    <p style="font-size:0.9rem; color:#aaa; margin-top:10px;">Trasforma la luce solare in elettricità per la tua casa e la tua auto. Ideale per la ricarica diurna.</p>
                </div>
                <div class="i-card">
                    <i class="fa-solid fa-wind"></i>
                    <h3>Eolico</h3>
                    <p style="font-size:0.9rem; color:#aaa; margin-top:10px;">Sfrutta la forza del vento. Una fonte complementare perfetta per le giornate nuvolose.</p>
                </div>
                <div class="i-card">
                    <i class="fa-solid fa-water"></i>
                    <h3>Idroelettrico</h3>
                    <p style="font-size:0.9rem; color:#aaa; margin-top:10px;">La fonte rinnovabile storica, costante e affidabile per la rete elettrica nazionale.</p>
                </div>
            </div>

            <div class="eco-grid">
                <!-- Simulatore -->
                <div class="energy-card">
                    <h3><i class="fa-solid fa-chart-line"></i> Simulatore Produzione Domestica</h3>
                    <p style="color:#aaa; margin-bottom:20px;">Configura il tuo impianto e vedi se riesci a coprire il fabbisogno giornaliero.</p>
                    
                    <div class="range-wrap">
                        <label>Pannelli Solari (kWp): <span id="val-solar" style="color:var(--success)">3.0 kW</span></label>
                        <input type="range" id="solar-input" min="0" max="10" step="0.5" value="3" oninput="updateEnergyCalc()">
                    </div>

                    <div class="range-wrap">
                        <label>Batteria Accumulo (kWh): <span id="val-batt" style="color:var(--success)">5.0 kWh</span></label>
                        <input type="range" id="batt-input" min="0" max="20" step="1" value="5" oninput="updateEnergyCalc()">
                    </div>

                    <canvas id="energyChart"></canvas>
                </div>

                <!-- Risultati Eco -->
                <div class="energy-card" style="display:flex; flex-direction:column; justify-content:center;">
                    <h3>Impatto Ambientale</h3>
                    <div class="eco-stat-box">
                        <p>Produzione Stimata:</p>
                        <h2 id="daily-prod">12.5 kWh</h2>
                        <p style="font-size:0.8rem; opacity:0.7;">in una giornata di sole</p>
                    </div>
                    
                    <div class="eco-stat-box" style="border-color:var(--primary); background:rgba(0, 242, 255, 0.1);">
                        <p>Auto ricaricabili al giorno:</p>
                        <h2 id="cars-chargeable">0.5</h2>
                        <p style="font-size:0.8rem; opacity:0.7;">(Basato su Tesla Model 3)</p>
                    </div>

                    <div class="eco-stat-box" style="border-color:#e74c3c; background:rgba(231, 76, 60, 0.1);">
                        <p>CO2 Risparmiata:</p>
                        <h2 id="co2-saved">4.2 kg</h2>
                        <p style="font-size:0.8rem; opacity:0.7;">rispetto a fonti fossili</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- SEZIONE 5: FORUM -->
        <section id="forum-section" class="section">
            <h2 style="text-align: center; margin-bottom: 2rem;">Community & Discussioni</h2>
            
            <!-- Controls per Nuovo Topic -->
            <div class="forum-controls">
                <button id="btn-new-topic" class="auth-btn" style="display:none;" onclick="toggleTopicForm()">
                    <i class="fa-solid fa-plus"></i> Inizia Nuova Discussione
                </button>
            </div>

            <!-- Form Nuovo Topic (Sticky) -->
            <div id="new-topic-form-container">
                <div class="new-topic-header">
                    <h3>Crea Nuova Discussione</h3>
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
                        <textarea id="topic-body" placeholder="Scrivi qui il tuo messaggio..." required></textarea>
                        <div class="error-msg" id="err-topic-body">Il messaggio deve essere di almeno 10 caratteri</div>
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:10px;">
                        <button type="button" class="auth-btn logout" onclick="toggleTopicForm()">Annulla</button>
                        <button type="submit" class="auth-btn">Pubblica</button>
                    </div>
                </form>
            </div>

            <div class="forum-grid" id="forum-container">
                <!-- Contenuti generati via JS -->
            </div>
        </section>

        <!-- SEZIONE NUOVA: PLUS (Route Planner Pro) -->
        <section id="plus-section" class="section">
            <h2 style="text-align: center; color: var(--gold); margin-bottom: 0.5rem;"><i class="fa-solid fa-bolt"></i> Route Planner Pro</h2>
            <p style="text-align: center; color: var(--text-muted); margin-bottom: 2rem;">Pianificazione viaggi intelligente esclusiva per utenti Plus.</p>
            
            <div class="route-grid">
                <!-- Form Pianificazione -->
                <div class="route-form-card">
                    <h3 style="margin-bottom:1.5rem; color:white;">Configura Viaggio</h3>
                    <form onsubmit="event.preventDefault(); calculateRoute();">
                        <div class="form-group">
                            <label>Città di Partenza</label>
                            <input type="text" id="route-start" placeholder="Es: Milano" required>
                        </div>
                        <div class="form-group">
                            <label>Destinazione</label>
                            <input type="text" id="route-end" placeholder="Es: Roma" required>
                        </div>
                        <div class="form-group">
                            <label>Distanza Stimata (km)</label>
                            <input type="number" id="route-km" placeholder="Es: 600" required>
                        </div>
                        <div class="form-group">
                            <label>Veicolo (dal tuo Garage)</label>
                            <select id="route-car-select">
                                <!-- Popolato dinamicamente col garage -->
                                <option value="">-- Seleziona Auto --</option>
                            </select>
                        </div>
                        <button type="submit" class="auth-btn" style="width:100%; background:linear-gradient(135deg, var(--gold), #ff8c00); color:black;">Calcola Itinerario</button>
                    </form>
                </div>

                <!-- Risultati -->
                <div class="route-results">
                    <h3 style="margin-bottom:1.5rem;">Il Tuo Itinerario</h3>
                    <div id="route-output" style="color: #aaa; text-align:center; padding: 20px;">
                        Compila il form per vedere le soste consigliate.
                    </div>
                </div>
            </div>
        </section>

        <!-- SEZIONE 6: PROFILO -->
        <section id="profile-section" class="section">
            <h2 style="margin-bottom: 2rem;">Il Mio Profilo</h2>
            <div class="profile-container">
                <!-- Sidebar Info -->
                <div class="profile-sidebar">
                    <div class="profile-pic-large">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <h3 id="profile-name" style="margin-bottom:5px;">User</h3>
                    <p id="profile-role" style="color:var(--primary); font-weight:bold;">Utente</p>
                    
                    <div style="text-align:left; margin-top:2rem;">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" id="profile-email" value="mario@mail.com" disabled style="opacity:0.6">
                        </div>
                        <div class="form-group">
                            <label>Nuova Password</label>
                            <input type="password" placeholder="********">
                        </div>
                        <button class="auth-btn" style="width:100%; font-size:0.9rem;">Aggiorna Dati</button>
                    </div>

                    <!-- Box Abbonamento Plus -->
                    <div id="plus-subscription-box" class="plus-offer-box">
                        <h4 style="color:var(--gold)"><i class="fa-solid fa-crown"></i> Passa a Plus</h4>
                        <p style="font-size:0.8rem; margin:10px 0;">Sblocca il Route Planner Pro e funzionalità esclusive!</p>
                        <button class="btn-plus" onclick="subscribePlus()">Abbonati (9.99€)</button>
                    </div>
                </div>

                <!-- Main Content Info -->
                <div class="profile-content">
                    <!-- Storico Ricariche -->
                    <h3><i class="fa-solid fa-clock-rotate-left"></i> Storico Calcoli</h3>
                    <p style="color:var(--text-muted); font-size:0.9rem;">I tuoi calcoli recenti salvati automaticamente.</p>
                    
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Veicolo</th>
                                <th>Batt. Iniziale</th>
                                <th>Potenza Colonnina</th>
                                <th>Tempo Stimato</th>
                            </tr>
                        </thead>
                        <tbody id="history-body">
                            <!-- Popolato via JS -->
                            <tr><td colspan="4" style="text-align:center; color:#666; padding:20px;">Nessun calcolo recente.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- SEZIONE 7: AUTH (LOGIN & REGISTER) -->
        <section id="auth-section" class="section">
            <div class="auth-wrapper">
                <div id="login-container">
                    <div class="auth-header">
                        <h2>Accedi</h2>
                        <p style="color: var(--text-muted)">Benvenuto in GreenSpark</p>
                        <p style="font-size: 0.8rem; color: var(--secondary); margin-top:10px;">
                            (Tip: Usa <b>admin</b> / <b>admin</b> per accesso amministratore)
                        </p>
                    </div>
                    <div id="login-success" class="success-msg">Login effettuato con successo!</div>
                    <form id="login-form" novalidate onsubmit="event.preventDefault(); handleLogin();">
                        <div class="form-group">
                            <label>Email o Username</label>
                            <input type="text" id="login-user" placeholder="Inserisci email" required>
                            <div class="error-msg" id="err-login-user">Username non valido</div>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" id="login-pass" placeholder="Password" required>
                            <div class="error-msg" id="err-login-pass">Password errata</div>
                        </div>
                        <button type="submit" class="auth-btn" style="width: 100%">Entra</button>
                    </form>
                    <div class="auth-toggle">
                        Non hai un account? <a onclick="toggleAuthMode('register')">Registrati ora</a>
                    </div>
                </div>

                <div id="register-container" style="display: none;">
                    <div class="auth-header"><h2>Crea Account</h2></div>
                    <div id="reg-success" class="success-msg">Registrazione completata! Ora puoi accedere.</div>
                    <form id="register-form" novalidate onsubmit="event.preventDefault(); handleRegister();">
                        <div class="form-group">
                            <label>Nome Utente</label>
                            <input type="text" id="reg-user" placeholder="Scegli un username" required>
                            <div class="error-msg" id="err-reg-user">Username obbligatorio (min 3 caratteri)</div>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" id="reg-email" placeholder="latuamail@esempio.com" required>
                            <div class="error-msg" id="err-reg-email">Inserisci una mail valida</div>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" id="reg-pass" placeholder="Minimo 6 caratteri" required>
                            <div class="error-msg" id="err-reg-pass">Password troppo corta</div>
                        </div>
                        <div class="form-group">
                            <label>Conferma Password</label>
                            <input type="password" id="reg-pass-conf" placeholder="Ripeti password" required>
                            <div class="error-msg" id="err-reg-pass-conf">Le password non coincidono</div>
                        </div>
                        <button type="submit" class="auth-btn" style="width: 100%">Registrati</button>
                    </form>
                    <div class="auth-toggle">Hai già un account? <a onclick="toggleAuthMode('login')">Accedi</a></div>
                </div>
            </div>
        </section>

        <!-- SEZIONE 8: ADMIN PANEL (Completo e Potenziato) -->
        <section id="admin-section" class="section">
            <h2 style="text-align:center; margin-bottom:2rem; color:var(--accent);">Pannello di Amministrazione</h2>
            <div class="admin-dashboard">
                
                <!-- 1. Gestione Utenti -->
                <div class="admin-section-block">
                    <h3 style="margin-bottom:1rem;"><i class="fa-solid fa-users"></i> Gestione Utenti Registrati</h3>
                    <!-- Form Aggiungi Utente -->
                    <div style="background:rgba(0,0,0,0.2); padding:15px; border-radius:10px; margin-bottom:1rem; border:1px solid rgba(255,255,255,0.1);">
                        <h4 style="color:var(--text-muted); margin-bottom:10px; font-size:0.9rem;">Aggiungi Nuovo Profilo</h4>
                        <div style="display:grid; grid-template-columns: 1fr 1fr 1fr auto; gap:10px; align-items:end;">
                            <div>
                                <label style="font-size:0.8rem">Username</label>
                                <input type="text" id="admin-new-user" placeholder="Username">
                            </div>
                            <div>
                                <label style="font-size:0.8rem">Email</label>
                                <input type="email" id="admin-new-email" placeholder="Email">
                            </div>
                            <div>
                                <label style="font-size:0.8rem">Password</label>
                                <input type="text" id="admin-new-pass" placeholder="Password">
                            </div>
                            <button class="auth-btn" onclick="adminAddUser()" style="font-size:0.8rem;">+ Aggiungi</button>
                        </div>
                    </div>

                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Ruolo</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody id="admin-users-body">
                            <!-- Popolato via JS -->
                        </tbody>
                    </table>
                </div>

                <!-- 2. Gestione Database Auto -->
                <div class="admin-section-block">
                    <h3 style="margin-bottom:1rem; color:var(--primary);"><i class="fa-solid fa-car"></i> Gestione Database Auto</h3>
                    
                    <!-- Form Aggiungi Auto -->
                    <div style="background:rgba(0,0,0,0.2); padding:15px; border-radius:10px; margin-bottom:1rem; border:1px solid rgba(255,255,255,0.1);">
                        <h4 style="color:var(--text-muted); margin-bottom:10px; font-size:0.9rem;">Inserisci Nuova Auto nel Sistema</h4>
                        <div style="display:grid; grid-template-columns: 1fr 1fr 1fr 1fr auto; gap:10px; align-items:end;">
                            <div>
                                <label style="font-size:0.8rem">Marca</label>
                                <input type="text" id="admin-car-brand" placeholder="Es. BMW">
                            </div>
                            <div>
                                <label style="font-size:0.8rem">Modello</label>
                                <input type="text" id="admin-car-model" placeholder="Es. i4">
                            </div>
                            <div>
                                <label style="font-size:0.8rem">Batt (kWh)</label>
                                <input type="number" id="admin-car-kwh" placeholder="80">
                            </div>
                            <div>
                                <label style="font-size:0.8rem">Max DC (kW)</label>
                                <input type="number" id="admin-car-dc" placeholder="200">
                            </div>
                            <button class="auth-btn" onclick="adminAddCar()" style="font-size:0.8rem; background:var(--success);">+ Aggiungi Auto</button>
                        </div>
                    </div>
                </div>

                <!-- 3. Gestione Dati Plus (Viaggi) -->
                <div>
                    <h3 style="margin-bottom:1rem; color:var(--gold);"><i class="fa-solid fa-bolt"></i> Gestione Viaggi Pro (Plus)</h3>
                    <p style="font-size:0.9rem; color:var(--text-muted); margin-bottom:10px;">Visualizza e gestisci i viaggi pianificati dagli utenti Plus.</p>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Utente</th>
                                <th>Partenza</th>
                                <th>Arrivo</th>
                                <th>Veicolo</th>
                                <th>Soste</th>
                            </tr>
                        </thead>
                        <tbody id="admin-routes-body">
                            <!-- Popolato via JS -->
                        </tbody>
                    </table>
                </div>

            </div>
        </section>

    </main>

    <!-- FOOTER POTENZIATO -->
    <footer>
        <div class="footer-container">
            <div class="footer-col">
                <h3><i class="fa-solid fa-bolt" style="color:var(--primary)"></i> GreenSpark</h3>
                <p style="margin-bottom: 1rem; font-size: 0.9rem; line-height: 1.6;">
                    Il punto di riferimento per la mobilità elettrica universitaria. Calcola tempi di ricarica, gestisci il tuo garage e scopri l'energia sostenibile.
                </p>
                <div class="social-icons">
                    <a href="#" class="social-btn"><i class="fa-brands fa-github"></i></a>
                    <a href="#" class="social-btn"><i class="fa-brands fa-linkedin-in"></i></a>
                    <a href="#" class="social-btn"><i class="fa-brands fa-instagram"></i></a>
                </div>
            </div>
            <div class="footer-col">
                <h3>Link Rapidi</h3>
                <ul class="footer-links">
                    <li><a href="#" onclick="switchTab('home')">Home</a></li>
                    <li><a href="#" onclick="switchTab('app')">Calcolatore & Mappa</a></li>
                    <li><a href="#" onclick="switchTab('garage')">Garage Virtuale</a></li>
                    <li><a href="#" onclick="switchTab('eco')">Eco-Hub</a></li>
                    <li><a href="#" onclick="switchTab('forum')">Community Forum</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Il Team</h3>
                <ul class="footer-links">
                    <li><a href="#">Mario Rossi (Dev)</a></li>
                    <li><a href="#">Giulia Bianchi (UX)</a></li>
                    <li><a href="#">Luca Verdi (DB)</a></li>
                    <li><a href="#">Sara Neri (Data)</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Contattaci</h3>
                <ul class="footer-links">
                    <li><i class="fa-solid fa-envelope" style="color:var(--primary); width:20px;"></i> info@greenspark.it</li>
                    <li><i class="fa-solid fa-location-dot" style="color:var(--primary); width:20px;"></i> Università di Salerno</li>
                    <li><i class="fa-solid fa-phone" style="color:var(--primary); width:20px;"></i> +39 089 1234567</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2025 Gruppo GreenSpark - Progetto Tecnologie Web Unisa | <a href="#" style="color:var(--text-muted); text-decoration:none;">Privacy Policy</a>
        </div>
    </footer>

    <script>
        /* --- 1. SIMULAZIONE DATABASE (PostgreSQL) --- */
        let db_auto = [
            { id: 1, marca: 'Tesla', modello: 'Model 3 SR', kwh: 50, max_ac: 11, max_dc: 170 },
            { id: 2, marca: 'Tesla', modello: 'Model Y LR', kwh: 75, max_ac: 11, max_dc: 250 },
            { id: 3, marca: 'Fiat', modello: '500e', kwh: 42, max_ac: 11, max_dc: 85 },
            { id: 4, marca: 'Volkswagen', modello: 'ID.3 Pro', kwh: 58, max_ac: 11, max_dc: 120 },
            { id: 5, marca: 'Renault', modello: 'Zoe R135', kwh: 52, max_ac: 22, max_dc: 50 },
            { id: 6, marca: 'Porsche', modello: 'Taycan', kwh: 83.7, max_ac: 22, max_dc: 270 },
            { id: 7, marca: 'Dacia', modello: 'Spring', kwh: 26.8, max_ac: 6.6, max_dc: 30 },
            { id: 8, marca: 'Hyundai', modello: 'Ioniq 5', kwh: 72.6, max_ac: 11, max_dc: 220 },
            { id: 9, marca: 'Peugeot', modello: 'e-208', kwh: 46, max_ac: 7.4, max_dc: 100 },
            { id: 10, marca: 'Audi', modello: 'Q4 e-tron', kwh: 77, max_ac: 11, max_dc: 135 }
        ];

        let db_forum = [
            {
                id: 1, title: "Opinioni su Tesla Model 3", author: "MarioRossi", date: "12/10/2025", category: "Veicoli",
                body: "Ciao a tutti, volevo condividere la mia esperienza...",
                comments: [{ user: "GiuliaB", text: "Confermo, ottima auto!" }]
            },
            {
                id: 2, title: "Colonnine Enel X vs Be Charge", author: "ElettroFan", date: "15/10/2025", category: "Ricarica",
                body: "Quale abbonamento usate per risparmiare?",
                comments: [{ user: "SaraNeri", text: "Io uso flat large di Enel X." }]
            }
        ];

        // Database Utenti (per Login e Funzionalità Admin)
        let db_users = [
            { id: 1, username: 'admin', email: 'admin@green.it', password: 'admin', role: 'admin' },
            { id: 2, username: 'MarioRossi', email: 'mario@mail.com', password: '123', role: 'user' },
            { id: 3, username: 'GiuliaB', email: 'giulia@mail.com', password: '123', role: 'user' }
        ];

        // Database Viaggi Plus
        let db_routes = [];

        // STATO UTENTE
        let userHistory = [];
        let myGarage = []; // Array per il garage
        let currentUserRole = null;
        let currentUserName = null;
        let currentUserObj = null; // Oggetto utente completo

        /* --- 2. LOGICA INIZIALIZZAZIONE --- */
        document.addEventListener('DOMContentLoaded', () => {
            renderCarLists(); // Popola select e drag list
            
            // Inizializza Grafico Eco
            updateEnergyCalc();
            
            renderForum();
        });

        // Funzione Helper per renderizzare le liste auto (chiamata anche dopo aggiunta auto admin)
        function renderCarLists() {
            // Select nel Calcolatore
            const select = document.getElementById('car-select');
            select.innerHTML = '<option value="">-- Scegli Auto --</option>';
            db_auto.forEach(auto => {
                const option = document.createElement('option');
                option.value = auto.id;
                option.text = `${auto.marca} ${auto.modello} (${auto.kwh} kWh)`;
                select.appendChild(option);
            });

            // Draggable List nel Garage Section
            const dragList = document.getElementById('draggable-list');
            dragList.innerHTML = '';
            initGarageShowroom();
        }

        /* --- 3. NAVIGAZIONE SPA --- */
        function switchTab(tabName) {
            document.querySelectorAll('.section').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));

            if(tabName === 'home') {
                document.getElementById('home-section').classList.add('active');
                document.getElementById('nav-home').classList.add('active');
            } else if (tabName === 'app') {
                document.getElementById('app-section').classList.add('active');
                document.getElementById('nav-app').classList.add('active');
            } else if (tabName === 'garage') {
                document.getElementById('garage-section').classList.add('active');
                document.getElementById('nav-garage').classList.add('active');
            } else if (tabName === 'eco') {
                document.getElementById('eco-section').classList.add('active');
                document.getElementById('nav-eco').classList.add('active');
                setTimeout(updateEnergyCalc, 100);
            } else if (tabName === 'forum') {
                document.getElementById('forum-section').classList.add('active');
                document.getElementById('nav-forum').classList.add('active');
                renderForum();
            } else if (tabName === 'plus') {
                // Controllo se è utente plus o admin
                if (currentUserRole !== 'plus' && currentUserRole !== 'admin') {
                    alert("Funzionalità riservata agli abbonati Plus!");
                    switchTab('home');
                    return;
                }
                document.getElementById('plus-section').classList.add('active');
                document.getElementById('nav-plus').classList.add('active');
                updateRouteCarSelect();
            } else if (tabName === 'profile') {
                if(!currentUserRole) { switchTab('auth'); return; }
                document.getElementById('profile-section').classList.add('active');
                renderProfile();
            } else if (tabName === 'admin') {
                if (currentUserRole !== 'admin') {
                    alert("Accesso Negato: Richiesti privilegi di amministratore.");
                    switchTab('home');
                    return;
                }
                document.getElementById('admin-section').classList.add('active');
                document.getElementById('nav-admin').classList.add('active');
                renderAdminUsers();
                renderAdminRoutes();
            } else if (tabName === 'auth') {
                document.getElementById('auth-section').classList.add('active');
            }
        }

        /* --- 4. LOGICA ECO-HUB --- */
        function updateEnergyCalc() {
            const solarKw = parseFloat(document.getElementById('solar-input').value);
            const battKwh = parseFloat(document.getElementById('batt-input').value);
            
            document.getElementById('val-solar').innerText = solarKw + " kWp";
            document.getElementById('val-batt').innerText = battKwh + " kWh";

            // Calcoli Semplificati
            const dailyProd = solarKw * 4.5; // media 4.5h sole
            document.getElementById('daily-prod').innerText = dailyProd.toFixed(1) + " kWh";
            document.getElementById('cars-chargeable').innerText = (dailyProd / 50).toFixed(2); // vs Tesla 50kwh
            document.getElementById('co2-saved').innerText = (dailyProd * 0.4).toFixed(1) + " kg"; // 0.4kg co2 per kwh fossile

            // DISEGNO CANVAS GRAFICO
            const cvs = document.getElementById('energyChart');
            if(!cvs) return;
            const ctx = cvs.getContext('2d');
            // Fix resolution
            cvs.width = cvs.clientWidth; 
            cvs.height = 300;
            const w = cvs.width, h = cvs.height;
            const padding = 30;
            const chartH = h - padding * 2;

            ctx.clearRect(0,0,w,h);
            
            // 0. Sfondo e Griglia
            // Griglia Y
            ctx.strokeStyle = "rgba(255,255,255,0.05)"; ctx.lineWidth=1;
            for(let i=0; i<=4; i++) {
                let y = h - padding - (i * chartH/4);
                ctx.beginPath(); ctx.moveTo(padding, y); ctx.lineTo(w-padding, y); ctx.stroke();
            }
            
            // Assi
            ctx.strokeStyle = "rgba(255,255,255,0.2)"; ctx.lineWidth=1;
            ctx.beginPath(); 
            ctx.moveTo(padding, padding); ctx.lineTo(padding, h-padding); // Y
            ctx.lineTo(w-padding, h-padding); // X
            ctx.stroke();

            // 1. Draw Solar Curve (Area Chart con Gradiente)
            const solGradient = ctx.createLinearGradient(0, padding, 0, h-padding);
            solGradient.addColorStop(0, "rgba(46, 204, 113, 0.6)");
            solGradient.addColorStop(1, "rgba(46, 204, 113, 0.0)");

            ctx.beginPath();
            ctx.moveTo(padding, h-padding); // Start bottom left

            const graphW = w - padding * 2;
            
            for(let x=0; x<=graphW; x+=2) {
                let hour = (x/graphW) * 24; // 0 to 24
                let y = h - padding; 
                
                // Produzione tra le 6 e le 20
                if(hour > 6 && hour < 20) {
                    let peak = solarKw * (chartH / 12); // Scala visuale basata su input e altezza grafico
                    // Funzione seno per la curva
                    let val = Math.sin((hour - 6) * Math.PI / 14); 
                    if(val < 0) val = 0;
                    y = (h - padding) - (val * peak);
                }
                ctx.lineTo(padding + x, y);
            }
            
            ctx.lineTo(w-padding, h-padding);
            ctx.closePath();
            
            // Fill
            ctx.fillStyle = solGradient;
            ctx.fill();
            
            // Stroke border
            ctx.strokeStyle = "#2ecc71"; ctx.lineWidth = 3; ctx.lineJoin = "round";
            ctx.stroke();

            // 2. Draw Consumption (Line) - Reso più visibile
            ctx.beginPath();
            ctx.strokeStyle = "#e74c3c"; ctx.lineWidth = 2; ctx.setLineDash([8, 4]);
            
            let baseLoadHeight = 40; // Pixel fittizi per carico base
            // Simuliamo un picco serale nel consumo
            for(let x=0; x<=graphW; x+=5) {
                let hour = (x/graphW) * 24;
                let y = h - padding - baseLoadHeight;
                // Picco serale 19-22
                if(hour > 18 && hour < 23) {
                     y -= 30 * Math.sin((hour-18) * Math.PI / 5);
                }
                if(x===0) ctx.moveTo(padding + x, y);
                else ctx.lineTo(padding + x, y);
            }
            ctx.stroke();
            ctx.setLineDash([]);

            // 3. Labels e Testi
            ctx.fillStyle="rgba(255,255,255,0.6)"; ctx.font="11px 'Segoe UI', sans-serif";
            ctx.textAlign="center";
            
            // Ore X Axis
            [6, 12, 18, 24].forEach(hr => {
                let posX = padding + (hr/24) * graphW;
                if(hr === 24) posX -= 10;
                ctx.fillText(hr + ":00", posX, h - 10);
            });

            // Legend
            ctx.textAlign = "right";
            ctx.fillStyle="#2ecc71"; ctx.fillText("⚡ Produzione FV", w-padding, padding);
            ctx.fillStyle="#e74c3c"; ctx.fillText("🏠 Consumo Casa", w-padding, padding+15);
            
            // Indicatore Batteria (Visual)
            ctx.textAlign = "left";
            ctx.fillStyle=battKwh > 10 ? "#00f2ff" : "#f1c40f";
            ctx.fillText(`🔋 Accumulo: ${battKwh} kWh`, padding + 10, padding);
        }

        /* --- 5. LOGICA GARAGE (DRAG & DROP) --- */
        function initGarageShowroom() {
            const dragList = document.getElementById('draggable-list');
            db_auto.forEach(auto => {
                const div = document.createElement('div');
                div.className = 'draggable-car';
                div.draggable = true;
                div.setAttribute('data-id', auto.id);
                div.addEventListener('dragstart', (e) => {
                    e.dataTransfer.setData("text/plain", auto.id);
                });
                div.innerHTML = `
                    <span><b>${auto.marca}</b> ${auto.modello}</span> 
                    <span style="font-size:0.8rem; color:#aaa;">${auto.kwh} kWh</span>
                `;
                dragList.appendChild(div);
            });
        }

        function allowDrop(e) {
            e.preventDefault();
            document.getElementById('garage-drop-zone').classList.add('drag-over');
        }

        function leaveDrop(e) {
            document.getElementById('garage-drop-zone').classList.remove('drag-over');
        }

        function drop(e) {
            e.preventDefault();
            document.getElementById('garage-drop-zone').classList.remove('drag-over');
            
            const carId = e.dataTransfer.getData("text/plain");
            const car = db_auto.find(c => c.id == carId);
            
            if(car) {
                myGarage.push(car);
                updateGarageUI();
            }
        }

        function updateGarageUI() {
            const dropZone = document.getElementById('garage-drop-zone');
            dropZone.innerHTML = '';
            
            let totalKwh = 0;
            let peakPower = 0;

            myGarage.forEach((car, index) => {
                totalKwh += car.kwh;
                peakPower += car.max_ac;
                const div = document.createElement('div');
                div.className = 'draggable-car';
                div.style.cursor = 'default';
                div.innerHTML = `
                    <span><i class="fa-solid fa-car-side" style="color:var(--primary)"></i> ${car.marca} ${car.modello}</span>
                    <div>
                        <button class="garage-action-btn" onclick="quickCharge(${car.id})"><i class="fa-solid fa-bolt"></i> Ricarica</button>
                        <i class="fa-solid fa-xmark" style="color:var(--accent); cursor:pointer; margin-left:10px;" onclick="removeCarFromGarage(${index})"></i>
                    </div>
                `;
                dropZone.appendChild(div);
            });

            if(myGarage.length === 0) {
                dropZone.innerHTML = '<p id="empty-garage-msg" style="color:#666; margin-top:50%; pointer-events:none;">Trascina qui le auto...</p>';
            }
            document.getElementById('garage-kwh').innerText = totalKwh.toFixed(1);
            document.getElementById('garage-kw-peak').innerText = peakPower.toFixed(1);
        }

        function removeCarFromGarage(index) {
            myGarage.splice(index, 1);
            updateGarageUI();
        }

        function quickCharge(carId) {
            switchTab('app');
            const select = document.getElementById('car-select');
            select.value = carId;
            updateCarSpecs();
            select.style.borderColor = '#00f2ff';
            setTimeout(() => select.style.borderColor = 'rgba(255,255,255,0.1)', 800);
        }

        /* --- 6. LOGICA MAPPA E CALCOLO --- */
        function selectStation(name, power) {
            document.getElementById('station-info').style.display = 'block';
            document.getElementById('st-name').innerText = name;
            document.getElementById('st-power').innerText = power;
            document.getElementById('power-input').value = power;
        }

        function updateCarSpecs() {
            const id = document.getElementById('car-select').value;
            const specsDiv = document.getElementById('car-specs');
            if(!id) { specsDiv.innerText = ""; return; }
            const auto = db_auto.find(a => a.id == id);
            specsDiv.innerText = `Batteria: ${auto.kwh} kWh | Max AC: ${auto.max_ac} kW | Max DC: ${auto.max_dc} kW`;
        }

        function calcola() {
            const carId = document.getElementById('car-select').value;
            const currentPct = parseInt(document.getElementById('battery-current').value);
            const stationPower = parseFloat(document.getElementById('power-input').value);

            if(!carId || isNaN(currentPct) || isNaN(stationPower)) { alert("Compila tutto!"); return; }

            const auto = db_auto.find(a => a.id == carId);
            let limitCar = (stationPower > 22) ? auto.max_dc : auto.max_ac;
            let realPower = Math.min(stationPower, limitCar);
            let missingKwh = (auto.kwh * (100 - currentPct)) / 100;
            let timeHours = missingKwh / realPower;
            let h = Math.floor(timeHours);
            let m = Math.round((timeHours - h) * 60);

            document.getElementById('result-box').style.display = 'block';
            document.getElementById('res-kwh').innerText = missingKwh.toFixed(1);
            document.getElementById('res-power').innerText = realPower;
            document.getElementById('res-time').innerText = `${h}h ${m}m`;
            drawCanvas(currentPct);

            const canvasWrapper = document.getElementById('canvas-wrapper');
            if (currentUserRole) {
                canvasWrapper.classList.remove('is-guest');
                userHistory.push({
                    car: auto.modello,
                    startPct: currentPct,
                    power: stationPower,
                    time: `${h}h ${m}m`
                });
            } else {
                canvasWrapper.classList.add('is-guest');
            }
        }

        function drawCanvas(pct) {
            const canvas = document.getElementById('batteryCanvas');
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0,0,100,100);
            ctx.beginPath(); ctx.arc(50,50,40,0,2*Math.PI); ctx.strokeStyle="rgba(255,255,255,0.1)"; ctx.lineWidth=10; ctx.stroke();
            let start = -0.5*Math.PI, end = start + ((pct/100)*2*Math.PI);
            let col = pct<20?'#ff7675':(pct<50?'#ffeaa7':'#00b894');
            ctx.beginPath(); ctx.arc(50,50,40,start,end); ctx.strokeStyle=col; ctx.lineWidth=10; ctx.lineCap='round'; ctx.stroke();
            ctx.fillStyle="#fff"; ctx.font="bold 16px Arial"; ctx.textAlign="center"; ctx.textBaseline="middle"; ctx.fillText(pct+"%",50,50);
        }

        /* --- 7. LOGICA FORUM --- */
        function renderForum() {
            const btnNew = document.getElementById('btn-new-topic');
            if(currentUserRole) btnNew.style.display = 'inline-block';
            else btnNew.style.display = 'none';

            const container = document.getElementById('forum-container');
            container.innerHTML = "";
            db_forum.forEach(post => {
                const card = document.createElement('div');
                card.className = "forum-card";
                card.id = `post-${post.id}`;
                let headerHtml = `
                    <div class="forum-header" onclick="togglePost(${post.id})">
                        <div>
                            <div class="forum-title">${post.title}</div>
                            <div class="forum-meta">
                                <span class="badge-cat">${post.category || 'Generale'}</span>
                                <span>di ${post.author} • ${post.date}</span>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-down" style="color:var(--primary)"></i>
                    </div>`;
                let bodyHtml = "";
                if (currentUserRole === null) {
                    bodyHtml = `<div class="forum-body"><div class="forum-locked-msg" style="display:block"><i class="fa-solid fa-lock"></i> Contenuto riservato. <a onclick="switchTab('auth')" style="color:var(--primary); cursor:pointer">Accedi</a></div></div>`;
                } else {
                    let commentsHtml = post.comments.map((c, index) => `
                        <div class="comment">
                            <div><span class="comment-user">${c.user}:</span> ${c.text}</div>
                            ${currentUserRole === 'admin' ? `<i class="fa-solid fa-trash btn-delete" onclick="deleteComment(${post.id}, ${index})"></i>` : ''}
                        </div>`).join('');
                    bodyHtml = `<div class="forum-body"><p>${post.body}</p><div class="comments-section"><h4 style="margin-bottom:10px; color:var(--secondary)">Commenti</h4><div id="comments-list-${post.id}">${commentsHtml}</div><div class="comment-form"><input type="text" id="input-comment-${post.id}" class="comment-input" placeholder="Scrivi..."><button class="auth-btn" style="font-size:0.8rem" onclick="addComment(${post.id})">Invia</button></div></div></div>`;
                }
                card.innerHTML = headerHtml + bodyHtml;
                container.appendChild(card);
            });
        }

        function toggleTopicForm() {
            const formContainer = document.getElementById('new-topic-form-container');
            if (formContainer.style.display === 'none' || formContainer.style.display === '') {
                formContainer.style.display = 'block';
            } else {
                formContainer.style.display = 'none';
                document.getElementById('new-topic-form').reset();
                document.querySelectorAll('.error-msg').forEach(el => el.style.display = 'none');
                document.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
            }
        }

        function submitNewTopic() {
            const titleInput = document.getElementById('topic-title');
            const catInput = document.getElementById('topic-category');
            const bodyInput = document.getElementById('topic-body');
            const title = titleInput.value.trim();
            const category = catInput.value;
            const body = bodyInput.value.trim();
            let isValid = true;
            if (title.length < 5) { titleInput.classList.add('error'); document.getElementById('err-topic-title').style.display = 'block'; isValid = false; } else { titleInput.classList.remove('error'); document.getElementById('err-topic-title').style.display = 'none'; }
            if (body.length < 10) { bodyInput.classList.add('error'); document.getElementById('err-topic-body').style.display = 'block'; isValid = false; } else { bodyInput.classList.remove('error'); document.getElementById('err-topic-body').style.display = 'none'; }
            if (!isValid) return;
            const newPost = { id: db_forum.length + 1, title: title, author: currentUserName, date: new Date().toLocaleDateString('it-IT'), category: category, body: body, comments: [] };
            db_forum.unshift(newPost);
            alert("Discussione pubblicata con successo!");
            toggleTopicForm();
            renderForum();
        }

        function togglePost(id) { document.getElementById(`post-${id}`).classList.toggle('active'); }
        function addComment(postId) {
            const input = document.getElementById(`input-comment-${postId}`);
            if(!input.value) return;
            db_forum.find(p => p.id === postId).comments.push({ user: currentUserName, text: input.value });
            renderForum();
            setTimeout(() => document.getElementById(`post-${postId}`).classList.add('active'), 50);
        }
        function deleteComment(postId, idx) {
            if(!confirm("Eliminare?")) return;
            db_forum.find(p => p.id === postId).comments.splice(idx, 1);
            renderForum();
            setTimeout(() => document.getElementById(`post-${postId}`).classList.add('active'), 50);
        }

        /* --- 8. LOGICA PROFILO & PLUS --- */
        function renderProfile() {
            document.getElementById('profile-name').innerText = currentUserName;
            document.getElementById('profile-email').value = currentUserObj.email;
            
            // Gestione Visualizzazione Ruolo
            let roleText = 'Utente Standard';
            if (currentUserRole === 'admin') roleText = 'Amministratore';
            if (currentUserRole === 'plus') roleText = 'Utente Plus ⚡';
            
            document.getElementById('profile-role').innerText = roleText;
            
            // Gestione Box Abbonamento (nascondi se già plus o admin)
            if (currentUserRole === 'plus' || currentUserRole === 'admin') {
                document.getElementById('plus-subscription-box').style.display = 'none';
            } else {
                document.getElementById('plus-subscription-box').style.display = 'block';
            }

            const tbody = document.getElementById('history-body');
            tbody.innerHTML = "";
            if(userHistory.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align:center; color:#666; padding:20px;">Nessun calcolo recente.</td></tr>';
            } else {
                userHistory.forEach(h => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `<td>${h.car}</td><td>${h.startPct}%</td><td>${h.power} kW</td><td>${h.time}</td>`;
                    tbody.appendChild(tr);
                });
            }
        }

        function subscribePlus() {
            if(!confirm("Confermi l'abbonamento mensile a 9.99€?")) return;
            
            // Update Stato
            currentUserRole = 'plus';
            currentUserObj.role = 'plus';
            
            // Aggiorna UI
            updateNavbarUser();
            renderProfile();
            
            alert("Congratulazioni! Ora sei un utente PLUS.");
        }

        /* --- 9. NUOVA LOGICA: ROUTE PLANNER (PLUS) --- */
        function updateRouteCarSelect() {
            const sel = document.getElementById('route-car-select');
            sel.innerHTML = '<option value="">-- Seleziona Auto --</option>';
            myGarage.forEach(car => {
                const opt = document.createElement('option');
                opt.value = car.id;
                opt.text = `${car.marca} ${car.modello} (${car.kwh} kWh)`;
                sel.appendChild(opt);
            });
            if(myGarage.length === 0) {
                sel.innerHTML = '<option value="">Garage Vuoto! Aggiungi auto.</option>';
            }
        }

        function calculateRoute() {
            const start = document.getElementById('route-start').value;
            const end = document.getElementById('route-end').value;
            const km = parseFloat(document.getElementById('route-km').value);
            const carId = document.getElementById('route-car-select').value;
            
            if(!start || !end || !km || !carId) { alert("Compila tutti i campi!"); return; }

            const car = db_auto.find(c => c.id == carId);
            
            // Stima molto semplice: Range medio 350km
            const range = car.kwh * 5; // Assumendo 5km/kWh
            const stops = Math.floor(km / range);
            
            let html = `<h4>Da ${start} a ${end} (${km} km)</h4>`;
            html += `<p style="color:var(--text-muted); font-size:0.9rem;">Veicolo: ${car.marca} ${car.modello} (Range ~${range.toFixed(0)}km)</p>`;
            html += `<hr style="border:0; border-top:1px solid #444; margin:15px 0;">`;
            
            if(stops === 0) {
                html += `<div class="stop-card" style="border-color:var(--success)">
                            <div><i class="fa-solid fa-check"></i> Nessuna sosta necessaria</div>
                            <small>Arrivo stimato con ${(100 - (km/range)*100).toFixed(0)}% batteria</small>
                         </div>`;
            } else {
                for(let i=1; i<=stops; i++) {
                    html += `<div class="stop-card">
                                <div><i class="fa-solid fa-charging-station"></i> Sosta Ricarica #${i}</div>
                                <small>Consigliato: Ionity Fast @ Km ${(i * range * 0.9).toFixed(0)}</small>
                             </div>`;
                }
            }

            document.getElementById('route-output').innerHTML = html;

            // Salva nel DB Routes per l'Admin
            db_routes.push({
                user: currentUserName,
                start: start,
                end: end,
                car: `${car.marca} ${car.modello}`,
                stops: stops
            });
        }

        /* --- 10. AUTH & ADMIN FUNCTIONS --- */
        function toggleAuthMode(m) {
            document.querySelectorAll('.success-msg').forEach(e=>e.style.display='none');
            document.getElementById('login-container').style.display = m==='register'?'none':'block';
            document.getElementById('register-container').style.display = m==='register'?'block':'none';
        }
        function showError(fid, eid, s) { 
            const el=document.getElementById(fid), err=document.getElementById(eid);
            s ? (el.classList.add('error'), err.style.display='block') : (el.classList.remove('error'), err.style.display='none');
        }
        
        function handleLogin() {
            const u = document.getElementById('login-user').value;
            const p = document.getElementById('login-pass').value;
            
            // Controllo su db_users (Array Reale)
            const foundUser = db_users.find(user => (user.username === u || user.email === u) && user.password === p);

            if(foundUser) {
                currentUserObj = foundUser;
                currentUserRole = foundUser.role; 
                currentUserName = foundUser.username;
                
                document.getElementById('login-success').style.display='block';
                setTimeout(()=>{
                    updateNavbarUser();
                    
                    switchTab('home');
                    document.getElementById('login-form').reset();
                    document.getElementById('login-success').style.display='none';
                    renderForum();
                }, 1000);
            } else {
                if(u.length<3) showError('login-user','err-login-user',true); 
                else {
                    // Errore generico se username ok ma non trovato
                    alert("Credenziali non valide!");
                }
            }
        }

        function updateNavbarUser() {
            document.getElementById('btn-login-nav').style.display='none';
            document.getElementById('user-logged-nav').style.display='flex';
            
            let displayHtml = currentUserName;
            let badgeText = 'Utente';
            let badgeColor = 'var(--secondary)';
            let iconClass = 'fa-solid fa-circle-user';
            
            if(currentUserRole === 'admin') {
                badgeText = 'Admin';
                badgeColor = 'var(--accent)';
                document.getElementById('nav-admin').style.display='block';
                document.getElementById('nav-plus').style.display='block'; // Admin vede anche Plus
            } else if (currentUserRole === 'plus') {
                badgeText = 'Plus';
                badgeColor = 'var(--gold)';
                displayHtml += ' <i class="fa-solid fa-bolt" style="color:var(--gold)"></i>';
                document.getElementById('nav-plus').style.display='block';
            } else {
                // Standard User
                document.getElementById('nav-admin').style.display='none';
                document.getElementById('nav-plus').style.display='none';
            }

            document.getElementById('display-username').innerHTML = displayHtml;
            const badge = document.getElementById('role-badge');
            badge.innerText = badgeText; 
            badge.style.background = badgeColor;
        }

        function handleRegister() {
            const u = document.getElementById('reg-user').value;
            const e = document.getElementById('reg-email').value;
            const p = document.getElementById('reg-pass').value;
            
            // Semplice push nel DB
            db_users.push({
                id: db_users.length + 1,
                username: u,
                email: e,
                password: p,
                role: 'user'
            });

            document.getElementById('reg-success').style.display='block';
            document.getElementById('register-form').reset();
            setTimeout(()=>toggleAuthMode('login'), 2000);
        }

        function logout() {
            if(confirm("Esci?")) {
                currentUserRole=null; currentUserName=null; currentUserObj=null; userHistory=[]; myGarage=[];
                document.getElementById('user-logged-nav').style.display='none';
                document.getElementById('btn-login-nav').style.display='block';
                document.getElementById('nav-admin').style.display='none';
                document.getElementById('nav-plus').style.display='none';
                document.getElementById('calc-form').reset();
                document.getElementById('result-box').style.display='none';
                document.getElementById('canvas-wrapper').classList.add('is-guest');
                switchTab('home');
                renderForum();
                updateGarageUI();
            }
        }

        /* --- 11. ADMIN FUNCTIONS (ADD USER & CAR) --- */
        function renderAdminUsers() {
            const tbody = document.getElementById('admin-users-body');
            tbody.innerHTML = '';
            db_users.forEach(u => {
                const tr = document.createElement('tr');
                let badgeStyle = u.role === 'admin' ? 'background:var(--accent)' : (u.role === 'plus' ? 'background:var(--gold); color:black' : 'background:var(--secondary)');
                tr.innerHTML = `<td>${u.username}</td><td>${u.email}</td><td><span class="role-badge" style="${badgeStyle}">${u.role}</span></td><td><button class="btn-delete" title="Non attivo in demo"><i class="fa-solid fa-trash"></i></button></td>`;
                tbody.appendChild(tr);
            });
        }

        function adminAddUser() {
            const u = document.getElementById('admin-new-user').value;
            const e = document.getElementById('admin-new-email').value;
            const p = document.getElementById('admin-new-pass').value;

            if(u && e && p) {
                db_users.push({
                    id: db_users.length + 1,
                    username: u,
                    email: e,
                    password: p,
                    role: 'user' // Default user standard
                });
                alert("Utente aggiunto! Ora può eseguire il login.");
                renderAdminUsers();
                // Reset inputs
                document.getElementById('admin-new-user').value = '';
                document.getElementById('admin-new-email').value = '';
                document.getElementById('admin-new-pass').value = '';
            } else {
                alert("Compila tutti i campi utente.");
            }
        }

        function adminAddCar() {
            const brand = document.getElementById('admin-car-brand').value;
            const model = document.getElementById('admin-car-model').value;
            const kwh = parseFloat(document.getElementById('admin-car-kwh').value);
            const dc = parseFloat(document.getElementById('admin-car-dc').value);

            if(brand && model && kwh && dc) {
                db_auto.push({
                    id: db_auto.length + 1, // Simple ID gen
                    marca: brand,
                    modello: model,
                    kwh: kwh,
                    max_ac: 11, // Default
                    max_dc: dc
                });
                alert("Auto aggiunta al Database!");
                
                // Aggiorna le liste
                renderCarLists();
                
                // Reset inputs
                document.getElementById('admin-car-brand').value = '';
                document.getElementById('admin-car-model').value = '';
                document.getElementById('admin-car-kwh').value = '';
                document.getElementById('admin-car-dc').value = '';
            } else {
                alert("Compila tutti i campi auto.");
            }
        }

        function renderAdminRoutes() {
            const tbody = document.getElementById('admin-routes-body');
            tbody.innerHTML = '';
            if(db_routes.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; color:#666;">Nessun viaggio pianificato dagli utenti Plus.</td></tr>';
                return;
            }
            db_routes.forEach(r => {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${r.user}</td><td>${r.start}</td><td>${r.end}</td><td>${r.car}</td><td>${r.stops}</td>`;
                tbody.appendChild(tr);
            });
        }
    </script>
</body>
</html>