<?php
require 'auth.php';
include 'config/conexao.php';

if (!isset($_GET['id'])) {
    die("ID do técnico não fornecido.");
}

$id = $_GET['id'];

// Verificar se o técnico pode ser excluído
$sql = "SELECT * FROM intervencoes WHERE tecnico_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // O técnico está associado a uma intervenção, não pode ser excluído
    header("Location: ..pulic/listar_tecnicos.php?erro=" . urlencode("Este técnico não pode ser excluído por estar associado a uma intervenção."));
    exit();
}

// Excluir o técnico
$sql = "DELETE FROM tb_utilizador WHERE Cod_utilizador = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: ..public/listar_tecnicos.php?sucesso=" . urlencode("Técnico excluído com sucesso!"));
} else {
    header("Location: ..public/listar_tecnicos.php?erro=" . urlencode("Erro ao excluir técnico: " . $stmt->error));
}

$stmt->close();
$conn->close();
