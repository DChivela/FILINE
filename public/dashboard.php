<?php
// list_pretriagem.php
require_once __DIR__ . '/../controller/auth.php';
require_once __DIR__ . '/../config/conexao.php';

// parametros de pesquisa e paginação
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$filter = isset($_GET['filter']) ? strtoupper(trim($_GET['filter'])) : '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Contagem por classificação (total global)
$countSql = "SELECT COALESCE(Classificacao_de_Risco,'AZUL') AS classif, COUNT(*) AS cnt
             FROM Tb_Pre_Triagem
             GROUP BY Classificacao_de_Risco";
$countStmt = $pdo->query($countSql);
$countsRaw = $countStmt->fetchAll(PDO::FETCH_ASSOC);
$classes = ['VERMELHO', 'LARANJA', 'AMARELO', 'VERDE', 'AZUL'];
$counts = array_fill_keys($classes, 0);
$total = 0;
foreach ($countsRaw as $c) {
  $k = strtoupper($c['classif'] ?? 'AZUL');
  if (!in_array($k, $classes)) $k = 'AZUL';
  $counts[$k] = (int) $c['cnt'];
  $total += (int) $c['cnt'];
}

// Monta where dinamico para pesquisa / filtro
$where = [];
$params = [];
if ($q !== '') {
  // busca por nome ou senha
  $where[] = "(p.Nome_Paciente LIKE :q OR p.Senha_de_Atendimento LIKE :q)";
  $params[':q'] = '%' . $q . '%';
}
if ($filter !== '' && in_array($filter, $classes)) {
  $where[] = "p.Classificacao_de_Risco = :filter";
  $params[':filter'] = $filter;
}
$whereSql = '';
if (!empty($where)) $whereSql = 'WHERE ' . implode(' AND ', $where);

// total de linhas que atendem ao filtro (para paginação)
$countFilteredSql = "SELECT COUNT(*) FROM Tb_Pre_Triagem p $whereSql";
$countFilteredStmt = $pdo->prepare($countFilteredSql);
$countFilteredStmt->execute($params);
$totalFiltered = (int) $countFilteredStmt->fetchColumn();
$totalPages = max(1, (int) ceil($totalFiltered / $perPage));

// query principal com ordenação por prioridade e por Data_de_Registo asc (mais antigo em primeiro)
$sql = "SELECT p.Cod_Pre_Triagem, p.Nome_Paciente, p.Senha_de_Atendimento, p.Sintoma_Principal, p.Data_de_Registo,
               p.Classificacao_de_Risco, p.Grupo_Ocorrencia, p.Motivos_Classificacao,
               t.Tipo_Sangue, a.Tipo_Alergia, e.morada
        FROM Tb_Pre_Triagem p
        LEFT JOIN tb_Tipo_Sangue t ON t.Cod_Tipo_Sangue = p.Tipo_Sangue
        LEFT JOIN Tb_Alergia a ON a.Cod_Alergia = p.Alergia
        LEFT JOIN enderecos e ON e.endereco = p.endereco
        $whereSql
        ORDER BY
          CASE
            WHEN p.Classificacao_de_Risco = 'VERMELHO' THEN 1
            WHEN p.Classificacao_de_Risco = 'LARANJA' THEN 2
            WHEN p.Classificacao_de_Risco = 'AMARELO' THEN 3
            WHEN p.Classificacao_de_Risco = 'VERDE' THEN 4
            WHEN p.Classificacao_de_Risco = 'AZUL' THEN 5
            ELSE 6
          END,
          p.Data_de_Registo ASC
        LIMIT :lim OFFSET :off";

