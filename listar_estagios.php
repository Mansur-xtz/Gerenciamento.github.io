<?php
require_once __DIR__ . '/config.php';
exigirLogin();
require_once __DIR__ . '/app/Model/Estagiobanco.php';

$usuarioId   = usuarioLogado();
$estagiosBanco = new Estagiosbanco();

// Excluir estágio via GET (com proteção de token simples)
if (isset($_GET['excluir_id']) && isset($_GET['token'])) {
    iniciarSessao();
    if (hash_equals($_SESSION['csrf_token'] ?? '', $_GET['token'])) {
        $excluirId = (int) $_GET['excluir_id'];
        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->prepare('DELETE FROM estagios WHERE id = :id')->execute([':id' => $excluirId]);
        $pdo->prepare('DELETE FROM user_est WHERE estagio_id = :id')->execute([':id' => $excluirId]);
    }
    header('Location: listar_estagios.php?message=excluido');
    exit;
}

$estagios = $estagiosBanco->listarestagios($usuarioId);
$busca    = trim($_GET['busca'] ?? '');
if ($busca) {
    $estagios = array_filter($estagios, fn($e) =>
        stripos($e['empresa'] ?? '', $busca) !== false ||
        stripos($e['funcionario'] ?? '', $busca) !== false
    );
}

$message = $_GET['message'] ?? null;
require __DIR__ . '/header.php';
?>
<section class="section">
    <div class="container">
        <div class="level mb-5">
            <div class="level-left">
                <h2 class="title" style="color:#1a1a2e;"><span class="icon"><i class="fas fa-briefcase"></i></span> Meus Estágios</h2>
            </div>
            <div class="level-right">
                <a href="adicionar_estagio.php" class="button has-text-weight-bold" style="background:#e94560; color:#fff; border:none;">
                    <span class="icon"><i class="fas fa-plus"></i></span><span>Novo Estágio</span>
                </a>
            </div>
        </div>

        <?php if ($message === 'excluido'): ?>
            <div class="notification is-warning is-light"><button class="delete"></button>Estágio excluído com sucesso.</div>
        <?php endif; ?>

        <!-- Busca -->
        <form method="GET" class="mb-5">
            <div class="field has-addons">
                <div class="control is-expanded">
                    <input class="input" type="text" name="busca" placeholder="Buscar por curso ou funcionário..." value="<?= htmlspecialchars($busca) ?>">
                </div>
                <div class="control">
                    <button type="submit" class="button" style="background:#1a1a2e; color:#fff;"><span class="icon"><i class="fas fa-search"></i></span></button>
                </div>
                <?php if ($busca): ?>
                <div class="control">
                    <a href="listar_estagios.php" class="button is-light">Limpar</a>
                </div>
                <?php endif; ?>
            </div>
        </form>

        <?php if (!empty($estagios)): ?>
            <div class="box" style="border-radius:12px; overflow:hidden; padding:0;">
                <table class="table is-fullwidth is-hoverable" style="margin:0;">
                    <thead style="background:#1a1a2e;">
                        <tr>
                            <th style="color:#fff;">Curso / Empresa</th>
                            <th style="color:#fff;">Funcionário</th>
                            <th style="color:#fff;">Início</th>
                            <th style="color:#fff;">Horário</th>
                            <th style="color:#fff;">Duração</th>
                            <th style="color:#fff;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($estagios as $estagio): ?>
                            <?php
                            $duracao = 'N/A';
                            if (!empty($estagio['data']) && !empty($estagio['data_termino'])) {
                                $di = new DateTime($estagio['data']);
                                $dt = new DateTime($estagio['data_termino']);
                                $diff = $di->diff($dt);
                                $duracao = $diff->m + ($diff->y * 12) . ' meses';
                            }
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($estagio['empresa'] ?? '') ?></td>
                                <td><?= htmlspecialchars($estagio['funcionario'] ?? '') ?></td>
                                <td><?= htmlspecialchars($estagio['data'] ?? '') ?></td>
                                <td><?= htmlspecialchars($estagio['horario'] ?? '') ?></td>
                                <td><?= $duracao ?></td>
                                <td>
                                    <a href="editar_estagio.php?id=<?= $estagio['id'] ?>" class="button is-small is-link is-light mr-1">
                                        <span class="icon"><i class="fas fa-edit"></i></span>
                                    </a>
                                    <a href="feedback_estagio.php?id=<?= $estagio['id'] ?>" class="button is-small is-info is-light mr-1">
                                        <span class="icon"><i class="fas fa-chart-line"></i></span>
                                    </a>
                                    <a href="listar_estagios.php?excluir_id=<?= $estagio['id'] ?>&token=<?= csrfToken() ?>"
                                       class="button is-small is-danger is-light"
                                       onclick="return confirm('Deseja excluir este estágio?')">
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
                <p><i class="fas fa-inbox fa-2x mb-3"></i></p>
                <?= $busca ? 'Nenhum estágio encontrado para "<strong>'.htmlspecialchars($busca).'</strong>".' : 'Você ainda não cadastrou nenhum estágio.' ?>
                <?php if (!$busca): ?><br><a href="adicionar_estagio.php" class="button mt-3" style="background:#e94560; color:#fff; border:none;">Adicionar primeiro estágio</a><?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/footer.php'; ?>
