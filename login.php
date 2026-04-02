<?php
require_once __DIR__ . '/config.php';
iniciarSessao();
if (usuarioLogado()) { header('Location: conteudo.php'); exit; }
$erro    = $_SESSION['mensagem_erro'] ?? null;    unset($_SESSION['mensagem_erro']);
$sucesso = $_SESSION['mensagem_sucesso'] ?? null; unset($_SESSION['mensagem_sucesso']);
if (($_GET['erro'] ?? '') === 'nao_autenticado') $erro = 'Faça login para acessar esta página.';
require __DIR__ . '/header.php';
?>
<section class="section" style="min-height:80vh; display:flex; align-items:center;">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-5">
                <div class="box" style="border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.12);">
                    <h1 class="title has-text-centered mb-5" style="color:#1a1a2e;">
                        <span class="icon"><i class="fas fa-lock"></i></span> Entrar
                    </h1>
                    <?php if ($erro): ?>
                        <div class="notification is-danger is-light"><button class="delete"></button><?= htmlspecialchars($erro) ?></div>
                    <?php endif; ?>
                    <?php if ($sucesso): ?>
                        <div class="notification is-success is-light"><button class="delete"></button><?= htmlspecialchars($sucesso) ?></div>
                    <?php endif; ?>
                    <form action="index.php?acao=login" method="POST">
                        <div class="field">
                            <label class="label">Usuário</label>
                            <div class="control has-icons-left">
                                <input class="input" type="text" name="usuario" placeholder="Seu usuário" required autofocus>
                                <span class="icon is-left"><i class="fas fa-user"></i></span>
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">Senha</label>
                            <div class="control has-icons-left">
                                <input class="input" type="password" name="senha" placeholder="Sua senha" required>
                                <span class="icon is-left"><i class="fas fa-key"></i></span>
                            </div>
                        </div>
                        <div class="field mt-5">
                            <button type="submit" class="button is-fullwidth has-text-weight-bold" style="background:#e94560; color:#fff; border:none; height:2.8em;">
                                <span class="icon"><i class="fas fa-sign-in-alt"></i></span><span>Entrar</span>
                            </button>
                        </div>
                    </form>
                    <hr>
                    <p class="has-text-centered">Não tem conta? <a href="registro.php" style="color:#e94560; font-weight:600;">Registre-se aqui</a></p>
                    <p class="has-text-centered mt-2"><a href="inicio.php" class="has-text-grey">← Voltar ao início</a></p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/footer.php'; ?>
