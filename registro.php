<?php
require_once __DIR__ . '/config.php';
iniciarSessao();
if (usuarioLogado()) { header('Location: conteudo.php'); exit; }
$erro    = $_SESSION['mensagem_erro'] ?? null;    unset($_SESSION['mensagem_erro']);
$sucesso = $_SESSION['mensagem_sucesso'] ?? null; unset($_SESSION['mensagem_sucesso']);
require __DIR__ . '/header.php';
?>
<section class="section" style="min-height:80vh; display:flex; align-items:center;">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-5">
                <div class="box" style="border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.12);">
                    <h1 class="title has-text-centered mb-5" style="color:#1a1a2e;">
                        <span class="icon"><i class="fas fa-user-plus"></i></span> Criar Conta
                    </h1>
                    <?php if ($erro): ?>
                        <div class="notification is-danger is-light"><button class="delete"></button><?= htmlspecialchars($erro) ?></div>
                    <?php endif; ?>
                    <?php if ($sucesso): ?>
                        <div class="notification is-success is-light"><button class="delete"></button><?= htmlspecialchars($sucesso) ?></div>
                    <?php endif; ?>
                    <form action="index.php?acao=cadastra" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                        <div class="field">
                            <label class="label">Usuário</label>
                            <div class="control has-icons-left">
                                <input class="input" type="text" name="usuario" placeholder="Escolha um nome de usuário" required>
                                <span class="icon is-left"><i class="fas fa-user"></i></span>
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">Senha <span class="tag is-warning is-light ml-1">mín. 6 caracteres</span></label>
                            <div class="control has-icons-left">
                                <input class="input" type="password" name="senha" id="senha" placeholder="Crie uma senha segura" required minlength="6">
                                <span class="icon is-left"><i class="fas fa-key"></i></span>
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">Confirmar Senha</label>
                            <div class="control has-icons-left">
                                <input class="input" type="password" name="confirmacao_senha" id="confirmacao_senha" placeholder="Repita a senha" required>
                                <span class="icon is-left"><i class="fas fa-check"></i></span>
                            </div>
                            <p class="help is-danger" id="senha-erro" style="display:none;">As senhas não coincidem.</p>
                        </div>
                        <div class="field mt-5">
                            <button type="submit" id="btn-registrar" class="button is-fullwidth has-text-weight-bold" style="background:#e94560; color:#fff; border:none; height:2.8em;">
                                <span class="icon"><i class="fas fa-user-plus"></i></span><span>Criar Conta</span>
                            </button>
                        </div>
                    </form>
                    <hr>
                    <p class="has-text-centered">Já tem conta? <a href="login.php" style="color:#e94560; font-weight:600;">Faça login</a></p>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    const s1 = document.getElementById('senha');
    const s2 = document.getElementById('confirmacao_senha');
    const err = document.getElementById('senha-erro');
    const btn = document.getElementById('btn-registrar');
    function verificar() {
        if (s2.value && s1.value !== s2.value) {
            err.style.display = 'block';
            s2.classList.add('is-danger');
            btn.disabled = true;
        } else {
            err.style.display = 'none';
            s2.classList.remove('is-danger');
            btn.disabled = false;
        }
    }
    s1.addEventListener('input', verificar);
    s2.addEventListener('input', verificar);
</script>
<?php require __DIR__ . '/footer.php'; ?>
