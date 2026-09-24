-- Execute uma única vez em bancos que ainda usam as colunas amount_eur.
ALTER TABLE attractions CHANGE COLUMN amount_eur amount_usd DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE expenses CHANGE COLUMN amount_eur amount_usd DECIMAL(10,2) UNSIGNED NOT NULL;