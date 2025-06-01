<?php
include 'db.php';

// Cadastro
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";
    $stmt = $conexao->prepare($sql);
    if ($stmt->execute([':username' => $username, ':password' => $password])) {
        $register_success = true;
    } else {
        $register_error = 'Erro ao cadastrar usuário.';
    }
}

// Login
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: index.php');
            exit;
        } else {
            $login_error = 'Senha incorreta.';
        }
    } else {
        $login_error = 'Usuário não encontrado.';
    }
}

// Exclusão de conta
if (isset($_POST['delete_account'])) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $conexao->prepare($sql);
        if ($stmt->execute([':id' => $user_id])) {
            session_destroy();
            $delete_success = true;
        } else {
            $delete_error = 'Erro ao excluir conta.';
        }
    }
}

if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Cadastro de novo carro
if (isset($_POST['add_car']) && isset($_SESSION['user_id'])) {
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $price = $_POST['price'];
    $image = $_POST['image'];
    $description = $_POST['description'];
    $sql = "INSERT INTO cars (brand, model, year, price, image, description) VALUES (:brand, :model, :year, :price, :image, :description)";
    $stmt = $conexao->prepare($sql);
    if ($stmt->execute([
        ':brand' => $brand,
        ':model' => $model,
        ':year' => $year,
        ':price' => $price,
        ':image' => $image,
        ':description' => $description
    ])) {
        $add_car_success = true;
    } else {
        $add_car_error = 'Erro ao cadastrar carro.';
    }
}
?>
