<?php
$host = "127.0.0.1";
$porta = "3306";
$banco = "car_shop";
$usuario = "root";
$senha = "";

try {
    $conexao = new PDO("mysql:host=" . $host . ";port=" . $porta . ";dbname=" . $banco, $usuario, $senha);
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $erro) {
    echo "Não foi possível conectar ao banco de dados.<br>" . $erro->getMessage();
    exit;
}
?>
