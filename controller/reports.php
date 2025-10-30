<?php
// resumo_pretriagem.php
// Ajusta o caminho para o teu ficheiro de conexão que expõe $pdo (PDO)
require_once __DIR__ . '/../config/conexao.php';
require 'auth.php';

// Recebe filtros via GET (para permitir links partilháveis)
$start_date = isset($_GET['start_date']) && $_GET['start_date'] !== '' ? $_GET['start_date'] : null;
$end_date   = isset($_GET['end_date']) && $_GET['end_date'] !== '' ? $_GET['end_date'] : null;
$status     = isset($_GET['situacao']) ? $_GET['situacao'] : 'todos';

// Definir valores default (últimos 7 dias) se datas não fornecidas
if (!$start_date || !$end_date) {
    $end_date = date('Y-m-d'); // hoje
    $start_date = date('Y-m-d', strtotime('-6 days')); // últimos 7 dias
}

// Normalizar para incluir todo o dia no intervalo (usa datetime no DB)
$period_start = $start_date . ' 00:00:00';
$period_end   = $end_date . ' 23:59:59';

// Lista de status conhecidos (ajusta conforme teu BD)
$known_status = ['Em Espera', 'Em Andamento', 'Atendido'];

// --- 1) Consulta de resumo: contagem por status no intervalo
try {
    $sql = "SELECT status, COUNT(*) AS cnt
            FROM tb_pre_triagem
            WHERE data_de_registo BETWEEN :start AND :end
            GROUP BY situacao";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':start' => $period_start, ':end' => $period_end]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // inicializa contadores
    $counts = [];
    foreach ($known_status as $s) $counts[$s] = 0;
    $counts['Total'] = 0;

    foreach ($rows as $r) {
        $s = $r['status'];
        $c = (int)$r['cnt'];
        if (!array_key_exists($s, $counts)) {
            // se houver status inesperado, adiciona dinamicamente
            $counts[$s] = $c;
        } else {
            $counts[$s] = $c;
        }
        $counts['Total'] += $c;
    }
} catch (Exception $e) {
    // Em caso de erro, preenche zeros (e podes registar o erro)
    $counts = array_fill_keys($known_status, 0);
    $counts['Total'] = 0;
    // opcional: error_log($e->getMessage());
}

// --- 2) Consulta detalhada (lista) com filtros aplicados
try {
    $params = [':start' => $period_start, ':end' => $period_end];
    $where = "WHERE data_de_registo BETWEEN :start AND :end";

    if ($status !== 'todos') {
        $where .= " AND status = :status";
        $params[':status'] = $status;
    }

    $sql_list = "SELECT cod_pre_triagem, nome_paciente, situacao, data_de_registo
                 FROM tb_pre_triagem
                 $where
                 ORDER BY data_de_registo DESC
                 LIMIT 1000"; // limite para não puxar tudo

    $stmt2 = $pdo->prepare($sql_list);
    $stmt2->execute($params);
    $list_rows = $stmt2->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $list_rows = [];
    // opcional: error_log($e->getMessage());
}
?>
<!doctype html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <title>Resumo Pré-triagem — Filine</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card .value {
            font-size: 1.8rem;
            font-weight: 600;
        }

        .small-muted {
            font-size: .9rem;
            color: #6c757d;
        }
    </style>
</head>

<body>

    <?php if (file_exists(__DIR__ . '/../public/header.php')) include '../public/header.php'; ?>
    <div class="container my-4">
        <h3>Resumo de Pré-triagem</h3>

        <!-- Formulário de filtros -->
        <form class="row g-2 align-items-end" method="get" action="">
            <div class="col-auto">
                <label class="form-label">Data Início</label>
                <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($start_date); ?>">
            </div>
            <div class="col-auto">
                <label class="form-label">Data Fim</label>
                <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($end_date); ?>">
            </div>
            <div class="col-auto">
                <label class="form-label">Status</label>
                <select name="situacao" class="form-select">
                    <option value="todos" <?php if ($status === 'todos') echo 'selected'; ?>>Todos</option>
                    <?php foreach ($known_status as $s): ?>
                        <option value="<?php echo htmlspecialchars($s); ?>" <?php if ($status === $s) echo 'selected'; ?>><?php echo htmlspecialchars($s); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary">Aplicar</button>
                <a class="btn btn-outline-secondary" href="reports.php">Reset</a>
            </div>
        </form>

        <!-- Cards de resumo -->
        <div class="row mt-4 g-3">
            <div class="col-md-3">
                <div class="card text-bg-light h-100">
                    <div class="card-body">
                        <h6 class="card-title">Total</h6>
                        <p class="value mb-0"><?php echo number_format($counts['Total']); ?></p>
                        <p class="small-muted mb-0"><?php echo htmlspecialchars($start_date); ?> → <?php echo htmlspecialchars($end_date); ?></p>
                    </div>
                </div>
            </div>

            <?php foreach ($known_status as $s): ?>
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-title"><?php echo htmlspecialchars($s); ?></h6>
                            <p class="value mb-0"><?php echo number_format($counts[$s] ?? 0); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php
            // Mostra quaisquer status inesperados encontrados no DB
            foreach ($counts as $key => $val) {
                if (!in_array($key, array_merge($known_status, ['Total']))) {
                    echo '<div class="col-md-3"><div class="card h-100"><div class="card-body">';
                    echo '<h6 class="card-title">' . htmlspecialchars($key) . '</h6>';
                    echo '<p class="value mb-0">' . number_format($val) . '</p>';
                    echo '</div></div></div>';
                }
            }
            ?>
        </div>

        <!-- Tabela com os registos filtrados -->
        <div class="card mt-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="card-title mb-0">Lista (limite 1000)</h5>
                    <div class="small-muted">Registos encontrados: <?php echo number_format(count($list_rows)); ?></div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width:70px;">ID</th>
                                <th>Nome</th>
                                <th style="width:160px;">Status</th>
                                <th style="width:200px;">Data Registo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($list_rows) === 0): ?>
                                <tr>
                                    <td colspan="4" class="text-center">Nenhum registo encontrado para este filtro.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($list_rows as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['cod_pre_triagem']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nome_paciente']); ?></td>
                                        <td><?php echo htmlspecialchars($row['situacao']); ?></td>
                                        <td><?= date('d/m/Y - H:i:s', strtotime($row['data_de_registo'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>