<?php
require_once __DIR__ . '/config.php';
exigirLogin();

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: listar_estagios.php'); exit; }

$pdo = new PDO('sqlite:' . DB_PATH);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$mensagem = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarCsrf();

    if (isset($_POST['atualizar'])) {
        $empresa    = trim($_POST['empresa'] ?? '');
        $funcionario = trim($_POST['funcionario'] ?? '');
        $data       = $_POST['data'] ?? '';
        $horario    = $_POST['horario'] ?? '';

        $stmt = $pdo->prepare('UPDATE estagios SET empresa=:e, funcionario=:f, data=:d, horario=:h WHERE id=:id');
        $stmt->execute([':e'=>$empresa, ':f'=>$funcionario, ':d'=>$data, ':h'=>$horario, ':id'=>$id]);
        $mensagem = ['tipo'=>'success', 'texto'=>'Estágio atualizado com sucesso!'];

    } elseif (isset($_POST['excluir'])) {
        $pdo->prepare('DELETE FROM estagios WHERE id=:id')->execute([':id'=>$id]);
        $pdo->prepare('DELETE FROM user_est WHERE estagio_id=:id')->execute([':id'=>$id]);
        header('Location: listar_estagios.php?message=excluido');
        exit;
    }
}

$stmt = $pdo->prepare('SELECT * FROM estagios WHERE id=:id');
$stmt->execute([':id'=>$id]);
$estagio = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$estagio) { header('Location: listar_estagios.php'); exit; }

require __DIR__ . '/header.php';
?>
<section class="section">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-7">
                <div class="box" style="border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.1);">
                    <h2 class="title mb-5" style="color:#1a1a2e;">
                        <span class="icon"><i class="fas fa-edit"></i></span> Editar Estágio
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
                                <input class="input" type="text" name="empresa" value="<?= htmlspecialchars($estagio['empresa']) ?>" required>
                                <span class="icon is-left"><i class="fas fa-building"></i></span>
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">Número do Funcionário</label>
                            <div class="control has-icons-left">
                                <input class="input" type="text" name="funcionario" value="<?= htmlspecialchars($estagio['funcionario']) ?>" required>
                                <span class="icon is-left"><i class="fas fa-id-badge"></i></span>
                            </div>
                        </div>
                        <div class="columns">
                            <div class="column">
                                <div class="field">
                                    <label class="label">Data de Início</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="date" name="data" value="<?= htmlspecialchars($estagio['data']) ?>" required>
                                        <span class="icon is-left"><i class="fas fa-calendar"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="column">
                                <div class="field">
                                    <label class="label">Horário</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="time" name="horario" value="<?= htmlspecialchars($estagio['horario']) ?>" required>
                                        <span class="icon is-left"><i class="fas fa-clock"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="field mt-5">
                            <div class="buttons">
                                <button type="submit" name="atualizar" class="button has-text-weight-bold" style="background:#e94560; color:#fff; border:none;">
                                    <span class="icon"><i class="fas fa-save"></i></span><span>Salvar Alterações</span>
                                </button>
                                <a href="listar_estagios.php" class="button is-light">Cancelar</a>
                                <button type="submit" name="excluir" class="button is-danger is-light"
                                    onclick="return confirm('Tem certeza que deseja excluir este estágio?')">
                                    <span class="icon"><i class="fas fa-trash"></i></span><span>Excluir</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/footer.php'; ?>
