<?php
require_once __DIR__ . '/config.php';
exigirLogin();

$estagioId = (int) ($_GET['id'] ?? 0);
if ($estagioId <= 0) { header('Location: listar_estagios.php'); exit; }

try {
    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare('SELECT * FROM estagios WHERE id = :id');
    $stmt->execute([':id' => $estagioId]);
    $estagio = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$estagio) { header('Location: listar_estagios.php'); exit; }

    $stmt = $pdo->prepare('SELECT * FROM avaliacoes WHERE estagio_id = :id ORDER BY id DESC');
    $stmt->execute([':id' => $estagioId]);
    $avaliacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare('SELECT * FROM relatorios WHERE estagio_id = :id ORDER BY id DESC');
    $stmt->execute([':id' => $estagioId]);
    $relatorios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Média de notas
    $mediaNotas = 0;
    if (!empty($avaliacoes)) {
        $mediaNotas = array_sum(array_column($avaliacoes, 'nota')) / count($avaliacoes);
    }

    // Estagiários vinculados
    $stmt = $pdo->prepare('SELECT * FROM estagiarios WHERE estagio_id = :id');
    $stmt->execute([':id' => $estagioId]);
    $estagiarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die('<p class="notification is-danger">Erro: ' . $e->getMessage() . '</p>');
}

// Calcular duração
$duracaoTexto = 'N/A';
if (!empty($estagio['data']) && !empty($estagio['data_termino'])) {
    $di   = new DateTime($estagio['data']);
    $dt   = new DateTime($estagio['data_termino']);
    $diff = $di->diff($dt);
    $duracaoTexto = ($diff->y * 12 + $diff->m) . ' meses';
}

