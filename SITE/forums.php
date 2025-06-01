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
                    <div class="sf-titulo">
                        <a href="forum.php?id=<?= $forum['id'] ?>"><b><?= htmlspecialchars($forum['name']) ?></b></a>
                        <span style="float:right; font-size:0.9em; color:#fff;">Posts: <?= $forum['post_count'] ?> | Comentários: <?= $forum['comment_count'] ?></span>
                    </div>
                    <div class="sf-descricao" style="padding:10px; background:#fff; color:#222; border-radius:0 0 10px 10px;">
                        <?= nl2br(htmlspecialchars($forum['description'])) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <a href="index.php">&larr; Voltar para o início</a>
    </div>
</body>
</html>
