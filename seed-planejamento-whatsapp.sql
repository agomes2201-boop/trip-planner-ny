-- Dados extraídos do planejamento compartilhado. Execute uma única vez.
INSERT INTO cities (name,country,sort_order,lodging_name,lodging_address,next_city,route_title,route_details,status_note)
SELECT 'Innsbruck','Áustria',6,'Pension Stoi Budget Guesthouse','Salurner Straße 7, Innsbruck','Ljubljana','Innsbruck → Ljubljana','Trem diurno; confirmar a opção definitiva e os horários antes da viagem.','Hospedagem próxima à Innsbruck Hbf.'
WHERE NOT EXISTS (SELECT 1 FROM cities WHERE name='Innsbruck');
INSERT INTO cities (name,country,sort_order,lodging_name,lodging_address,next_city,route_title,route_details,status_note)
SELECT 'Ljubljana','Eslovênia',7,'ibis Styles Ljubljana The Fuzzy Log','Miklošičeva cesta 9, Ljubljana','Zagreb','Ljubljana → Zagreb','A definir.','Hospedagem a aproximadamente 7 minutos a pé da estação.'
WHERE NOT EXISTS (SELECT 1 FROM cities WHERE name='Ljubljana');

UPDATE cities SET departure_date='2026-10-08' WHERE name='Veneza';
UPDATE cities SET arrival_date='2026-10-08',departure_date='2026-10-11' WHERE name='Dobbiaco';
UPDATE cities SET arrival_date='2026-10-11',departure_date='2026-10-12' WHERE name='Bolzano';
UPDATE cities SET arrival_date='2026-10-12',departure_date='2026-10-13' WHERE name='Tirano';
UPDATE cities SET arrival_date='2026-10-13',departure_date='2026-10-15' WHERE name='Flums';
UPDATE cities SET arrival_date='2026-10-15',departure_date='2026-10-16' WHERE name='Innsbruck';
UPDATE cities SET arrival_date='2026-10-16',departure_date='2026-10-18' WHERE name='Ljubljana';

INSERT INTO attractions(city_id,name,description,sort_order)
SELECT id,'Centro histórico de Dobbiaco','08/10: passeio leve após a chegada. Centro histórico, café e caminhada tranquila. Transporte regional coberto pelo Guest Pass quando elegível.',1 FROM cities WHERE name='Dobbiaco';
INSERT INTO attractions(city_id,name,description,sort_order)
SELECT id,'Lago di Misurina','09/10: linha 445 desde Dobbiaco Autostazione. Planejamento: saída por volta de 09:05 e chegada em Misurina Genzianella por volta de 09:35. Caminhada pela margem e lanche levado. Confirmar tabela oficial.',2 FROM cities WHERE name='Dobbiaco';
INSERT INTO attractions(city_id,name,description,sort_order)
SELECT id,'Lago di Landro','09/10: retorno pela linha 445, com parada planejada de aproximadamente 2 horas. Guest Pass previsto para cobrir o ônibus regional.',3 FROM cities WHERE name='Dobbiaco';
INSERT INTO attractions(city_id,name,description,sort_order)
SELECT id,'Punto Panoramico Tre Cime','Vista das Tre Cime pela linha 445. Planejamento prevê observar na passagem, sem usar a linha especial 444.',4 FROM cities WHERE name='Dobbiaco';
INSERT INTO attractions(city_id,name,description,sort_order)
SELECT id,'Lago di Braies','10/10: linha 442 a partir de Dobbiaco. Planejamento sugere cerca de 3h30 no lago e caminhada conforme as condições da trilha. Confirmar horários.',5 FROM cities WHERE name='Dobbiaco';
INSERT INTO attractions(city_id,name,description,sort_order)
SELECT id,'Lago di Dobbiaco','10/10 à tarde: passeio leve de aproximadamente 2 horas. A linha 445 conecta a estação de Dobbiaco ao lago.',6 FROM cities WHERE name='Dobbiaco';

