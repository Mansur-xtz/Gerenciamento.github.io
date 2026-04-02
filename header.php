<?php
require_once __DIR__ . '/config.php';
iniciarSessao();
$usuarioAtual = usuarioLogado();
$nomeUsuario  = '';
if ($usuarioAtual) {
    require_once __DIR__ . '/app/Model/UserBanco.php';
    $dadosUsuario = (new UserBanco())->buscarPorId($usuarioAtual);
    $nomeUsuario  = $dadosUsuario['nome'] ?? '';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.2/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary: #1a1a2e;
            --primary-light: #16213e;
            --accent: #e94560;
            --accent-hover: #c73652;
        }
        body { background-color: #f5f7fa; font-family: 'Segoe UI', sans-serif; }
        .navbar { background: linear-gradient(135deg, var(--primary), var(--primary-light)); box-shadow: 0 2px 12px rgba(0,0,0,0.3); }
        .navbar-brand .navbar-item { color: #fff; font-size: 1.2rem; font-weight: 700; letter-spacing: 0.5px; }
        .navbar-item, .navbar-link { color: rgba(255,255,255,0.85) !important; transition: color 0.2s; }
        .navbar-item:hover, .navbar-link:hover { color: #fff !important; background: rgba(255,255,255,0.1) !important; }
        .navbar-item.has-text-danger { color: #ff6b6b !important; }
        .tag.is-accent { background: var(--accent); color: #fff; }
        /* Fechar notificações */
        .notification .delete { float: right; }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Fechar notificações
            document.querySelectorAll('.notification .delete').forEach(btn => {
                btn.addEventListener('click', () => btn.closest('.notification').remove());
            });
            // Burger menu
            const burger = document.querySelector('.navbar-burger');
            if (burger) {
                burger.addEventListener('click', () => {
                    burger.classList.toggle('is-active');
                    document.getElementById(burger.dataset.target).classList.toggle('is-active');
                });
            }
        });
    </script>
</head>
<body>
<nav class="navbar" role="navigation">
    <div class="navbar-brand">
        <a class="navbar-item" href="<?= $usuarioAtual ? 'conteudo.php' : 'inicio.php' ?>">
            <span class="icon mr-2"><i class="fas fa-graduation-cap"></i></span>
            <?= APP_NAME ?>
        </a>
        <a role="button" class="navbar-burger" data-target="navMenu" aria-label="menu">
            <span></span><span></span><span></span>
        </a>
    </div>
    <div id="navMenu" class="navbar-menu">
        <div class="navbar-end">
            <?php if ($usuarioAtual): ?>
                <a class="navbar-item" href="conteudo.php"><span class="icon"><i class="fas fa-home"></i></span>&nbsp;Dashboard</a>
                <a class="navbar-item" href="listar_estagios.php"><span class="icon"><i class="fas fa-briefcase"></i></span>&nbsp;Estágios</a>
                <a class="navbar-item" href="listar_estagiarios.php"><span class="icon"><i class="fas fa-users"></i></span>&nbsp;Estagiários</a>
                <div class="navbar-item has-dropdown is-hoverable">
                    <a class="navbar-link">
                        <span class="icon"><i class="fas fa-user-circle"></i></span>&nbsp;<?= htmlspecialchars($nomeUsuario) ?>
                    </a>
                    <div class="navbar-dropdown is-right" style="background:#1a1a2e; border:none;">
                        <a class="navbar-item" href="logout.php" style="color:#ff6b6b;">
                            <span class="icon"><i class="fas fa-sign-out-alt"></i></span>&nbsp;Sair
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a class="navbar-item" href="login.php"><span class="icon"><i class="fas fa-sign-in-alt"></i></span>&nbsp;Login</a>
                <a class="navbar-item" href="registro.php"><span class="icon"><i class="fas fa-user-plus"></i></span>&nbsp;Registrar</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
