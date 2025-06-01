<?php
// auth.php: Lógica de autenticação, cadastro e exclusão de conta
session_start();
include 'db.php';

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

// Cadastro
if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    if ($username && $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $conexao->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
            $stmt->execute([$username, $hash]);
            $_SESSION['user'] = $username;
            redirect('index.php');
        } catch (PDOException $e) {
            $error = 'Usuário já existe!';
        }
    } else {
        $error = 'Preencha todos os campos!';
    }
}

// Login
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $stmt = $conexao->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $username;
        redirect('index.php');
    } else {
        $error = 'Usuário ou senha inválidos!';
    }
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    redirect('index.php');
}

// Excluir conta
if (isset($_POST['delete_account']) && isset($_SESSION['user'])) {
    $username = $_SESSION['user'];
    $stmt = $conexao->prepare('DELETE FROM users WHERE username = ?');
    $stmt->execute([$username]);
    session_destroy();
    redirect('index.php');
}
