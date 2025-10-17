<?php
require 'auth.php';
include 'config/conexao.php';

if (!isset($_GET['id'])) {
    die("ID do técnico não fornecido.");
}

$id = $_GET['id'];

// Recuperar dados do técnico
$sql = "SELECT Cod_Utilizador, Nome_Enfermeiro, Email, Contacto, Perfil_Acesso FROM tb_utilizador WHERE Cod_Utilizador = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Técnico de saúde não encontrado.");
}

$tecnico = $result->fetch_assoc();

$usuarios = $conn->query("SELECT * FROM usuarios");

// Processar a atualização do técnico
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = $_POST['Nome_Enfermeiro'];
    $email = $_POST['Email'];
    $telefone = $_POST['Contacto'];
    $usuario_id = $_POST['Perfil_Acesso'];

    $sql = "UPDATE tb_utilizador SET Nome_Enfermeiro = ?, Email = ?, Contacto = ?, Perfil_Acesso = ? WHERE Cod_Utilizador = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $nome, $email, $telefone, $usuario_id, $id);

    if ($stmt->execute()) {
        header("Location: ../public/listar_tecnicos.php?sucesso=" . urlencode("Técnico atualizado com sucesso!"));
        exit();
    } else {
        $error = "Erro ao atualizar: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
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
    <?php include 'loader.php'; ?>
    <div class="container mt-5">
        <h2 class="mb-4">Editar Técnico</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="editar_tecnico.php?id=<?= $tecnico['id'] ?>" class="card p-4 shadow">
            <div class="mb-3">
                <label class="form-label">Nome:</label>
                <input type="text" name="nome" class="form-control" required value="<?= htmlspecialchars($tecnico['nome']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Email:</label>
                <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($tecnico['email']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Telefone:</label>
                <input type="text" name="telefone" class="form-control" required maxlength="15" value="<?= htmlspecialchars($tecnico['telefone']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Usuário Associado:</label>
                <select name="user_id" class="form-select" required>
                    <?php while ($row = $usuarios->fetch_assoc()) {
                        $selected = $tecnico['user_id'] == $row['id'] ? 'selected' : '';
                        echo "<option value='{$row['id']}' {$selected}>{$row['nome']}</option>";
                    } ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
            <a href="listar_tecnicos.php" class="btn btn-secondary">Voltar</a>
        </form>
    </div>
</body>

</html>