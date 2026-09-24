<?php
declare(strict_types=1);
require __DIR__ . '/db.php';
function h(?string $v): string { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
$totals = $pdo->query("SELECT status, COALESCE(SUM(amount_usd),0) total FROM expenses GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
$byCity = $pdo->query("SELECT c.name, SUM(CASE WHEN e.status='paid' THEN e.amount_usd ELSE 0 END) paid, SUM(CASE WHEN e.status='planned' THEN e.amount_usd ELSE 0 END) planned FROM cities c LEFT JOIN expenses e ON e.city_id=c.id GROUP BY c.id,c.name HAVING paid>0 OR planned>0 ORDER BY c.sort_order")->fetchAll();
$byCategory = $pdo->query("SELECT category, SUM(CASE WHEN status='paid' THEN amount_usd ELSE 0 END) paid, SUM(CASE WHEN status='planned' THEN amount_usd ELSE 0 END) planned FROM expenses GROUP BY category ORDER BY paid DESC,planned DESC")->fetchAll();
$recent = $pdo->query("SELECT e.*,c.name city_name FROM expenses e JOIN cities c ON c.id=e.city_id ORDER BY expense_date DESC,e.id DESC LIMIT 50")->fetchAll();
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Resumo geral de gastos</title><link rel="stylesheet" href="assets/style.css"><link rel="stylesheet" href="assets/features.css"></head><body>
<header class="topbar compact"><div><p class="eyebrow">VISÃO FINANCEIRA</p><h1>Gastos da viagem</h1></div><a class="header-link" href="index.php">← Voltar ao roteiro</a></header>
<main class="summary-layout">
  <section class="summary-hero"><div><small>Total efetivamente pago</small><strong>$ <?=number_format((float)($totals['paid']??0),2,'.',',')?></strong></div><div class="planned"><small>Orçamento previsto</small><strong>$ <?=number_format((float)($totals['planned']??0),2,'.',',')?></strong></div><a class="save" href="gastos.php">+ Registrar gasto</a></section>
  <div class="summary-grid"><section class="content-card"><h2>Por cidade</h2><div class="bars"><?php foreach($byCity as $row):?><div class="bar-row"><strong><?=h($row['name'])?></strong><span>Pago: $ <?=number_format((float)$row['paid'],2,'.',',')?></span><span>Previsto: $ <?=number_format((float)$row['planned'],2,'.',',')?></span></div><?php endforeach;?></div></section>
  <section class="content-card"><h2>Por categoria</h2><div class="bars"><?php foreach($byCategory as $row):?><div class="bar-row"><strong><?=h($row['category'])?></strong><span>Pago: $ <?=number_format((float)$row['paid'],2,'.',',')?></span><span>Previsto: $ <?=number_format((float)$row['planned'],2,'.',',')?></span></div><?php endforeach;?></div></section></div>
  <section class="content-card"><div class="section-heading"><h2>Lançamentos</h2><a class="action-link" href="gastos.php">Gerenciar</a></div><div class="expense-table-wrap"><table class="expense-table"><thead><tr><th>Data</th><th>Nome</th><th>Cidade</th><th>Categoria</th><th>Situação</th><th>Valor</th></tr></thead><tbody><?php foreach($recent as $row):?><tr><td><?=date('d/m/Y',strtotime($row['expense_date']))?></td><td><?=h($row['name'])?></td><td><?=h($row['city_name'])?></td><td><?=h($row['category'])?></td><td><small class="status <?=h($row['status'])?>"><?=$row['status']==='paid'?'Pago':'Previsto'?></small></td><td class="money">$ <?=number_format((float)$row['amount_usd'],2,'.',',')?></td></tr><?php endforeach;?></tbody></table></div></section>
</main></body></html>
