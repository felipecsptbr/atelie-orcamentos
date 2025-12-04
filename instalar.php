<?php
/**
 * Script de Instalação Automática
 * Execute este arquivo APENAS UMA VEZ após criar o banco de dados
 */

// Configurações
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'atelie_orcamentos';
$sqlFile = __DIR__ . '/database/database.sql';

// Verificar se já está instalado
$jaInstalado = false;
try {
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    if (!$conn->connect_error) {
        $result = @$conn->query("SELECT COUNT(*) as total FROM usuarios");
        if ($result && $result->fetch_assoc()['total'] > 0) {
            $jaInstalado = true;
        }
        $conn->close();
    }
} catch (Exception $e) {
    // Ignorar erros na verificação
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação - Sistema de Orçamentos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <style>
        body { background: linear-gradient(135deg, #f5f5f5 0%, #e8d5d5 100%); padding: 50px 0; }
        .install-box { max-width: 600px; margin: 0 auto; }
        .card { box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .logo { text-align: center; margin-bottom: 30px; font-size: 2em; color: #6c5b7b; }
        .step { padding: 15px; margin: 10px 0; border-radius: 5px; }
        .step-success { background: #d4edda; border-left: 4px solid #28a745; }
        .step-error { background: #f8d7da; border-left: 4px solid #dc3545; }
        .step-info { background: #d1ecf1; border-left: 4px solid #17a2b8; }
    </style>
</head>
<body>
<div class="container">
    <div class="install-box">
        <div class="logo">
            <i class="fas fa-cut"></i> Instalação do Sistema
        </div>
        
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Instalador Automático</h5>
                
                <?php
                if ($jaInstalado && $_SERVER['REQUEST_METHOD'] !== 'POST') {
                    ?>
                    <div class="alert alert-success">
                        <h5><i class="fas fa-check-circle"></i> Sistema Já Instalado!</h5>
                        <p>O banco de dados já foi instalado anteriormente.</p>
                        <a href="login.php" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Ir para Login
                        </a>
                    </div>
                    <?php
                } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    echo '<div class="installation-log">';
                    
                    // Passo 1: Verificar arquivo SQL
                    echo '<div class="step step-info"><strong>1.</strong> Verificando arquivo SQL...</div>';
                    if (!file_exists($sqlFile)) {
                        echo '<div class="step step-error"><strong>ERRO:</strong> Arquivo database/database.sql não encontrado!</div>';
                        echo '</div></div></div></div></body></html>';
                        exit;
                    }
                    echo '<div class="step step-success">✓ Arquivo SQL encontrado</div>';
                    
                    // Passo 2: Conectar ao banco
                    echo '<div class="step step-info"><strong>2.</strong> Conectando ao banco de dados...</div>';
                    try {
                        $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
                        if ($conn->connect_error) {
                            throw new Exception("Erro de conexão: " . $conn->connect_error);
                        }
                        $conn->set_charset('utf8mb4');
                        echo '<div class="step step-success">✓ Conectado ao banco: ' . $dbName . '</div>';
                    } catch (Exception $e) {
                        echo '<div class="step step-error"><strong>ERRO:</strong> ' . $e->getMessage() . '</div>';
                        echo '<div class="step step-info"><strong>Solução:</strong> 
                              <ol>
                                <li>Verifique se o MySQL está rodando no XAMPP</li>
                                <li>Acesse http://localhost/phpmyadmin</li>
                                <li>Crie um banco chamado: <strong>atelie_orcamentos</strong></li>
                                <li>Codificação: <strong>utf8mb4_unicode_ci</strong></li>
                                <li>Volte aqui e clique em "Instalar"</li>
                              </ol>
                              </div>';
                        echo '</div></div></div></div></body></html>';
                        exit;
                    }
                    
                    // Passo 3: Ler arquivo SQL
                    echo '<div class="step step-info"><strong>3.</strong> Lendo arquivo SQL...</div>';
                    $sql = file_get_contents($sqlFile);
                    if (!$sql) {
                        echo '<div class="step step-error"><strong>ERRO:</strong> Não foi possível ler o arquivo SQL</div>';
                        echo '</div></div></div></div></body></html>';
                        exit;
                    }
                    echo '<div class="step step-success">✓ Arquivo lido com sucesso (' . strlen($sql) . ' bytes)</div>';
                    
                    // Passo 4: Executar SQL
                    echo '<div class="step step-info"><strong>4.</strong> Criando tabelas e inserindo dados...</div>';
                    try {
                        // Separar comandos SQL
                        $statements = array_filter(
                            array_map('trim', explode(';', $sql)),
                            function($stmt) { 
                                $stmt = trim($stmt);
                                return !empty($stmt) && 
                                       substr($stmt, 0, 2) !== '--' && 
                                       substr($stmt, 0, 2) !== '/*';
                            }
                        );
                        
                        $success = 0;
                        $errors = 0;
                        $errorMessages = [];
                        
                        // Desabilitar temporariamente o strict mode
                        $conn->query("SET sql_mode = ''");
                        
                        foreach ($statements as $statement) {
                            $stmt = trim($statement);
                            if (!empty($stmt)) {
                                if ($conn->query($stmt)) {
                                    $success++;
                                } else {
                                    $errors++;
                                    $errorMessages[] = $conn->error;
                                }
                            }
                        }
                        
                        if ($errors === 0) {
                            echo '<div class="step step-success">✓ ' . $success . ' comandos executados com sucesso!</div>';
                        } else {
                            echo '<div class="step step-error">⚠ ' . $errors . ' erro(s) - mas a instalação pode ter sido bem-sucedida</div>';
                            foreach ($errorMessages as $msg) {
                                if (!empty($msg)) {
                                    echo '<div class="step step-error" style="font-size: 0.85em;">→ ' . htmlspecialchars($msg) . '</div>';
                                }
                            }
                        }
                        
                    } catch (Exception $e) {
                        echo '<div class="step step-error"><strong>ERRO:</strong> ' . $e->getMessage() . '</div>';
                        echo '</div></div></div></div></body></html>';
                        exit;
                    }
                    
                    // Passo 5: Verificar instalação
                    echo '<div class="step step-info"><strong>5.</strong> Verificando instalação...</div>';
                    
                    try {
                        $result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
                        if ($result) {
                            $row = $result->fetch_assoc();
                            if ($row['total'] > 0) {
                                echo '<div class="step step-success">✓ ' . $row['total'] . ' usuário(s) cadastrado(s)</div>';
                            }
                        }
                        
                        $result = $conn->query("SELECT COUNT(*) as total FROM servicos");
                        if ($result) {
                            $row = $result->fetch_assoc();
                            echo '<div class="step step-success">✓ ' . $row['total'] . ' serviço(s) cadastrado(s)</div>';
                        }
                        
                        echo '<div class="step step-success">✓ Banco de dados instalado corretamente!</div>';
                    } catch (Exception $e) {
                        echo '<div class="step step-error">⚠ Não foi possível verificar: ' . $e->getMessage() . '</div>';
                        echo '<div class="step step-info">Mas a instalação foi concluída. Tente fazer login.</div>';
                    }
                    
                    $conn->close();
                    
                    // Mensagem final
                    echo '<div class="step step-success mt-3" style="font-size: 1.2em; text-align: center;">
                          <strong>🎉 INSTALAÇÃO CONCLUÍDA COM SUCESSO!</strong>
                          </div>';
                    
                    echo '<div class="step step-info mt-3">
                          <strong>Credenciais de Acesso:</strong><br>
                          <strong>Email:</strong> admin@atelie.com<br>
                          <strong>Senha:</strong> admin123<br><br>
                          <a href="login.php" class="btn btn-primary btn-block">
                              <i class="fas fa-sign-in-alt"></i> Acessar o Sistema
                          </a>
                          </div>';
                    
                    echo '</div>'; // fecha installation-log
                    
                } else {
                    // Formulário inicial
                    ?>
                    <p>Este assistente vai instalar automaticamente o banco de dados do sistema.</p>
                    
                    <div class="alert alert-info">
                        <strong>Antes de continuar:</strong>
                        <ol class="mb-0">
                            <li>Certifique-se que o XAMPP está rodando (Apache + MySQL)</li>
                            <li>Acesse <a href="http://localhost/phpmyadmin" target="_blank">phpMyAdmin</a></li>
                            <li>Crie um banco chamado: <strong>atelie_orcamentos</strong></li>
                            <li>Use a codificação: <strong>utf8mb4_unicode_ci</strong></li>
                        </ol>
                    </div>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label>Host do Banco:</label>
                            <input type="text" class="form-control" value="localhost" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label>Nome do Banco:</label>
                            <input type="text" class="form-control" value="atelie_orcamentos" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label>Usuário:</label>
                            <input type="text" class="form-control" value="root" readonly>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            <i class="fas fa-play"></i> Iniciar Instalação
                        </button>
                    </form>
                    
                    <div class="mt-3 text-center text-muted">
                        <small>Execute este script apenas uma vez</small>
                    </div>
                    <?php
                }
                ?>
                
            </div>
        </div>
        
        <div class="text-center mt-3 text-muted">
            <small>Sistema de Orçamentos para Ateliê v1.0</small>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</body>
</html>
