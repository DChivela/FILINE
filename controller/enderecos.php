<?php
require 'auth.php';
include '../config/conexao.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// --- AÇÃO: Cadastrar ou Editar ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $acao   = $_POST['acao'];
  $morada = trim($_POST['morada']);
  $rua    = trim($_POST['rua']);

  try {
    if ($acao === 'cadastrar') {
      $endereco = trim($_POST['endereco']);
      $stmt = $pdo->prepare("INSERT INTO enderecos (endereco, morada, rua) VALUES (?, ?, ?)");
      $stmt->execute([$endereco, $morada, $rua]);
      $_SESSION['success'] = "Endereço cadastrado com sucesso.";
    } elseif ($acao === 'editar') {
      $endereco = $_POST['endereco'];
      $stmt = $pdo->prepare("UPDATE enderecos SET morada = ?, rua = ? WHERE endereco = ?");
      $stmt->execute([$morada, $rua, $endereco]);
      $_SESSION['success'] = "Endereço atualizado com sucesso.";
    }
    header("Location: enderecos.php");
    exit;
  } catch (PDOException $e) {
    $_SESSION['error'] = "Erro: " . $e->getMessage();
    header("Location: enderecos.php");
    exit;
  }
}

// --- Buscar endereço para edição ---
$endereco_editar = null;
if (isset($_GET['edit'])) {
  $endereco_id = $_GET['edit'];
  $stmt = $pdo->prepare("SELECT endereco, morada, rua FROM enderecos WHERE endereco = ?");
  $stmt->execute([$endereco_id]);
  $endereco_editar = $stmt->fetch(PDO::FETCH_ASSOC);
}

// --- AÇÃO: Eliminar ---
if (isset($_GET['delete'])) {
  $endereco_id = $_GET['delete'];
  try {
    // Verifica se está ligado a outra tabela (exemplo: tb_pre_triagem)
    $verifica = $pdo->prepare("SELECT COUNT(*) FROM tb_pre_triagem WHERE Endereco = ?");
    $verifica->execute([$endereco_id]);
    $ligado = $verifica->fetchColumn();

    if ($ligado > 0) {
      $_SESSION['error'] = "Não é possível eliminar. Este endereço está ligado a outros registos.";
    } else {
      $stmt = $pdo->prepare("DELETE FROM enderecos WHERE endereco = ?");
      $stmt->execute([$endereco_id]);
      $_SESSION['success'] = "Endereço eliminado com sucesso.";
    }
  } catch (PDOException $e) {
    $_SESSION['error'] = "Erro: " . $e->getMessage();
  }
  header("Location: enderecos.php");
  exit;
}

// --- Listar todos os endereços ---
$enderecos = $pdo->query("SELECT endereco, morada, rua FROM enderecos ORDER BY morada ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title>Endereços</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="../public/favicon.ico" type="image/x-icon">
</head>
<body class="bg-light">
    <?php include '../public/header.php'; ?>
  <div class="container mt-5">
    <h2 class="mb-4">Endereços</h2>

    <?php
    if (isset($_SESSION['success'])) {
      echo "<div class='alert alert-success text-center'>{$_SESSION['success']}</div>";
      unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
      echo "<div class='alert alert-danger text-center'>{$_SESSION['error']}</div>";
      unset($_SESSION['error']);
    }
    ?>

    <!-- Formulário de Cadastro/Edição -->
    <form method="post" action="enderecos.php" class="card p-4 shadow mb-4">
      <input type="hidden" name="acao" value="<?= $endereco_editar ? 'editar' : 'cadastrar' ?>">
      <?php if ($endereco_editar): ?>
        <input type="hidden" name="endereco" value="<?= htmlspecialchars($endereco_editar['endereco']) ?>">
      <?php endif; ?>

      <?php if (!$endereco_editar): ?>
        <!-- <div class="mb-3">
          <label class="form-label">Código do Endereço:</label>
          <input type="text" name="endereco" class="form-control" required>
        </div> -->
      <?php endif; ?>

      <div class="mb-3">
        <label class="form-label">Morada:</label>
        <input type="text" name="morada" class="form-control" required value="<?= $endereco_editar ? htmlspecialchars($endereco_editar['morada']) : '' ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Rua:</label>
        <input type="text" name="rua" class="form-control" value="<?= $endereco_editar ? htmlspecialchars($endereco_editar['rua']) : '' ?>">
      </div>
      <button type="submit" class="btn btn-<?= $endereco_editar ? 'warning' : 'primary' ?> ">
        <?= $endereco_editar ? 'Atualizar' : 'Gravar' ?>
      </button>
      <a href="dashboard.php" class="btn btn-secondary mt-2">Voltar</a>
    </form>

    <!-- Tabela de Endereços -->
    <div class="card shadow">
      <div class="card-body table-responsive">
        <table class="table table-striped align-middle">
          <thead class="table-primary">
            <tr>
              <th>Código</th>
              <th>Morada</th>
              <th>Rua</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($enderecos as $end): ?>
              <tr>
                <td><?= htmlspecialchars($end['endereco']) ?></td>
                <td><?= htmlspecialchars($end['morada']) ?></td>
                <td><?= htmlspecialchars($end['rua']) ?></td>
                <td>
                  <a href="enderecos.php?edit=<?= urlencode($end['endereco']) ?>" class="btn btn-sm btn-warning">Editar</a>
                  <a href="enderecos.php?delete=<?= urlencode($end['endereco']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja eliminar?')">Eliminar</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>
