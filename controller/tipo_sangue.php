<?php
require 'auth.php';
include '../config/conexao.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Mensagens
if (isset($_SESSION['error'])) {
    echo "<div class='alert alert-danger text-center'>{$_SESSION['error']}</div>";
    unset($_SESSION['error']);
}
if (isset($_SESSION['success'])) {
    echo "<div class='alert alert-success text-center'>{$_SESSION['success']}</div>";
    unset($_SESSION['success']);
}

try {
    // AÇÃO: Cadastrar
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['acao'] === 'cadastrar') {
        $sangue = trim($_POST['tipo_sangue']);
        $stmt = $pdo->prepare("INSERT INTO tb_tipo_sangue (tipo_sangue) VALUES (?)");
        $stmt->execute([$sangue]);

        $_SESSION['success'] = "Tipo de sangue cadastrado com sucesso.";
        header("Location: tipo_sangue.php");
        exit;
    }

    // AÇÃO: Editar
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['acao'] === 'editar') {
        $id = (int)$_POST['Cod_Tipo_Sangue'];
        $sangue = trim($_POST['tipo_sangue']);
        $stmt = $pdo->prepare("UPDATE tb_tipo_sangue SET tipo_sangue=? WHERE Cod_Tipo_Sangue=?");
        $stmt->execute([$sangue, $id]);

        $_SESSION['success'] = "Tipo de sangue atualizado com sucesso.";
        header("Location: tipo_sangue.php");
        exit;
    }

    // Buscar alergia para edição
    $sangue_editar = null;
    if (isset($_GET['edit'])) {
        $id_edit = (int)$_GET['edit'];
        $stmt = $pdo->prepare("SELECT Cod_Tipo_Sangue, tipo_sangue FROM tb_tipo_sangue WHERE Cod_Tipo_Sangue = ?");
        $stmt->execute([$id_edit]);
        $sangue_editar = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // AÇÃO: Eliminar
    if (isset($_GET['delete'])) {
        $id = (int)$_GET['delete'];

        // Verifica ligação com tb_paciente_alergia
        $check = $pdo->prepare("SELECT COUNT(*) FROM tb_pre_triagem WHERE Tipo_Sangue = ?");
        $check->execute([$id]);
        $ligado = $check->fetchColumn();

        if ($ligado > 0) {
            $_SESSION['error'] = "Não é possível eliminar. A alergia está ligada a outros registros.";
        } else {
            $stmt = $pdo->prepare("DELETE FROM tb_tipo_sangue WHERE Cod_Tipo_Sangue = ?");
            $stmt->execute([$id]);
            $_SESSION['success'] = "Tipo de sangue eliminado com sucesso.";
        }
        header("Location: tipo_sangue.php");
        exit;
    }

    // Listar todas as alergias
    $sangues = $pdo->query("SELECT Cod_Tipo_Sangue, tipo_sangue FROM tb_tipo_sangue ORDER BY tipo_sangue")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "Erro: " . $e->getMessage();
    header("Location: tipo_sangue.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Tipos Sangueneos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="icon" href="../public/favicon.ico" type="image/x-icon">
</head>

<body class="bg-light">
    <?php include '../public/header.php'; ?>
    <div class="container" style ="justify-content: center; align-items: center;">
        <h2 class="mb-4">Tipo Sangueneos <i class="bi bi-droplet-half"></i></h2>

        <!-- Formulário de Cadastro/Edição -->
        <div class="card mb-4 shadow ">
            <div class="card-body">
                <form method="post" class="row g-3">
                    <input type="hidden" name="acao" value="<?= $sangue_editar ? 'editar' : 'cadastrar' ?>">
                    <?php if ($sangue_editar): ?>
                        <input type="hidden" name="Cod_Tipo_Sangue" value="<?= $sangue_editar['Cod_Tipo_Sangue'] ?>">
                    <?php endif; ?>

                    <div class="col-md-10">
                        <input type="text" name="tipo_sangue" class="form-control" placeholder="Tipo de sangue" required
                            value="<?= $sangue_editar ? htmlspecialchars($sangue_editar['tipo_sangue']) : '' ?>">
                    </div>

                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-<?= $sangue_editar ? 'warning' : 'primary' ?>">
                            <?= $sangue_editar ? 'Atualizar' : 'Gravar' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabela de Alergias -->
        <div class="card shadow ">
            <div class="card-body table-responsive">
                <table class="table table-striped align-middle ">
                    <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Tipo Sangueneo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sangues as $al): ?>
                            <tr>
                                <td><?= $al['Cod_Tipo_Sangue'] ?></td>
                                <td><?= htmlspecialchars($al['tipo_sangue']) ?></td>
                                <td>
                                    <a href="tipo_sangue.php?edit=<?= $al['Cod_Tipo_Sangue'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <a href="tipo_sangue.php?delete=<?= $al['Cod_Tipo_Sangue'] ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Tem certeza que deseja eliminar este tipo sangueneo?')">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <a href="../public/dashboard.php" class="btn btn-secondary mt-4">Voltar</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>