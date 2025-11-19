<?php
require_once 'config.php';

// Função para formatar moeda
function formatCurrency($value) {
    return 'R$ ' . number_format($value, 2, ',', '.');
}

// Verificar ID do orçamento
if (!isset($_GET['id'])) {
    die('ID do orçamento não informado');
}

$orcamentoId = intval($_GET['id']);

try {
    $pdo = getConnection();
    
    // Buscar dados do orçamento
    $stmt = $pdo->prepare("
        SELECT o.*, c.nome as cliente_nome, c.telefone as cliente_telefone, 
               c.email as cliente_email, c.endereco as cliente_endereco
        FROM orcamentos o
        LEFT JOIN clientes c ON o.cliente_id = c.id
        WHERE o.id = ?
    ");
    $stmt->execute([$orcamentoId]);
    $orcamento = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$orcamento) {
        die('Orçamento não encontrado');
    }
    
    // Buscar itens do orçamento
    $stmt = $pdo->prepare("
        SELECT * FROM orcamento_itens
        WHERE orcamento_id = ?
        ORDER BY id
    ");
    $stmt->execute([$orcamentoId]);
    $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("Erro ao carregar orçamento: " . $e->getMessage());
}

// Gerar HTML para PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #667eea;
            font-size: 28px;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 11px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-section h2 {
            background-color: #667eea;
            color: white;
            padding: 8px 12px;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 5px 10px;
            width: 30%;
            background-color: #f5f5f5;
        }
        .info-value {
            display: table-cell;
            padding: 5px 10px;
            border-bottom: 1px solid #eee;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th {
            background-color: #667eea;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
        }
        table td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .total-section {
            margin-top: 20px;
            text-align: right;
        }
        .total-row {
            padding: 5px 0;
            font-size: 13px;
        }
        .total-row.final {
            font-size: 16px;
            font-weight: bold;
            color: #667eea;
            border-top: 2px solid #667eea;
            padding-top: 10px;
            margin-top: 10px;
        }
        .observacoes {
            background-color: #fffbea;
            border-left: 4px solid #f59e0b;
            padding: 12px;
            margin-top: 20px;
        }
        .observacoes h3 {
            font-size: 12px;
            margin-bottom: 8px;
            color: #92400e;
        }
        .observacoes p {
            font-size: 11px;
            color: #78350f;
            line-height: 1.5;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #eee;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-pendente {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-aprovado {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-concluido {
            background-color: #dbeafe;
            color: #1e40af;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Ateliê Orçamentos</h1>
        <p>Sistema de Gestão de Orçamentos para Costura</p>
    </div>

    <div class="info-section">
        <h2>Dados do Orçamento</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Número:</div>
                <div class="info-value"><strong>' . htmlspecialchars($orcamento['numero']) . '</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Data:</div>
                <div class="info-value">' . date('d/m/Y', strtotime($orcamento['data_orcamento'])) . '</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    <span class="status status-' . $orcamento['status'] . '">' . strtoupper($orcamento['status']) . '</span>
                </div>
            </div>
        </div>
    </div>

    <div class="info-section">
        <h2>Dados do Cliente</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nome:</div>
                <div class="info-value">' . htmlspecialchars($orcamento['cliente_nome'] ?? 'Não informado') . '</div>
            </div>
            <div class="info-row">
                <div class="info-label">Telefone:</div>
                <div class="info-value">' . htmlspecialchars($orcamento['cliente_telefone'] ?? 'Não informado') . '</div>
            </div>
            <div class="info-row">
                <div class="info-label">E-mail:</div>
                <div class="info-value">' . htmlspecialchars($orcamento['cliente_email'] ?? 'Não informado') . '</div>
            </div>
        </div>
    </div>

    <div class="info-section">
        <h2>Serviços</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">#</th>
                    <th style="width: 40%;">Descrição</th>
                    <th style="width: 15%; text-align: center;">Qtd.</th>
                    <th style="width: 17%; text-align: right;">Valor Unit.</th>
                    <th style="width: 18%; text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>';

$itemNum = 1;
foreach ($itens as $item) {
    $html .= '
                <tr>
                    <td style="text-align: center;">' . $itemNum++ . '</td>
                    <td>' . htmlspecialchars($item['descricao']) . '</td>
                    <td style="text-align: center;">' . $item['quantidade'] . '</td>
                    <td style="text-align: right;">' . formatCurrency($item['preco_unitario']) . '</td>
                    <td style="text-align: right;"><strong>' . formatCurrency($item['subtotal']) . '</strong></td>
                </tr>';
}

$html .= '
            </tbody>
        </table>
    </div>

    <div class="total-section">
        <div class="total-row final">
            <strong>VALOR TOTAL: ' . formatCurrency($orcamento['valor_total']) . '</strong>
        </div>
    </div>';

if (!empty($orcamento['observacoes'])) {
    $html .= '
    <div class="observacoes">
        <h3>Observações</h3>
        <p>' . nl2br(htmlspecialchars($orcamento['observacoes'])) . '</p>
    </div>';
}

$html .= '
    <div class="footer">
        <p>Documento gerado em ' . date('d/m/Y H:i') . '</p>
        <p>Ateliê Orçamentos - Sistema de Gestão © ' . date('Y') . '</p>
    </div>
</body>
</html>';

// Verificar se deve gerar PDF ou mostrar HTML
if (isset($_GET['preview'])) {
    // Mostrar preview em HTML
    echo $html;
} else {
    // Gerar PDF usando DomPDF
    require_once 'dompdf/autoload.inc.php';
    
    $options = new \Dompdf\Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('chroot', realpath(__DIR__));
    
    $dompdf = new \Dompdf\Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    // Enviar PDF para o navegador
    $dompdf->stream('orcamento_' . $orcamento['numero'] . '.pdf', [
        'Attachment' => false
    ]);
}
?>
