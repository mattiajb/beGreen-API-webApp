// --- CONFIGURAZIONE ---
const API_KEY_OCM = '8062adbb-9f3f-4cab-9a2d-ada3ef4a5594'; 
const statusEl = document.getElementById('status');
let map;

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
            e.preventDefault();
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
                let maxPower = 0;
                connections.forEach(conn => {
                    if (conn.PowerKW && conn.PowerKW > maxPower) {
                        maxPower = conn.PowerKW;
                    }
                });
                const powerLabel = maxPower > 0 ? `${maxPower} kW` : "N/D";
                const marker = L.marker([info.Latitude, info.Longitude]).addTo(map);
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
    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption && selectedOption.value) {
        const capacity = selectedOption.dataset.battery;
        const maxPower = selectedOption.dataset.power;
        display.innerHTML = `Capacità Batteria: <b>${capacity} kWh</b> | Max Ricarica Auto: <b>${maxPower} kW</b>`;
        display.style.color = "#00aaa0";
    } else {
        display.innerHTML = "";
    }
}

// --- LOGICA MATEMATICA DI CALCOLO ---
function calculateCharging() {
    const select = document.getElementById('car-select');
    const batteryInput = document.getElementById('battery-current');
    const powerInput = document.getElementById('power-input');
    if (!select.value || !batteryInput.value || !powerInput.value) {
        alert("Per favore, compila tutti i campi (Auto, % Batteria, Potenza).");
        return;
    }
    const currentBattPercent = parseFloat(batteryInput.value);
    const stationPower = parseFloat(powerInput.value);
    const selectedOption = select.options[select.selectedIndex];
    const carCapacity = parseFloat(selectedOption.dataset.battery);
    const carMaxPower = parseFloat(selectedOption.dataset.power);
    if (currentBattPercent < 0 || currentBattPercent >= 100) {
        alert("La percentuale della batteria deve essere compresa tra 0 e 99.");
        return;
    }
    if (stationPower <= 0) {
        alert("La potenza della colonnina deve essere maggiore di 0.");
        return;
    }

    // 5. IL CALCOLO REALE    
    const percentNeeded = 100 - currentBattPercent;
    const kwhNeeded = (carCapacity * percentNeeded) / 100;
    const realChargingPower = Math.min(stationPower, carMaxPower);
    const timeHours = kwhNeeded / realChargingPower;
    const hours = Math.floor(timeHours); // Parte intera (ore)
    const minutes = Math.round((timeHours - hours) * 60); // Parte decimale convertita in minuti
    let timeString = "";
    if (hours > 0) timeString += `${hours}h `;
    timeString += `${minutes}m`;
    document.getElementById('result-box').style.display = 'block';
    document.getElementById('res-kwh').innerText = kwhNeeded.toFixed(1); // 1 decimale
    document.getElementById('res-power').innerText = realChargingPower.toFixed(1);
    document.getElementById('res-time').innerText = timeString;
}