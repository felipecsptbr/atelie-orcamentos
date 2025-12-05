<?php
/**
 * Sidebar do Sistema - Menu de Navegação
 */
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link text-center">
        <i class="fas fa-cut" style="font-size: 1.5rem; color: var(--accent-color);"></i>
        <span class="brand-text font-weight-light ml-2"><strong>Ateliê</strong> Orçamentos</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <i class="fas fa-user-circle fa-2x text-white"></i>
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="index.php" class="nav-link <?php echo $currentPage == 'index.php' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                
                <!-- Orçamentos -->
                <li class="nav-item">
                    <a href="orcamentos.php" class="nav-link <?php echo $currentPage == 'orcamentos.php' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>Orçamentos</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="orcamento_novo.php" class="nav-link <?php echo $currentPage == 'orcamento_novo.php' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-plus-circle"></i>
                        <p>Novo Orçamento</p>
                    </a>
                </li>
                
                <!-- Clientes -->
                <li class="nav-item">
                    <a href="clientes.php" class="nav-link <?php echo $currentPage == 'clientes.php' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Clientes</p>
                    </a>
                </li>
                
                <!-- Serviços -->
                <li class="nav-item">
                    <a href="servicos.php" class="nav-link <?php echo $currentPage == 'servicos.php' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-cut"></i>
                        <p>Serviços</p>
                    </a>
                </li>
                
                <li class="nav-header">SISTEMA</li>
                
                <!-- Relatórios -->
                <li class="nav-item">
                    <a href="relatorios.php" class="nav-link <?php echo $currentPage == 'relatorios.php' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Relatórios</p>
                    </a>
                </li>
                
                <!-- Sair -->
                <li class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Sair</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
