-- Pulizia preliminare (opzionale, se vuoi ricreare il DB da zero)
DROP TABLE IF EXISTS community_posts;
DROP TABLE IF EXISTS vehicles;
DROP TABLE IF EXISTS charging_stations;
DROP TABLE IF EXISTS users;
DROP TYPE IF EXISTS user_role;

-- 1. Creazione del tipo enumerato per i Ruoli
-- Questo garantisce che nel DB finiscano solo questi valori specifici
CREATE TYPE user_role AS ENUM ('user', 'plus', 'admin');

-- 2. Tabella Utenti
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL, -- Qui andrebbe l'hash BCRYPT
    role user_role DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Tabella Colonnine di Ricarica (per map.php)
CREATE TABLE charging_stations (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100),
    latitude DECIMAL(9,6),
    longitude DECIMAL(9,6),
    status VARCHAR(20) DEFAULT 'active', -- active, maintenance, busy
    power_kw INT,
    address VARCHAR(255)
);

-- 4. Tabella Veicoli
CREATE TABLE vehicles (
    id SERIAL PRIMARY KEY,
    brand VARCHAR(50) NOT NULL,
    model VARCHAR(100) NOT NULL,
    battery_capacity NUMERIC(5, 2) NOT NULL, -- IMPORTANTE: deve chiamarsi così
    max_charge_power NUMERIC(5, 2) NOT NULL
);

-- 5. Tabella Community (per community.html - accessibile solo a Plus/Admin)
CREATE TABLE community_posts (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id) ON DELETE CASCADE,
    title VARCHAR(100),
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------
-- POPOLAMENTO DATI (Seed)
-- --------------------------------------------------------

-- Inserimento Utenti
-- NOTA: Le password qui sono testo semplice per l'esempio. 
-- In produzione usa password_hash('tua_pass', PASSWORD_DEFAULT) in PHP.
INSERT INTO users (username, email, password_hash, role) VALUES
('admin_begreen', 'admin@begreen.it', '$2y$10$Esempi0HashPswAdmin...', 'admin'),
('elon_plus', 'elon@tesla.com', '$2y$10$Esempi0HashPswPlus...', 'plus'),
('mario_rossi', 'mario@email.it', '$2y$10$Esempi0HashPswUser1...', 'user'),
('luigi_verdi', 'luigi@email.it', '$2y$10$Esempi0HashPswUser2...', 'user');

-- Inserimento Colonnine (Coordinate zona Fisciano/Salerno per esempio)
INSERT INTO charging_stations (name, latitude, longitude, status, power_kw, address) VALUES
('Unisa Campus Nord', 40.773560, 14.787940, 'active', 22, 'Via Giovanni Paolo II, Fisciano'),
('Piazza della Concordia', 40.676300, 14.765600, 'busy', 50, 'Piazza della Concordia, Salerno'),
('Ikea Baronissi Fast Charge', 40.743200, 14.772100, 'active', 150, 'Via S. Allende, Baronissi'),
('Stazione Manutenzione', 40.700000, 14.750000, 'maintenance', 11, 'Via dei Guasti, Salerno');

-- Inserimento Veicoli
INSERT INTO vehicles (brand, model, battery_capacity, max_charge_power) VALUES 
('Tesla', 'Model 3 Standard', 57.5, 170.0),
('Fiat', '500e', 42.0, 85.0),
('Volkswagen', 'ID.3 Pro', 58.0, 120.0),
('Hyundai', 'Kona Electric', 64.0, 77.0);

-- Inserimento Post Community (Visibili solo a Plus/Admin)
INSERT INTO community_posts (user_id, title, content) VALUES
(2, 'Consigli per viaggi lunghi', 'Ho appena fatto Salerno-Milano con due sole soste. Ecco la mia strategia...'),
(1, 'Manutenzione colonnina Unisa', 'Avviso che la colonnina Nord sarà in manutenzione domani mattina.'),
(2, 'Batterie allo stato solido?', 'Cosa ne pensate delle nuove news sulla tecnologia solid-state?');