  <head>
    <link rel="stylesheet" href="http://localhost:3000/public/css/header.css">
    <link rel="stylesheet" href="http://localhost:8000/public/css/header.css">
  </head>

  <nav class="navbar navbar-expand-lg topbar">
    <div class="container-fluid">
      <a class="navbar-brand text-white fw-bold" href="../index.php">FILINE-ON</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topbarMenu" aria-controls="topbarMenu" aria-expanded="false" aria-label="Toggle navigation" id="menuToggle">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end" id="topbarMenu">
        <ul class="navbar-nav gap-2">
          <li class="nav-item">
            <a class="nav-link text-white" href="http://localhost:8000/index.php">INÍCIO</a>
          </li>
          <?php if (!isset($_SESSION['Cod_Utilizador']) ):?>
          <a class="nav-link text-white" href="http://localhost:8000/public/list_pretriagem.php">CONSULTAR ESPERA</a>
          <?php else: ?>
          <li class="nav-item">
            <a class="nav-link text-white" href="http://localhost:8000/public/dashboard.php">LISTA DE ESPERA</a>
          </li>
          <?php endif; ?>

          <?php if (isset($_SESSION['Cod_Utilizador'])): ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                TÉCNICOS SAÚDE
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="../controller/tecnico_saude.php">NOVO TÉCNICO</a></li>
                <li><a class="dropdown-item" href="../public/listar_tecnicos.php">LISTA DE TÉCNICOS</a></li>
                <!-- <li><a class="dropdown-item" href="../controller/categorias_tecnicos.php">Categorias</a></li> -->
              </ul>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                GERAL
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="../controller/alergias.php">ALERGIAS</a></li>
                <li><a class="dropdown-item" href="../controller/enderecos.php">BAIRROS</a></li>
                <li><a class="dropdown-item" href="http://localhost:8000/public/reports.php">RELATÓRIOS</a></li>
                <li><a class="dropdown-item" href="../controller/tipo_sangue.php">TIPOS SANGUENEOS</a></li>
                <!-- <li><a class="dropdown-item" href="../controller/categorias_tecnicos.php">Categorias</a></li> -->
              </ul>
            </li>
          <?php endif; ?>
          <?php if (!isset($_SESSION['Cod_Utilizador'])): ?>
            <li class="nav-item">
              <a class="nav-link text-white" href="http://localhost:8000/public/login.php">ENTRAR</a>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link text-white" href="http://localhost:8000/public/logout.php">LOGOUT</a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>


  <!--CÓDIGO ANTIGO -->
  <!--div class="topbar d-flex justify-content-between align-items-center px-3 py-2">
    <div><strong class="text-white">Filine-ON</strong></div>
    <div class="d-flex align-items-center gap-3">
      <a class="text-white" href="http://localhost:3000/index.php">Início</a>
      <a class="text-white" href="http://localhost:3000/public/list_pretriagem.php">Consultar Espera</a>

      < ?php if (isset($_SESSION['Cod_Utilizador'])): ?>
        <div class="dropdown">
          <a class="dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Cadastrar Técnico
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="../controller/tecnico_saude.php">Novo Técnico</a></li>
            <li><a class="dropdown-item" href="../controller/listar_tecnicos.php">Lista de Técnicos</a></li>
            <li><a class="dropdown-item" href="../controller/categorias_tecnicos.php">Categorias</a></li>
          </ul>
        </div>
      < ?php endif; ?>

      < ?php if (!isset($_SESSION['Cod_Utilizador'])): ?>
        <a class="text-white" href="http://localhost:3000/public/login.php">Entrar</a>
      < ?php else: ?>
        <a class="text-white" href="http://localhost:3000/public/logout.php">Logout</a>
      < ?php endif; ?>
    </div>
  </div -->

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


  <script>
    const menuToggle = document.getElementById('menuToggle');
    menuToggle.addEventListener('click', function() {
      this.classList.toggle('open');
    });
  </script>