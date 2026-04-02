<?php
require_once __DIR__ . '/config.php';
exigirLogin();
require_once __DIR__ . '/app/Model/Estagiobanco.php';
require_once __DIR__ . '/app/Model/UserBanco.php';

$usuarioId   = usuarioLogado();
$dadosUser   = (new UserBanco())->buscarPorId($usuarioId);
$nomeUsuario = $dadosUser['nome'] ?? 'Usuário';

$estagiosBanco   = new Estagiosbanco();
$estagios        = $estagiosBanco->listarestagios($usuarioId);
$totalEstagios   = count($estagios);
$totalAvaliacoes = $estagiosBanco->contarAvaliacoes($usuarioId);
$totalRelatorios = $estagiosBanco->contarRelatorios($usuarioId);

// Contar estagiários vinculados aos estágios do usuário
$pdo = new PDO('sqlite:' . DB_PATH);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $pdo->prepare(
    'SELECT COUNT(DISTINCT est.id) as total FROM estagiarios est
     INNER JOIN estagios e ON est.estagio_id = e.id
     INNER JOIN user_est ue ON e.id = ue.estagio_id
     WHERE ue.usuario_id = :uid'
);
$stmt->bindValue(':uid', $usuarioId, PDO::PARAM_INT);
$stmt->execute();
$totalEstagiarios = (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

require __DIR__ . '/header.php';
?>
<style>
.stat-card { border-radius:12px; padding:1.5rem; color:#fff; display:flex; align-items:center; gap:1rem; box-shadow:0 4px 15px rgba(0,0,0,0.15); transition:transform .2s; }
.stat-card:hover { transform:translateY(-4px); }
.stat-card .stat-icon { font-size:2.5rem; opacity:0.85; }
.stat-card .stat-info .stat-num { font-size:2rem; font-weight:700; line-height:1; }
.stat-card .stat-info .stat-label { font-size:0.85rem; opacity:0.9; margin-top:4px; }
.action-btn { border-radius:10px; height:auto; padding:1rem 1.2rem; display:flex; align-items:center; gap:.75rem; text-align:left; font-weight:600; border:2px solid transparent; transition:all .2s; }
.action-btn:hover { transform:translateY(-2px); box-shadow:0 4px 15px rgba(0,0,0,0.15); }
</style>

<section class="section">
    <div class="container">
        <!-- Boas-vindas -->
        <div class="mb-6">
            <h1 class="title" style="color:#1a1a2e;">
                Olá, <span style="color:#e94560;"><?= htmlspecialchars($nomeUsuario) ?></span>! 👋
            </h1>
            <p class="subtitle has-text-grey">Aqui está um resumo das suas atividades.</p>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="columns is-multiline mb-6">
            <div class="column is-3">
                <div class="stat-card" style="background:linear-gradient(135deg,#667eea,#764ba2);">
                    <div class="stat-icon"><i class="fas fa-briefcase"></i></div>
                    <div class="stat-info">
                        <div class="stat-num"><?= $totalEstagios ?></div>
                        <div class="stat-label">Estágios Cadastrados</div>
                    </div>
                </div>
            </div>
            <div class="column is-3">
                <div class="stat-card" style="background:linear-gradient(135deg,#f093fb,#f5576c);">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-info">
                        <div class="stat-num"><?= $totalEstagiarios ?></div>
                        <div class="stat-label">Estagiários Ativos</div>
                    </div>
                </div>
            </div>
            <div class="column is-3">
                <div class="stat-card" style="background:linear-gradient(135deg,#4facfe,#00f2fe);">
                    <div class="stat-icon"><i class="fas fa-star"></i></div>
                    <div class="stat-info">
                        <div class="stat-num"><?= $totalAvaliacoes ?></div>
                        <div class="stat-label">Avaliações Feitas</div>
                    </div>
                </div>
            </div>
            <div class="column is-3">
                <div class="stat-card" style="background:linear-gradient(135deg,#43e97b,#38f9d7);">
                    <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
                    <div class="stat-info">
                        <div class="stat-num"><?= $totalRelatorios ?></div>
                        <div class="stat-label">Relatórios Enviados</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ações Rápidas -->
        <h2 class="title is-5 mb-4" style="color:#1a1a2e;"><span class="icon"><i class="fas fa-bolt"></i></span> Ações Rápidas</h2>
        <div class="columns is-multiline mb-6">
            <?php
            $acoes = [
                ['href'=>'adicionar_estagio.php',   'icon'=>'fa-plus-circle',   'label'=>'Adicionar Estágio',      'color'=>'#667eea'],
                ['href'=>'listar_estagios.php',      'icon'=>'fa-list',          'label'=>'Ver Meus Estágios',      'color'=>'#764ba2'],
                ['href'=>'registro_estagiario.php',  'icon'=>'fa-user-plus',     'label'=>'Registrar Estagiário',   'color'=>'#f5576c'],
                ['href'=>'listar_estagiarios.php',   'icon'=>'fa-users',         'label'=>'Ver Estagiários',        'color'=>'#f093fb'],
                ['href'=>'avaliar_estagio.php',      'icon'=>'fa-star',          'label'=>'Avaliar Estágio',        'color'=>'#4facfe'],
                ['href'=>'criar_relatorio.php',      'icon'=>'fa-file-alt',      'label'=>'Criar Relatório',        'color'=>'#43e97b'],
            ];
            foreach ($acoes as $a):
            ?>
            <div class="column is-4">
                <a href="<?= $a['href'] ?>" class="button is-fullwidth action-btn" style="background:#fff; color:<?= $a['color'] ?>; border-color:<?= $a['color'] ?>20;">
                    <span class="icon" style="color:<?= $a['color'] ?>;"><i class="fas <?= $a['icon'] ?>"></i></span>
                    <span><?= $a['label'] ?></span>
                </a>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($estagios)): ?>
        <!-- Estágios Recentes -->
        <h2 class="title is-5 mb-4" style="color:#1a1a2e;"><span class="icon"><i class="fas fa-clock"></i></span> Estágios Recentes</h2>
        <div class="box" style="border-radius:12px; overflow:hidden; padding:0;">
            <table class="table is-fullwidth is-hoverable" style="margin:0;">
                <thead style="background:#1a1a2e; color:#fff;">
                    <tr>
                        <th style="color:#fff;">Curso / Empresa</th>
                        <th style="color:#fff;">Funcionário</th>
                        <th style="color:#fff;">Início</th>
                        <th style="color:#fff;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($estagios, 0, 5) as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['empresa'] ?? '') ?></td>
                        <td><?= htmlspecialchars($e['funcionario'] ?? '') ?></td>
                        <td><?= htmlspecialchars($e['data'] ?? '') ?></td>
                        <td>
                            <a href="feedback_estagio.php?id=<?= $e['id'] ?>" class="button is-small is-link is-light">Ver Detalhes</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/footer.php'; ?>
