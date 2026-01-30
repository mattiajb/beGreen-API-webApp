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
    max_charge_power NUMERIC(5, 2) NOT NULL,
    category VARCHAR(20) DEFAULT 'normal',
	price NUMERIC(10, 2) DEFAULT 0.00,
    image_url VARCHAR(255) DEFAULT ''
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
INSERT INTO vehicles (brand, model, battery_capacity, max_charge_power, category, price, image_url) VALUES 
('Fiat', '500e La Prima', 42.0, 85.0, 'economy', 29900.00, 'https://cdn.pixabay.com/photo/2020/12/01/18/06/fiat-500-5794833_1280.jpg'),
('Dacia', 'Spring', 27.4, 30.0, 'economy', 21000.00, 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/18/2022_Dacia_Spring_Electric.jpg/1200px-2022_Dacia_Spring_Electric.jpg'),
('Renault', 'Zoe E-Tech', 52.0, 50.0, 'economy', 33000.00, 'https://upload.wikimedia.org/wikipedia/commons/e/ea/Renault_Zoe_R135_Z.E._50_Experience_%E2%80%93_Frontansicht%2C_28._Juni_2020%2C_M%C3%BCnster.jpg'),

('Tesla', 'Model 3', 57.5, 170.0, 'normal', 42490.00, 'https://cdn.pixabay.com/photo/2021/01/21/11/09/tesla-5937063_1280.jpg'),
('Volkswagen', 'ID.3 Pro', 58.0, 120.0, 'normal', 41900.00, 'https://upload.wikimedia.org/wikipedia/commons/3/30/VW_ID.3_Pro_Performance_1st_Max_Mangangrey.jpg'),
('Hyundai', 'Kona Electric', 64.0, 77.0, 'normal', 38500.00, 'https://upload.wikimedia.org/wikipedia/commons/0/05/2018_Hyundai_Kona_SE_1.0.jpg'),

('Porsche', 'Taycan 4S', 79.2, 270.0, 'luxury', 115000.00, 'https://cdn.pixabay.com/photo/2019/12/28/19/52/porsche-4725597_1280.jpg'),
('Audi', 'e-tron GT', 85.0, 270.0, 'luxury', 108000.00, 'https://upload.wikimedia.org/wikipedia/commons/5/50/Audi_e-tron_GT_IMG_4374.jpg'),
('Tesla', 'Model S Plaid', 100.0, 250.0, 'luxury', 130990.00, 'https://cdn.pixabay.com/photo/2016/06/21/20/09/tesla-model-s-1471719_1280.jpg');