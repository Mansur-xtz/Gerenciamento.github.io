<?php
// ─── Configuração central da aplicação ─────────────────────────────────────
define('DB_PATH', __DIR__ . '/banco.db');
define('APP_NAME', 'Ape Stage Solutions');

// Inicia sessão de forma segura (se ainda não iniciada)
function iniciarSessao(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Retorna o ID do usuário logado ou null
function usuarioLogado(): ?int {
    iniciarSessao();
    return isset($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : null;
}

// Redireciona para o login se o usuário não estiver autenticado
function exigirLogin(): void {
    if (usuarioLogado() === null) {
        header('Location: login.php?erro=nao_autenticado');
        exit;
    }
}

// Gera e valida token CSRF
function csrfToken(): string {
    iniciarSessao();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validarCsrf(): void {
    iniciarSessao();
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('<p class="notification is-danger">Requisição inválida. Por favor, tente novamente.</p>');
    }
}
