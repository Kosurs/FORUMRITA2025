<?php
session_start();
include 'db.php';

$forum_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($forum_id <= 0) {
    echo '<p>Fórum não encontrado.</p>';
    exit;
}

// Busca informações do fórum
$stmt = $conexao->prepare('SELECT * FROM forums WHERE id = ?');
$stmt->execute([$forum_id]);
$forum = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$forum) {
    echo '<p>Fórum não encontrado.</p>';
    exit;
}

// Busca id do usuário logado
$user_id = null;
if (isset($_SESSION['user'])) {
    $stmtUser = $conexao->prepare('SELECT id FROM users WHERE username = ?');
    $stmtUser->execute([$_SESSION['user']]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
    if ($user) $user_id = $user['id'];
}

// Criação de post
if (isset($_POST['create_post']) && $user_id) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    if ($title && $content) {
        $stmtPost = $conexao->prepare('INSERT INTO posts (forum_id, user_id, title, content) VALUES (?, ?, ?, ?)');
        $stmtPost->execute([$forum_id, $user_id, $title, $content]);
        $success = 'Post criado com sucesso!';
    } else {
        $error = 'Preencha todos os campos!';
    }
}

// Edição de post
if (isset($_POST['edit_post']) && $user_id) {
    $post_id = intval($_POST['post_id']);
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $stmt = $conexao->prepare('UPDATE posts SET title=?, content=?, updated_at=NOW() WHERE id=? AND user_id=?');
    $stmt->execute([$title, $content, $post_id, $user_id]);
    $success = 'Post editado com sucesso!';
}

// Exclusão de post
if (isset($_POST['delete_post']) && $user_id) {
    $post_id = intval($_POST['post_id']);
    $stmt = $conexao->prepare('DELETE FROM posts WHERE id=? AND user_id=?');
    $stmt->execute([$post_id, $user_id]);
    $success = 'Post excluído!';
}

// Criação de comentário
if (isset($_POST['create_comment']) && $user_id) {
    $comment_content = trim($_POST['comment_content']);
    $post_id = intval($_POST['post_id']);
    if ($comment_content && $post_id) {
        $stmtComment = $conexao->prepare('INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)');
        $stmtComment->execute([$post_id, $user_id, $comment_content]);
        $success = 'Comentário adicionado!';
    } else {
        $error = 'Preencha o comentário!';
    }
}

// Edição de comentário
if (isset($_POST['edit_comment']) && $user_id) {
    $comment_id = intval($_POST['comment_id']);
    $content = trim($_POST['content']);
    $stmt = $conexao->prepare('UPDATE comments SET content=?, created_at=NOW() WHERE id=? AND user_id=?');
    $stmt->execute([$content, $comment_id, $user_id]);
    $success = 'Comentário editado!';
}

// Exclusão de comentário
if (isset($_POST['delete_comment']) && $user_id) {
    $comment_id = intval($_POST['comment_id']);
    $stmt = $conexao->prepare('DELETE FROM comments WHERE id=? AND user_id=?');
    $stmt->execute([$comment_id, $user_id]);
    $success = 'Comentário excluído!';
}

