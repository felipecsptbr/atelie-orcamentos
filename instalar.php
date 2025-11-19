<?php
/**
 * INSTALADOR AUTOMÁTICO DO BANCO DE DADOS
 * Sistema de Orçamentos para Ateliê de Costura
 */

$pageTitle = 'Instalação do Sistema - Ateliê Orçamentos';

// Configurações do banco
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'atelie_orcamentos';

$success = false;
$errors = [];
$steps = [];

if ($_POST && isset($_POST['install'])) {
    try {
        // Passo 1: Conectar ao MySQL e criar banco
        $steps[] = "Conectando ao MySQL...";
        $pdo = new PDO("mysql:host={$host};charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $steps[] = "✅ Conectado ao MySQL com sucesso!";
        
        // Criar banco de dados
        $steps[] = "Criando banco de dados...";
        $pdo->exec("CREATE DATABASE IF NOT EXISTS {$database} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $steps[] = "✅ Banco de dados criado!";
        
        // Selecionar o banco
        $pdo->exec("USE {$database}");
        $steps[] = "✅ Banco selecionado!";
        
        // Criar tabelas diretamente
        $steps[] = "Criando tabelas...";
        
        // Tabela clientes
        $pdo->exec("CREATE TABLE IF NOT EXISTS clientes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(255) NOT NULL,
            telefone VARCHAR(20),
            email VARCHAR(255),
            endereco TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // Tabela servicos
        $pdo->exec("CREATE TABLE IF NOT EXISTS servicos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(255) NOT NULL,
            descricao TEXT,
            preco_base DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            ativo TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // Tabela orcamentos
        $pdo->exec("CREATE TABLE IF NOT EXISTS orcamentos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            cliente_id INT,
            numero VARCHAR(50) UNIQUE NOT NULL,
            data_orcamento DATE NOT NULL,
            valor_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            observacoes TEXT,
            status VARCHAR(20) DEFAULT 'pendente',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // Tabela orcamento_itens
        $pdo->exec("CREATE TABLE IF NOT EXISTS orcamento_itens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            orcamento_id INT NOT NULL,
            servico_id INT,
            descricao VARCHAR(255) NOT NULL,
            quantidade INT NOT NULL DEFAULT 1,
            preco_unitario DECIMAL(10,2) NOT NULL,
            subtotal DECIMAL(10,2) NOT NULL,
            FOREIGN KEY (orcamento_id) REFERENCES orcamentos(id) ON DELETE CASCADE,
            FOREIGN KEY (servico_id) REFERENCES servicos(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        $steps[] = "✅ Tabelas criadas com sucesso!";
        
        // Inserir serviços padrão
        $steps[] = "Inserindo serviços padrão...";
        
        $servicosCount = $pdo->query("SELECT COUNT(*) FROM servicos")->fetchColumn();
        
        if ($servicosCount == 0) {
            $servicos = [
                ['Conserto de calça (barra)', 'Ajuste de barra de calça', 15.00],
                ['Conserto de calça (cintura)', 'Ajuste na cintura', 25.00],
                ['Conserto de camisa', 'Ajustes diversos em camisa', 20.00],
                ['Troca de zíper', 'Substituição de zíper', 30.00],
                ['Ajuste de vestido', 'Ajustes em vestido', 40.00],
                ['Barra de saia', 'Ajuste de barra de saia', 15.00],
                ['Conserto de rasgo', 'Reparo de rasgo em tecido', 25.00],
                ['Colocação de elástico', 'Troca ou colocação de elástico', 20.00],
                ['Ajuste de manga', 'Ajuste no comprimento da manga', 18.00],
                ['Costura de botões', 'Reposição de botões', 5.00],
                ['Customização', 'Customização de peça', 50.00],
                ['Outros serviços', 'Serviços diversos', 0.00]
            ];

            $stmt = $pdo->prepare("INSERT INTO servicos (nome, descricao, preco_base) VALUES (?, ?, ?)");
            foreach ($servicos as $servico) {
                $stmt->execute($servico);
            }
            
            $steps[] = "✅ 12 serviços cadastrados!";
        } else {
            $steps[] = "✅ Serviços já existem no banco!";
        }
        
        // Inserir clientes de exemplo
        $steps[] = "Inserindo clientes de exemplo...";
        
        $clientesCount = $pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
        
        if ($clientesCount == 0) {
            $clientes = [
                ['Maria Silva', '(11) 98765-4321', 'maria@example.com', 'Rua das Flores, 123'],
                ['João Santos', '(11) 91234-5678', 'joao@example.com', 'Av. Principal, 456'],
                ['Ana Costa', '(11) 99999-8888', 'ana@example.com', 'Rua do Comércio, 789']
            ];

            $stmt = $pdo->prepare("INSERT INTO clientes (nome, telefone, email, endereco) VALUES (?, ?, ?, ?)");
            foreach ($clientes as $cliente) {
                $stmt->execute($cliente);
            }
            
            $steps[] = "✅ 3 clientes de exemplo cadastrados!";
        } else {
            $steps[] = "✅ Clientes já existem no banco!";
        }
        
        // Passo 2: Verificar instalação
        $steps[] = "Verificando instalação...";
        
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        $steps[] = "✅ Tabelas criadas: " . implode(', ', $tables);
        
        $servicosCount = $pdo->query("SELECT COUNT(*) FROM servicos")->fetchColumn();
        $steps[] = "✅ {$servicosCount} serviços cadastrados!";
        
        $clientesCount = $pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
        $steps[] = "✅ {$clientesCount} clientes de exemplo inseridos!";
        
        $success = true;
        $steps[] = "🎉 Instalação concluída com sucesso!";
        
    } catch (Exception $e) {
        $errors[] = "Erro: " . $e->getMessage();
    }
}

// Verificar se já está instalado
$isInstalled = false;
try {
    $testPdo = new PDO("mysql:host={$host};dbname={$database};charset=utf8mb4", $username, $password);
    $testPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar se as tabelas existem
    $tables = $testPdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    if (count($tables) >= 4) { // clientes, servicos, orcamentos, itens_orcamento
        $isInstalled = true;
        
        $servicosCount = $testPdo->query("SELECT COUNT(*) FROM servicos")->fetchColumn();
        $clientesCount = $testPdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
        $orcamentosCount = $testPdo->query("SELECT COUNT(*) FROM orcamentos")->fetchColumn();
    }
} catch (Exception $e) {
    // Banco não existe ainda
}

ob_start();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .install-card {
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .step-item {
            padding: 8px 0;
            border-left: 3px solid #e9ecef;
            padding-left: 15px;
            margin-bottom: 10px;
        }
        .step-item.success {
            border-left-color: #28a745;
            color: #155724;
        }
        .step-item.error {
            border-left-color: #dc3545;
            color: #721c24;
        }
    </style>
</head>
<body class="gradient-bg">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card install-card">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h1 class="h3 mb-0">
                            <i class="bi bi-database-gear me-2"></i>
                            Instalação do Sistema
                        </h1>
                        <p class="mb-0">Sistema de Orçamentos para Ateliê de Costura</p>
                    </div>
                    
                    <div class="card-body p-5">
                        
                        <?php if ($isInstalled && !$_POST): ?>
                        <!-- Sistema já instalado -->
                        <div class="text-center mb-4">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                            <h2 class="text-success mt-3">Sistema já instalado!</h2>
                        </div>
                        
                        <div class="alert alert-success">
                            <h5><i class="bi bi-info-circle me-2"></i>Status do Banco de Dados:</h5>
                            <ul class="mb-0">
                                <li><strong><?php echo count($tables); ?></strong> tabelas criadas</li>
                                <li><strong><?php echo $servicosCount; ?></strong> serviços cadastrados</li>
                                <li><strong><?php echo $clientesCount; ?></strong> clientes de exemplo</li>
                                <li><strong><?php echo $orcamentosCount; ?></strong> orçamentos no sistema</li>
                            </ul>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="index.php" class="btn btn-primary btn-lg">
                                <i class="bi bi-house-door me-2"></i>
                                Acessar Sistema
                            </a>
                            <button type="button" class="btn btn-outline-warning" 
                                    onclick="if(confirm('Tem certeza? Isso irá recriar todas as tabelas!')) { document.getElementById('reinstallForm').submit(); }">
                                <i class="bi bi-arrow-clockwise me-2"></i>
                                Reinstalar Banco
                            </button>
                        </div>
                        
                        <form id="reinstallForm" method="POST" style="display: none;">
                            <input type="hidden" name="install" value="1">
                        </form>
                        
                        <?php elseif ($success): ?>
                        <!-- Instalação concluída -->
                        <div class="text-center mb-4">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                            <h2 class="text-success mt-3">Instalação Concluída!</h2>
                        </div>
                        
                        <div class="alert alert-success">
                            <h5><i class="bi bi-list-check me-2"></i>Passos executados:</h5>
                            <div class="steps-container">
                                <?php foreach ($steps as $step): ?>
                                <div class="step-item success"><?php echo $step; ?></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <a href="index.php" class="btn btn-success btn-lg">
                                <i class="bi bi-play-circle me-2"></i>
                                Começar a Usar o Sistema
                            </a>
                        </div>
                        
                        <?php elseif (!empty($errors)): ?>
                        <!-- Erro na instalação -->
                        <div class="text-center mb-4">
                            <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 4rem;"></i>
                            <h2 class="text-danger mt-3">Erro na Instalação</h2>
                        </div>
                        
                        <div class="alert alert-danger">
                            <h5><i class="bi bi-exclamation-circle me-2"></i>Erros encontrados:</h5>
                            <?php foreach ($errors as $error): ?>
                            <div class="step-item error"><?php echo $error; ?></div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if (!empty($steps)): ?>
                        <div class="alert alert-info">
                            <h5><i class="bi bi-info-circle me-2"></i>Passos executados antes do erro:</h5>
                            <?php foreach ($steps as $step): ?>
                            <div class="step-item success"><?php echo $step; ?></div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="d-grid">
                            <button type="button" class="btn btn-warning" onclick="location.reload()">
                                <i class="bi bi-arrow-clockwise me-2"></i>
                                Tentar Novamente
                            </button>
                        </div>
                        
                        <?php else: ?>
                        <!-- Tela inicial de instalação -->
                        <div class="text-center mb-4">
                            <i class="bi bi-database me-2" style="font-size: 4rem; color: #667eea;"></i>
                            <h2 class="mt-3">Configurar Banco de Dados</h2>
                            <p class="text-muted">Configure o banco MySQL para o sistema de orçamentos</p>
                        </div>
                        
                        <div class="alert alert-info">
                            <h5><i class="bi bi-info-circle me-2"></i>O que será criado:</h5>
                            <ul class="mb-0">
                                <li>Banco de dados: <strong>atelie_orcamentos</strong></li>
                                <li>4 tabelas principais (clientes, serviços, orçamentos, itens)</li>
                                <li>12 serviços pré-configurados</li>
                                <li>3 clientes de exemplo</li>
                                <li>Views e procedures para relatórios</li>
                                <li>Índices para melhor performance</li>
                            </ul>
                        </div>
                        
                        <div class="alert alert-warning">
                            <h5><i class="bi bi-exclamation-triangle me-2"></i>Requisitos:</h5>
                            <ul class="mb-0">
                                <li>XAMPP com MySQL rodando</li>
                                <li>Usuário: <strong>root</strong> (sem senha)</li>
                                <li>Arquivo <strong>database.sql</strong> na pasta</li>
                            </ul>
                        </div>
                        
                        <form method="POST">
                            <div class="d-grid">
                                <button type="submit" name="install" value="1" class="btn btn-primary btn-lg">
                                    <i class="bi bi-database-fill-add me-2"></i>
                                    Instalar Banco de Dados
                                </button>
                            </div>
                        </form>
                        
                        <?php endif; ?>
                        
                        <hr class="my-4">
                        
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="p-3">
                                    <i class="bi bi-shield-check text-success mb-2" style="font-size: 2rem;"></i>
                                    <h6>Seguro</h6>
                                    <small class="text-muted">Configuração automática e segura</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3">
                                    <i class="bi bi-lightning text-warning mb-2" style="font-size: 2rem;"></i>
                                    <h6>Rápido</h6>
                                    <small class="text-muted">Instalação em poucos segundos</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3">
                                    <i class="bi bi-gear text-info mb-2" style="font-size: 2rem;"></i>
                                    <h6>Completo</h6>
                                    <small class="text-muted">Tudo configurado automaticamente</small>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    
                    <div class="card-footer bg-light text-center">
                        <small class="text-muted">
                            Sistema de Orçamentos para Ateliê de Costura - PHP & MySQL
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$content = ob_get_clean();
echo $content;
?>