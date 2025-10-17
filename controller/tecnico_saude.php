<?php
// session_start();
require 'auth.php';
include '../config/conexao.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// $tecnicos = $conn->query("SELECT * FROM tecnicos order by nome asc");
$usuarios = $pdo->query("SELECT * FROM tb_utilizador order by Nome_Enfermeiro asc");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $nome     = $_POST['Nome_Enfermeiro'];
  $email    = $_POST['Email'];
  $telefone = $_POST['Contacto'];
  $senha    = password_hash($_POST['Senha'], PASSWORD_DEFAULT);
  $nivel_acesso = $_POST['Perfil_Acesso'];

  $sql  = "INSERT INTO tb_utilizador (Nome_Enfermeiro, Email, Contacto, Senha, Perfil_Acesso) VALUES (?, ?, ?, ?, ?)";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$nome, $email, $telefone, $senha, $nivel_acesso]);

  header("Location: ../public/listar_tecnicos.php?sucesso=" . urlencode("Técnico de saúde cadastrado com sucesso!"));
  exit();
 } //catch (PDOException $e) {
//   $error = "Erro ao gravar: " . $e->getMessage();
?>


<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="title" content="FILINE 2025">
  <meta name="description" content="Sistema de Gestão Filas de Espera em Instituições de Saúde">
  <meta name="keywords" content="Gestão, Pacientes, Filas, Espera">
  <meta name="author" content="Filine - Estefânio Da Silva & Domingos Chivela">
  <title>Técnico</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Favicon -->
  <link rel="icon" href="../public/favicon.ico" type="image/x-icon">
</head>

<body class="bg-light">
  <div class="container mt-5">
    <h2 class="mb-4">Técnico</h2>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="tecnico_saude.php" class="card p-4 shadow">
      <div class="mb-3">
        <label class="form-label">Nome:</label>
        <input type="text" name="Nome_Enfermeiro" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email:</label>
        <input type="email" name="Email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Senha:</label>
        <input type="password" name="Senha" class="form-control" required>
      </div>
      <div class="row">
        <div class="form-group col-md-6">
          <label class="required"><i class="bi bi-telephone-inbound"></i> Telefone:</label>
          <input type="text" name="Contacto" id="telefone" class="form-control" required maxlength="15" oninput="mascaraTelefone(this)" placeholder="244 941-172-010">
        </div>
        <div class="form-group col-md-6">
          <label class="required">Perfil de Acesso:</label>
          <select name="Perfil_Acesso" class="form-select">
            <option value="normal">Normal</option>
            <option value="admin">Administrador</option>
          </select>
        </div>
      </div>


      <button type="submit" class="btn btn-primary mt-4">Gravar</button>
      <a href="../public/dashboard.php" class="btn btn-secondary mt-2">Voltar</a>
    </form>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<script>
  function mascaraTelefone(input) {
    // Remove tudo o que não for número
    let valor = input.value.replace(/\D/g, '');

    // Se começar com o código do país, formate corretamente
    if (valor.length <= 3) {
      input.value = '244 ' + valor; // Exibe apenas o código do país
    } else if (valor.length <= 6) {
      input.value = '244 ' + valor.substring(3, 6) + '-' + valor.substring(6); // +244 941-
    } else if (valor.length <= 9) {
      input.value = '244 ' + valor.substring(3, 6) + '-' + valor.substring(6, 9) + '-' + valor.substring(9); // +244 941-001-
    } else if (valor.length <= 13) {
      input.value = '244 ' + valor.substring(3, 6) + '-' + valor.substring(6, 9) + '-' + valor.substring(9, 12); // +244 941-001-002
    }
  }
</script>