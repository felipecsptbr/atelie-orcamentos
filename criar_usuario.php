<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Criar Novo Usuário</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #f5f5f5 0%, #e8d5d5 100%); padding: 50px; }
        .card { max-width: 600px; margin: 0 auto; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .btn-primary { background-color: #c06c84; border-color: #c06c84; }
        .btn-primary:hover { background-color: #a85670; border-color: #a85670; }
    </style>
</head>
<body>
<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$sucesso = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'] ?? '';
    $confirma_senha = $_POST['confirma_senha'] ?? '';
    
    if (empty($nome) || empty($email) || empty($senha) || empty($confirma_senha)) {
        $erro = 'Por favor, preencha todos os campos.';
    } elseif ($senha !== $confirma_senha) {
        $erro = 'As senhas não coincidem.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter no mínimo 6 caracteres.';
    } else {
        try {
            $db = Database::getInstance();
            $conn = $db->getConnection();
            
            // Verificar se email já existe
            $sql = "SELECT id FROM usuarios WHERE email = :email";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if ($stmt->fetch()) {
                $erro = 'Este email já está cadastrado.';
            } else {
                // Inserir novo usuário
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                
                $sql = "INSERT INTO usuarios (nome, email, senha, ativo, data_criacao) 
                        VALUES (:nome, :email, :senha, 1, NOW())";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':nome', $nome);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':senha', $senha_hash);
                $stmt->execute();
                
                $sucesso = 'Usuário criado com sucesso! Você já pode fazer login.';
            }
        } catch (Exception $e) {
            $erro = 'Erro ao criar usuário: ' . $e->getMessage();
        }
    }
}
?>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h3 class="card-title"><i class="fas fa-user-plus"></i> Criar Novo Usuário Administrador</h3>
    </div>
    <div class="card-body">
        <?php if ($sucesso): ?>
            <div class="alert alert-success">
                <i class="icon fas fa-check"></i> <?php echo htmlspecialchars($sucesso); ?>
                <br><br>
                <a href="login.php" class="btn btn-success btn-sm">
                    <i class="fas fa-sign-in-alt"></i> Ir para Login
                </a>
            </div>
        <?php else: ?>
            <?php if ($erro): ?>
                <div class="alert alert-danger">
                    <i class="icon fas fa-ban"></i> <?php echo htmlspecialchars($erro); ?>
                </div>
            <?php endif; ?>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Use este formulário para criar um novo usuário administrador.
                <br><strong>Importante:</strong> Após criar o usuário, delete este arquivo por segurança!
            </div>
            
            <form method="post" action="">
                <div class="form-group">
                    <label for="nome">Nome Completo</label>
                    <input type="text" class="form-control" id="nome" name="nome" 
                           value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="senha">Senha (mínimo 6 caracteres)</label>
                    <input type="password" class="form-control" id="senha" name="senha" required>
                </div>
                
                <div class="form-group">
                    <label for="confirma_senha">Confirmar Senha</label>
                    <input type="password" class="form-control" id="confirma_senha" name="confirma_senha" required>
                </div>
                
                <hr>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-save"></i> Criar Usuário
                </button>
                
                <a href="login.php" class="btn btn-secondary btn-block mt-2">
                    <i class="fas fa-arrow-left"></i> Voltar para Login
                </a>
            </form>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
