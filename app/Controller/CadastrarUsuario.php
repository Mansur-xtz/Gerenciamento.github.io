<?php
require_once __DIR__ . '/../Model/UserBanco.php';

class CadastrarUsuario {
    public function retornar(): void {
        iniciarSessao();

        $usuario = trim($_POST['usuario'] ?? '');
        $senha   = $_POST['senha'] ?? '';
        $confirmacao = $_POST['confirmacao_senha'] ?? '';

        if (empty($usuario) || empty($senha)) {
            $_SESSION['mensagem_erro'] = 'Usuário e senha são obrigatórios.';
            header('Location: /registro.php');
            exit;
        }

        if (strlen($senha) < 6) {
            $_SESSION['mensagem_erro'] = 'A senha deve ter pelo menos 6 caracteres.';
            header('Location: /registro.php');
            exit;
        }

        if ($senha !== $confirmacao) {
            $_SESSION['mensagem_erro'] = 'As senhas não coincidem.';
            header('Location: /registro.php');
            exit;
        }

        try {
            (new UserBanco())->cadastrarUsuario($usuario, $senha, true);
            $_SESSION['mensagem_sucesso'] = 'Conta criada com sucesso! Faça login para continuar.';
            header('Location: /login.php');
            exit;
        } catch (Exception $e) {
            $_SESSION['mensagem_erro'] = $e->getMessage();
            header('Location: /registro.php');
            exit;
        }
    }
}
