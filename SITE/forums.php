<?php
session_start();
include 'db.php';

// Criação de subfórum
if (isset($_POST['create_forum']) && isset($_SESSION['user'])) {
    $name = trim($_POST['forum_name']);
    $description = trim($_POST['forum_description']);
    if ($name && $description) {
        $stmt = $conexao->prepare('INSERT INTO forums (name, description) VALUES (?, ?)');
        $stmt->execute([$name, $description]);
        header('Location: forums.php');
        exit;
    } else {
        $error = 'Preencha todos os campos!';
    }
}

// Busca todos os subfóruns com contagem de posts e comentários
$stmt = $conexao->query('SELECT f.*, 
    (SELECT COUNT(*) FROM posts p WHERE p.forum_id = f.id) AS post_count,
    (SELECT COUNT(*) FROM comments c JOIN posts p ON c.post_id = p.id WHERE p.forum_id = f.id) AS comment_count
    FROM forums f ORDER BY name');
$forums = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Fóruns</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <header>
        <div class="navbar">
            <img src="https://i.ibb.co/d0F66Kw6/Whats-App-Image-2025-05-29-at-18-37-15-removebg-preview.png" alt="Logo" class="logo-img">
            <div class="brand">Fórum Rita Matos Luna</div>
        </div>
    </header>
    <div class="caixa">
        <h1>Fóruns</h1>
        <?php if (isset($_SESSION['user'])): ?>
        <div class="auth-forms" style="max-width:400px;margin:20px auto;">
            <h3>Criar novo Fórum</h3>
            <?php if (isset($error)) echo '<div style="color:red">'.htmlspecialchars($error).'</div>'; ?>
            <form method="post">
                <input type="text" name="forum_name" placeholder="Nome do subfórum" required><br>
                <textarea name="forum_description" placeholder="Descrição" required style="width:100%;min-height:60px;"></textarea><br>
                <button type="submit" name="create_forum">Criar Subfórum</button>
            </form>
        </div>
        <?php endif; ?>
        <?php if (count($forums) === 0): ?>
            <p>Nenhum Fórum cadastrado.</p>
        <?php else: ?>
            <?php foreach ($forums as $forum): ?>
                <div class="subforum">
                    <div class="sf-titulo" style="display:flex;align-items:center;gap:10px;">
                        <img src="https://i.ibb.co/gFwDDny3/image-removebg-preview-1.png" alt="Foto do fórum" style="height:32px;width:32px;object-fit:contain;">
                        <a href="forum.php?id=<?= $forum['id'] ?>"><b><?= htmlspecialchars($forum['name']) ?></b></a>
                        <span style="flex:1 1 auto;"></span>
                        <span style="font-size:0.9em; color:#fff;">Posts: <?= $forum['post_count'] ?> | Comentários: <?= $forum['comment_count'] ?></span>
                    </div>
                    <div class="sf-descricao" style="padding:10px; background:#fff; color:#222; border-radius:0 0 10px 10px;">
                        <?= nl2br(htmlspecialchars($forum['description'])) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <div style="text-align:center; margin-top:2em;">
            <a href="index.php" title="Voltar para Início" style="display:inline-flex;align-items:center;gap:8px;padding:10px 22px;background:#327f32;color:#fff;font-weight:bold;border-radius:8px;font-size:1.1em;text-decoration:none;box-shadow:0 2px 8px rgba(50,127,50,0.10);transition:background 0.2s;">
                <img src="https://i.ibb.co/ZRKbDVzf/image-removebg-preview.png" alt="Voltar" style="height:1.5em;vertical-align:middle;">
                Voltar para Início
            </a>
        </div>
    </div>
</body>
</html>
