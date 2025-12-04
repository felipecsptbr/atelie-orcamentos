<?php
/**
 * Header do Sistema - AdminLTE
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $pageTitle ?? 'Sistema de Orçamentos'; ?> - Ateliê</title>
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
    <!-- Chart.js -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/custom.css">
    
    <style>
        :root {
            --primary-color: #c06c84;
            --secondary-color: #6c5b7b;
            --accent-color: #f8b195;
            --light-bg: #f5f5f5;
        }
        
        .main-header .navbar-nav .nav-link {
            height: 3.5rem;
            padding: 1rem;
        }
        
        .main-sidebar {
            background: linear-gradient(180deg, var(--secondary-color) 0%, #4a3f5a 100%);
        }
        
        .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active {
            background-color: var(--primary-color);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover, .btn-primary:focus {
            background-color: #a85670;
            border-color: #a85670;
        }
        
        .small-box.bg-info {
            background: linear-gradient(135deg, #c06c84 0%, #d88ba0 100%) !important;
        }
        
        .small-box.bg-success {
            background: linear-gradient(135deg, #6c5b7b 0%, #8a7a9a 100%) !important;
        }
        
        .small-box.bg-warning {
            background: linear-gradient(135deg, #f8b195 0%, #fcc5af 100%) !important;
        }
        
        .small-box.bg-danger {
            background: linear-gradient(135deg, #f67280 0%, #fa8d99 100%) !important;
        }
        
        .card-primary:not(.card-outline)>.card-header {
            background-color: var(--primary-color);
        }
        
        .brand-link {
            background-color: var(--secondary-color) !important;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .badge-status-pendente {
            background-color: #ffc107;
            color: #000;
        }
        
        .badge-status-aprovado {
            background-color: #28a745;
        }
        
        .badge-status-em_execucao {
            background-color: #17a2b8;
        }
        
        .badge-status-concluido {
            background-color: #6c757d;
        }
        
        .badge-status-cancelado {
            background-color: #dc3545;
        }
        
        @media (max-width: 768px) {
            .content-wrapper {
                padding: 0.5rem;
            }
            
            .card-body {
                padding: 0.75rem;
            }
            
            .btn {
                padding: 0.5rem 0.75rem;
                font-size: 0.875rem;
            }
            
            .table {
                font-size: 0.875rem;
            }
        }
    </style>
    
    <?php if (isset($extraCSS)) echo $extraCSS; ?>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="index.php" class="nav-link">Home</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="orcamentos.php" class="nav-link">Orçamentos</a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="#" title="<?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>">
                    <i class="fas fa-user"></i>
                    <span class="d-none d-md-inline ml-1"><?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php" title="Sair">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="d-none d-md-inline ml-1">Sair</span>
                </a>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->
