<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Ateliê Orçamentos'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-custom {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .card-custom:hover {
            transform: translateY(-2px);
        }
        .btn-custom {
            border-radius: 25px;
            padding: 10px 25px;
        }
        .navbar-brand {
            font-weight: bold;
            color: #667eea !important;
        }
        .servico-card {
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        .servico-card:hover {
            border-color: #28a745;
            transform: scale(1.02);
        }
        .servico-card.selected {
            border-color: #007bff;
            background-color: #f8f9fa;
        }
        
        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 0.9rem;
            }
            .nav-link {
                font-size: 0.85rem;
                padding: 0.3rem 0.5rem;
            }
            .card-custom {
                margin-bottom: 1rem;
            }
            h1, .h2 {
                font-size: 1.5rem !important;
            }
            .display-4 {
                font-size: 2rem !important;
            }
            .table-responsive {
                font-size: 0.85rem;
            }
            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
            .servico-card {
                margin-bottom: 0.5rem;
            }
        }
        
        @media (max-width: 576px) {
            .container-fluid {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
            .card-body {
                padding: 1rem;
            }
            h1, .h2 {
                font-size: 1.25rem !important;
            }
            .navbar-brand i {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-scissors"></i>
                <span class="d-none d-sm-inline">Ateliê Orçamentos</span>
                <span class="d-inline d-sm-none">Ateliê</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="index.php">
                        <i class="bi bi-list"></i> <span class="d-none d-md-inline">Orçamentos</span>
                    </a>
                    <a class="nav-link" href="novo-orcamento.php">
                        <i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Novo</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <main class="col-12 px-md-4">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php echo $content; ?>
            </main>
        </div>
    </div>

    <footer class="bg-white border-top mt-5 py-4">
        <div class="container">
            <div class="text-center text-muted">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> Ateliê Orçamentos - Sistema de Gestão</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php if (isset($scripts)) echo $scripts; ?>
</body>
</html>