INSERT INTO attractions(city_id,name,description,sort_order)
SELECT id,'Lago di Carezza','11/10: passeio principal do dia. Linha regional 180 desde Bolzano Autostazione; objetivo de embarque entre 11:30 e 12:30. Confirmar horário de domingo. Guest Pass previsto até 23:59 do dia de check-out.',1 FROM cities WHERE name='Bolzano';
INSERT INTO attractions(city_id,name,description,sort_order)
SELECT id,'Centro histórico de Bolzano','Rota a pé: Piazza Walther, Duomo di Bolzano, Via dei Portici e Piazza delle Erbe.',2 FROM cities WHERE name='Bolzano';

INSERT INTO attractions(city_id,name,description,sort_order)
SELECT id,'Santuario della Madonna di Tirano','12/10: visita ao interior, arquitetura, exterior e entorno. Planejado para o fim da tarde, após o check-in.',1 FROM cities WHERE name='Tirano';
INSERT INTO attractions(city_id,name,description,sort_order)
SELECT id,'Centro histórico de Tirano','Rota a pé por Piazza Cavour, ruas históricas e Piazza Marinoni. Comprar lanches para o dia seguinte.',2 FROM cities WHERE name='Tirano';

INSERT INTO attractions(city_id,name,description,sort_order)
SELECT id,'Rota Bernina e Albula','13/10: Tirano → Alp Grüm → St. Moritz → Chur → Sargans → Flums. Saver Day Pass já comprado. Priorizar trem regional e a rota cênica via Albula, não a alternativa mais rápida via Vereina.',1 FROM cities WHERE name='Flums';
INSERT INTO attractions(city_id,name,description,sort_order)
SELECT id,'Alp Grüm','Parada recomendada de 30–45 minutos para ver o glaciar de Palü e as montanhas. Conferir a combinação exata de trens regionais.',2 FROM cities WHERE name='Flums';
INSERT INTO attractions(city_id,name,description,sort_order)
SELECT id,'St. Moritz','Parada de aproximadamente 1h–1h30: estação, lago, centro e Via Serlas. Almoço levado de Tirano.',3 FROM cities WHERE name='Flums';
INSERT INTO attractions(city_id,name,description,sort_order)
SELECT id,'Vaduz, Triesenberg e Malbun','14/10: trem Flums–Sargans pago separadamente; LIEmobil Day Ticket de todas as zonas previsto. Linha 12E para Vaduz e linha 21 para Triesenberg/Malbun. Inclui Städtle, Catedral de St. Florin, Parlamento e exterior do Schloss Vaduz.',4 FROM cities WHERE name='Flums';

INSERT INTO attractions(city_id,name,description,sort_order)
SELECT id,'Top of Innsbruck — Nordkette','15/10: Hungerburgbahn → Seegrube → Hafelekar. Planejamento considera ida e volta para 2 adultos e subida no início da tarde. Confirmar preço e clima antes da compra.',1 FROM cities WHERE name='Innsbruck';
INSERT INTO attractions(city_id,name,description,sort_order)
SELECT id,'Centro histórico de Innsbruck','Rota a pé: Golden Roof, Hofkirche (exterior), Maria-Theresien-Straße e Triumphpforte.',2 FROM cities WHERE name='Innsbruck';

INSERT INTO attractions(city_id,name,description,sort_order)
SELECT id,'Centro histórico de Ljubljana','Prešeren Square, Triple Bridge, rio Ljubljanica, Dragon Bridge, Mestni trg, Town Hall, Robba Fountain, Catedral de São Nicolau e Mercado Central.',1 FROM cities WHERE name='Ljubljana';
INSERT INTO attractions(city_id,name,description,sort_order)
SELECT id,'Lago Bled ao pôr do sol','17/10: ônibus Ljubljana ↔ Bled; caminhar pela margem, procurar pontos panorâmicos, provar kremšnita e escolher um bom ponto antes do pôr do sol. Confirmar especialmente o último ônibus de retorno.',2 FROM cities WHERE name='Ljubljana';

