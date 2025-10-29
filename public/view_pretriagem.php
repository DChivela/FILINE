<?php
// view_pretriagem.php
require_once __DIR__ . '/../config/conexao.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
  echo "ID inválido.";
  exit;
}

// buscar registo com joins
$sql = "SELECT p.*, t.Tipo_Sangue, a.Tipo_Alergia, e.morada
        FROM Tb_Pre_Triagem p
        LEFT JOIN tb_Tipo_Sangue t ON t.Cod_Tipo_Sangue = p.Tipo_Sangue
        LEFT JOIN Tb_Alergia a ON a.Cod_Alergia = p.Alergia
        LEFT JOIN enderecos e ON e.endereco = p.endereco
        WHERE p.Cod_Pre_Triagem = :id
        LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$r = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$r) {
  echo "Registo não encontrado.";
  exit;
}

// format helpers
function esc($v)
{
  return htmlspecialchars($v);
}
function tempo_humano($datetime_str)
{
  if (empty($datetime_str)) return '-';
  try {
    $then = new DateTime($datetime_str);
    $now = new DateTime();
    $diff = $now->diff($then);
    if ($diff->y > 0) return $diff->y . 'y ' . $diff->m . 'm';
    if ($diff->m > 0) return $diff->m . ' mes(es) ' . $diff->d . 'd';
    if ($diff->d > 0) return $diff->d . ' dia(s) ' . $diff->h . 'h';
    if ($diff->h > 0) return $diff->h . 'h ' . $diff->i . 'm';
    if ($diff->i > 0) return $diff->i . 'm';
    return $diff->s . 's';
  } catch (Exception $e) {
    return '-';
  }
}
?>
<!doctype html>
<html lang="pt">

<head>
  <meta charset="utf-8">
  <title>Detalhe Pré-Triagem #<?= esc($r['Senha_de_Atendimento']) ?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="icon" href="../public/favicon.ico" type="image/x-icon">
  <style>
    .label {
      font-weight: 700;
      color: #333;
    }

    .value {
      color: #222;
    }

    .box {
      background: #fff;
      border-radius: 8px;
      padding: 16px;
      box-shadow: 0 1px 6px rgba(0, 0, 0, 0.05);
    }

    .muted {
      color: #6c757d;
    }
  </style>
</head>

<body>
  <div class="container mt-4">
    <a href="dashboard.php" class="btn btn-sm btn-secondary mb-3">&larr; Voltar à lista</a>

    <div class="row">
      <div class="col-md-8">
        <div class="box">
          <h4>Paciente: <?= esc($r['Nome_Paciente']) ?> <small class="muted">(#<?= esc($r['Senha_de_Atendimento']) ?>)</small></h4>
          <p class="small-muted">Registado: <?= esc($r['Data_de_Registo']) ?> — Tempo de espera: <?= esc(tempo_humano($r['Data_de_Registo'])) ?></p>

          <hr>
          <div class="row mb-2">
            <div class="col-sm-6">
              <div class="label">Contacto</div>
              <div class="value"><?= esc($r['Contacto']) ?></div>
            </div>
            <div class="col-sm-6">
              <div class="label">Data Nasc.</div>
              <div class="value"><?= esc($r['Data_Nascimento']) ?></div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-sm-6">
              <div class="label">Tipo Sangue</div>
              <div class="value"><?= esc($r['Tipo_Sangue']) ?></div>
            </div>
            <div class="col-sm-6">
              <div class="label">Alergia</div>
              <div class="value"><?= esc($r['Tipo_Alergia']) ?></div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-sm-6">
              <div class="label">Grupo de Ocorrência</div>
              <div class="value"><?= esc($r['Grupo_Ocorrencia']) ?></div>
            </div>
            <div class="col-sm-6">
              <div class="label">Classificação</div>
              <div class="value"><?= esc($r['Classificacao_de_Risco']) ?></div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-sm-6">
              <div class="label">Sintoma Principal</div>
              <div class="value"><?= esc($r['Sintoma_Principal']) ?></div>
            </div>
            <div class="col-sm-6">
              <div class="label">Morada</div>
              <div class="value"><?= esc($r['morada']) ?></div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-sm-6">
              <div class="label">Motivos da Classificação</div>
              <div class="value"><?= esc($r['Motivos_Classificacao'] ?? '') ?></div>
            </div>
            <div class="col-sm-6">
              <div class="label">Observações</div>
              <div class="value"><?= esc($r['Observacoes'] ?? '') ?></div>
            </div>
          </div>





          <!-- <hr>
          <h6>Dados Adicionais</h6>
          <pre style="white-space:pre-wrap; background:#f7f7f7; padding:10px; border-radius:6px;"><?= esc(json_encode($r, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre> -->
        </div>
      </div>

      <div class="col-md-4">
        <div class="box">
          <div hidden>
            <h6>Operações</h6>
            <p>
              <a href="edit_pretriagem.php?id=<?= (int)$r['Cod_Pre_Triagem'] ?>" class="btn btn-sm btn-primary mb-2">Editar (se existir)</a>
              <a href="delete_pretriagem.php?id=<?= (int)$r['Cod_Pre_Triagem'] ?>" class="btn btn-sm btn-danger mb-2" onclick="return confirm('Tem a certeza que deseja eliminar?')">Eliminar</a>
            </p>
            <hr>
          </div>
          <h6>Registo</h6>
          <p class="muted">ID: <?= (int)$r['Cod_Pre_Triagem'] ?></p>
          <p class="muted">Senha: <?= esc($r['Senha_de_Atendimento']) ?></p>
          <p class="muted">Criado: <?= esc($r['Data_de_Registo']) ?></p>
          <div class="label">Estado: <p class="muted"><?= esc($r['Situacao']) ?></p></div>
        </div>
      </div>
    </div>

  </div>
</body>

</html>