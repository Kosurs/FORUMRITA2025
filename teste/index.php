<?php
session_start();
include 'db.php';
include 'auth.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loja de Carros</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Loja de Carros</h1>
        <nav>
            <a href="index.php">Início</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <span style="color:#fff; margin-left:20px; font-weight:bold;">Usuário: <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <form method="post" style="display:inline; margin-left:10px;">
                    <button type="submit" name="logout" class="delete-btn" style="background:#334155; padding:4px 12px; font-size:0.95em;">Sair</button>
                </form>
            <?php else: ?>
                <form method="post" style="display:inline; margin-left:20px;">
                    <button type="button" onclick="window.scrollTo({top: document.querySelector('.auth-section').offsetTop, behavior: 'smooth'});" class="delete-btn" style="background:#38bdf8; padding:4px 12px; font-size:0.95em;">Login</button>
                </form>
            <?php endif; ?>
        </nav>
    </header>
    <main>
        <section class="auth-section">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <div class="auth-forms">
                    <div class="form-box">
                        <h2>Cadastro</h2>
                        <?php if (isset($register_success)) echo '<p class="success">Cadastro realizado!</p>';
                        if (isset($register_error)) echo '<p class="error">'.$register_error.'</p>'; ?>
                        <form method="post">
                            <input type="text" name="username" placeholder="Usuário" required>
                            <input type="password" name="password" placeholder="Senha" required>
                            <button type="submit" name="register">Cadastrar</button>
                        </form>
                    </div>
                    <div class="form-box">
                        <h2>Login</h2>
                        <?php if (isset($login_error)) echo '<p class="error">'.$login_error.'</p>'; ?>
                        <form method="post">
                            <input type="text" name="username" placeholder="Usuário" required>
                            <input type="password" name="password" placeholder="Senha" required>
                            <button type="submit" name="login">Entrar</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="user-box">
                    <p>Bem-vindo, <b><?php echo $_SESSION['username']; ?></b>!</p>
                    <form method="post" style="display:inline;">
                        <button type="submit" name="logout" class="delete-btn" style="background:#334155; margin-right:10px;">Sair</button>
                    </form>
                    <form method="post" style="display:inline;">
                        <button type="submit" name="delete_account" class="delete-btn" onclick="return confirm('Tem certeza que deseja excluir sua conta?');">Excluir Conta</button>
                    </form>
                    <?php if (isset($delete_success)) echo '<p class="success">Conta excluída com sucesso.</p>';
                    if (isset($delete_error)) echo '<p class="error">'.$delete_error.'</p>'; ?>
                </div>
            <?php endif; ?>
        </section>
        <?php if (isset($_SESSION['user_id'])): ?>
            <section class="auth-section" style="margin-bottom: 30px;">
                <div class="form-box" style="max-width:400px; margin:0 auto;">
                    <h2>Adicionar Novo Carro</h2>
                    <?php if (isset($add_car_success)) echo '<p class="success">Carro cadastrado com sucesso!</p>';
                    if (isset($add_car_error)) echo '<p class="error">'.$add_car_error.'</p>'; ?>
                    <form method="post">
                        <input type="text" name="brand" placeholder="Marca" required>
                        <input type="text" name="model" placeholder="Modelo" required>
                        <input type="number" name="year" placeholder="Ano" required>
                        <input type="number" step="0.01" name="price" placeholder="Preço" required>
                        <input type="text" name="image" placeholder="URL da Imagem">
                        <textarea name="description" placeholder="Descrição" required></textarea>
                        <button type="submit" name="add_car">Adicionar Carro</button>
                    </form>
                </div>
            </section>
        <?php endif; ?>
        <h2>Carros à Venda</h2>
        <div class="car-list">
            <?php
            $sql = "SELECT * FROM cars";
            $stmt = $conexao->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($result) > 0) {
                foreach($result as $row) {
                    echo '<div class="car-card">';
                    if ($row['image']) {
                        echo '<img src="' . $row['image'] . '" alt="' . $row['brand'] . ' ' . $row['model'] . '">';
                    }
                    echo '<h3>' . $row['brand'] . ' ' . $row['model'] . '</h3>';
                    echo '<p>Ano: ' . $row['year'] . '</p>';
                    echo '<p>Preço: R$ ' . number_format($row['price'], 2, ',', '.') . '</p>';
                    echo '<p>' . $row['description'] . '</p>';
                    echo '</div>';
                }
            } else {
                echo '<p>Nenhum carro disponível no momento.</p>';
            }
            ?>
        </div>
    </main>
    <footer>
        <p>&copy; <?php echo date('Y'); ?> Loja de Carros</p>
    </footer>
</body>
</html>
