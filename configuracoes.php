<?php
/**
 * Configurações do Sistema
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Configurações';
$mensagem = '';
$tipo_mensagem = '';

// Processar salvamento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar'])) {
    try {
        $db = getDB();
        
        $nome_atelie = trim($_POST['nome_atelie'] ?? '');
        $endereco = trim($_POST['endereco'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $whatsapp = trim($_POST['whatsapp'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $instagram = trim($_POST['instagram'] ?? '');
        $mensagem_rodape = trim($_POST['mensagem_rodape'] ?? '');
        $validade_padrao = intval($_POST['validade_padrao'] ?? 7);
        $prazo_execucao_padrao = trim($_POST['prazo_execucao_padrao'] ?? '');
        $forma_pagamento_padrao = trim($_POST['forma_pagamento_padrao'] ?? '');
        
        // Upload de logo
        $logo_atual = $_POST['logo_atual'] ?? '';
        $logo = $logo_atual;
        
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ALLOWED_EXTENSIONS)) {
                throw new Exception('Formato de imagem não permitido. Use: ' . implode(', ', ALLOWED_EXTENSIONS));
            }
            
            if ($_FILES['logo']['size'] > UPLOAD_MAX_SIZE) {
                throw new Exception('Arquivo muito grande. Tamanho máximo: 5MB');
            }
            
            $logo_dir = __DIR__ . '/uploads/logo';
            if (!is_dir($logo_dir)) {
                mkdir($logo_dir, 0755, true);
            }
            
            $logo_name = 'logo_' . time() . '.' . $ext;
            $logo_path = $logo_dir . '/' . $logo_name;
            
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $logo_path)) {
                // Remover logo antiga
                if ($logo_atual && file_exists($logo_dir . '/' . $logo_atual)) {
                    unlink($logo_dir . '/' . $logo_atual);
                }
                $logo = $logo_name;
            }
        }
        
        // Verificar se existe configuração
        $config_existe = $db->querySingle("SELECT id FROM configuracoes LIMIT 1");
        
        if ($config_existe) {
            $sql = "UPDATE configuracoes SET 
                    nome_atelie = ?, endereco = ?, telefone = ?, whatsapp = ?, 
                    email = ?, instagram = ?, logo = ?, mensagem_rodape = ?,
                    validade_padrao = ?, prazo_execucao_padrao = ?, forma_pagamento_padrao = ?
                    WHERE id = ?";
            $db->execute($sql, [
                $nome_atelie, $endereco, $telefone, $whatsapp, 
                $email, $instagram, $logo, $mensagem_rodape,
                $validade_padrao, $prazo_execucao_padrao, $forma_pagamento_padrao,
                $config_existe['id']
            ]);
        } else {
            $sql = "INSERT INTO configuracoes 
                    (nome_atelie, endereco, telefone, whatsapp, email, instagram, logo, mensagem_rodape,
                     validade_padrao, prazo_execucao_padrao, forma_pagamento_padrao)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $db->execute($sql, [
                $nome_atelie, $endereco, $telefone, $whatsapp, 
                $email, $instagram, $logo, $mensagem_rodape,
                $validade_padrao, $prazo_execucao_padrao, $forma_pagamento_padrao
            ]);
        }
        
        $mensagem = 'Configurações salvas com sucesso!';
        $tipo_mensagem = 'success';
        
    } catch (Exception $e) {
        $mensagem = 'Erro ao salvar: ' . $e->getMessage();
        $tipo_mensagem = 'danger';
    }
}

// Backup
if (isset($_GET['backup'])) {
    try {
        $db = getDB();
        $backup_dir = __DIR__ . '/backups';
        if (!is_dir($backup_dir)) {
            mkdir($backup_dir, 0755, true);
        }
        
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $filepath = $backup_dir . '/' . $filename;
        
        // Comando mysqldump
        $command = sprintf(
            'mysqldump -h%s -u%s -p%s %s > %s',
            DB_HOST,
            DB_USER,
            DB_PASS,
            DB_NAME,
            $filepath
        );
        
        exec($command, $output, $return);
        
        if ($return === 0 && file_exists($filepath)) {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            exit;
        } else {
            $mensagem = 'Erro ao gerar backup. Verifique se o mysqldump está disponível.';
            $tipo_mensagem = 'warning';
        }
        
    } catch (Exception $e) {
        $mensagem = 'Erro ao gerar backup: ' . $e->getMessage();
        $tipo_mensagem = 'danger';
    }
}

// Carregar configurações
try {
    $db = getDB();
    $config = $db->querySingle("SELECT * FROM configuracoes LIMIT 1");
    if (!$config) {
        $config = [
            'nome_atelie' => 'Meu Ateliê',
            'validade_padrao' => 7,
            'prazo_execucao_padrao' => '3 dias úteis',
            'forma_pagamento_padrao' => 'À combinar'
        ];
    }
} catch (Exception $e) {
    $config = [];
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Configurações</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Configurações</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <?php if ($mensagem): ?>
                <div class="alert alert-<?php echo $tipo_mensagem; ?> alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>
            
            <form method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-8">
                        <!-- Dados do Ateliê -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Dados do Ateliê</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Nome do Ateliê *</label>
                                    <input type="text" class="form-control" name="nome_atelie" 
                                           value="<?php echo htmlspecialchars($config['nome_atelie'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label>Endereço</label>
                                    <input type="text" class="form-control" name="endereco" 
                                           value="<?php echo htmlspecialchars($config['endereco'] ?? ''); ?>">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Telefone</label>
                                            <input type="text" class="form-control telefone" name="telefone" 
                                                   value="<?php echo htmlspecialchars($config['telefone'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>WhatsApp</label>
                                            <input type="text" class="form-control celular" name="whatsapp" 
                                                   value="<?php echo htmlspecialchars($config['whatsapp'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" class="form-control" name="email" 
                                                   value="<?php echo htmlspecialchars($config['email'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Instagram</label>
                                            <input type="text" class="form-control" name="instagram" 
                                                   value="<?php echo htmlspecialchars($config['instagram'] ?? ''); ?>" 
                                                   placeholder="@seuatelie">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Logo do Ateliê</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="logo" name="logo" accept="image/*">
                                        <label class="custom-file-label" for="logo">Escolher arquivo...</label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Formatos aceitos: JPG, PNG, GIF | Tamanho máximo: 5MB
                                    </small>
                                    <?php if (!empty($config['logo'])): ?>
                                        <div class="mt-2">
                                            <img src="uploads/logo/<?php echo htmlspecialchars($config['logo']); ?>" 
                                                 alt="Logo" style="max-height: 100px;">
                                        </div>
                                        <input type="hidden" name="logo_atual" value="<?php echo htmlspecialchars($config['logo']); ?>">
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Padrões para Orçamentos -->
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">Padrões para Orçamentos</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Validade Padrão (dias)</label>
                                            <input type="number" class="form-control" name="validade_padrao" 
                                                   value="<?php echo $config['validade_padrao'] ?? 7; ?>" min="1">
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Prazo de Execução Padrão</label>
                                            <input type="text" class="form-control" name="prazo_execucao_padrao" 
                                                   value="<?php echo htmlspecialchars($config['prazo_execucao_padrao'] ?? ''); ?>" 
                                                   placeholder="Ex: 3 dias úteis">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Forma de Pagamento Padrão</label>
                                    <input type="text" class="form-control" name="forma_pagamento_padrao" 
                                           value="<?php echo htmlspecialchars($config['forma_pagamento_padrao'] ?? ''); ?>" 
                                           placeholder="Ex: À vista, Pix, Cartão">
                                </div>
                                
                                <div class="form-group">
                                    <label>Mensagem do Rodapé (PDF)</label>
                                    <textarea class="form-control" name="mensagem_rodape" rows="2" 
                                              placeholder="Ex: Agradecemos a sua preferência!"><?php echo htmlspecialchars($config['mensagem_rodape'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <!-- Ações -->
                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title">Ações</h3>
                            </div>
                            <div class="card-body">
                                <button type="submit" name="salvar" class="btn btn-primary btn-block">
                                    <i class="fas fa-save"></i> Salvar Configurações
                                </button>
                                
                                <hr>
                                
                                <a href="?backup=1" class="btn btn-info btn-block">
                                    <i class="fas fa-download"></i> Fazer Backup
                                </a>
                                
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle"></i> O backup será baixado automaticamente.
                                </small>
                            </div>
                        </div>
                        
                        <!-- Estatísticas do Sistema -->
                        <div class="card card-warning">
                            <div class="card-header">
                                <h3 class="card-title">Estatísticas do Sistema</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                try {
                                    $stats = [
                                        'clientes' => $db->querySingle("SELECT COUNT(*) as total FROM clientes WHERE ativo = 1")['total'] ?? 0,
                                        'servicos' => $db->querySingle("SELECT COUNT(*) as total FROM servicos WHERE ativo = 1")['total'] ?? 0,
                                        'orcamentos' => $db->querySingle("SELECT COUNT(*) as total FROM orcamentos")['total'] ?? 0
                                    ];
                                ?>
                                <table class="table table-sm">
                                    <tr>
                                        <th>Clientes Ativos:</th>
                                        <td class="text-right"><strong><?php echo $stats['clientes']; ?></strong></td>
                                    </tr>
                                    <tr>
                                        <th>Serviços Cadastrados:</th>
                                        <td class="text-right"><strong><?php echo $stats['servicos']; ?></strong></td>
                                    </tr>
                                    <tr>
                                        <th>Orçamentos Gerados:</th>
                                        <td class="text-right"><strong><?php echo $stats['orcamentos']; ?></strong></td>
                                    </tr>
                                </table>
                                <?php } catch (Exception $e) {} ?>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            
        </div>
    </section>
</div>

<?php
$extraJS = "
<script>
$(document).ready(function() {
    // Atualizar label do input file
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\\\').pop();
        $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
    });
});
</script>
";

include __DIR__ . '/includes/footer.php';
?>
