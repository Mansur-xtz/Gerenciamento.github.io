<?php
require_once __DIR__ . '/config.php';
exigirLogin();
require_once __DIR__ . '/app/Model/Estagiario.php';

$pdo = new PDO('sqlite:' . DB_PATH);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$model = new Estagiario($pdo);

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: /listar_estagiarios.php'); exit; }

$estagiario = $model->buscarPorId($id);
if (!$estagiario) { header('Location: /listar_estagiarios.php'); exit; }

$mensagem = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarCsrf();
    $nome        = trim($_POST['nome'] ?? '');
    $cpf_cnpj    = preg_replace('/\D/', '', $_POST['cpf_cnpj'] ?? '');
    $telefone    = preg_replace('/\D/', '', $_POST['telefone'] ?? '');
    $empresa     = trim($_POST['empresa'] ?? '');
    $data_inicio = $_POST['data_inicio'] ?? '';
    $duracao     = (int) ($_POST['duracao'] ?? 0);

    $erros = [];
    if (empty($nome))   $erros[] = 'Nome obrigatório.';
    if (!in_array(strlen($cpf_cnpj), [11, 14])) $erros[] = 'CPF (11 dígitos) ou CNPJ (14 dígitos) inválido.';
    if (strlen($telefone) < 10) $erros[] = 'Telefone inválido.';

    if (empty($erros)) {
        $model->atualizarEstagiario($id, $nome, $cpf_cnpj, $telefone, $empresa, $data_inicio, $duracao);
        header('Location: /listar_estagiarios.php?message=sucesso');
        exit;
    }
    $mensagem = ['tipo'=>'danger', 'texto'=>implode(' ', $erros)];
}

require __DIR__ . '/header.php';
?>
<section class="section">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-7">
                <div class="box" style="border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.1);">
                    <h2 class="title mb-5" style="color:#1a1a2e;">
                        <span class="icon"><i class="fas fa-user-edit"></i></span> Editar Estagiário
                    </h2>
                    <?php if ($mensagem): ?>
                        <div class="notification is-<?= $mensagem['tipo'] ?> is-light">
                            <button class="delete"></button><?= htmlspecialchars($mensagem['texto']) ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="editarEstagiario.php?id=<?= $id ?>">
                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                        <div class="columns">
                            <div class="column">
                                <div class="field">
                                    <label class="label">Nome</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="text" name="nome" value="<?= htmlspecialchars($estagiario['nome'] ?? '') ?>" required>
                                        <span class="icon is-left"><i class="fas fa-user"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="column">
                                <div class="field">
                                    <label class="label">CPF / CNPJ</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="text" name="cpf_cnpj" value="<?= htmlspecialchars($estagiario['cpf_cnpj'] ?? '') ?>" required>
                                        <span class="icon is-left"><i class="fas fa-id-card"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="columns">
                            <div class="column">
                                <div class="field">
                                    <label class="label">Telefone</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="text" name="telefone" value="<?= htmlspecialchars($estagiario['telefone'] ?? '') ?>" required>
                                        <span class="icon is-left"><i class="fas fa-phone"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="column">
                                <div class="field">
                                    <label class="label">Curso / Empresa</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="text" name="empresa" value="<?= htmlspecialchars($estagiario['empresa'] ?? '') ?>">
                                        <span class="icon is-left"><i class="fas fa-building"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="columns">
                            <div class="column">
                                <div class="field">
                                    <label class="label">Data de Início</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="date" name="data_inicio" value="<?= htmlspecialchars($estagiario['data_inicio'] ?? '') ?>">
                                        <span class="icon is-left"><i class="fas fa-calendar"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="column">
                                <div class="field">
                                    <label class="label">Duração (meses)</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="number" name="duracao" min="1" value="<?= htmlspecialchars($estagiario['duracao'] ?? '') ?>">
                                        <span class="icon is-left"><i class="fas fa-hourglass-half"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="field mt-5">
                            <div class="buttons">
                                <button type="submit" class="button has-text-weight-bold" style="background:#e94560; color:#fff; border:none;">
                                    <span class="icon"><i class="fas fa-save"></i></span><span>Salvar Alterações</span>
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
<?php require __DIR__ . '/footer.php'; ?>
