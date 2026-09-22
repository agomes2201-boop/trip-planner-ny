<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/db.php';
function h(?string $v): string { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
$cities = $pdo->query('SELECT id, name FROM cities ORDER BY sort_order, id')->fetchAll();
$cityId = filter_input(INPUT_GET, 'cidade', FILTER_VALIDATE_INT) ?: filter_input(INPUT_POST, 'city_id', FILTER_VALIDATE_INT) ?: ($cities[0]['id'] ?? 0);
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(403); exit('Sessão expirada.'); }
    $action = $_POST['action'] ?? 'save';
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if ($action === 'delete' && $id) {
        $pdo->prepare('DELETE FROM attractions WHERE id = ?')->execute([$id]); $message = 'Passeio excluído.';
    } else {
        $amount = (float)str_replace(',', '.', trim((string)($_POST['amount_eur'] ?? '0')));
        $date = ($_POST['attraction_date'] ?? '') ?: null;
        $data = [':city_id'=>$cityId, ':name'=>trim((string)($_POST['name'] ?? '')), ':description'=>trim((string)($_POST['description'] ?? '')), ':maps_url'=>trim((string)($_POST['maps_url'] ?? '')), ':attraction_date'=>$date, ':amount_eur'=>$amount, ':sort_order'=>(int)($_POST['sort_order'] ?? 0)];
        $pdo->beginTransaction();
        if ($id) { $data[':id']=$id; $pdo->prepare('UPDATE attractions SET city_id=:city_id,name=:name,description=:description,maps_url=:maps_url,attraction_date=:attraction_date,amount_eur=:amount_eur,sort_order=:sort_order WHERE id=:id')->execute($data); }
        else { $pdo->prepare('INSERT INTO attractions(city_id,name,description,maps_url,attraction_date,amount_eur,sort_order) VALUES(:city_id,:name,:description,:maps_url,:attraction_date,:amount_eur,:sort_order)')->execute($data); $id=(int)$pdo->lastInsertId(); }
        if ($amount > 0) {
            $stmt=$pdo->prepare("INSERT INTO expenses(city_id,name,category,amount_eur,expense_date,status,notes,attraction_id) VALUES(?,?,'Passeios e ingressos',?,?,'planned','Gerado automaticamente pelo cadastro de passeios.',?) ON DUPLICATE KEY UPDATE city_id=VALUES(city_id),name=VALUES(name),amount_eur=VALUES(amount_eur),expense_date=VALUES(expense_date)");
            $stmt->execute([$cityId,'Passeio: '.$data[':name'],$amount,$date ?: date('Y-m-d'),$id]);
        } else { $pdo->prepare('DELETE FROM expenses WHERE attraction_id=?')->execute([$id]); }
        $pdo->commit();
        $message = 'Passeio salvo.';
    }
}
$_SESSION['csrf'] = bin2hex(random_bytes(24));
$stmt=$pdo->prepare('SELECT * FROM attractions WHERE city_id=? ORDER BY sort_order,name'); $stmt->execute([$cityId]); $items=$stmt->fetchAll();
$editId=filter_input(INPUT_GET,'editar',FILTER_VALIDATE_INT); $edit=null; foreach($items as $item) if((int)$item['id']===(int)$editId)$edit=$item;
$edit ??= ['id'=>'','city_id'=>$cityId,'name'=>'','description'=>'','maps_url'=>'','attraction_date'=>'','amount_eur'=>'','completed'=>0,'sort_order'=>0];
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Passeios</title><link rel="stylesheet" href="assets/style.css"><link rel="stylesheet" href="assets/features.css"><link rel="stylesheet" href="assets/v3.css"></head><body>
<header class="topbar compact"><div><p class="eyebrow">PLANEJADOR DE VIAGEM</p><h1>Passeios e pontos turísticos</h1></div><a class="header-link" href="index.php?cidade=<?= (int)$cityId ?>">← Voltar</a></header>
<main class="manage-layout"><section class="form-card">
<?php if($message):?><div class="success"><?=h($message)?></div><?php endif;?>
<form class="city-filter" method="get"><label>Cidade<select name="cidade" onchange="this.form.submit()"><?php foreach($cities as $city):?><option value="<?=$city['id']?>" <?=$city['id']==$cityId?'selected':''?>><?=h($city['name'])?></option><?php endforeach;?></select></label></form>
<h2><?= $edit['id']?'Editar passeio':'Novo passeio' ?></h2><form method="post"><input type="hidden" name="csrf" value="<?=h($_SESSION['csrf'])?>"><input type="hidden" name="id" value="<?=h((string)$edit['id'])?>"><input type="hidden" name="city_id" value="<?=(int)$cityId?>">
<div class="form-grid"><label class="wide">Nome<input required name="name" value="<?=h($edit['name'])?>"></label><label>Data do passeio<input type="date" name="attraction_date" value="<?=h($edit['attraction_date'])?>"></label><label>Valor em euro<input inputmode="decimal" name="amount_eur" placeholder="0,00" value="<?=h((string)$edit['amount_eur'])?>"></label><label class="wide">Descrição, transporte e dicas<textarea name="description" rows="5"><?=h($edit['description'])?></textarea></label><label class="wide">Link do trajeto no Maps<input type="url" name="maps_url" value="<?=h($edit['maps_url'])?>"></label><label>Ordem<input type="number" name="sort_order" value="<?=h((string)$edit['sort_order'])?>"></label></div><p class="form-hint">Ao informar um valor, o passeio aparecerá automaticamente nos gastos como “Previsto”.</p><button class="save">Salvar passeio</button></form>
</section><aside class="records-panel"><h2>Passeios cadastrados</h2><?php foreach($items as $item):?><article><div><strong><?=h($item['name'])?></strong><p><?= $item['attraction_date']?date('d/m/Y',strtotime($item['attraction_date'])):'Sem data' ?> · € <?=number_format((float)$item['amount_eur'],2,',','.')?> · <?=$item['completed']?'Realizado':'Pendente'?></p><p><?=h(mb_strimwidth($item['description'],0,100,'…'))?></p></div><div class="record-actions"><a href="?cidade=<?=$cityId?>&editar=<?=$item['id']?>">Editar</a><form method="post" onsubmit="return confirm('Excluir este passeio?')"><input type="hidden" name="csrf" value="<?=h($_SESSION['csrf'])?>"><input type="hidden" name="city_id" value="<?=$cityId?>"><input type="hidden" name="id" value="<?=$item['id']?>"><input type="hidden" name="action" value="delete"><button>Excluir</button></form></div></article><?php endforeach;?><?php if(!$items):?><p>Nenhum passeio nesta cidade.</p><?php endif;?></aside></main></body></html>
