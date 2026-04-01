<?php
require_once __DIR__ . '/config.php';
exigirLogin();

$usuarioId = usuarioLogado();
$mensagem  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarCsrf();

    $empresa    = trim($_POST['empresa'] ?? '');
    $funcionario = trim($_POST['funcionario'] ?? '');
    $data       = $_POST['data'] ?? '';
    $horario    = $_POST['horario'] ?? '';
    $duracao    = (int) ($_POST['duracao'] ?? 0);

    $erros = [];
    if (empty($empresa))     $erros[] = 'Informe o nome do curso/empresa.';
    if (empty($funcionario)) $erros[] = 'Informe o número do funcionário.';
    if (empty($data))        $erros[] = 'Informe a data de início.';
    if (empty($horario))     $erros[] = 'Informe o horário.';
    if ($duracao <= 0)       $erros[] = 'Duração deve ser maior que zero.';

    if (empty($erros)) {
        require_once __DIR__ . '/app/Model/Estagiobanco.php';
        $dataInicio  = new DateTime($data);
        $dataTermino = (clone $dataInicio)->modify("+{$duracao} months")->format('Y-m-d');

        $banco = new Estagiosbanco();
        $novoId = $banco->cadastrarestagios($empresa, $funcionario, $data, $dataTermino, $horario, $usuarioId);

        if ($novoId) {
            $pdo = new PDO('sqlite:' . DB_PATH);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->prepare('INSERT INTO user_est (usuario_id, estagio_id) VALUES (:uid, :eid)')
                ->execute([':uid' => $usuarioId, ':eid' => $novoId]);
            header('Location: listar_estagios.php?message=sucesso');
            exit;
        }
        $mensagem = ['tipo' => 'danger', 'texto' => 'Erro ao salvar o estágio. Tente novamente.'];
    } else {
        $mensagem = ['tipo' => 'danger', 'texto' => implode(' ', $erros)];
    }
}

require __DIR__ . '/header.php';
?>
<section class="section">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-7">
                <div class="box" style="border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.1);">
                    <h2 class="title mb-5" style="color:#1a1a2e;">
                        <span class="icon"><i class="fas fa-plus-circle"></i></span> Adicionar Estágio
                    </h2>
                    <?php if ($mensagem): ?>
                        <div class="notification is-<?= $mensagem['tipo'] ?> is-light">
                            <button class="delete"></button><?= htmlspecialchars($mensagem['texto']) ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                        <div class="field">
                            <label class="label">Curso / Empresa</label>
                            <div class="control has-icons-left">
                                <input class="input" type="text" name="empresa" placeholder="Nome do curso ou empresa" required>
                                <span class="icon is-left"><i class="fas fa-building"></i></span>
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">Número do Funcionário</label>
                            <div class="control has-icons-left">
                                <input class="input" type="text" name="funcionario" placeholder="Matrícula ou número" required>
                                <span class="icon is-left"><i class="fas fa-id-badge"></i></span>
                            </div>
                        </div>
                        <div class="columns">
                            <div class="column">
                                <div class="field">
                                    <label class="label">Data de Início</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="date" name="data" required>
                                        <span class="icon is-left"><i class="fas fa-calendar"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="column">
                                <div class="field">
                                    <label class="label">Horário</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="time" name="horario" required>
                                        <span class="icon is-left"><i class="fas fa-clock"></i></span>
                                    </div>
                                </div>
                            </div>
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
                                    <span class="icon"><i class="fas fa-save"></i></span><span>Salvar Estágio</span>
                                </button>
                                <a href="listar_estagios.php" class="button is-light">Cancelar</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/footer.php'; ?>
