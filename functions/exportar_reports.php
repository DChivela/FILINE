<?php
// export_pretriagem.php
// Ficheiro independente para exportar pré-triagem (XLSX)

// Ajusta caminhos conforme a tua estrutura
require_once __DIR__ . '/../config/conexao.php'; // espera $pdo (PDO)
require_once __DIR__ . '/../controller/auth.php'; // garante autorização (se já tens auth)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Recebe filtros (via GET)
$start_date = isset($_GET['start_date']) && $_GET['start_date'] !== '' ? $_GET['start_date'] : null;
$end_date   = isset($_GET['end_date']) && $_GET['end_date'] !== '' ? $_GET['end_date'] : null;
$status     = isset($_GET['situacao']) && $_GET['situacao'] !== '' ? $_GET['situacao'] : 'todos';

// Defaults (últimos 7 dias) caso faltem
if (!$start_date || !$end_date) {
    $end_date = date('Y-m-d');
    $start_date = date('Y-m-d', strtotime('-6 days'));
}

$period_start = $start_date . ' 00:00:00';
$period_end   = $end_date . ' 23:59:59';

// Monta query com os mesmos nomes de colunas/tabela que usas
$whereExport = "WHERE data_de_registo BETWEEN :start AND :end";
$paramsExport = [
    ':start' => $period_start,
    ':end'   => $period_end
];

if ($status !== 'todos') {
    $whereExport .= " AND situacao = :situacao";
    $paramsExport[':situacao'] = $status;
}

$sqlExport = "SELECT cod_pre_triagem, nome_paciente, situacao, classificacao_de_risco as classificacao, data_de_registo
              FROM tb_pre_triagem
              $whereExport
              ORDER BY data_de_registo DESC";

try {
    $stmtExport = $pdo->prepare($sqlExport);
    $stmtExport->execute($paramsExport);
    $exportRows = $stmtExport->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Se falhar, devolve um HTTP 500 mínimo e mensagem simples (podes também registar o erro)
    http_response_code(500);
    error_log("Export error: " . $e->getMessage());
    echo "Ocorreu um erro ao gerar o export. Verifica os logs.";
    exit;
}

// Carrega PhpSpreadsheet (ajusta caminho se necessário)
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Cria e preenche a spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Pré-triagem');

// Cabeçalho
$headers = ['ID', 'Nome', 'Estado', 'Classificação', 'Data Registo'];
$col = 'A';
foreach ($headers as $h) {
    $sheet->setCellValue($col . '1', $h);
    $col++;
}

// Preenche linhas
$rowNum = 2;
foreach ($exportRows as $r) {
    $sheet->setCellValue('A' . $rowNum, $r['cod_pre_triagem']);
    $sheet->setCellValue('B' . $rowNum, $r['nome_paciente']);
    $sheet->setCellValue('C' . $rowNum, $r['situacao']);
    $sheet->setCellValue('D' . $rowNum, $r['classificacao']);
    $sheet->setCellValue('E' . $rowNum, $r['data_de_registo']);
    $rowNum++;
}

// Formatação simples: cabeçalho em negrito + alinhamento + bordas leves
$headerRange = 'A1:E1';
$sheet->getStyle($headerRange)->getFont()->setBold(true);
$sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A1:E' . ($rowNum-1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// Auto-size das colunas
foreach (range('A', 'E') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

/*O ob_end_clean limpa o buffer de saída, isso significa que:
Antes de enviar o Excel, havia dados invisíveis já no buffer de saída e
ele os limpa (cenas como por exemplo, espaços, mensagens, ou eco de algum include)*/
if (ob_get_length()) {
    ob_end_clean();
}

// Preparar nome do ficheiro
$filename = sprintf("Relatório_%s_%s.xlsx", $start_date, $end_date);

// Headers para download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
header('Expires: 0');
header('Pragma: public');

// Escrever e enviar
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
