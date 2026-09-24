-- Para quem já instalou a primeira versão. Execute uma única vez no phpMyAdmin.
CREATE TABLE IF NOT EXISTS attractions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, city_id INT UNSIGNED NOT NULL,
  name VARCHAR(180) NOT NULL, description TEXT, maps_url VARCHAR(500) DEFAULT '',
  sort_order INT NOT NULL DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_attractions_city FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE CASCADE,
  INDEX idx_attractions_city (city_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS expenses (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, city_id INT UNSIGNED NOT NULL,
  name VARCHAR(180) NOT NULL, category VARCHAR(80) NOT NULL,
  amount_usd DECIMAL(10,2) UNSIGNED NOT NULL, expense_date DATE NOT NULL,
  status ENUM('planned','paid') NOT NULL DEFAULT 'paid', notes VARCHAR(255) DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_expenses_city FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE CASCADE,
  INDEX idx_expenses_city_date (city_id,expense_date), INDEX idx_expenses_category (category), INDEX idx_expenses_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
