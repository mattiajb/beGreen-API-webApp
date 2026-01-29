// --- CONFIGURAZIONE ---
const API_KEY_OCM = '8062adbb-9f3f-4cab-9a2d-ada3ef4a5594'; 
const statusEl = document.getElementById('status');
let map; // Variabile globale per la mappa

// --- INIZIALIZZAZIONE ---
document.addEventListener('DOMContentLoaded', () => {
    initApp();
});

const userIcon = L.icon({
    iconUrl: '../src_image/images/user_marker.png',
    iconSize: [40, 40],
    iconAnchor: [20, 40],
    popupAnchor: [0, -40]
});

function initApp() {
    // 1. Gestione Geolocalizzazione
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(initMap, handleGeoError);
    } else {
        statusEl.innerText = "Geolocalizzazione non supportata dal browser.";
        // Fallback su Roma se il GPS non va
        initMap({ coords: { latitude: 41.9028, longitude: 12.4964 } });
    }

    // 2. Listener per il cambio auto (aggiorna specifiche)
    const carSelect = document.getElementById('car-select');
    if (carSelect) {
        carSelect.addEventListener('change', updateCarSpecs);
    }

    // 3. Listener per il calcolo (submit form)
    const calcForm = document.getElementById('calc-form');
    if (calcForm) {
        calcForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Evita il ricaricamento della pagina
            calculateCharging();
        });
    }
}

// --- LOGICA MAPPA (Leaflet + OpenChargeMap) ---

function initMap(position) {
    const { latitude, longitude } = position.coords;
    statusEl.innerText = "Posizione trovata. Ricerco le colonnine...";

    // Creazione Mappa
    map = L.map('map').setView([latitude, longitude], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors | beGreen'
    }).addTo(map);

    // Marker posizione utente
    L.marker([latitude, longitude], {icon: userIcon}).addTo(map)
        .bindPopup("<b>La tua posizione</b>").openPopup();

    // Caricamento stazioni
    fetchStations(latitude, longitude);
}

function fetchStations(lat, lon) {
    // API OpenChargeMap: raggio 20KM, max 50 risultati
    const url = `https://api.openchargemap.io/v3/poi/?output=json&latitude=${lat}&longitude=${lon}&distance=30&distanceunit=KM&maxresults=50&key=${API_KEY_OCM}`;

    fetch(url)
        .then(res => res.json())
        .then(data => {
            if (data.length === 0) {
                statusEl.innerText = "Nessuna colonnina trovata nei paraggi.";
                return;
            }

            data.forEach(station => {
                const info = station.AddressInfo;
                const connections = station.Connections || [];
                
                // Trova la potenza massima disponibile in questa stazione
                let maxPower = 0;
                connections.forEach(conn => {
                    if (conn.PowerKW && conn.PowerKW > maxPower) {
                        maxPower = conn.PowerKW;
                    }
                });

                // Se non c'è info sulla potenza, mettiamo 0 (verrà gestito come N/D)
                const powerLabel = maxPower > 0 ? `${maxPower} kW` : "N/D";

                // Creazione Marker
                const marker = L.marker([info.Latitude, info.Longitude]).addTo(map);
                
                // Contenuto Popup con bottone "Seleziona"
                const popupContent = `
                    <div class="popup-content" style="text-align:center">
                        <b>${info.Title}</b><br>
                        <span style="font-size:0.9rem; color:#666">${info.AddressLine1 || ''}</span><br>
                        <strong>Potenza: ${powerLabel}</strong><br>
                        ${maxPower > 0 ? 
                            `<button onclick="setStationPower(${maxPower})" 
                              style="margin-top:8px; background-color:#00aaa0; color:white; border:none; padding:5px 10px; border-radius:4px; cursor:pointer;">
                              Usa questa colonnina
                            </button>` 
                            : '<em style="font-size:0.8rem">Potenza non specificata</em>'
                        }
                    </div>
                `;
                marker.bindPopup(popupContent);
            });
            statusEl.innerText = `Trovate ${data.length} stazioni nel raggio di 30 Km.`;
        })
        .catch(err => {
            console.error("Errore API:", err);
            statusEl.innerText = "Errore durante il caricamento delle stazioni.";
        });
}

