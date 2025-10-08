<?php
// public/index.php
require_once __DIR__ . '/src/config.php';
$stmt = $pdo->query("SELECT Cod_Tipo_Sangue, Tipo_Sangue FROM tb_Tipo_Sangue ORDER BY Tipo_Sangue");
$tipos = $stmt->fetchAll();
$stmt = $pdo->query("SELECT Cod_Alergia, Tipo_Alergia FROM Tb_Alergia ORDER BY Tipo_Alergia");
$alergias = $stmt->fetchAll();
?>
<!doctype html>
<html lang="pt">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pré-Triagem</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="public/css/styles.css">
  <style>
    /* .topbar { background:#0b6aa3; color:white; padding:10px 20px; }
    footer { background:#0b6aa3; color:white; padding:10px; text-align:center; position:fixed; left:0; right:0; bottom:0; }
    body { padding-bottom:60px; }
    .required:after { content:" *"; color:#d00; } */
  </style>
</head>

<body>
  <div class="topbar d-flex justify-content-between align-items-center">
    <div><strong>Filine-ON</strong></div>
    <div>
      <a class="text-white mr-3" href="#">Início</a>
      <a class="text-white mr-3" href="public/list_pretriagem.php">Consultar Espera</a>
      <a class="text-white" href="#">Entrar</a>
    </div>
  </div>

  <div class="container mt-4">
    <form action="public/processa_pretriagem.php" method="post" class="needs-validation" novalidate>
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label class="required">Endereço (Morada)</label>
            <input name="Endereco" class="form-control" />
          </div>
          <div class="form-group">
            <label class="required">Email</label>
            <input name="Email" type="email" class="form-control" />
          </div>
          <div class="form-group">
            <label class="required">Contato Telefónico</label>
            <input name="Contacto" class="form-control" required />
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label class="required">Nome do Paciente</label>
            <input name="Nome_Paciente" class="form-control" required />
          </div>
          <div class="form-group">
            <label>Género do Paciente</label>
            <select name="Genero_Paciente" class="form-control">
              <option value="" required>Selecione</option>
              <option>Masculino</option>
              <option>Feminino</option>
              <option>Outro</option>
            </select>
          </div>
          <div class="form-group">
            <label>Tipo de Ocorrência</label>
            <input name="Tipo_Ocorrencia" class="form-control" required/>
          </div>

          <div class="form-group">
            <label>Classificação de Risco</label>
            <input name="Classificacao_de_Risco" class="form-control" />
          </div>

          <div class="form-group">
            <label>Tipo de Sangue</label>
            <select name="Tipo_Sangue" class="form-control">
              <option value="">--</option>
              <?php foreach ($tipos as $t): ?>
                <option value="<?= htmlspecialchars($t['Cod_Tipo_Sangue']) ?>"><?= htmlspecialchars($t['Tipo_Sangue']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Situação</label>
            <input name="Situacao" class="form-control" required />
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label>Nome do Encarregado</label>
            <input name="Nome_Encarregado" class="form-control" />
          </div>

          <div class="form-group">
            <label>Data de Nascimento</label>
            <input name="Data_Nascimento" type="date" class="form-control" />
          </div>

          <div class="form-group">
            <label>Sintoma Principal</label>
            <input name="Sintoma_Principal" class="form-control" required />
          </div>

          <div class="form-group">
            <label>Data de Registo</label>
            <input name="Data_de_Registo" type="datetime-local" class="form-control" />
          </div>

          <div class="form-group">
            <label>Alergia</label>
            <select name="Alergia" class="form-control">
              <option value="">Nenhuma</option>
              <?php foreach ($alergias as $a): ?>
                <option value="<?= htmlspecialchars($a['Cod_Alergia']) ?>"><?= htmlspecialchars($a['Tipo_Alergia']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Senha de Atendimento (opcional)</label>
            <input disabled name="Senha_de_Atendimento" class="form-control" placeholder="Senha Automática" />
          </div>

        </div>
      </div>

      <div class="text-right">
        <button class="btn btn-primary">Registar Pré-Triagem</button>
      </div>
    </form>
  </div>

  <footer>© 2024 Filine-ON. Todos os direitos reservados.</footer>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script>
    // frontend basic validation bootstrap
    (function() {
      'use strict';
      window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        Array.prototype.filter.call(forms, function(form) {
          form.addEventListener('submit', function(event) {
            if (form.checkValidity() === false) {
              event.preventDefault();
              event.stopPropagation();
            }
            form.classList.add('was-validated');
          }, false);
        });
      }, false);
    })();
  </script>
</body>

</html>