CREATE TABLE cities (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  country VARCHAR(120) DEFAULT '',
  sort_order INT NOT NULL DEFAULT 0,
  arrival_date DATE NULL,
  departure_date DATE NULL,
  lodging_name VARCHAR(180) DEFAULT '', lodging_address TEXT, lodging_url VARCHAR(500) DEFAULT '',
  arrival_station VARCHAR(180) DEFAULT '', arrival_url VARCHAR(500) DEFAULT '', arrival_distance VARCHAR(180) DEFAULT '', arrival_route_url VARCHAR(500) DEFAULT '',
  departure_station VARCHAR(180) DEFAULT '', departure_url VARCHAR(500) DEFAULT '', departure_distance VARCHAR(180) DEFAULT '', departure_route_url VARCHAR(500) DEFAULT '',
  next_city VARCHAR(120) DEFAULT '', route_title VARCHAR(255) DEFAULT '', route_details TEXT, route_url VARCHAR(500) DEFAULT '', status_note TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO cities (name,country,sort_order,lodging_name,lodging_address,arrival_station,arrival_url,arrival_distance,departure_station,departure_url,departure_distance,next_city,route_title,route_details,route_url,status_note) VALUES
('Veneza','Itália',1,'A definir','','','','','','','','Dobbiaco','Veneza → Dobbiaco','A definir','','Hospedagem e trajeto ainda não definidos.'),
('Dobbiaco','Itália',2,'Residence Rogger','Via Valle San Silvestro / Wahlen 6, 39034 Dobbiaco, Itália','Autostazione Dobbiaco','https://maps.google.com/maps?q=46.724461,12.22614','Ônibus: 448 (10 min) · A pé: 3 km (45 min)','Autostazione Dobbiaco','https://maps.app.goo.gl/JyMswWguAfid8CKcA','A pé: 1,6 km (20 min)','Bolzano','Dobbiaco → Bolzano','Transporte a definir.','https://maps.app.goo.gl/DjVdBEPefr1REudAA',''),
('Bolzano','Itália',3,'Youth Hostel','Rittnerstraße 23 / I-39100 Bozen','Autostazione Bolzano','https://maps.app.goo.gl/AME78z9EcoXT8cnHA','A pé: 260 m (3 min)','Autostazione Bolzano','https://maps.app.goo.gl/AME78z9EcoXT8cnHA','A pé: 260 m (3 min)','Tirano','Bolzano → Tirano','Trem: Bolzano → Milano → Tirano','',''),
('Tirano','Itália',4,'TIME TO ESCAPE','7 Piazza Marinoni, presso Pizza Express, 23037 Tirano, Itália','Estação de Tirano','https://maps.app.goo.gl/qKQMN4VsQJWk1Qs87','A pé: 350 m (5 min)','Estação de Tirano','https://maps.app.goo.gl/qKQMN4VsQJWk1Qs87','A pé: 350 m (5 min)','Flums','Tirano → Flums','Trem regional. O Saver Day Pass pode ser usado em todo o trajeto.','https://maps.app.goo.gl/YPKCXnhJTVU1kbBa8',''),
('Flums','Suíça',5,'Villa Silvia','Kirchstrasse 6, Flums, Sankt Gallen 8890, Suíça','Estação de Flums','','A pé: 800 m (12 min)','','','','Innsbruck','Flums → Innsbruck','Trem regional.','','');

UPDATE cities SET arrival_route_url='https://maps.app.goo.gl/Jc5Fb7tNj49gBs128', departure_route_url='https://maps.app.goo.gl/Jc5Fb7tNj49gBs128' WHERE name='Tirano';
UPDATE cities SET arrival_route_url='https://maps.app.goo.gl/7ZdmpVzdDK92GQS56' WHERE name='Flums';

CREATE TABLE attractions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  city_id INT UNSIGNED NOT NULL,
  name VARCHAR(180) NOT NULL,
  description TEXT,
  maps_url VARCHAR(500) DEFAULT '',
  attraction_date DATE NULL,
  amount_usd DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT 0,
  completed TINYINT(1) NOT NULL DEFAULT 0,
  sort_order INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_attractions_city FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE CASCADE,
  INDEX idx_attractions_city (city_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE expenses (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  city_id INT UNSIGNED NOT NULL,
  name VARCHAR(180) NOT NULL,
  category VARCHAR(80) NOT NULL,
  amount_usd DECIMAL(10,2) UNSIGNED NOT NULL,
  expense_date DATE NOT NULL,
  status ENUM('planned','paid') NOT NULL DEFAULT 'paid',
  notes VARCHAR(255) DEFAULT '',
  attraction_id INT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_expenses_city FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE CASCADE,
  CONSTRAINT fk_expenses_attraction FOREIGN KEY (attraction_id) REFERENCES attractions(id) ON DELETE CASCADE,
  INDEX idx_expenses_city_date (city_id, expense_date),
  INDEX idx_expenses_category (category), INDEX idx_expenses_status (status), UNIQUE KEY uq_expense_attraction (attraction_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