require __DIR__ . '/header.php';
?>
<style>
.stars-display { color:#f5a623; font-size:1.2rem; letter-spacing:2px; }
.stars-display .empty { color:#ddd; }
.info-card { border-radius:10px; border-left:4px solid #e94560; padding:1.25rem 1.5rem; background:#fff; margin-bottom:1rem; box-shadow:0 2px 8px rgba(0,0,0,0.06); }
</style>

<section class="section">
    <div class="container">
        <div class="level mb-5">
            <div class="level-left">
                <h2 class="title" style="color:#1a1a2e;">
                    <span class="icon"><i class="fas fa-chart-line"></i></span> Feedback do Estágio
                </h2>
            </div>
            <div class="level-right">
                <a href="listar_estagios.php" class="button is-light">
                    <span class="icon"><i class="fas fa-arrow-left"></i></span><span>Voltar</span>
                </a>
            </div>
        </div>

        <!-- Info do Estágio -->
        <div class="box mb-5" style="border-radius:12px; border-top:4px solid #e94560;">
            <h3 class="subtitle has-text-weight-bold mb-4" style="color:#1a1a2e;">
                <span class="icon"><i class="fas fa-briefcase"></i></span> Informações do Estágio
            </h3>
            <div class="columns is-multiline">
                <div class="column is-3">
                    <p class="has-text-grey is-size-7 mb-1">CURSO / EMPRESA</p>
                    <p class="has-text-weight-semibold"><?= htmlspecialchars($estagio['empresa']) ?></p>
                </div>
                <div class="column is-3">
                    <p class="has-text-grey is-size-7 mb-1">FUNCIONÁRIO</p>
                    <p class="has-text-weight-semibold"><?= htmlspecialchars($estagio['funcionario']) ?></p>
                </div>
                <div class="column is-2">
                    <p class="has-text-grey is-size-7 mb-1">DATA DE INÍCIO</p>
                    <p class="has-text-weight-semibold"><?= htmlspecialchars($estagio['data']) ?></p>
                </div>
                <div class="column is-2">
                    <p class="has-text-grey is-size-7 mb-1">HORÁRIO</p>
                    <p class="has-text-weight-semibold"><?= htmlspecialchars($estagio['horario']) ?></p>
                </div>
                <div class="column is-2">
                    <p class="has-text-grey is-size-7 mb-1">DURAÇÃO</p>
                    <p class="has-text-weight-semibold"><?= $duracaoTexto ?></p>
                </div>
            </div>
            <?php if (!empty($avaliacoes)): ?>
            <hr>
            <div class="has-text-centered">
                <p class="has-text-grey is-size-7 mb-1">MÉDIA GERAL</p>
                <div class="stars-display">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="<?= $i <= round($mediaNotas) ? '' : 'empty' ?>">★</span>
                    <?php endfor; ?>
                </div>
                <p class="is-size-6 mt-1"><strong><?= number_format($mediaNotas, 1) ?></strong> / 5
                    <span class="has-text-grey">(<?= count($avaliacoes) ?> avaliação<?= count($avaliacoes) > 1 ? 'ões' : '' ?>)</span>
                </p>
            </div>
            <?php endif; ?>
        </div>

        <div class="columns">
            <!-- Estagiários -->
            <div class="column is-4">
                <div class="box" style="border-radius:12px; border-top:4px solid #667eea; height:100%;">
                    <h3 class="subtitle has-text-weight-bold mb-4" style="color:#1a1a2e;">
                        <span class="icon"><i class="fas fa-users"></i></span> Estagiários (<?= count($estagiarios) ?>)
                    </h3>
                    <?php if (!empty($estagiarios)): ?>
                        <?php foreach ($estagiarios as $est): ?>
                            <div class="info-card">
                                <p class="has-text-weight-semibold"><?= htmlspecialchars($est['nome']) ?></p>
                                <p class="is-size-7 has-text-grey"><?= htmlspecialchars($est['cpf_cnpj'] ?? '') ?></p>
                                <p class="is-size-7 has-text-grey"><?= htmlspecialchars($est['telefone'] ?? '') ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="has-text-grey">Nenhum estagiário vinculado.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Avaliações e Relatórios -->
            <div class="column is-8">
                <!-- Avaliações -->
                <div class="box mb-4" style="border-radius:12px; border-top:4px solid #f5a623;">
                    <div class="level mb-4">
                        <div class="level-left">
                            <h3 class="subtitle has-text-weight-bold mb-0" style="color:#1a1a2e;">
                                <span class="icon"><i class="fas fa-star"></i></span> Avaliações
                            </h3>
                        </div>
                        <div class="level-right">
                            <a href="avaliar_estagio.php" class="button is-small is-warning is-light">+ Nova Avaliação</a>
                        </div>
                    </div>
                    <?php if (!empty($avaliacoes)): ?>
                        <?php foreach ($avaliacoes as $av): ?>
                            <div class="info-card" style="border-left-color:#f5a623;">
                                <div class="stars-display" style="font-size:1rem;">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="<?= $i <= $av['nota'] ? '' : 'empty' ?>">★</span>
                                    <?php endfor; ?>
                                    <span class="has-text-grey is-size-7 ml-2"><?= $av['nota'] ?>/5</span>
                                </div>
                                <p class="mt-2"><?= nl2br(htmlspecialchars($av['comentario'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="has-text-grey">Nenhuma avaliação registrada.</p>
                    <?php endif; ?>
                </div>

                <!-- Relatórios -->
                <div class="box" style="border-radius:12px; border-top:4px solid #43e97b;">
                    <div class="level mb-4">
                        <div class="level-left">
                            <h3 class="subtitle has-text-weight-bold mb-0" style="color:#1a1a2e;">
                                <span class="icon"><i class="fas fa-file-alt"></i></span> Relatórios
                            </h3>
                        </div>
                        <div class="level-right">
                            <a href="criar_relatorio.php" class="button is-small is-success is-light">+ Novo Relatório</a>
                        </div>
                    </div>
                    <?php if (!empty($relatorios)): ?>
                        <?php foreach ($relatorios as $rel): ?>
                            <div class="info-card" style="border-left-color:#43e97b;">
                                <p><?= nl2br(htmlspecialchars($rel['relatorio'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="has-text-grey">Nenhum relatório enviado.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/footer.php'; ?>
