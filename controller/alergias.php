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
        $alergia = trim($_POST['tipo_alergia']);
        $stmt = $pdo->prepare("INSERT INTO tb_alergia (tipo_alergia) VALUES (?)");
        $stmt->execute([$alergia]);

        $_SESSION['success'] = "Alergia cadastrada com sucesso.";
        header("Location: alergias.php");
        exit;
    }

    // AÇÃO: Editar
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['acao'] === 'editar') {
        $id = (int)$_POST['cod_alergia'];
        $alergia = trim($_POST['tipo_alergia']);
        $stmt = $pdo->prepare("UPDATE tb_alergia SET tipo_alergia=? WHERE cod_alergia=?");
        $stmt->execute([$alergia, $id]);

        $_SESSION['success'] = "Alergia atualizada com sucesso.";
        header("Location: alergias.php");
        exit;
    }

    // Buscar alergia para edição
    $alergia_editar = null;
    if (isset($_GET['edit'])) {
        $id_edit = (int)$_GET['edit'];
        $stmt = $pdo->prepare("SELECT cod_alergia, tipo_alergia FROM tb_alergia WHERE cod_alergia = ?");
        $stmt->execute([$id_edit]);
        $alergia_editar = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // AÇÃO: Eliminar
    if (isset($_GET['delete'])) {
        $id = (int)$_GET['delete'];

        // Verifica ligação com tb_paciente_alergia
        $check = $pdo->prepare("SELECT COUNT(*) FROM tb_pre_triagem WHERE Alergia = ?");
        $check->execute([$id]);
        $ligado = $check->fetchColumn();

        if ($ligado > 0) {
            $_SESSION['error'] = "Não é possível eliminar. A alergia está ligada a outros registros.";
        } else {
            $stmt = $pdo->prepare("DELETE FROM tb_alergia WHERE cod_alergia = ?");
            $stmt->execute([$id]);
            $_SESSION['success'] = "Alergia eliminada com sucesso.";
        }
        header("Location: alergias.php");
        exit;
    }

    // Listar todas as alergias
    $alergias = $pdo->query("SELECT cod_alergia, tipo_alergia FROM tb_alergia ORDER BY tipo_alergia")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "Erro: " . $e->getMessage();
    header("Location: alergias.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Gestão de Alergias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../public/favicon.ico" type="image/x-icon">
</head>

<body class="bg-light">
    <?php include '../public/header.php'; ?>
    <div class="container" style ="justify-content: center; align-items: center;">
        <h2 class="mb-4">Alergias</h2>

        <!-- Formulário de Cadastro/Edição -->
        <div class="card mb-4 shadow ">
            <div class="card-body">
                <form method="post" class="row g-3">
                    <input type="hidden" name="acao" value="<?= $alergia_editar ? 'editar' : 'cadastrar' ?>">
                    <?php if ($alergia_editar): ?>
                        <input type="hidden" name="cod_alergia" value="<?= $alergia_editar['cod_alergia'] ?>">
                    <?php endif; ?>

                    <div class="col-md-10">
                        <input type="text" name="tipo_alergia" class="form-control" placeholder="Nome da alergia" required
                            value="<?= $alergia_editar ? htmlspecialchars($alergia_editar['tipo_alergia']) : '' ?>">
                    </div>

                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-<?= $alergia_editar ? 'warning' : 'primary' ?>">
                            <?= $alergia_editar ? 'Atualizar' : 'Gravar' ?>
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
                            <th>Alergia</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alergias as $al): ?>
                            <tr>
                                <td><?= $al['cod_alergia'] ?></td>
                                <td><?= htmlspecialchars($al['tipo_alergia']) ?></td>
                                <td>
                                    <a href="alergias.php?edit=<?= $al['cod_alergia'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <a href="alergias.php?delete=<?= $al['cod_alergia'] ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Tem certeza que deseja eliminar esta alergia?')">Eliminar</a>
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