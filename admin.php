<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/db.php';
function h(?string $v): string { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

$fields = ['name','country','arrival_date','departure_date','lodging_name','lodging_address','lodging_url','arrival_station','arrival_url','arrival_distance','arrival_route_url','departure_station','departure_url','departure_distance','departure_route_url','next_city','route_title','route_details','route_url','status_note'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(403); exit('Sessão expirada. Atualize a página.'); }
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $data = [];
    foreach ($fields as $field) { $data[$field] = trim((string)($_POST[$field] ?? '')); }
    $data['sort_order'] = (int)($_POST['sort_order'] ?? 0);
    if ($id) {
        $sets = implode(', ', array_map(fn($f) => "$f = :$f", array_merge($fields, ['sort_order'])));
        $data['id'] = $id;
        $pdo->prepare("UPDATE cities SET $sets WHERE id = :id")->execute($data);
        $message = 'Cidade atualizada com sucesso.';
    } else {
        $columns = array_merge($fields, ['sort_order']);
        $pdo->prepare('INSERT INTO cities (' . implode(',', $columns) . ') VALUES (:' . implode(',:', $columns) . ')')->execute($data);
        $message = 'Cidade adicionada com sucesso.';
    }
}

$_SESSION['csrf'] = bin2hex(random_bytes(24));
$cities = $pdo->query('SELECT * FROM cities ORDER BY sort_order, id')->fetchAll();
$editId = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT);
$edit = null;
foreach ($cities as $city) if ((int)$city['id'] === (int)$editId) $edit = $city;
$edit ??= array_fill_keys(array_merge(['id','sort_order'], $fields), '');
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Editar roteiro</title><link rel="stylesheet" href="assets/style.css"><link rel="stylesheet" href="assets/features.css"><link rel="stylesheet" href="assets/v3.css"></head>
<body><header class="topbar"><div><p class="eyebrow">PAINEL DE EDIÇÃO</p><h1>Atualizar roteiro</h1></div><a class="header-link" href="index.php">← Voltar ao roteiro</a></header>
<main class="admin-layout">
  <aside class="admin-list"><h2>Cidades</h2><?php foreach ($cities as $city): ?><a href="?editar=<?= (int)$city['id'] ?>"><?= h($city['name']) ?><small><?= h($city['country']) ?></small></a><?php endforeach; ?><a class="new-city" href="admin.php">+ Nova cidade</a></aside>
  <section class="form-card">
    <?php if ($message): ?><div class="success"><?= h($message) ?></div><?php endif; ?>
    <h2><?= $edit['id'] ? 'Editar ' . h($edit['name']) : 'Adicionar cidade' ?></h2>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>"><input type="hidden" name="id" value="<?= h((string)$edit['id']) ?>">
      <div class="form-grid">
        <label>Nome<input required name="name" value="<?= h($edit['name']) ?>"></label>
        <label>País<input name="country" value="<?= h($edit['country']) ?>"></label>
        <label>Ordem<input type="number" name="sort_order" value="<?= h((string)$edit['sort_order']) ?>"></label>
        <label>Próxima cidade<input name="next_city" value="<?= h($edit['next_city']) ?>"></label>
        <label>Data de chegada<input type="date" name="arrival_date" value="<?= h($edit['arrival_date']) ?>"></label>
        <label>Data de partida<input type="date" name="departure_date" value="<?= h($edit['departure_date']) ?>"></label>
        <label class="wide">Hospedagem<input name="lodging_name" value="<?= h($edit['lodging_name']) ?>"></label>
        <label class="wide">Endereço<input name="lodging_address" value="<?= h($edit['lodging_address']) ?>"></label>
        <label class="wide">Link da hospedagem<input type="url" name="lodging_url" value="<?= h($edit['lodging_url']) ?>"></label>
        <label>Estação de chegada<input name="arrival_station" value="<?= h($edit['arrival_station']) ?>"></label>
        <label>Distância até hospedagem<input name="arrival_distance" value="<?= h($edit['arrival_distance']) ?>"></label>
        <label class="wide">Link da estação de chegada<input type="url" name="arrival_url" value="<?= h($edit['arrival_url']) ?>"></label>
        <label class="wide">Link estação → hospedagem<input type="url" name="arrival_route_url" value="<?= h($edit['arrival_route_url']) ?>"></label>
        <label>Estação de saída<input name="departure_station" value="<?= h($edit['departure_station']) ?>"></label>
        <label>Distância até estação<input name="departure_distance" value="<?= h($edit['departure_distance']) ?>"></label>
        <label class="wide">Link da estação de saída<input type="url" name="departure_url" value="<?= h($edit['departure_url']) ?>"></label>
        <label class="wide">Link hospedagem → estação<input type="url" name="departure_route_url" value="<?= h($edit['departure_route_url']) ?>"></label>
        <label class="wide">Título do próximo trajeto<input name="route_title" value="<?= h($edit['route_title']) ?>"></label>
        <label class="wide">Detalhes do trajeto<textarea name="route_details" rows="3"><?= h($edit['route_details']) ?></textarea></label>
        <label class="wide">Link do trajeto completo<input type="url" name="route_url" value="<?= h($edit['route_url']) ?>"></label>
        <label class="wide">Observação<textarea name="status_note" rows="3"><?= h($edit['status_note']) ?></textarea></label>
      </div><button class="save" type="submit">Salvar alterações</button>
    </form>
  </section>
</main></body></html>
