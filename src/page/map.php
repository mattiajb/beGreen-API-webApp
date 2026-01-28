<!DOCTYPE html>
<html lang="it">
    <head>
        <title>Charge Map | beGreen</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/svg+xml" href="../src_image/favicon/favicon.svg" />
        <link rel="shortcut icon" href="../src_image/favicon/favicon.ico"/>
        <link rel="manifest" href="../src_image/favicon/site.webmanifest"/>
        <link rel="stylesheet" href="../css/style.css">
        <!-- CLASSE ESTERNA DA VALUTARE -->
        <!-- <script src="../js/map_js.js"></script> -->
        
        <!-- STYLESHEET DELLA MAPPA -> LIBRERIA JS LEAFLET -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    </head>
    <body>
        <!-- NAVBAR -->
        <header class="site-header">
            <nav class="navbar">
                <a href="home.html" class="logo"><img src="../src_image/images/beGreen_cyan.png" alt="Logo beGreen" class="logo-img"> beGreen </a>
                <ul class="nav-links">
                    <li><a href="home.html" class="nav-item">Home</a></li>
                    <li><a href="map.php" class="nav-item active">Charge Map</a></li>
                    <li><a href="autosalone.html" class="nav-item">Autosalone</a></li>
                    <li><a href="community.html" class="nav-plus"> Community+ </a></li>
                    <li><a href="admin.html" class="nav-admin"> Admin Panel</a></li>
                </ul>
                <div class="log-container">
                    <a href="log.php" class="log-btn"><img src="../src_image/images/white_user.png" alt="Logo user" class="logo-user"> Accedi </a>
                    <div class="user-display">
                        <a href="profile.html" class="user-info"> 
                            <span class="user-name">Username</span>
                            <span class="user-type">Utente</span></a>
                            <button class="logout-btn" onclick="logout()"> Esci </button>
                    </div>
                </div>
            </nav>
        </header>

        <!-- IMPORT LIBRERIA DELLA MAPPA -> LIBRERIA JS LEAFLET -->
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

        <main class="map-layout">

            <!-- ===== COLONNA SINISTRA: MAPPA ===== -->
            <section class="map-section">
                <div id="map"></div>

                <!-- STATO GEOLOCALIZZAZIONE -->
                <div class="info-panel" id="status">
                    Richiesta posizione in corso...
                </div>
            </section>

            <!-- ===== COLONNA DESTRA: CALCOLO ===== -->
            <aside class="calc-section">
                <h2>Calcolo Ricarica</h2>
                <p>Seleziona una colonnina dalla mappa per iniziare il calcolo.</p>

                <div class="calc-placeholder">
                    <form id="calc-form" onsubmit="event.preventDefault(); calcola();">
                        <div class="form-group">
                            <label>Seleziona il tuo veicolo dall'autosalone:</label>
                            <select id="car-select" onchange="updateCarSpecs()">
                                <option value="">-- Scegli Auto --</option>
                                <!-- DA POPOLARE CON DB AUTOSALONE -->
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
                    <!-- AREA RISULTATI -->
                    <div id="result-box" class="result-box">
                            <h3 style="margin-bottom:10px;">Stima Risultato</h3>
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div>
                                    <p>Energia necessaria: <strong id="res-kwh">-</strong> kWh</p>
                                    <p>Potenza effettiva: <strong id="res-power">-</strong> kW</p>
                                    <p style="font-size:1.2rem; margin-top:10px;">Tempo Totale: <strong id="res-time" style="color:var(--primary)">-</strong></p>
                                    <p class="guest-msg"><i class="fa-solid fa-lock"></i> Registrati per visualizzare il grafico</p>
                                </div>
                                <!-- ELEMENTO VISUALIZZABILE DA USER LOGGATO -->
                                <div id="canvas-wrapper" class="canvas-container is-guest">
                                    <div class="guest-lock"></div>
                                    <canvas id="batteryCanvas" width="100" height="100"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

        </main>

        <script>
            // --- CONFIGURAZIONE ---
            const API_KEY = '8062adbb-9f3f-4cab-9a2d-ada3ef4a5594'; 
            const statusEl = document.getElementById('status');
            let map;

            // --- GEOLOCALIZZAZIONE UTENTE ---
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(initMap, handleError);
            } else {
                statusEl.innerText = "Il tuo browser non supporta la geolocalizzazione.";
            }

            // --- CREAZIONE MARKER UTENTE ---
            const userIcon = L.icon({
                iconUrl: '../src_image/images/user_marker.png', 
                iconSize: [40, 40],        
                iconAnchor: [20, 40],      
                popupAnchor: [0, -40]      
            });

            // --- CREAZIONE MAPPA ---
            function initMap(position) {
                statusEl.innerText = "Geolocalizzazione avvenuta correttamente";

                const { latitude, longitude } = position.coords;

                map = L.map('map').setView([latitude, longitude], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors - beGreen'
                }).addTo(map);

                L.marker([latitude, longitude], {icon: userIcon})
                    .addTo(map)
                    .bindPopup("<b>La tua posizione</b>")
                    .openPopup();
                    

                loadStations(latitude, longitude);
            }

            // --- CALCOLO STAZIONI DI RICARICA ---
            function loadStations(lat, lon) {
                const url = `https://api.openchargemap.io/v3/poi/?output=json&latitude=${lat}&longitude=${lon}&distance=20&distanceunit=KM&maxresults=30&key=${API_KEY}`;

                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(station => {
                            const { Latitude, Longitude, Title } = station.AddressInfo;
                            const power = station.Connections[0]?.PowerKW || "N/D";

                            const marker = L.marker([Latitude, Longitude]).addTo(map);
                            marker.bindPopup(`
                                <b>${Title}</b><br>
                                Potenza: ${power} kW<br>
                                <button onclick="fillCalc(${power})" style="margin-top:5px; cursor:pointer;">Usa questa potenza</button>
                            `);
                        });
                    });
            }

            // --- POSIZIONAMENTO MARKER SULLA MAPPA ---
            function renderMarkers(stations) {
                stations.forEach(station => {
                    const { Latitude, Longitude } = station.AddressInfo;
                    const title = station.AddressInfo.Title;
                    const address = station.AddressInfo.AddressLine1;

                    // --- CREAZIONE MARKER X OGNI STAZIONE ---
                    L.marker([Latitude, Longitude])
                        .addTo(map)
                        .bindPopup(`<b>${title}</b><br>${address || 'Indirizzo non disponibile'}`);
                });
            }

            // --- GESTIONE ERRORI DI GEOLOCALIZZAZIONE ---
            function handleError(error) {
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        statusEl.innerText = "Permesso di geolocalizzazione negato";
                        break;
                    case error.POSITION_UNAVAILABLE:
                        statusEl.innerText = "Posizione non disponibile";
                        break;
                    default:
                        statusEl.innerText = "Errore sconosciuto nella geolocalizzazione";
                }
            }

            // -- FUNZIONI CALCOLO TEMPO DI RICARICA

            // --- DEMO COLLEZIONE DI AUTO
            const db_auto = [
                { id: 1, modello: "Tesla Model 3", kwh: 60, max_ac: 11, max_dc: 170 },
                { id: 2, modello: "Volkswagen ID.3", kwh: 58, max_ac: 11, max_dc: 120 },
                { id: 3, modello: "Fiat 500e", kwh: 42, max_ac: 11, max_dc: 85 }
            ];

            let currentUserRole = null;   // oppure 'user', 'plus'
            let userHistory = [];

            function initCarSelect() {
                const select = document.getElementById('car-select');
                db_auto.forEach(auto => {
                    const opt = document.createElement('option');
                    opt.value = auto.id;
                    opt.textContent = auto.modello;
                    select.appendChild(opt);
                });
            }

            // -- FINE DEMO -> RIMUOVERE ^^^

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

                if(!carId || isNaN(currentPct) || isNaN(stationPower)) { alert("Compila tutti i campi per utilizzare la funzione di calcolo."); return; }

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

            // -- DEMO DA SOSTITUIRE CON DB
            initCarSelect();

        </script>
    </body>
</html>
