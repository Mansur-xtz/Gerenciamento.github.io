<?php
require_once __DIR__ . '/config.php';
exigirLogin();
require_once __DIR__ . '/app/Model/Estagiobanco.php';

$usuarioId = usuarioLogado();
$banco     = new Estagiosbanco();
$estagios  = $banco->listarestagios($usuarioId);

$erro    = $_SESSION['mensagem_erro'] ?? null;    unset($_SESSION['mensagem_erro']);
$sucesso = $_SESSION['mensagem_sucesso'] ?? null; unset($_SESSION['mensagem_sucesso']);

require __DIR__ . '/header.php';
?>
<section class="section">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-8">
                <div class="box" style="border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.1);">
                    <h2 class="title mb-5" style="color:#1a1a2e;">
                        <span class="icon"><i class="fas fa-user-plus"></i></span> Cadastrar Estagiário
                    </h2>
                    <?php if ($erro): ?>
                        <div class="notification is-danger is-light"><button class="delete"></button><?= htmlspecialchars($erro) ?></div>
                    <?php endif; ?>
                    <?php if ($sucesso): ?>
                        <div class="notification is-success is-light"><button class="delete"></button><?= htmlspecialchars($sucesso) ?></div>
                    <?php endif; ?>
                    <form action="app/Controller/CadastrarEstagiario.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                        <div class="columns">
                            <div class="column">
                                <div class="field">
                                    <label class="label">Nome Completo</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="text" name="nome" placeholder="Nome do estagiário" required>
                                        <span class="icon is-left"><i class="fas fa-user"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="column">
                                <div class="field">
                                    <label class="label">CPF / CNPJ</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="text" name="cpf_cnpj" id="cpf_cnpj"
                                               placeholder="Apenas números (11 ou 14 dígitos)" maxlength="18" required>
                                        <span class="icon is-left"><i class="fas fa-id-card"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="columns">
                            <div class="column">
                                <div class="field">
                                    <label class="label">Data de Nascimento</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="date" name="data_nascimento" required>
                                        <span class="icon is-left"><i class="fas fa-birthday-cake"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="column">
                                <div class="field">
                                    <label class="label">Telefone (com DDD)</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="tel" name="telefone" id="telefone"
                                               placeholder="(00) 00000-0000" maxlength="15" required>
                                        <span class="icon is-left"><i class="fas fa-phone"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">Estágio Vinculado</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select name="estagio_id" id="estagio_id" onchange="preencherData()" required>
                                        <option value="" disabled selected>Selecione um estágio</option>
                                        <?php if (!empty($estagios)): ?>
                                            <?php foreach ($estagios as $e): ?>
                                                <option value="<?= htmlspecialchars($e['id']) ?>"
                                                        data-inicio="<?= htmlspecialchars($e['data'] ?? '') ?>">
                                                    <?= htmlspecialchars($e['empresa']) ?> — <?= htmlspecialchars($e['data'] ?? '') ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="" disabled>Nenhum estágio disponível — <a href="adicionar_estagio.php">crie um primeiro</a></option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="data_inicio" id="data_inicio">
                        </div>
                        <div class="field">
                            <label class="label">Duração (meses)</label>
                            <div class="control has-icons-left">
                                <input class="input" type="number" name="duracao" min="1" max="60" placeholder="Ex: 6" required>
                                <span class="icon is-left"><i class="fas fa-hourglass-half"></i></span>
                            </div>
                        </div>
                        <div class="field mt-5">
                            <div class="buttons">
                                <button type="submit" class="button has-text-weight-bold" style="background:#e94560; color:#fff; border:none;">
                                    <span class="icon"><i class="fas fa-save"></i></span><span>Cadastrar Estagiário</span>
                                </button>
                                <a href="listar_estagiarios.php" class="button is-light">Cancelar</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
function preencherData() {
    const sel = document.getElementById('estagio_id');
    const opt = sel.options[sel.selectedIndex];
    document.getElementById('data_inicio').value = opt.getAttribute('data-inicio') || '';
}
// Máscara de telefone
document.getElementById('telefone').addEventListener('input', function() {
    let v = this.value.replace(/\D/g,'');
    if (v.length <= 10)
        v = v.replace(/(\d{2})(\d{4})(\d{0,4})/,'($1) $2-$3');
    else
        v = v.replace(/(\d{2})(\d{5})(\d{0,4})/,'($1) $2-$3');
    this.value = v;
});
</script>
<?php require __DIR__ . '/footer.php'; ?>