$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) $stmt->bindValue($k, $v);
$stmt->bindValue(':lim', (int)$perPage, PDO::PARAM_INT);
$stmt->bindValue(':off', (int)$offset, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// helper para tempo desde registro
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
  <title>Lista de Espera — Filine-ON</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="title" content="FILINE 2025">
  <meta name="description" content="Sistema de Gestão Filas de Espera em Instituições de Saúde">
  <meta name="keywords" content="Gestão, Pacientes, Filas, Espera">
  <meta name="author" content="Filine - Estefânio Da Silva & Domingos Chivela">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="icon" href="../public/favicon.ico" type="image/x-icon">
  <style>
    body {
      background: #f8f9fa;
    }

    .topbar {
      background: #0b6aa3;
      color: white;
      padding: 10px 20px;
    }

    .card-risk {
      color: white;
      border-radius: 8px;
    }

    .card-risk .count {
      font-size: 1.6rem;
      font-weight: 700;
    }

    .risk-vermelho {
      background: #d9534f;
    }

    .risk-laranja {
      background: #f39c12;
    }

    .risk-amarelo {
      background: #f7dc6f;
      color: #000;
    }

    .risk-verde {
      background: #5cb85c;
    }

    .risk-azul {
      background: #3498db;
    }

    .badge-class {
      padding: .45em .6em;
      font-weight: 600;
      color: #fff;
      border-radius: .35rem;
    }

    .badge-class.vermelho {
      background: #c9302c;
    }

    .badge-class.laranja {
      background: #d98d0a;
    }

    .badge-class.amarelo {
      background: #c9b037;
      color: #000;
    }

    .badge-class.verde {
      background: #3c8d40;
    }

    .badge-class.azul {
      background: #1f78b4;
    }

    .table-row-clickable:hover {
      background: #f1f7fb;
      cursor: pointer;
    }

    .small-muted {
      color: #6c757d;
      font-size: .9rem;
    }

    .filter-btn.active {
      box-shadow: 0 0 0 0.2rem rgba(0, 0, 0, 0.08);
    }

    footer {
      background: #0b6aa3;
      color: white;
      padding: 10px;
      text-align: center;
      margin-top: 20px;
    }
  </style>
</head>

<body>
  <div class="topbar d-flex justify-content-between align-items-center">
    <div><strong>Filine-ON</strong></div>
    <div>
      <a class="text-white mr-3" href="../index.php">Início</a>
      <a class="text-white mr-3" href="list_pretriagem.php">Consultar Espera</a>
      <a class="text-white" href="public/login.php">Entrar</a>
      <a class="text-white" href="../controller/tecnico_saude.php">Cadastrar Técnico</a>
    </div>
  </div>

  <div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="mb-0">Lista de Espera</h4>
      <div>
        <form class="form-inline" method="get" action="">
          <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
          <div class="form-group mr-2">
            <input name="q" value="<?= htmlspecialchars($q) ?>" class="form-control form-control-sm" placeholder="Pesquisar nome ou senha">
          </div>
          <button class="btn btn-sm btn-outline-primary mr-2" type="submit">Pesquisar</button>
          <?php if ($q !== '' || $filter !== ''): ?>
            <a class="btn btn-sm btn-secondary" href="list_pretriagem.php">Limpar</a>
          <?php endif; ?>
        </form>
      </div>
    </div>

    <!-- CARDS DE CONTAGEM -->
    <div class="row mb-3">
      <?php
      $cardInfo = [
        ['key' => 'VERMELHO', 'class' => 'risk-vermelho', 'icon' => 'fas fa-exclamation-triangle'],
        ['key' => 'LARANJA', 'class' => 'risk-laranja', 'icon' => 'fas fa-exclamation-circle'],
        ['key' => 'AMARELO', 'class' => 'risk-amarelo', 'icon' => 'fas fa-hourglass-half'],
        ['key' => 'VERDE', 'class' => 'risk-verde', 'icon' => 'fas fa-check-circle'],
        ['key' => 'AZUL', 'class' => 'risk-azul', 'icon' => 'fas fa-info-circle']
      ];
      foreach ($cardInfo as $ci):
      ?>
        <div class="col-md-2 col-sm-6 mb-2">
          <div class="card card-risk <?= $ci['class'] ?> p-3 filter-btn" data-filter="<?= $ci['key'] ?>" role="button">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="small-muted"><?= $ci['key'] ?></div>
                <div class="count"><?= $counts[$ci['key']] ?></div>
              </div>
              <div><i class="<?= $ci['icon'] ?> fa-2x"></i></div>
            </div>
            <div class="small-muted mt-2"><?= $total ? round($counts[$ci['key']] / $total * 100) : 0 ?>% do total</div>
          </div>
        </div>
      <?php endforeach; ?>
      <div class="col-md-2 col-sm-6 mb-2">
        <div class="card p-3" role="button" id="clearFilterBtn">
          <div class="small-muted">Mostrar Todos</div>
          <div class="count"><?= $total ?></div>
        </div>
      </div>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'ok'): ?>
      <div class="alert alert-success">Registado com sucesso. Senha: <strong><?= htmlspecialchars($_GET['senha'] ?? '') ?></strong>. Classificação: <strong><?= htmlspecialchars($_GET['class'] ?? '') ?></strong></div>
    <?php endif; ?>

    <!-- TABELA -->
    <div class="table-responsive">
      <table class="table table-hover table-sm">
        <thead class="thead-light">
          <tr>
            <th style="width:100px">Senha</th>
            <th>Nome</th>
            <th>Sintoma</th>
            <th>Grupo</th>
            <th>Classificação</th>
            <th>Tempo de Espera</th>
            <th>Data de Registo</th>
            <th>Sangue</th>
            <th>Alergia</th>
            <th>Morada</th>
          </tr>
        </thead>
        <tbody id="listaCorpo">
          <?php foreach ($rows as $r):
            $class = strtoupper(trim($r['Classificacao_de_Risco'] ?? 'AZUL'));
            $badgeClass = 'azul';
            if ($class === 'VERMELHO') $badgeClass = 'vermelho';
            elseif ($class === 'LARANJA') $badgeClass = 'laranja';
            elseif ($class === 'AMARELO') $badgeClass = 'amarelo';
            elseif ($class === 'VERDE') $badgeClass = 'verde';
            $tempo = tempo_humano($r['Data_de_Registo']);
          ?>
            <tr class="table-row-clickable" data-class="<?= $class ?>" data-id="<?= (int)$r['Cod_Pre_Triagem'] ?>">
              <td><?= htmlspecialchars($r['Senha_de_Atendimento']) ?></td>
              <td><?= htmlspecialchars($r['Nome_Paciente']) ?></td>
              <td><?= htmlspecialchars($r['Sintoma_Principal']) ?></td>
              <td><?= htmlspecialchars($r['Grupo_Ocorrencia'] ?? '') ?></td>
              <td><span class="badge-class <?= $badgeClass ?>"><?= htmlspecialchars($class) ?></span></td>
              <td><?= htmlspecialchars($tempo) ?></td>
              <td><?= htmlspecialchars($r['Data_de_Registo']) ?></td>
              <td><?= htmlspecialchars($r['Tipo_Sangue']) ?></td>
              <td><?= htmlspecialchars($r['Tipo_Alergia']) ?></td>
              <td><?= htmlspecialchars($r['morada']) ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($rows)): ?>
            <tr>
              <td colspan="9" class="text-center">Nenhum registo encontrado.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- PAGINAÇÃO -->
    <nav aria-label="Pagination">
      <ul class="pagination">
        <?php
        $baseUrl = 'list_pretriagem.php?';
        // preserva q e filter ao construir links
        $baseParams = [];
        if ($q !== '') $baseParams['q'] = $q;
        if ($filter !== '') $baseParams['filter'] = $filter;
        for ($p = 1; $p <= $totalPages; $p++):
          $params = $baseParams;
          $params['page'] = $p;
          $href = $baseUrl . http_build_query($params);
        ?>
          <li class="page-item <?= $p === $page ? 'active' : '' ?>"><a class="page-link" href="<?= $href ?>"><?= $p ?></a></li>
        <?php endfor; ?>
      </ul>
    </nav>

  </div>

  <footer class="fixed-bottom">© 2024 Filine-ON. Todos os direitos reservados.</footer>

  <script>
    // filtro por classificação (click nos cards)
    document.querySelectorAll('.filter-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const filt = this.getAttribute('data-filter');
        // redireciona incluindo o filtro e preservando pesquisa
        const params = new URLSearchParams(window.location.search);
        params.set('filter', filt);
        params.delete('page');
        window.location.search = params.toString();
      });
    });
    document.getElementById('clearFilterBtn').addEventListener('click', function() {
      const params = new URLSearchParams(window.location.search);
      params.delete('filter');
      params.delete('page');
      window.location.search = params.toString();
    });

    // clique na linha -> abrir detalhe
    document.querySelectorAll('.table-row-clickable').forEach(row => {
      row.addEventListener('click', function() {
        const id = this.dataset.id;
        if (!id) return;
        window.location.href = 'view_pretriagem.php?id=' + encodeURIComponent(id);
      });
    });
  </script>
</body>

</html>