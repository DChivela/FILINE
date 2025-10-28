<?php
require 'auth.php';
include '../config/conexao.php';

if (!isset($_GET['id'])) {
    die("ID do técnico não fornecido.");
}

$id = (int)$_GET['id'];

// Recuperar dados do técnico
$sql = "SELECT Cod_Utilizador, Nome_Enfermeiro, Email, Contacto, Perfil_Acesso FROM tb_utilizador WHERE Cod_Utilizador = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$tecnico = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tecnico) {
    die("Técnico de saúde não encontrado.");
}

// Processar a atualização do técnico
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = $_POST['Nome_Enfermeiro'];
    $email = $_POST['Email'];
    $telefone = $_POST['Contacto'];
    $perfil = $_POST['Perfil_Acesso'];

    $sql = "UPDATE tb_utilizador SET Nome_Enfermeiro = ?, Email = ?, Contacto = ?, Perfil_Acesso = ? WHERE Cod_Utilizador = ?";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$nome, $email, $telefone, $perfil, $id])) {
        header("Location: ../public/listar_tecnicos.php?sucesso=" . urlencode("Técnico atualizado com sucesso!"));
        exit();
    } else {
        $error = "Erro ao atualizar.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Editar Técnico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../public/favicon.ico" type="image/x-icon">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4">Editar Técnico</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="editar_tecnico.php?id=<?= $tecnico['Cod_Utilizador'] ?>" class="card p-4 shadow">
            <div class="mb-3">
                <label class="form-label">Nome:</label>
                <input type="text" name="Nome_Enfermeiro" class="form-control" required value="<?= htmlspecialchars($tecnico['Nome_Enfermeiro']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Email:</label>
                <input type="email" name="Email" class="form-control" required value="<?= htmlspecialchars($tecnico['Email']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Telefone:</label>
                <input type="text" name="Contacto" class="form-control" required maxlength="15" value="<?= htmlspecialchars($tecnico['Contacto']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Perfil de Acesso:</label>
                <select name="Perfil_Acesso" class="form-select" required>
                    <option value="normal" <?= ($tecnico['Perfil_Acesso'] == 'normal') ? 'selected' : '' ?>>Normal</option>
                    <option value="admin" <?= ($tecnico['Perfil_Acesso'] == 'admin') ? 'selected' : '' ?>>Administrador</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
            <a href="../public/listar_tecnicos.php" class="btn btn-secondary">Voltar</a>
        </form>
    </div>
</body>

</html>
