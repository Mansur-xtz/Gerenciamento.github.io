<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/app/Model/UserBanco.php';
require_once __DIR__ . '/app/Controller/ValidarUsuario.php';
require_once __DIR__ . '/app/Controller/CadastrarUsuario.php';
require_once __DIR__ . '/app/Controller/ExcluirUsuario.php';

iniciarSessao();

$acao = $_GET['acao'] ?? null;

if (!$acao) {
    header('Location: inicio.php');
    exit;
}

switch ($acao) {
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: login.php'); exit; }
        $usuario = trim($_POST['usuario'] ?? '');
        $senha   = $_POST['senha'] ?? '';
        if (empty($usuario) || empty($senha)) {
            $_SESSION['mensagem_erro'] = 'Preencha usuário e senha.';
            header('Location: login.php');
            exit;
        }
        $usuarioId = (new ValidarUsuario())->retornar($usuario, $senha);
        if ($usuarioId) {
            $_SESSION['usuario_id'] = $usuarioId;
            header('Location: conteudo.php');
            exit;
        }
        $_SESSION['mensagem_erro'] = 'Usuário ou senha incorretos.';
        header('Location: login.php');
        exit;

    case 'cadastra':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: registro.php'); exit; }
        validarCsrf();
        (new CadastrarUsuario())->retornar();
        break;

    case 'excluir':
        exigirLogin();
        validarCsrf();
        (new ExcluirUsuario())->retornar();
        header('Location: listar_estagios.php');
        exit;

    default:
        header('Location: inicio.php');
        exit;
}
