<?php
require 'auth.php';
include '../config/conexao.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID do técnico não fornecido ou inválido.");
}

$id = (int)$_GET['id'];

try {
    // Verifica se o técnico existe
    $stmt = $pdo->prepare("SELECT Cod_Utilizador FROM tb_utilizador WHERE Cod_Utilizador = ?");
    $stmt->execute([$id]);
    if ($stmt->rowCount() === 0) {
        die("Técnico não encontrado.");
    }

    // Elimina o técnico
    $stmt = $pdo->prepare("DELETE FROM tb_utilizador WHERE Cod_Utilizador = ?");
    $stmt->execute([$id]);

    header("Location: ../public/listar_tecnicos.php?sucesso=" . urlencode("Técnico excluído com sucesso!"));
    exit();
} catch (PDOException $e) {
    $erro = "Erro ao excluir: " . $e->getMessage();
    header("Location: ../public/listar_tecnicos.php?erro=" . urlencode($erro));
    exit();
}
?>
