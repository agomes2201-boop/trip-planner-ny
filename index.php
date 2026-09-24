<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle_attraction') {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(403); exit('Sessão expirada. Atualize a página.'); }
    $attractionId = filter_input(INPUT_POST, 'attraction_id', FILTER_VALIDATE_INT);
    $cityId = filter_input(INPUT_POST, 'city_id', FILTER_VALIDATE_INT);
    if ($attractionId && $cityId) {
        $stmt = $pdo->prepare('UPDATE attractions SET completed = IF(completed=1,0,1) WHERE id=? AND city_id=?');
        $stmt->execute([$attractionId, $cityId]);
    }
    header('Location: ?cidade=' . (int)$cityId . '#passeios'); exit;
}
$_SESSION['csrf'] = bin2hex(random_bytes(24));

$cities = $pdo->query('SELECT * FROM cities ORDER BY sort_order, id')->fetchAll();
$selectedId = filter_input(INPUT_GET, 'cidade', FILTER_VALIDATE_INT) ?: ($cities[0]['id'] ?? 0);
$selected = null;
foreach ($cities as $city) {
    if ((int)$city['id'] === (int)$selectedId) { $selected = $city; break; }
}
$selected ??= $cities[0] ?? null;
$attractions = [];
$expenses = [];
$expenseSummary = [];
$tripTotal = 0.0;
if ($selected) {
    $stmt = $pdo->prepare('SELECT * FROM attractions WHERE city_id = ? ORDER BY sort_order, name');
    $stmt->execute([$selected['id']]);
    $attractions = $stmt->fetchAll();
    $stmt = $pdo->prepare('SELECT * FROM expenses WHERE city_id = ? ORDER BY expense_date DESC, id DESC');
    $stmt->execute([$selected['id']]);
    $expenses = $stmt->fetchAll();
}
$tripTotal = (float)$pdo->query("SELECT COALESCE(SUM(amount_usd), 0) FROM expenses WHERE status='paid'")->fetchColumn();
$plannedTotal = (float)$pdo->query("SELECT COALESCE(SUM(amount_usd), 0) FROM expenses WHERE status='planned'")->fetchColumn();
$expenseSummary = $pdo->query("SELECT category, SUM(amount_usd) total FROM expenses WHERE status='paid' GROUP BY category ORDER BY total DESC")->fetchAll();