UPDATE attractions SET attraction_date='2026-10-08' WHERE name='Centro histórico de Dobbiaco';
UPDATE attractions SET attraction_date='2026-10-09' WHERE name IN ('Lago di Misurina','Lago di Landro','Punto Panoramico Tre Cime');
UPDATE attractions SET attraction_date='2026-10-10' WHERE name IN ('Lago di Braies','Lago di Dobbiaco');
UPDATE attractions SET attraction_date='2026-10-11' WHERE name IN ('Lago di Carezza','Centro histórico de Bolzano');
UPDATE attractions SET attraction_date='2026-10-12' WHERE name IN ('Santuario della Madonna di Tirano','Centro histórico de Tirano');
UPDATE attractions SET attraction_date='2026-10-13' WHERE name IN ('Rota Bernina e Albula','Alp Grüm','St. Moritz');
UPDATE attractions SET attraction_date='2026-10-14' WHERE name='Vaduz, Triesenberg e Malbun';
UPDATE attractions SET attraction_date='2026-10-15' WHERE name IN ('Top of Innsbruck — Nordkette','Centro histórico de Innsbruck');
UPDATE attractions SET attraction_date='2026-10-16' WHERE name='Centro histórico de Ljubljana';
UPDATE attractions SET attraction_date='2026-10-17' WHERE name='Lago Bled ao pôr do sol';

-- Orçamentos diários do casal, em dólares, marcados como previsão.
INSERT INTO expenses(city_id,name,category,amount_usd,expense_date,status,notes) SELECT id,'Orçamento do dia 08','Outros',70,'2026-10-08','planned','Chegada, alimentação e compras; FlixBus já comprado fora deste valor.' FROM cities WHERE name='Dobbiaco';
INSERT INTO expenses(city_id,name,category,amount_usd,expense_date,status,notes) SELECT id,'Orçamento do dia 09','Outros',55,'2026-10-09','planned','Misurina e Lago di Landro; transportes regionais previstos com Guest Pass.' FROM cities WHERE name='Dobbiaco';
INSERT INTO expenses(city_id,name,category,amount_usd,expense_date,status,notes) SELECT id,'Orçamento do dia 10','Outros',40,'2026-10-10','planned','Braies e Lago di Dobbiaco.' FROM cities WHERE name='Dobbiaco';
INSERT INTO expenses(city_id,name,category,amount_usd,expense_date,status,notes) SELECT id,'Orçamento do dia 11','Outros',75,'2026-10-11','planned','Bolzano e Carezza; hospedagem não incluída.' FROM cities WHERE name='Bolzano';
INSERT INTO expenses(city_id,name,category,amount_usd,expense_date,status,notes) SELECT id,'Orçamento do dia 12','Outros',85,'2026-10-12','planned','Chegada e passeio em Tirano; passagens já compradas fora do valor.' FROM cities WHERE name='Tirano';
INSERT INTO expenses(city_id,name,category,amount_usd,expense_date,status,notes) SELECT id,'Orçamento local do dia 15','Outros',187,'2026-10-15','planned','Alimentação e Top of Innsbruck; trem em reais não convertido.' FROM cities WHERE name='Innsbruck';
INSERT INTO expenses(city_id,name,category,amount_usd,expense_date,status,notes) SELECT id,'Orçamento local do dia 16','Outros',75,'2026-10-16','planned','Despesas locais; trem em reais e hospedagem já paga não incluídos.' FROM cities WHERE name='Ljubljana';
INSERT INTO expenses(city_id,name,category,amount_usd,expense_date,status,notes) SELECT id,'Orçamento do dia 17','Outros',140,'2026-10-17','planned','Ljubljana e Bled; valor de segurança para o casal.' FROM cities WHERE name='Ljubljana';
