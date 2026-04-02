<?php
require_once __DIR__ . '/config.php';
exigirLogin();
require_once __DIR__ . '/app/Model/Estagiobanco.php';

$usuarioId = usuarioLogado();
$banco     = new Estagiosbanco();
$estagios  = $banco->listarestagios($usuarioId);
$mensagem  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarCsrf();
    $estagioId = (int) ($_POST['estagio_id'] ?? 0);
    $relatorio = trim($_POST['relatorio'] ?? '');

    if ($estagioId <= 0 || empty($relatorio)) {
        $mensagem = ['tipo'=>'danger', 'texto'=>'Selecione um estágio e preencha o relatório.'];
    } else {
        try {
            $pdo = new PDO('sqlite:' . DB_PATH);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->prepare(
                'INSERT INTO relatorios (estagio_id, usuario_id, relatorio) VALUES (:eid, :uid, :rel)'
            )->execute([':eid'=>$estagioId, ':uid'=>$usuarioId, ':rel'=>$relatorio]);
            $mensagem = ['tipo'=>'success', 'texto'=>'Relatório enviado com sucesso!'];
        } catch (PDOException $e) {
            $mensagem = ['tipo'=>'danger', 'texto'=>'Erro ao salvar relatório: ' . $e->getMessage()];
        }
    }
}

require __DIR__ . '/header.php';
?>
<section class="section">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-8">
                <div class="box" style="border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.1);">
                    <h2 class="title mb-5" style="color:#1a1a2e;">
                        <span class="icon"><i class="fas fa-file-alt"></i></span> Criar Relatório de Estágio
                    </h2>
                    <?php if ($mensagem): ?>
                        <div class="notification is-<?= $mensagem['tipo'] ?> is-light">
                            <button class="delete"></button><?= htmlspecialchars($mensagem['texto']) ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($estagios)): ?>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                        <div class="field">
                            <label class="label">Estágio</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select name="estagio_id" required>
                                        <option value="" disabled selected>Selecione um estágio</option>
                                        <?php foreach ($estagios as $e): ?>
                                            <option value="<?= htmlspecialchars($e['id']) ?>">
                                                <?= htmlspecialchars($e['empresa']) ?> — <?= htmlspecialchars($e['data'] ?? '') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">Relatório</label>
                            <div class="control">
                                <textarea class="textarea" name="relatorio" rows="10"
                                    placeholder="Descreva as atividades desenvolvidas, aprendizados, desafios enfrentados e resultados obtidos durante o período de estágio..." required></textarea>
                            </div>
                            <p class="help" id="char-count">0 caracteres</p>
                        </div>
                        <div class="field mt-5">
                            <div class="buttons">
                                <button type="submit" class="button has-text-weight-bold" style="background:#e94560; color:#fff; border:none;">
                                    <span class="icon"><i class="fas fa-paper-plane"></i></span><span>Enviar Relatório</span>
                                </button>
                                <a href="conteudo.php" class="button is-light">Cancelar</a>
                            </div>
                        </div>
                    </form>
                    <?php else: ?>
                        <div class="notification is-warning is-light">
                            Nenhum estágio disponível para relatório.
                            <a href="adicionar_estagio.php">Adicionar estágio</a>.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    const ta = document.querySelector('textarea[name="relatorio"]');
    const cc = document.getElementById('char-count');
    if (ta && cc) {
        ta.addEventListener('input', () => cc.textContent = ta.value.length + ' caracteres');
    }
</script>
<?php require __DIR__ . '/footer.php'; ?>