function h(?string $value): string { return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'); }
function dateBr(?string $date): string { return $date ? date('d/m/Y', strtotime($date)) : 'A definir'; }
function linkButton(?string $url, string $label): string {
    if (!$url) return '';
    return '<a class="map-link" href="' . h($url) . '" target="_blank" rel="noopener">' . h($label) . ' <span aria-hidden="true">↗</span></a>';
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#092b3c">
  <title>Planejador de viagem · NY 2027</title>
  <link rel="stylesheet" href="assets/style.css?v=5">
  <link rel="stylesheet" href="assets/features.css">
  <link rel="stylesheet" href="assets/v3.css">
</head>
<body>
<header class="topbar" style="
  background-image: linear-gradient(rgba(9, 43, 60, 0.72), rgba(9, 43, 60, 0.58)), url('assets/header-viagem-europa.png');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
">
  <div>
    <p class="eyebrow">Planejador de viagem</p>
    <h1>NEW YORK · Junho 2027</h1>
  </div>
  <div class="countdown" aria-label="Contagem regressiva">
    <strong id="days">—</strong><span>dias para a viagem</span>
  </div>
</header>

<main class="layout">
  <nav class="city-nav" aria-label="Cidades do roteiro">
    <?php foreach ($cities as $index => $city): ?>
      <a class="city-card <?= $selected && $city['id'] === $selected['id'] ? 'active' : '' ?>" href="?cidade=<?= (int)$city['id'] ?>">
        <span class="city-number"><?= str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
        <strong><?= h($city['name']) ?></strong>
        <small><?= h($city['country']) ?></small>
        <?php if ($city['arrival_date'] || $city['departure_date']): ?><small class="city-dates"><?= dateBr($city['arrival_date']) ?> → <?= dateBr($city['departure_date']) ?></small><?php endif; ?>
      </a>
    <?php endforeach; ?>
  </nav>

  <?php if ($selected): ?>
  <div class="section-tabs" aria-label="Seções de <?= h($selected['name']) ?>">
    <a href="#trajeto">Trajeto</a><a href="#passeios">Passeios <span><?= count($attractions) ?></span></a><a href="#gastos">Gastos</a>
  </div>
  <article class="detail-card" id="trajeto">
    <div class="detail-heading">
      <div><p class="eyebrow">TRAJETO E HOSPEDAGEM</p><h2><?= h($selected['name']) ?></h2></div>
      <div class="city-period"><span>Chegada <strong><?= dateBr($selected['arrival_date']) ?></strong></span><span>Partida <strong><?= dateBr($selected['departure_date']) ?></strong></span><?php if ($selected['next_city']): ?><span class="next-badge">Próxima: <?= h($selected['next_city']) ?></span><?php endif; ?></div>
    </div>

    <?php if ($selected['status_note']): ?><div class="notice"><?= nl2br(h($selected['status_note'])) ?></div><?php endif; ?>

    <div class="info-grid">
      <section>
        <h3><span>⌂</span> Hospedagem</h3>
        <p class="primary"><?= h($selected['lodging_name']) ?></p>
        <p><?= h($selected['lodging_address']) ?: 'A definir' ?></p>
        <?= linkButton($selected['lodging_url'], 'Abrir hospedagem no mapa') ?>
      </section>
      <section>
        <h3><span>↓</span> Estação de chegada</h3>
        <p class="primary"><?= h($selected['arrival_station']) ?: 'A definir' ?></p>
        <?php if ($selected['arrival_distance']): ?><p><?= h($selected['arrival_distance']) ?></p><?php endif; ?>
        <?= linkButton($selected['arrival_url'], 'Ver estação') ?>
        <?= linkButton($selected['arrival_route_url'], 'Ver rota até a hospedagem') ?>
      </section>
      <section>
        <h3><span>→</span> Saída para o próximo destino</h3>
        <p class="primary"><?= h($selected['departure_station']) ?: 'A definir' ?></p>
        <?php if ($selected['departure_distance']): ?><p><?= h($selected['departure_distance']) ?></p><?php endif; ?>
        <?= linkButton($selected['departure_url'], 'Ver estação de saída') ?>
        <?= linkButton($selected['departure_route_url'], 'Ver rota até a estação') ?>
      </section>
      <section>
        <h3><span>⌁</span> Próximo trajeto</h3>
        <p class="primary"><?= h($selected['route_title']) ?: 'A definir' ?></p>
        <?php if ($selected['route_details']): ?><p><?= nl2br(h($selected['route_details'])) ?></p><?php endif; ?>
        <?= linkButton($selected['route_url'], 'Abrir trajeto completo') ?>
      </section>
    </div>
  </article>

  <section class="content-card" id="passeios">
    <div class="section-heading"><div><p class="eyebrow">O QUE VISITAR</p><h2>Passeios em <?= h($selected['name']) ?></h2></div><a class="action-link" href="passeios.php?cidade=<?= (int)$selected['id'] ?>">Gerenciar passeios</a></div>
    <?php if ($attractions): ?><div class="attraction-grid">
      <?php foreach ($attractions as $item): ?><article class="attraction-card <?= $item['completed'] ? 'completed' : '' ?>">
        <form class="check-form" method="post"><input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>"><input type="hidden" name="action" value="toggle_attraction"><input type="hidden" name="city_id" value="<?= (int)$selected['id'] ?>"><input type="hidden" name="attraction_id" value="<?= (int)$item['id'] ?>"><label><input type="checkbox" <?= $item['completed'] ? 'checked' : '' ?> onchange="this.form.submit()"><span><?= $item['completed'] ? 'Realizado' : 'Marcar como realizado' ?></span></label></form>
        <span class="pin">◎</span><h3><?= h($item['name']) ?></h3>
        <div class="attraction-meta"><span>📅 <?= dateBr($item['attraction_date']) ?></span><?php if ((float)$item['amount_usd'] > 0): ?><strong>$ <?= number_format((float)$item['amount_usd'], 2, '.', ',') ?></strong><?php endif; ?></div>
        <p><?= nl2br(h($item['description'])) ?></p>
        <?= linkButton($item['maps_url'], 'Abrir trajeto no Maps') ?>
      </article><?php endforeach; ?>
    </div><?php else: ?><div class="empty-state"><strong>Nenhum passeio cadastrado.</strong><p>Adicione pontos turísticos, dicas, horários e informações de transporte.</p></div><?php endif; ?>
  </section>

  <section class="content-card" id="gastos">
    <div class="section-heading"><div><p class="eyebrow">CONTROLE FINANCEIRO</p><h2>Gastos da viagem</h2></div><a class="action-link" href="gastos.php?cidade=<?= (int)$selected['id'] ?>">Registrar gasto</a></div>
    <div class="expense-overview">
      <div class="total-box"><small>Total pago</small><strong>$ <?= number_format($tripTotal, 2, '.', ',') ?></strong></div>
      <div class="total-box planned"><small>Orçamento previsto</small><strong>$ <?= number_format($plannedTotal, 2, '.', ',') ?></strong></div>
      <div class="category-chips"><?php foreach ($expenseSummary as $summary): ?><span><?= h($summary['category']) ?><strong>$ <?= number_format((float)$summary['total'], 2, '.', ',') ?></strong></span><?php endforeach; ?></div>
    </div>
    <?php if ($expenses): ?><div class="expense-table-wrap"><table class="expense-table"><thead><tr><th>Gasto</th><th>Tipo</th><th>Data</th><th>Valor</th></tr></thead><tbody>
      <?php foreach ($expenses as $expense): ?><tr><td><?= h($expense['name']) ?> <small class="status <?= h($expense['status']) ?>"><?= $expense['status']==='paid'?'Pago':'Previsto' ?></small></td><td><span class="category-label"><?= h($expense['category']) ?></span></td><td><?= date('d/m/Y', strtotime($expense['expense_date'])) ?></td><td class="money">$ <?= number_format((float)$expense['amount_usd'], 2, '.', ',') ?></td></tr><?php endforeach; ?>
    </tbody></table></div><?php else: ?><div class="empty-state"><strong>Nenhum gasto em <?= h($selected['name']) ?>.</strong><p>Os valores registrados serão somados automaticamente.</p></div><?php endif; ?>
  </section>
  <?php else: ?><div class="empty">Nenhuma cidade cadastrada.</div><?php endif; ?>
</main>

<footer><span>Início da viagem: 09/06/2027</span><nav><a href="admin.php">Cidades e trajetos</a><a href="passeios.php">Passeios</a><a href="gastos.php">Registrar gasto</a><a href="resumo-gastos.php">Resumo geral</a></nav></footer>
<script src="assets/app.js?v=20270609"></script>
</body>
</html>
