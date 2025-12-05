<?php
/**
 * Gerador de PDF para Orçamentos
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/fpdf/fpdf.php';

// Função auxiliar para converter UTF-8 (substitui utf8_decode deprecado)
function convert_utf8($text) {
    if (function_exists('iconv')) {
        return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text);
    }
    return mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
}

$orcamento_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$orcamento_id) {
    die('Orçamento inválido');
}

// Carregar dados
try {
    $db = getDB();
    
    $sql = "SELECT o.*, c.nome as cliente_nome, c.telefone as cliente_telefone, 
            c.whatsapp as cliente_whatsapp, c.email as cliente_email, c.endereco as cliente_endereco
            FROM orcamentos o
            INNER JOIN clientes c ON o.cliente_id = c.id
            WHERE o.id = ?";
    $orcamento = $db->querySingle($sql, [$orcamento_id]);
    
    if (!$orcamento) {
        die('Orçamento não encontrado');
    }
    
    $itens = $db->query("SELECT i.*, s.nome as servico_nome 
                         FROM itens_orcamento i
                         INNER JOIN servicos s ON i.servico_id = s.id
                         WHERE i.orcamento_id = ?
                         ORDER BY i.ordem", [$orcamento_id]);
    
    $config = $db->querySingle("SELECT * FROM configuracoes LIMIT 1");
    
} catch (Exception $e) {
    die("Erro ao carregar dados: " . $e->getMessage());
}

// Criar PDF
class PDF extends FPDF {
    private $config;
    private $orcamento;
    
    public function setData($config, $orcamento) {
        $this->config = $config;
        $this->orcamento = $orcamento;
    }
    
    // Método para calcular número de linhas em MultiCell
    function NbLines($w, $txt) {
        $cw = &$this->CurrentFont['cw'];
        if($w==0)
            $w = $this->w-$this->rMargin-$this->x;
        $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
        $s = str_replace("\r",'', (string)$txt);
        $nb = strlen($s);
        if($nb>0 && $s[$nb-1]=="\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while($i<$nb)
        {
            $c = $s[$i];
            if($c=="\n")
            {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep = $i;
            $l += $cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i = $sep+1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }
    
    function Header() {
        // Logo (se existir)
        $logo_path = __DIR__ . '/uploads/logo/' . ($this->config['logo'] ?? '');
        if (!empty($this->config['logo']) && file_exists($logo_path)) {
            $this->Image($logo_path, 10, 10, 30);
            $x_start = 45;
        } else {
            $x_start = 10;
        }
        
        // Nome do Ateliê
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(192, 108, 132);
        $this->SetXY($x_start, 10);
        $this->Cell(0, 8, convert_utf8($this->config['nome_atelie']), 0, 1);
        
        // Dados do Ateliê
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(0, 0, 0);
        $this->SetX($x_start);
        
        $dados_atelie = [];
        if (!empty($this->config['endereco'])) $dados_atelie[] = $this->config['endereco'];
        if (!empty($this->config['telefone']) && $this->config['telefone'] != '(00) 0000-0000') {
            $dados_atelie[] = 'Tel: ' . $this->config['telefone'];
        }
        if (!empty($this->config['email'])) $dados_atelie[] = 'Email: ' . $this->config['email'];
        
        if (!empty($dados_atelie)) {
            $this->Cell(0, 5, convert_utf8(implode(' | ', $dados_atelie)), 0, 1);
        }
        
        // Título do orçamento
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(108, 91, 123);
        $this->Cell(0, 8, convert_utf8('ORÇAMENTO Nº: ' . $this->orcamento['numero']), 0, 1, 'C');
        
        $this->Ln(3);
    }
    
    function Footer() {
        $this->SetY(-20);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128, 128, 128);
        
        if ($this->config['mensagem_rodape']) {
            $this->Cell(0, 5, convert_utf8($this->config['mensagem_rodape']), 0, 1, 'C');
        }
        
        $this->Cell(0, 5, convert_utf8('Página ' . $this->PageNo()), 0, 0, 'C');
    }
    
    function DadosBox($titulo, $dados, $w) {
        $this->SetFillColor(245, 245, 245);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell($w, 6, convert_utf8($titulo), 1, 1, 'C', true);
        
        $this->SetFont('Arial', '', 9);
        foreach ($dados as $label => $valor) {
            $this->Cell($w/2, 5, convert_utf8($label . ':'), 1, 0);
            $this->Cell($w/2, 5, convert_utf8($valor), 1, 1);
        }
    }
}

// Instanciar PDF
$pdf = new PDF();
$pdf->setData($config, $orcamento);
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 25);

// Dados do Cliente e do Orçamento
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(248, 177, 149);
$pdf->Cell(95, 6, 'DADOS DO CLIENTE', 1, 0, 'C', true);
$pdf->Cell(5, 6, '', 0, 0);
$pdf->Cell(95, 6, convert_utf8('DADOS DO ORÇAMENTO'), 1, 1, 'C', true);

// Cliente
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(30, 5, 'Nome:', 1, 0);
$pdf->Cell(65, 5, convert_utf8($orcamento['cliente_nome']), 1, 0);
$pdf->Cell(5, 5, '', 0, 0);
$pdf->Cell(30, 5, 'Data:', 1, 0);
$pdf->Cell(65, 5, date('d/m/Y', strtotime($orcamento['data_orcamento'])), 1, 1);

$pdf->Cell(30, 5, 'Telefone:', 1, 0);
$pdf->Cell(65, 5, convert_utf8($orcamento['cliente_telefone']), 1, 0);
$pdf->Cell(5, 5, '', 0, 0);
$pdf->Cell(30, 5, 'Validade:', 1, 0);
$pdf->Cell(65, 5, date('d/m/Y', strtotime($orcamento['data_validade'])), 1, 1);

if ($orcamento['cliente_email']) {
    $pdf->Cell(30, 5, 'Email:', 1, 0);
    $pdf->Cell(65, 5, convert_utf8($orcamento['cliente_email']), 1, 0);
} else {
    $pdf->Cell(95, 5, '', 1, 0);
}
$pdf->Cell(5, 5, '', 0, 0);
$pdf->Cell(30, 5, 'Status:', 1, 0);
$pdf->Cell(65, 5, convert_utf8(ucfirst(str_replace('_', ' ', $orcamento['status']))), 1, 1);

$pdf->Ln(5);

// Tabela de Serviços
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(108, 91, 123);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(10, 7, '#', 1, 0, 'C', true);
$pdf->Cell(85, 7, convert_utf8('Descrição'), 1, 0, 'C', true);
$pdf->Cell(20, 7, 'Qtd', 1, 0, 'C', true);
$pdf->Cell(35, 7, 'Valor Unit.', 1, 0, 'C', true);
$pdf->Cell(45, 7, 'Total', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(0, 0, 0);
$num = 1;

foreach ($itens as $item) {
    $descricao = $item['servico_nome'];
    if ($item['descricao']) {
        $descricao .= "\n" . $item['descricao'];
    }
    
    $height = 6;
    $nb_lines = $pdf->NbLines(85, convert_utf8($descricao));
    if ($nb_lines > 1) {
        $height = 5 * $nb_lines;
    }
    
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    
    $pdf->MultiCell(10, $height, $num++, 1, 'C');
    $pdf->SetXY($x + 10, $y);
    
    $pdf->MultiCell(85, 5, convert_utf8($descricao), 1);
    $pdf->SetXY($x + 95, $y);
    
    $pdf->Cell(20, $height, $item['quantidade'], 1, 0, 'C');
    $pdf->Cell(35, $height, 'R$ ' . number_format($item['valor_unitario'], 2, ',', '.'), 1, 0, 'R');
    $pdf->Cell(45, $height, 'R$ ' . number_format($item['valor_total'], 2, ',', '.'), 1, 1, 'R');
}

// Totais
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(150, 6, 'Subtotal:', 1, 0, 'R');
$pdf->Cell(45, 6, 'R$ ' . number_format($orcamento['subtotal'], 2, ',', '.'), 1, 1, 'R');

if ($orcamento['desconto_valor'] > 0) {
    $desconto_label = 'Desconto';
    $desconto_real = $orcamento['desconto_valor'];
    
    if ($orcamento['desconto_tipo'] == 'percentual') {
        $desconto_label .= ' (' . number_format($orcamento['desconto_valor'], 2, ',', '.') . '%)';
        $desconto_real = $orcamento['subtotal'] * ($orcamento['desconto_valor'] / 100);
    }
    
    $pdf->SetTextColor(220, 53, 69);
    $pdf->Cell(150, 6, convert_utf8($desconto_label . ':'), 1, 0, 'R');
    $pdf->Cell(45, 6, '- R$ ' . number_format($desconto_real, 2, ',', '.'), 1, 1, 'R');
    $pdf->SetTextColor(0, 0, 0);
}

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(40, 167, 69);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(150, 8, 'TOTAL:', 1, 0, 'R', true);
$pdf->Cell(45, 8, 'R$ ' . number_format($orcamento['total'], 2, ',', '.'), 1, 1, 'R', true);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(5);

// Informações Adicionais
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, convert_utf8('INFORMAÇÕES ADICIONAIS'), 0, 1);
$pdf->SetFont('Arial', '', 9);

if ($orcamento['prazo_execucao']) {
    $pdf->Cell(50, 5, convert_utf8('Prazo de execução:'), 0, 0);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(0, 5, convert_utf8($orcamento['prazo_execucao']), 0, 1);
    $pdf->SetFont('Arial', '', 9);
}

if ($orcamento['forma_pagamento']) {
    $pdf->Cell(50, 5, 'Forma de pagamento:', 0, 0);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(0, 5, convert_utf8($orcamento['forma_pagamento']), 0, 1);
    $pdf->SetFont('Arial', '', 9);
}

if ($orcamento['observacoes']) {
    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(0, 5, convert_utf8('Observações:'), 0, 1);
    $pdf->SetFont('Arial', '', 9);
    $pdf->MultiCell(0, 5, convert_utf8($orcamento['observacoes']));
}

// Método para contar linhas
if (!method_exists($pdf, 'NbLines')) {
    class PDF_Extended extends PDF {
        function NbLines($w, $txt) {
            $cw = &$this->CurrentFont['cw'];
            if($w==0)
                $w = $this->w-$this->rMargin-$this->x;
            $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
            $s = str_replace("\r",'',$txt);
            $nb = strlen($s);
            if($nb>0 && $s[$nb-1]=="\n")
                $nb--;
            $sep = -1;
            $i = 0;
            $j = 0;
            $l = 0;
            $nl = 1;
            while($i<$nb) {
                $c = $s[$i];
                if($c=="\n") {
                    $i++;
                    $sep = -1;
                    $j = $i;
                    $l = 0;
                    $nl++;
                    continue;
                }
                if($c==' ')
                    $sep = $i;
                $l += $cw[$c];
                if($l>$wmax) {
                    if($sep==-1) {
                        if($i==$j)
                            $i++;
                    } else
                        $i = $sep+1;
                    $sep = -1;
                    $j = $i;
                    $l = 0;
                    $nl++;
                } else
                    $i++;
            }
            return $nl;
        }
    }
}

// Output PDF
$pdf->Output('I', 'Orcamento_' . $orcamento['numero'] . '.pdf');
?>
