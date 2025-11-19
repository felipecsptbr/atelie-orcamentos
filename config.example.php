<?php
// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'atelie_orcamentos');
define('DB_USER', 'root');
define('DB_PASS', '');

// IMPORTANTE: Renomeie este arquivo para config.php e ajuste as configurações acima
// para o seu ambiente de produção

// Conectar ao banco de dados
function getConnection() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        // Se o banco não existe, criar
        try {
            $tempPdo = new PDO("mysql:host=" . DB_HOST . ";charset=utf8mb4", DB_USER, DB_PASS);
            $tempPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $tempPdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Tentar conectar novamente
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch(PDOException $e2) {
            die("Erro de conexão: " . $e2->getMessage());
        }
    }
}

// Função para criar as tabelas
function createTables($pdo) {
    // Criar tabela de clientes
    $pdo->exec("CREATE TABLE IF NOT EXISTS clientes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        telefone VARCHAR(20),
        email VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Criar tabela de serviços
    $pdo->exec("CREATE TABLE IF NOT EXISTS servicos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        descricao TEXT,
        preco_base DECIMAL(10,2) NOT NULL,
        ativo TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Criar tabela de orçamentos
    $pdo->exec("CREATE TABLE IF NOT EXISTS orcamentos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cliente_id INT,
        numero VARCHAR(50) UNIQUE NOT NULL,
        data_orcamento DATE NOT NULL,
        valor_total DECIMAL(10,2) NOT NULL,
        observacoes TEXT,
        status VARCHAR(20) DEFAULT 'pendente',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Criar tabela de itens do orçamento
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

// Função para inserir serviços padrão
function insertDefaultServices($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM servicos");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
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
    }
}
?>
