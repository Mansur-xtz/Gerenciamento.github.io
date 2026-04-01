<?php
require_once __DIR__ . '/config.php';
iniciarSessao();
if (usuarioLogado()) { header('Location: conteudo.php'); exit; }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> — Gestão de Estágios</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.2/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background: #f5f7fa; }

        /* NAV */
        .top-nav { background: linear-gradient(135deg, #1a1a2e, #16213e); padding: .9rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .top-nav .brand { color: #fff; font-size: 1.2rem; font-weight: 700; display: flex; align-items: center; gap: .5rem; text-decoration: none; }
        .top-nav .nav-links a { color: rgba(255,255,255,.8); text-decoration: none; margin-left: 1.5rem; font-size: .95rem; transition: color .2s; }
        .top-nav .nav-links a:hover { color: #fff; }
        .top-nav .nav-links .btn-cta { background: #e94560; color: #fff !important; padding: .45rem 1.2rem; border-radius: 6px; font-weight: 600; }

        /* HERO */
        .hero-section { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); color: #fff; padding: 7rem 2rem 5rem; text-align: center; position: relative; overflow: hidden; }
        .hero-section::before { content:''; position:absolute; top:-60px; right:-60px; width:300px; height:300px; border-radius:50%; background:rgba(233,69,96,.15); }
        .hero-section::after  { content:''; position:absolute; bottom:-80px; left:-60px; width:250px; height:250px; border-radius:50%; background:rgba(102,126,234,.15); }
        .hero-section h1 { font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 800; margin-bottom: 1.2rem; position: relative; z-index: 1; }
        .hero-section h1 span { color: #e94560; }
        .hero-section p { font-size: 1.15rem; color: rgba(255,255,255,.8); max-width: 560px; margin: 0 auto 2.5rem; position: relative; z-index: 1; }
        .hero-btns { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; position: relative; z-index: 1; }
        .hero-btns a { padding: .85rem 2.2rem; border-radius: 8px; font-weight: 700; font-size: 1rem; text-decoration: none; transition: transform .2s, box-shadow .2s; }
        .hero-btns a:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,.3); }
        .btn-primary { background: #e94560; color: #fff; }
        .btn-outline { background: transparent; color: #fff; border: 2px solid rgba(255,255,255,.5); }

        /* FEATURES */
        .features-section { padding: 5rem 2rem; background: #fff; }
        .features-section h2 { text-align: center; font-size: 2rem; font-weight: 700; color: #1a1a2e; margin-bottom: .75rem; }
        .features-section .sub { text-align: center; color: #666; margin-bottom: 3.5rem; }
        .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 2rem; max-width: 1100px; margin: 0 auto; }
        .feature-card { background: #f5f7fa; border-radius: 14px; padding: 2rem 1.5rem; text-align: center; transition: transform .25s, box-shadow .25s; }
        .feature-card:hover { transform: translateY(-6px); box-shadow: 0 12px 30px rgba(0,0,0,.1); }
        .feature-card .icon-wrap { width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.2rem; font-size: 1.6rem; }
        .feature-card h3 { font-size: 1.05rem; font-weight: 700; color: #1a1a2e; margin-bottom: .5rem; }
        .feature-card p { color: #666; font-size: .9rem; line-height: 1.6; }

        /* STATS */
        .stats-section { background: linear-gradient(135deg, #1a1a2e, #16213e); padding: 4rem 2rem; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 2rem; max-width: 900px; margin: 0 auto; text-align: center; }
        .stat-item .num { font-size: 2.8rem; font-weight: 800; color: #e94560; }
        .stat-item .lbl { color: rgba(255,255,255,.7); font-size: .9rem; margin-top: .25rem; }

        /* CTA */
        .cta-section { padding: 5rem 2rem; background: #f5f7fa; text-align: center; }
        .cta-section h2 { font-size: 2rem; font-weight: 700; color: #1a1a2e; margin-bottom: 1rem; }
        .cta-section p { color: #666; margin-bottom: 2rem; }
        .cta-section a { background: #e94560; color: #fff; padding: 1rem 3rem; border-radius: 8px; font-weight: 700; font-size: 1.1rem; text-decoration: none; transition: background .2s, transform .2s; display: inline-block; }
        .cta-section a:hover { background: #c73652; transform: translateY(-2px); }

        /* FOOTER */
        footer { background: #1a1a2e; color: rgba(255,255,255,.6); text-align: center; padding: 2rem; font-size: .85rem; }
        footer strong { color: #fff; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="top-nav">
    <a href="inicio.php" class="brand"><i class="fas fa-graduation-cap"></i> <?= APP_NAME ?></a>
    <div class="nav-links">
        <a href="login.php">Entrar</a>
        <a href="registro.php" class="btn-cta">Criar Conta</a>
    </div>
</nav>

<!-- Hero -->
<section class="hero-section">
    <h1>Gerencie estágios com <span>eficiência</span> e profissionalismo</h1>
    <p>Cadastre estagiários, acompanhe relatórios, envie avaliações e tenha tudo centralizado em um só lugar.</p>
    <div class="hero-btns">
        <a href="registro.php" class="btn-primary">Começar Gratuitamente</a>
        <a href="login.php" class="btn-outline">Já tenho conta</a>
    </div>
</section>

<!-- Features -->
<section class="features-section">
    <h2>Tudo que você precisa</h2>
    <p class="sub">Ferramentas completas para gestão de estágios do início ao fim.</p>
    <div class="features-grid">
        <?php
        $features = [
            ['icon'=>'fa-shield-alt',  'cor'=>'#e94560', 'bg'=>'#fde8ec', 'title'=>'Acesso Seguro',         'desc'=>'Login com senha criptografada e proteção de sessão em cada página.'],
            ['icon'=>'fa-users',       'cor'=>'#667eea', 'bg'=>'#edf0fd', 'title'=>'Gestão de Estagiários', 'desc'=>'Cadastre, edite e acompanhe todos os seus estagiários em um painel centralizado.'],
            ['icon'=>'fa-star',        'cor'=>'#f5a623', 'bg'=>'#fef6e7', 'title'=>'Avaliações com Nota',   'desc'=>'Avalie cada estágio com um sistema de estrelas e comentários detalhados.'],
            ['icon'=>'fa-file-alt',    'cor'=>'#43e97b', 'bg'=>'#e8fdf1', 'title'=>'Relatórios de Progresso','desc'=>'Crie e armazene relatórios de cada período para acompanhamento completo.'],
            ['icon'=>'fa-chart-bar',   'cor'=>'#4facfe', 'bg'=>'#e7f5ff', 'title'=>'Dashboard com Métricas','desc'=>'Visualize estatísticas de estágios, avaliações e relatórios na tela inicial.'],
            ['icon'=>'fa-search',      'cor'=>'#f093fb', 'bg'=>'#fdeaff', 'title'=>'Busca Rápida',          'desc'=>'Encontre estagiários e estágios rapidamente com a barra de pesquisa integrada.'],
        ];
        foreach ($features as $f):
        ?>
        <div class="feature-card">
            <div class="icon-wrap" style="background:<?= $f['bg'] ?>; color:<?= $f['cor'] ?>;">
                <i class="fas <?= $f['icon'] ?>"></i>
            </div>
            <h3><?= $f['title'] ?></h3>
            <p><?= $f['desc'] ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Stats -->
<section class="stats-section">
    <div class="stats-grid">
        <div class="stat-item"><div class="num">100%</div><div class="lbl">Dados Protegidos</div></div>
        <div class="stat-item"><div class="num">5★</div><div class="lbl">Sistema de Avaliação</div></div>
        <div class="stat-item"><div class="num">∞</div><div class="lbl">Estágios Cadastráveis</div></div>
        <div class="stat-item"><div class="num">0</div><div class="lbl">Custo de Uso</div></div>
    </div>
</section>

<!-- CTA Final -->
<section class="cta-section">
    <h2>Pronto para começar?</h2>
    <p>Crie sua conta agora e gerencie seus estágios de forma profissional.</p>
    <a href="registro.php">Criar conta gratuita</a>
</section>

<footer>
    <p>&copy; <?= date('Y') ?> <strong><?= APP_NAME ?></strong> — Sistema de Gestão de Estágios. Todos os direitos reservados.</p>
</footer>

</body>
</html>
