<?php
require '../config/conexao.php';
require '../controller/auth.php';

// Obter os dados da pré-triagem
if (!isset($_GET['pretriagem_id'])) {
    die("Paciente não encontrado.");
}

$id = (int) $_GET['pretriagem_id'];

$stmt = $pdo->prepare("
    SELECT 
        p.*, 
        ts.Tipo_Sangue AS Tipo_Sangue_Nome,
        a.Tipo_Alergia AS Alergia_Nome, e.morada AS Endereco_Morada
    FROM tb_pre_triagem p
    LEFT JOIN tb_tipo_sangue ts ON p.Tipo_Sangue = ts.Cod_Tipo_Sangue
    LEFT JOIN tb_alergia a ON p.Alergia = a.Cod_Alergia
    LEFT JOIN enderecos e ON p.Endereco = e.Endereco
    WHERE p.Cod_Pre_Triagem = :id
");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$paciente = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$paciente) {
    die("Registo não encontrado.");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Triagem</title>
    <meta name="title" content="FILINE 2025">
    <meta name="description" content="Sistema de Gestão Filas de Espera em Instituições de Saúde">
    <meta name="keywords" content="Gestão, Pacientes, Filas, Espera">
    <meta name="author" content="Filine - Estefânio Da Silva & Domingos Chivela">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
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
    <?php include '../public/header.php'; ?>

    <div class="container mt-4">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <h2 class="mb-0">Triagem do Paciente</h2>

            <form method="POST" action="gravar_triagem.php">
                <input type="hidden" name="Cod_Pre_Triagem" value="<?= htmlspecialchars($paciente['Cod_Pre_Triagem']) ?>">

                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Nome do Paciente:</label>
                        <input type="text" name="Nome_Paciente" class="form-control"
                            value="<?= htmlspecialchars($paciente['Nome_Paciente']) ?>" readonly>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Género:</label>
                        <input type="text" name="Genero" class="form-control"
                            value="<?= htmlspecialchars($paciente['Genero_Paciente']) ?>" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Ocorrência:</label>
                        <input type="text" name="Grupo_Ocorrencia" class="form-control"
                            value="<?= htmlspecialchars($paciente['Grupo_Ocorrencia']) ?>" readonly>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Detalhes da Ocorrência:</label>
                        <input type="text" name="Motivos_Classificacao" class="form-control"
                            value="<?= htmlspecialchars($paciente['Motivos_Classificacao']) ?>" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Tipo Sangueneo:</label>
                        <input type="text" name="Tipo_Sangue_Nome" class="form-control"
                            value="<?= htmlspecialchars($paciente['Tipo_Sangue_Nome']) ?>" readonly>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Alergia:</label>
                        <input type="text" name="Alergia" class="form-control"
                            value="<?= htmlspecialchars($paciente['Alergia_Nome']) ?>" readonly>
                    </div>
                </div>
                <hr>

                <div class="row">
                    <div class="col-md-3">
                        <label>Peso (kg):</label>
                        <input type="text" name="Peso" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Altura (cm):</label>
                        <input type="text" name="Altura" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>P/A:</label>
                        <input type="text" name="PA" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>P/B:</label>
                        <input type="text" name="PB" class="form-control">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <label>Edema:</label>
                        <input type="text" name="Edema" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Pulso:</label>
                        <input type="text" name="Pulso" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>SatO2:</label>
                        <input type="text" name="SatO2" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>P. Respiratório:</label>
                        <input type="text" name="P_Respiratorio" class="form-control">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <label>Dor:</label>
                        <input type="text" name="Dor" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Temperatura (°C):</label>
                        <input type="text" name="Temperatura" class="form-control">
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Data de Nascimento:</label>
                        <input type="text" name="Data_Nascimento" class="form-control"
                            value="<?= htmlspecialchars($paciente['Data_Nascimento']) ?>" readonly>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Contacto:</label>
                        <input type="text" name="Contacto" class="form-control"
                            value="<?= htmlspecialchars($paciente['Contacto']) ?>" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Senha de Atendimento:</label>
                        <input type="text" name="Data_Nascimento" class="form-control"
                            value="<?= htmlspecialchars($paciente['Senha_de_Atendimento']) ?>" readonly>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Bairro:</label>
                        <input type="text" name="Contacto" class="form-control"
                            value="<?= htmlspecialchars($paciente['Endereco_Morada']) ?>" readonly>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Gravar Triagem</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>