// Busca posts do fórum
$stmt = $conexao->prepare('SELECT p.*, u.username FROM posts p JOIN users u ON p.user_id = u.id WHERE p.forum_id = ? ORDER BY p.created_at DESC');
$stmt->execute([$forum_id]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Busca comentários agrupados por post_id
$comments = [];
if ($posts) {
    $post_ids = array_column($posts, 'id');
    if ($post_ids) {
        $in = str_repeat('?,', count($post_ids) - 1) . '?';
        $stmt = $conexao->prepare("SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.post_id IN ($in) ORDER BY c.created_at ASC");
        $stmt->execute($post_ids);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $comment) {
            $comments[$comment['post_id']][] = $comment;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($forum['name']) ?> - Fórum</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <div class="caixa">
        <h1><?= htmlspecialchars($forum['name']) ?></h1>
        <p><?= htmlspecialchars($forum['description']) ?></p>
        <hr>
        <?php if (isset($success)) echo '<div style="color:green">'.htmlspecialchars($success).'</div>'; ?>
        <?php if (isset($error)) echo '<div style="color:red">'.htmlspecialchars($error).'</div>'; ?>
        <?php if ($user_id): ?>
        <div class="auth-forms">
            <h3>Criar novo post</h3>
            <form method="post">
                <input type="text" name="title" placeholder="Título do post" required><br>
                <textarea name="content" placeholder="Conteúdo" required style="width:100%;min-height:80px;"></textarea><br>
                <button type="submit" name="create_post">Publicar</button>
            </form>
        </div>
        <?php else: ?>
        <p><a href="index.php">Faça login para criar um post</a></p>
        <?php endif; ?>
        <hr>
        <h2>Posts</h2>
        <?php if (count($posts) === 0): ?>
            <p>Nenhum post ainda.</p>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <div class="subforum" style="margin-bottom:20px;">
                    <div class="sf-titulo">
                        <b><?= htmlspecialchars($post['title']) ?></b>
                        <span style="float:right;font-size:0.9em;">por <?= htmlspecialchars($post['username']) ?> em <?= date('d/m/Y H:i', strtotime($post['created_at'])) ?><?php if ($post['updated_at'] && $post['updated_at'] != $post['created_at']) echo ' (editado)'; ?></span>
                    </div>
                    <div class="sf-descricao" style="padding:15px; background:#fff; color:#222; border-radius:0 0 10px 10px;">
                        <?php if ($user_id && $post['user_id'] == $user_id && isset($_GET['edit_post']) && $_GET['edit_post'] == $post['id']): ?>
                            <form method="post">
                                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required><br>
                                <textarea name="content" required style="width:100%;min-height:80px;"><?= htmlspecialchars($post['content']) ?></textarea><br>
                                <button type="submit" name="edit_post">Salvar</button>
                                <a href="forum.php?id=<?= $forum_id ?>">Cancelar</a>
                            </form>
                        <?php else: ?>
                            <?= nl2br(htmlspecialchars($post['content'])) ?>
                            <?php if ($user_id && $post['user_id'] == $user_id): ?>
                                <form method="post" style="display:inline; float:right; margin-left:10px;">
                                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                    <button type="submit" name="delete_post" onclick="return confirm('Excluir este post?');">Excluir</button>
                                </form>
                                <a href="forum.php?id=<?= $forum_id ?>&edit_post=<?= $post['id'] ?>" style="float:right;">Editar</a>
                            <?php endif; ?>
                        <?php endif; ?>
                        <div style="clear:both;"></div>
                    </div>
                    <div style="padding:10px 20px; background:#f9f9f9; border-radius:0 0 10px 10px;">
                        <b>Comentários:</b>
                        <?php if (!empty($comments[$post['id']])): ?>
                            <?php foreach ($comments[$post['id']] as $comment): ?>
                                <div style="margin:8px 0; padding:8px; background:#eee; border-radius:5px; color:#222;">
                                    <?php if ($user_id && $comment['user_id'] == $user_id && isset($_GET['edit_comment']) && $_GET['edit_comment'] == $comment['id']): ?>
                                        <form method="post">
                                            <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                            <textarea name="content" required style="width:100%;min-height:40px;"><?= htmlspecialchars($comment['content']) ?></textarea><br>
                                            <button type="submit" name="edit_comment">Salvar</button>
                                            <a href="forum.php?id=<?= $forum_id ?>">Cancelar</a>
                                        </form>
                                    <?php else: ?>
                                        <span style="font-weight:bold; color:#327f32;"><?= htmlspecialchars($comment['username']) ?></span>:
                                        <?= nl2br(htmlspecialchars($comment['content'])) ?>
                                        <span style="float:right; font-size:0.85em; color:#888;">em <?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?></span>
                                        <?php if ($user_id && $comment['user_id'] == $user_id): ?>
                                            <form method="post" style="display:inline; float:right; margin-left:10px;">
                                                <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                                <button type="submit" name="delete_comment" onclick="return confirm('Excluir este comentário?');">Excluir</button>
                                            </form>
                                            <a href="forum.php?id=<?= $forum_id ?>&edit_comment=<?= $comment['id'] ?>" style="float:right;">Editar</a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <div style="clear:both;"></div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div style="color:#888;">Nenhum comentário.</div>
                        <?php endif; ?>
                        <?php if ($user_id): ?>
                        <form method="post" style="margin-top:10px;">
                            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                            <textarea name="comment_content" placeholder="Comente aqui..." required style="width:100%;min-height:40px;"></textarea><br>
                            <button type="submit" name="create_comment">Comentar</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <a href="forums.php">&larr; Voltar para Fóruns</a>
    </div>
</body>
</html>
