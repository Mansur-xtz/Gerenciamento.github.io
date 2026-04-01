<?php
require_once __DIR__ . '/config.php';
exigirLogin();
require_once __DIR__ . '/app/Model/Estagiario.php';

$pdo = new PDO('sqlite:' . DB_PATH);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$model = new Estagiario($pdo);

// Excluir
if (isset($_GET['excluir_id']) && isset($_GET['token'])) {
    iniciarSessao();
    if (hash_equals($_SESSION['csrf_token'] ?? '', $_GET['token'])) {
        $model->excluirEstagiario((int) $_GET['excluir_id']);
    }
    header('Location: /listar_estagiarios.php?message=excluido');
    exit;
}

$busca      = trim($_GET['busca'] ?? '');
$estagiarios = $model->listarEstagiarios();
if ($busca) {
    $estagiarios = array_filter($estagiarios, fn($e) =>
        stripos($e['nome'] ?? '', $busca) !== false ||
        stripos($e['cpf_cnpj'] ?? '', $busca) !== false
    );
}

$message = $_GET['message'] ?? null;
require __DIR__ . '/header.php';
?>
<section class="section">
    <div class="container">
        <div class="level mb-5">
            <div class="level-left">
                <h2 class="title" style="color:#1a1a2e;"><span class="icon"><i class="fas fa-users"></i></span> Estagiários</h2>
            </div>
            <div class="level-right">
                <a href="registro_estagiario.php" class="button has-text-weight-bold" style="background:#e94560; color:#fff; border:none;">
                    <span class="icon"><i class="fas fa-user-plus"></i></span><span>Novo Estagiário</span>
                </a>
            </div>
        </div>

        <?php if ($message === 'sucesso'): ?>
            <div class="notification is-success is-light"><button class="delete"></button>Operação realizada com sucesso!</div>
        <?php elseif ($message === 'excluido'): ?>
            <div class="notification is-warning is-light"><button class="delete"></button>Estagiário excluído com sucesso.</div>
        <?php endif; ?>

        <form method="GET" class="mb-5">
            <div class="field has-addons">
                <div class="control is-expanded">
                    <input class="input" type="text" name="busca" placeholder="Buscar por nome ou CPF/CNPJ..." value="<?= htmlspecialchars($busca) ?>">
                </div>
                <div class="control">
                    <button type="submit" class="button" style="background:#1a1a2e; color:#fff;">
                        <span class="icon"><i class="fas fa-search"></i></span>
                    </button>
                </div>
                <?php if ($busca): ?>
                <div class="control"><a href="listar_estagiarios.php" class="button is-light">Limpar</a></div>
                <?php endif; ?>
            </div>
        </form>

        <?php if (!empty($estagiarios)): ?>
            <div class="box" style="border-radius:12px; overflow:hidden; padding:0;">
                <table class="table is-fullwidth is-hoverable" style="margin:0;">
                    <thead style="background:#1a1a2e;">
                        <tr>
                            <th style="color:#fff;">Nome</th>
                            <th style="color:#fff;">CPF/CNPJ</th>
                            <th style="color:#fff;">Telefone</th>
                            <th style="color:#fff;">Início</th>
                            <th style="color:#fff;">Duração</th>
                            <th style="color:#fff;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($estagiarios as $est): ?>
                        <tr>
                            <td><?= htmlspecialchars($est['nome'] ?? '') ?></td>
                            <td><?= htmlspecialchars($est['cpf_cnpj'] ?? '') ?></td>
                            <td><?= htmlspecialchars($est['telefone'] ?? '') ?></td>
                            <td><?= htmlspecialchars($est['data_inicio'] ?? '') ?></td>
                            <td><?= htmlspecialchars($est['duracao'] ?? '') ?> meses</td>
                            <td>
                                <a href="editarEstagiario.php?id=<?= $est['id'] ?>" class="button is-small is-link is-light mr-1">
                                    <span class="icon"><i class="fas fa-edit"></i></span>
                                </a>
                                <a href="listar_estagiarios.php?excluir_id=<?= $est['id'] ?>&token=<?= csrfToken() ?>"
                                   class="button is-small is-danger is-light"
                                   onclick="return confirm('Deseja excluir este estagiário?')">
                                    <span class="icon"><i class="fas fa-trash"></i></span>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="notification is-warning is-light has-text-centered">
                <p><i class="fas fa-users-slash fa-2x mb-3"></i></p>
                <?= $busca ? 'Nenhum estagiário encontrado.' : 'Nenhum estagiário cadastrado ainda.' ?>
                <?php if (!$busca): ?>
                    <br><a href="registro_estagiario.php" class="button mt-3" style="background:#e94560; color:#fff; border:none;">Cadastrar primeiro estagiário</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/footer.php'; ?>
