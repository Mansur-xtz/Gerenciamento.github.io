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
    $estagioId  = (int) ($_POST['estagio_id'] ?? 0);
    $nota       = (int) ($_POST['nota'] ?? 0);
    $comentario = trim($_POST['comentario'] ?? '');

    $erros = [];
    if ($estagioId <= 0)              $erros[] = 'Selecione um estágio.';
    if ($nota < 1 || $nota > 5)      $erros[] = 'Nota deve ser entre 1 e 5.';
    if (empty($comentario))           $erros[] = 'Comentário obrigatório.';

    if (empty($erros)) {
        try {
            $pdo = new PDO('sqlite:' . DB_PATH);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->prepare(
                'INSERT INTO avaliacoes (estagio_id, usuario_id, nota, comentario) VALUES (:eid, :uid, :nota, :com)'
            )->execute([':eid'=>$estagioId, ':uid'=>$usuarioId, ':nota'=>$nota, ':com'=>$comentario]);
            $mensagem = ['tipo'=>'success', 'texto'=>'Avaliação enviada com sucesso!'];
        } catch (PDOException $e) {
            $mensagem = ['tipo'=>'danger', 'texto'=>'Erro ao salvar avaliação: ' . $e->getMessage()];
        }
    } else {
        $mensagem = ['tipo'=>'danger', 'texto'=>implode(' ', $erros)];
    }
}

require __DIR__ . '/header.php';
?>
<style>
.star-rating { display:flex; flex-direction:row-reverse; justify-content:flex-end; gap:4px; }
.star-rating input { display:none; }
.star-rating label { font-size:2rem; color:#ddd; cursor:pointer; transition:color .15s; }
.star-rating input:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label { color:#f5a623; }
</style>

<section class="section">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-7">
                <div class="box" style="border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.1);">
                    <h2 class="title mb-5" style="color:#1a1a2e;">
                        <span class="icon"><i class="fas fa-star"></i></span> Avaliar Estágio
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
                            <label class="label">Selecione o Estágio</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select name="estagio_id" required>
                                        <option value="" disabled selected>Escolha um estágio</option>
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
                            <label class="label">Nota</label>
                            <div class="star-rating">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" name="nota" id="star<?= $i ?>" value="<?= $i ?>">
                                    <label for="star<?= $i ?>" title="<?= $i ?> estrela<?= $i > 1 ? 's' : '' ?>">&#9733;</label>
                                <?php endfor; ?>
                            </div>
                            <p class="help">Clique nas estrelas para avaliar (1 a 5)</p>
                        </div>

                        <div class="field">
                            <label class="label">Comentário</label>
                            <div class="control">
                                <textarea class="textarea" name="comentario" rows="4"
                                    placeholder="Descreva o desempenho, pontos fortes e áreas de melhoria..." required></textarea>
                            </div>
                        </div>

                        <div class="field mt-5">
                            <div class="buttons">
                                <button type="submit" class="button has-text-weight-bold" style="background:#e94560; color:#fff; border:none;">
                                    <span class="icon"><i class="fas fa-paper-plane"></i></span><span>Enviar Avaliação</span>
                                </button>
                                <a href="conteudo.php" class="button is-light">Cancelar</a>
                            </div>
                        </div>
                    </form>
                    <?php else: ?>
                        <div class="notification is-warning is-light">
                            Você não possui estágios cadastrados para avaliar.
                            <a href="adicionar_estagio.php">Adicionar estágio</a>.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/footer.php'; ?>
