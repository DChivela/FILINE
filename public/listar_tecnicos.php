<?php
// incluir conexão com PDO
require_once '../controller/auth.php';
require_once '../config/conexao.php';

// Mensagens de feedback
$mensagemSucesso = $_GET['sucesso'] ?? '';
$mensagemErro = $_GET['erro'] ?? '';

// Obter lista de técnicos
try {
    $stmt = $pdo->query("SELECT * FROM tb_utilizador ORDER BY Nome_Enfermeiro ASC");
    $tecnicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $mensagemErro = "Erro ao buscar técnicos: " . $e->getMessage();
    $tecnicos = [];
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Lista de Técnicos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../public/favicon.ico" type="image/x-icon">
</head>
<body class="bg-light">
    <?php include 'header.php'; ?>
<div class="container mt-5">
    <h2 class="mb-4">Lista de Técnicos/Usuários</h2>

    <div class="d-flex flex-column flex-sm-row justify-content-between mb-3">
        <a href="../controller/tecnico_saude.php" class="btn btn-success mb-2 mb-sm-0">Cadastrar Técnico</a>
        <a href="dashboard.php" class="btn btn-secondary">Voltar</a>
    </div>

    <?php if ($mensagemSucesso): ?>
        <div class="alert alert-success"><?= htmlspecialchars($mensagemSucesso) ?></div>
    <?php endif; ?>

    <?php if ($mensagemErro): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($mensagemErro) ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th class="d-none d-md-table-cell">#</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Contacto</th>
                    <th class="d-none d-md-table-cell">Perfil</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($tecnicos) > 0): ?>
                    <?php foreach ($tecnicos as $index => $tecnico): ?>
                        <tr>
                            <td class="d-none d-md-table-cell"><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($tecnico['Nome_Enfermeiro']) ?></td>
                            <td><?= htmlspecialchars($tecnico['Email']) ?></td>
                            <td><?= htmlspecialchars($tecnico['Contacto']) ?></td>
                            <td class="d-none d-md-table-cell"><?= htmlspecialchars($tecnico['Perfil_Acesso']) ?></td>
                            <td>
                                <a href="../controller/editar_tecnico.php?id=<?= $tecnico['Cod_Utilizador'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                <a href="#" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalConfirmarEliminacao" data-id="<?= $tecnico['Cod_Utilizador'] ?>">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center">Nenhum técnico encontrado.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de Confirmação de Eliminação -->
<div class="modal fade" id="modalConfirmarEliminacao" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEliminarLabel">Confirmar Eliminação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                Tem certeza que deseja eliminar este técnico?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" class="btn btn-danger" id="confirmarEliminacaoBtn">Sim, Eliminar</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const modal = document.getElementById('modalConfirmarEliminacao');
    modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const tecnicoId = button.getAttribute('data-id');
        const confirmarBtn = modal.querySelector('#confirmarEliminacaoBtn');
        confirmarBtn.href = `../controller/excluir_tecnico.php?id=${tecnicoId}`;
    });
</script>
</body>
</html>
