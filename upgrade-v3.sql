-- Execute uma única vez se a versão 2 já estiver instalada.
ALTER TABLE cities ADD COLUMN arrival_date DATE NULL AFTER sort_order;
ALTER TABLE cities ADD COLUMN departure_date DATE NULL AFTER arrival_date;
ALTER TABLE attractions ADD COLUMN attraction_date DATE NULL AFTER maps_url;
ALTER TABLE attractions ADD COLUMN amount_eur DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT 0 AFTER attraction_date;
ALTER TABLE attractions ADD COLUMN completed TINYINT(1) NOT NULL DEFAULT 0 AFTER amount_eur;
ALTER TABLE expenses ADD COLUMN attraction_id INT UNSIGNED NULL AFTER notes;
ALTER TABLE expenses ADD CONSTRAINT fk_expenses_attraction FOREIGN KEY (attraction_id) REFERENCES attractions(id) ON DELETE CASCADE;
ALTER TABLE expenses ADD UNIQUE KEY uq_expense_attraction (attraction_id);

-- Cria nos gastos os passeios que já possuam valor após a atualização.
INSERT INTO expenses (city_id,name,category,amount_eur,expense_date,status,notes,attraction_id)
SELECT city_id,CONCAT('Passeio: ',name),'Passeios e ingressos',amount_eur,COALESCE(attraction_date,CURDATE()),'planned','Gerado automaticamente pelo cadastro de passeios.',id
FROM attractions WHERE amount_eur>0
ON DUPLICATE KEY UPDATE name=VALUES(name),amount_eur=VALUES(amount_eur),expense_date=VALUES(expense_date);
