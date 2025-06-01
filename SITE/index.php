<?php
include 'auth.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rita Luna</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <header>
        <div class="navbar">
            <nav class="navigation hide" id="navigation">
                <span class="close-icon" id="close-icon" onclick="showIconBar()"><i class="fa fa-close"></i></span>
                <ul class="nav-list">
                    <li class="nav-item"><a href="forums.php">Fóruns</a></li>
                    <?php if (!isset($_SESSION['user'])): ?>
                    <li class="nav-item"><a href="login.php">Login</a></li>
                    <li class="nav-item"><a href="register.php">Cadastro</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <a class="bar-icon" id="iconBar" onclick="hideIconBar()"><i class="fa fa-bars"></i></a>
            <img src="https://i.ibb.co/d0F66Kw6/Whats-App-Image-2025-05-29-at-18-37-15-removebg-preview.png" alt="Logo" class="logo-img">
            <div class="brand">Fórum Rita Matos Luna</div>
        </div>
        <div class="search-box">
            <div>
                <input type="text" name="q" placeholder="search ...">
                <button><i></i></button>
            </div>
        </div>
    </header>
    <?php if (isset($_SESSION['user'])): ?>
    <div class="user-info" style="max-width:400px;margin:20px auto;">
        <div>Bem-vindo, <b><?= htmlspecialchars($_SESSION['user']) ?></b>!</div>
        <form method="post" style="display:inline;">
            <button type="submit" name="delete_account" onclick="return confirm('Tem certeza que deseja excluir sua conta?');">Excluir Conta</button>
        </form>
        <a href="?logout=1" style="margin-left:10px;">Sair</a>
    </div>
    <?php else: ?>
    <div style="width:100%;background:#f7f7f7;padding:1em 0;text-align:center;box-shadow:0 2px 8px rgba(50,127,50,0.05);">
        <a href="register.php" style="display:inline-block;margin:0 10px;padding:10px 24px;background:#327f32;color:#fff;border-radius:5px;font-weight:bold;text-decoration:none;transition:background 0.2s;">Criar Conta</a>
        <a href="login.php" style="display:inline-block;margin:0 10px;padding:10px 24px;background:#b77acc;color:#fff;border-radius:5px;font-weight:bold;text-decoration:none;transition:background 0.2s;">Login</a>
    </div>
    <?php endif; ?>
    <div class="caixa">
        <h1>Bem-vindo ao Fórum Rita Matos Luna!</h1>
        <p>Escolha um Fórum para começar a participar das discussões sobre a nossa escola!.</p>
        <a href="forums.php" class="a2" style="font-size:1.2em;">Ver Fóruns</a>
    </div>
    <!-- Exemplo de botão de voltar na index, ajuste conforme necessário -->
    <div style="text-align:center; margin-top:2em;">
        <a href="javascript:history.back()" title="Voltar" style="display:inline-flex;align-items:center;gap:8px;padding:10px 22px;background:#327f32;color:#fff;font-weight:bold;border-radius:8px;font-size:1.1em;text-decoration:none;box-shadow:0 2px 8px rgba(50,127,50,0.10);transition:background 0.2s;">
            <img src="https://i.ibb.co/ZRKbDVzf/image-removebg-preview.png" alt="Voltar" style="height:1.5em;vertical-align:middle;">
            Voltar
        </a>
    </div>
    <footer>
        <span>&copy; FEITO POR CAVALCANTE, TITIU SAULO  E SAMUELZIN</span>
    </footer>
    <script src="main.js"></script>
</body>
</html>
