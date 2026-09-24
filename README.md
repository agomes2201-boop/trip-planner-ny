# Planejador de viagem — Europa 2026 (v3)

Projeto em PHP 8+, MySQL e CSS/JavaScript puro, preparado para hospedagem compartilhada.

## Instalação na Hostinger

1. No hPanel, crie um banco MySQL e guarde nome, usuário e senha.
2. Abra o phpMyAdmin, selecione o banco e importe `database.sql`.
3. Renomeie `config.example.php` para `config.php` e preencha os dados do banco.
4. Envie todos os arquivos da pasta para `public_html` (ou uma subpasta).
5. Abra o endereço do site. Os links do rodapé permitem administrar cidades, passeios e gastos.

## Atualizando a primeira versão

Se o projeto anterior já estiver instalado, preserve o banco e importe apenas `upgrade-v2.sql`. Depois substitua os arquivos do site. Não importe `database.sql` novamente.

Se a versão 2 já estiver instalada, importe também `upgrade-v3.sql`. Essa atualização adiciona datas das cidades e passeios, checklist de realização e integração automática do valor do passeio com a página de gastos.

## Dados extraídos do planejamento do WhatsApp

Depois de criar ou atualizar o banco, importe `seed-planejamento-whatsapp.sql` uma única vez. Ele adiciona cidades, passeios, dicas, linhas de ônibus, horários indicativos e estimativas diárias em dólar. Os valores entram como **Previstos**, não como pagamentos realizados.

## Novidades

- Carrossel de cidades preservado
- Passeios e pontos turísticos por cidade
- Descrição livre para ônibus, horários e dicas
- Registro de gastos por cidade, categoria, data e situação (previsto ou pago)
- Página geral com total pago, orçamento previsto e agrupamentos

## Segurança

O painel de edição não possui login nesta primeira versão. Se o site ficar público, proteja `admin.php` com senha pelo hPanel (Diretórios protegidos por senha) antes de divulgar o endereço.

## Requisitos

- PHP 8.0 ou superior
- Extensão PDO MySQL
- MySQL 5.7+ ou MariaDB equivalente
