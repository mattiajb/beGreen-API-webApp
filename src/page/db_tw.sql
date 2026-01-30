-- 1. Setup Database e Utente (da eseguire se non esistono)
-- CREATE USER www WITH PASSWORD 'www';
-- CREATE DATABASE "TW";
-- GRANT ALL PRIVILEGES ON DATABASE "TW" TO www;

-- \c TW  <-- Se usi psql, decommenta per connetterti al DB

-- 2. Pulizia (Reset)
DROP TABLE IF EXISTS vehicles CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TYPE IF EXISTS user_role CASCADE;

-- 3. Creazione Tipo Ruolo
CREATE TYPE user_role AS ENUM ('user', 'plus', 'admin');

-- 4. Tabella Utenti
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role user_role DEFAULT 'user'
);

-- 5. Tabella Veicoli
CREATE TABLE vehicles (
    id SERIAL PRIMARY KEY,
    brand VARCHAR(50) NOT NULL,
    model VARCHAR(100) NOT NULL,
    battery_capacity NUMERIC(5, 2) NOT NULL, 
    max_charge_power NUMERIC(5, 2) NOT NULL
);

-- 6. Gestione Permessi Fondamentale per l'utente 'www'
ALTER TABLE users OWNER TO www;
ALTER TABLE vehicles OWNER TO www;

-- Concede permessi sullo schema public
GRANT USAGE ON SCHEMA public TO www;
GRANT CREATE ON SCHEMA public TO www;

-- Concede permessi su TUTTE le tabelle attuali
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO www;

-- *** IMPORTANTE: Concede permessi sulle SEQUENZE (per gli ID auto-increment) ***
GRANT USAGE, SELECT, UPDATE ON ALL SEQUENCES IN SCHEMA public TO www;

-- Assicura che le future tabelle/sequenze ereditino i permessi
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON TABLES TO www;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON SEQUENCES TO www;

-- 7. Seed Dati (Veicoli)
INSERT INTO vehicles (brand, model, battery_capacity, max_charge_power) VALUES 
('Tesla', 'Model 3 Standard', 57.5, 170.0),
('Fiat', '500e', 42.0, 85.0),
('Volkswagen', 'ID.3 Pro', 58.0, 120.0),
('Hyundai', 'Kona Electric', 64.0, 77.0);