// Funzione globale chiamata dal bottone dentro il Popup della mappa
window.setStationPower = function(kw) {
    const powerInput = document.getElementById('power-input');
    if (powerInput) {
        powerInput.value = kw;
        
        // Effetto visivo: scrolla verso il calcolatore su mobile
        const calcSection = document.querySelector('.calc-section');
        if (calcSection) {
            calcSection.scrollIntoView({ behavior: 'smooth' });
        }
        
        // Evidenzia il campo input per feedback utente
        powerInput.style.backgroundColor = "#e0f7fa";
        setTimeout(() => powerInput.style.backgroundColor = "", 1000);
    }
};

function handleGeoError(error) {
    console.warn("Errore Geolocalizzazione:", error);
    statusEl.innerText = "Impossibile ottenere la tua posizione. Mostro Roma.";
    initMap({ coords: { latitude: 41.9028, longitude: 12.4964 } });
}

// --- LOGICA INTERFACCIA UTENTE (UI) ---

function updateCarSpecs() {
    const select = document.getElementById('car-select');
    const display = document.getElementById('car-specs-display');
    
    // Ottieni l'opzione selezionata
    const selectedOption = select.options[select.selectedIndex];

    // Controlla se è un'opzione valida (ha un value)
    if (selectedOption && selectedOption.value) {
        // Leggiamo i dati "nascosti" nell'HTML (data-battery e data-power)
        // L'oggetto dataset converte data-battery in dataset.battery
        const capacity = selectedOption.dataset.battery;
        const maxPower = selectedOption.dataset.power;
        
        display.innerHTML = `Capacità Batteria: <b>${capacity} kWh</b> | Max Ricarica Auto: <b>${maxPower} kW</b>`;
        display.style.color = "#00aaa0";
    } else {
        display.innerHTML = ""; // Pulisci se torna su "-- Scegli Auto --"
    }
}

// --- LOGICA MATEMATICA DI CALCOLO ---

function calculateCharging() {
    // 1. Recupero valori dal DOM
    const select = document.getElementById('car-select');
    const batteryInput = document.getElementById('battery-current');
    const powerInput = document.getElementById('power-input');

    // 2. Controllo preliminare campi vuoti
    if (!select.value || !batteryInput.value || !powerInput.value) {
        alert("Per favore, compila tutti i campi (Auto, % Batteria, Potenza).");
        return;
    }

    // 3. Conversione in numeri
    const currentBattPercent = parseFloat(batteryInput.value);
    const stationPower = parseFloat(powerInput.value);
    
    // Recupero dati auto dall'opzione selezionata
    const selectedOption = select.options[select.selectedIndex];
    const carCapacity = parseFloat(selectedOption.dataset.battery);
    const carMaxPower = parseFloat(selectedOption.dataset.power);

    // 4. Validazione Logica (per evitare l'errore -1 o >100)
    if (currentBattPercent < 0 || currentBattPercent >= 100) {
        alert("La percentuale della batteria deve essere compresa tra 0 e 99.");
        return;
    }
    if (stationPower <= 0) {
        alert("La potenza della colonnina deve essere maggiore di 0.");
        return;
    }

    // 5. IL CALCOLO REALE
    
    // A. Quanta energia serve? (es. ho 20%, mi serve l'80% di 50kWh = 40kWh)
    const percentNeeded = 100 - currentBattPercent;
    const kwhNeeded = (carCapacity * percentNeeded) / 100;

    // B. A che velocità carico? 
    // La velocità è limitata dal "collo di bottiglia":
    // Se la colonnina da 150kW ma l'auto accetta max 50kW -> carico a 50kW.
    // Se la colonnina da 22kW e l'auto accetta 100kW -> carico a 22kW.
    const realChargingPower = Math.min(stationPower, carMaxPower);

    // C. Tempo (Ore) = Energia (kWh) / Potenza (kW)
    const timeHours = kwhNeeded / realChargingPower;

    // 6. Formattazione Tempo (Ore e Minuti)
    const hours = Math.floor(timeHours); // Parte intera (ore)
    const minutes = Math.round((timeHours - hours) * 60); // Parte decimale convertita in minuti

    // Creazione stringa tempo (es. "1h 30m" o "45m")
    let timeString = "";
    if (hours > 0) timeString += `${hours}h `;
    timeString += `${minutes}m`;

    // 7. Visualizzazione Risultati
    document.getElementById('result-box').style.display = 'block';
    
    document.getElementById('res-kwh').innerText = kwhNeeded.toFixed(1); // 1 decimale
    document.getElementById('res-power').innerText = realChargingPower.toFixed(1);
    document.getElementById('res-time').innerText = timeString;
}