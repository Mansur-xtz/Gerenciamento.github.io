<?php
require_once __DIR__ . '/../../config.php';
exigirLogin();
validarCsrf();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /registro_estagiario.php');
    exit;
}

$nome            = trim($_POST['nome'] ?? '');
$cpf_cnpj        = preg_replace('/\D/', '', $_POST['cpf_cnpj'] ?? '');
$telefone        = preg_replace('/\D/', '', $_POST['telefone'] ?? '');
$data_nascimento = $_POST['data_nascimento'] ?? '';
$estagio_id      = (int) ($_POST['estagio_id'] ?? 0);
$duracao         = (int) ($_POST['duracao'] ?? 0);
$data_inicio     = $_POST['data_inicio'] ?? '';

// Validações
$erros = [];
if (empty($nome))            $erros[] = 'Nome é obrigatório.';
if (!in_array(strlen($cpf_cnpj), [11, 14])) $erros[] = 'CPF (11 dígitos) ou CNPJ (14 dígitos) inválido.';
if (strlen($telefone) < 10)  $erros[] = 'Telefone inválido (mínimo 10 dígitos com DDD).';
if (empty($data_nascimento)) $erros[] = 'Data de nascimento obrigatória.';
if ($estagio_id <= 0)        $erros[] = 'Selecione um estágio válido.';
if ($duracao <= 0)           $erros[] = 'Duração deve ser maior que zero.';

if (!empty($erros)) {
    iniciarSessao();
    $_SESSION['mensagem_erro'] = implode(' ', $erros);
    header('Location: /registro_estagiario.php');
    exit;
}

try {
    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='estagiarios'");
    if (!$query->fetch()) {
        throw new Exception("Tabela 'estagiarios' não encontrada.");
    }

    $stmt = $pdo->prepare(
        'INSERT INTO estagiarios (nome, cpf_cnpj, telefone, data_nascimento, estagio_id, duracao, data_inicio)
         VALUES (:nome, :cpf_cnpj, :telefone, :data_nascimento, :estagio_id, :duracao, :data_inicio)'
    );
    $stmt->execute([
        ':nome'            => $nome,
        ':cpf_cnpj'        => $cpf_cnpj,
        ':telefone'        => $telefone,
        ':data_nascimento' => $data_nascimento,
        ':estagio_id'      => $estagio_id,
        ':duracao'         => $duracao,
        ':data_inicio'     => $data_inicio,
    ]);

    header('Location: /listar_estagiarios.php?message=sucesso');
    exit;
} catch (Exception $e) {
    iniciarSessao();
    $_SESSION['mensagem_erro'] = 'Erro ao cadastrar estagiário: ' . $e->getMessage();
    header('Location: /registro_estagiario.php');
    exit;
}
