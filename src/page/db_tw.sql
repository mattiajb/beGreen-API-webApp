DROP TABLE IF EXISTS vehicles CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TYPE IF EXISTS user_role CASCADE;
DROP TYPE IF EXISTS forum_db;

-- Creazione Tipo Ruolo
CREATE TYPE user_role AS ENUM ('user', 'plus', 'admin');

-- Tabella Utenti
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role user_role DEFAULT 'user'
);

-- Tabella Veicoli
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

-- Tabella Community+
CREATE TABLE forum_db (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    category VARCHAR(50) NOT NULL,
    body TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_forum_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
);

-- Gestione Permessi per l'utente 'www'
ALTER TABLE users OWNER TO www;
ALTER TABLE vehicles OWNER TO www;
ALTER TABLE forum_db OWNER TO www;

GRANT USAGE ON SCHEMA public TO www;
GRANT CREATE ON SCHEMA public TO www;

GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO www;

GRANT USAGE, SELECT, UPDATE ON ALL SEQUENCES IN SCHEMA public TO www;

ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON TABLES TO www;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON SEQUENCES TO www;

-- Inserimento Utenti default
INSERT INTO users (username, email, password_hash, role)
VALUES
( 
  'Gerardo',
  'gerardo@studenti.unisa.it',
  '$2y$10$EMQp/ZLOOUB0ZyWKBKVzoe4cUPXHIKDdsRr1IlsRK/V.tduQbiije',
  'user'
),
(
  'Sabrina',
  's.senatore@unisa.it',
  '$2y$10$yt375umc7iB1fmJ4YR77GOSpxnq2WjAhRNQ9ZuNgUO4rEBsoOro96',
  'plus'
),
(
  'Admin',
  'admin@begreen.it',
  '$2y$10$qsX5tpMSISOTFR4YfRgNG.T1ZLB2lWqyJ4Bc0Umf2LCPU6.XZNW2u',
  'admin'
);

-- Inserimento DB veicoli
INSERT INTO vehicles (brand, model, battery_capacity, max_charge_power, category, price, image_url) VALUES 

-- CATEGORIA ECONOMY (City Car accessibili)
('Dacia', 'Spring', 26.8, 30.0, 'economy', 21450.00, 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d4/Dacia_Spring_Extreme_IMG_7667.jpg/500px-Dacia_Spring_Extreme_IMG_7667.jpg'),
('Fiat', '500e', 42.0, 85.0, 'economy', 29950.00, 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8a/Fiat-500-vorne2.jpg/500px-Fiat-500-vorne2.jpg'),
('Renault', 'Twingo E-Tech', 22.0, 22.0, 'economy', 24050.00, 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/21/Renault_Twingo_concept_2023.jpg/500px-Renault_Twingo_concept_2023.jpg'),
('Smart', 'EQ fortwo', 17.6, 22.0, 'economy', 25210.00, 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a2/Smart_Fortwo_EQ_Car2goStuttgart_IMG_0750.jpg/500px-Smart_Fortwo_EQ_Car2goStuttgart_IMG_0750.jpg'),

-- CATEGORIA NORMAL (Berline e SUV di fascia media)
('Tesla', 'Model 3 RWD', 60.0, 170.0, 'normal', 42490.00, 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/83/Tesla_Model_3_parked%2C_front_driver_side.jpg/500px-Tesla_Model_3_parked%2C_front_driver_side.jpg'),
('Jeep', 'Avenger', 54.0, 100.0, 'normal', 37900.00, 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/62/Jeep_Avenger_Auto_Zuerich_2023_1X7A1154.jpg/500px-Jeep_Avenger_Auto_Zuerich_2023_1X7A1154.jpg'),
('Volvo', 'EX30', 69.0, 153.0, 'normal', 36900.00, 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/cc/Volvo_EX30_Auto_Zuerich_2023_1X7A0949.jpg/500px-Volvo_EX30_Auto_Zuerich_2023_1X7A0949.jpg'),
('MG', 'MG4 Electric', 64.0, 135.0, 'normal', 30790.00, 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/MG4_Electric_%E2%80%93_f_21042025.jpg/500px-MG4_Electric_%E2%80%93_f_21042025.jpg'),

-- CATEGORIA LUXURY (Supercar e Ammiraglie)
('Porsche', 'Taycan 4S', 93.4, 270.0, 'luxury', 115000.00, 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d1/Porsche_Taycan_4S_Turbo.jpg/1280px-Porsche_Taycan_4S_Turbo.jpg'),
('Audi', 'RS e-tron GT', 93.4, 270.0, 'luxury', 155000.00, 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6c/Audi_e-tron_gt_concept_Genf_2019_1Y7A5440.jpg/250px-Audi_e-tron_gt_concept_Genf_2019_1Y7A5440.jpg'),
('Tesla', 'Model S Plaid', 100.0, 250.0, 'luxury', 130990.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQfjORXINwBNGVBA6dShEGeqi--_8VasOUvSg&s'),
('Mercedes-Benz', 'EQS 450+', 107.8, 200.0, 'luxury', 118000.00, 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/01/Mercedes-Benz_V297_IAA_2021_1X7A0245.jpg/500px-Mercedes-Benz_V297_IAA_2021_1X7A0245.jpg');