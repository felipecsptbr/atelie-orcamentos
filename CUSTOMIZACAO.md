# 🎨 GUIA DE CUSTOMIZAÇÃO

## Como Personalizar o Sistema para seu Ateliê

Este guia mostra como fazer as principais personalizações de forma simples e rápida.

---

## 🎨 1. ALTERAR CORES DO SISTEMA

### Localização: `includes/header.php`

Encontre a seção CSS com as variáveis:

```css
:root {
    --primary-color: #c06c84;      /* Rosa principal */
    --secondary-color: #6c5b7b;    /* Roxo secundário */
    --accent-color: #f8b195;       /* Pêssego destaque */
    --light-bg: #f5f5f5;          /* Fundo claro */
}
```

### Exemplos de Paletas:

#### 🌸 Rosa Romântico (Padrão)
```css
--primary-color: #c06c84;
--secondary-color: #6c5b7b;
--accent-color: #f8b195;
```

#### 💙 Azul Profissional
```css
--primary-color: #4A90E2;
--secondary-color: #2C3E50;
--accent-color: #8CD4F5;
```

#### 💜 Roxo Elegante
```css
--primary-color: #9B59B6;
--secondary-color: #8E44AD;
--accent-color: #D7BDE2;
```

#### 🌿 Verde Natural
```css
--primary-color: #27AE60;
--secondary-color: #1E8449;
--accent-color: #A3E4D7;
```

#### 🍊 Laranja Vibrante
```css
--primary-color: #E67E22;
--secondary-color: #D35400;
--accent-color: #F8B195;
```

---

## 🖼️ 2. ADICIONAR LOGO DO ATELIÊ

### Via Interface (Recomendado)
1. Faça login no sistema
2. Menu: **Configurações**
3. Campo: **Logo do Ateliê**
4. Clique em **"Escolher arquivo"**
5. Selecione sua logo (PNG, JPG ou GIF)
6. Clique em **"Salvar Configurações"**

### Requisitos da Logo:
- Formato: PNG (com fundo transparente) ou JPG
- Tamanho recomendado: 300x100px
- Peso máximo: 5MB
- Proporção: Horizontal funciona melhor

---

## 📝 3. PERSONALIZAR TEXTOS DO PDF

### Localização: `orcamento_pdf.php`

#### Alterar Mensagem do Rodapé:
Via interface em **Configurações > Mensagem do Rodapé**

Ou direto no código (linha ~150):
```php
if ($this->config['mensagem_rodape']) {
    $this->Cell(0, 5, utf8_decode($this->config['mensagem_rodape']), 0, 1, 'C');
}
```

#### Alterar Cabeçalho do PDF:
Localize (linha ~30):
```php
$this->Cell(0, 8, utf8_decode('ORÇAMENTO Nº: ' . $this->orcamento['numero']), 0, 1, 'C');
```

Pode mudar para:
```php
$this->Cell(0, 8, utf8_decode('PROPOSTA COMERCIAL Nº: ' . $this->orcamento['numero']), 0, 1, 'C');
```

---

## 🎯 4. ADICIONAR NOVOS CAMPOS

### Exemplo: Adicionar CPF ao Cliente

#### 1. Atualizar Banco de Dados:
```sql
ALTER TABLE clientes ADD COLUMN cpf VARCHAR(14) AFTER telefone;
```

#### 2. Atualizar Formulário (`clientes.php`):
```php
<div class="form-group">
    <label>CPF</label>
    <input type="text" class="form-control cpf" name="cpf" id="cliente_cpf">
</div>
```

#### 3. Atualizar Salvamento:
```php
$cpf = trim($_POST['cpf'] ?? '');
// Incluir $cpf nas queries INSERT/UPDATE
```

---

## 📋 5. ADICIONAR NOVAS CATEGORIAS DE SERVIÇOS

### Localização: `database/database.sql` e `servicos.php`

#### 1. Alterar ENUM no Banco:
```sql
ALTER TABLE servicos MODIFY categoria 
ENUM('ajustes','confeccoes','consertos','reformas','bordados','outros') 
DEFAULT 'outros';
```

#### 2. Atualizar Formulário (`servicos.php`):
```php
<select class="form-control" name="categoria">
    <option value="ajustes">Ajustes</option>
    <option value="confeccoes">Confecções</option>
    <option value="consertos">Consertos</option>
    <option value="reformas">Reformas</option>
    <option value="bordados">Bordados</option>
    <option value="outros">Outros</option>
</select>
```

---

## 🔔 6. ADICIONAR NOVOS STATUS DE ORÇAMENTO

### Exemplo: Adicionar "Aguardando Material"

#### 1. Alterar ENUM:
```sql
ALTER TABLE orcamentos MODIFY status 
ENUM('pendente','aprovado','aguardando_material','em_execucao','concluido','cancelado') 
DEFAULT 'pendente';
```

#### 2. Atualizar CSS (`includes/header.php`):
```css
.badge-status-aguardando_material {
    background-color: #fd7e14;
    color: #fff;
}
```

#### 3. Adicionar em Filtros/Selects:
```php
<option value="aguardando_material">Aguardando Material</option>
```

---

## 💬 7. PERSONALIZAR MENSAGENS DO SISTEMA

### Mensagens de Sucesso/Erro (`*. php`):

Encontre e modifique:
```php
$mensagem = 'Cliente cadastrado com sucesso!';
```

Para algo mais pessoal:
```php
$mensagem = 'Oba! Cliente cadastrado com sucesso! 🎉';
```

---

## 📧 8. ADICIONAR ENVIO DE EMAIL

### Instalar PHPMailer (via Composer ou manual):
```bash
composer require phpmailer/phpmailer
```

### Criar função de envio (`includes/email.php`):
```php
<?php
use PHPMailer\PHPMailer\PHPMailer;

function enviarEmail($destinatario, $assunto, $corpo) {
    $mail = new PHPMailer(true);
    
    // Configurar SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'seu@email.com';
    $mail->Password = 'sua_senha';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    
    // Destinatário e conteúdo
    $mail->setFrom('seu@email.com', 'Ateliê');
    $mail->addAddress($destinatario);
    $mail->Subject = $assunto;
    $mail->Body = $corpo;
    
    return $mail->send();
}
?>
```

---

## 🎨 9. MODIFICAR LAYOUT DO DASHBOARD

### Localização: `index.php`

#### Reordenar Cards:
Mova os blocos `<div class="col-lg-3">` para a ordem desejada.

#### Adicionar Novo Card:
```php
<div class="col-lg-3 col-6">
    <div class="small-box bg-primary">
        <div class="inner">
            <h3>42</h3>
            <p>Novo Indicador</p>
        </div>
        <div class="icon">
            <i class="fas fa-star"></i>
        </div>
    </div>
</div>
```

---

## 🔒 10. ALTERAR TEMPO DE SESSÃO

### Localização: `config/config.php`

```php
define('SESSION_LIFETIME', 7200); // 2 horas em segundos
```

Valores comuns:
- 1 hora: 3600
- 2 horas: 7200 (padrão)
- 4 horas: 14400
- 8 horas: 28800

---

## 📱 11. ADICIONAR PWA (Progressive Web App)

### 1. Criar `manifest.json`:
```json
{
    "name": "Ateliê Orçamentos",
    "short_name": "Ateliê",
    "start_url": "/atelie-orcamentos/",
    "display": "standalone",
    "background_color": "#ffffff",
    "theme_color": "#c06c84",
    "icons": [{
        "src": "assets/img/icon-192.png",
        "sizes": "192x192",
        "type": "image/png"
    }]
}
```

### 2. Adicionar no `header.php`:
```html
<link rel="manifest" href="manifest.json">
<meta name="theme-color" content="#c06c84">
```

---

## 🖨️ 12. PERSONALIZAR LAYOUT DO PDF

### Localização: `orcamento_pdf.php`

#### Alterar Fonte:
```php
$pdf->SetFont('Arial', 'B', 14);  // Família, Estilo, Tamanho
```

Fontes disponíveis: Arial, Times, Courier, Symbol, ZapfDingbats

#### Alterar Cores:
```php
$pdf->SetFillColor(192, 108, 132);  // RGB
$pdf->SetTextColor(0, 0, 0);        // Preto
```

#### Adicionar Linha/Borda:
```php
$pdf->Line(10, 50, 200, 50);  // x1, y1, x2, y2
```

---

## 🌐 13. TRADUZIR/MUDAR IDIOMA

Embora o sistema esteja em português, você pode:

1. Buscar termos específicos em todos os arquivos
2. Substituir por termos personalizados
3. Exemplo: "Orçamento" → "Proposta"

**Ferramenta útil**: Busca e substituição global do VS Code (Ctrl+Shift+H)

---

## 💾 14. CONFIGURAR BACKUP AUTOMÁTICO

### Criar script `backup_automatico.php`:
```php
<?php
$comando = sprintf(
    'mysqldump -h%s -u%s -p%s %s > backups/auto_%s.sql',
    'localhost', 'root', '', 'atelie_orcamentos', date('Y-m-d')
);
exec($comando);
?>
```

### Configurar no cron (Linux) ou Agendador (Windows):
- Executar diariamente às 23h
- Limpar backups antigos (> 30 dias)

---

## 🎭 15. ADICIONAR ANIMAÇÕES

### Localização: `assets/css/custom.css`

Adicione animações CSS:
```css
@keyframes slideIn {
    from { transform: translateX(-100%); }
    to { transform: translateX(0); }
}

.card {
    animation: slideIn 0.5s ease-out;
}
```

---

## 📊 16. ADICIONAR NOVOS GRÁFICOS

### Localização: `index.php` ou `relatorios.php`

Exemplo de gráfico de pizza:
```javascript
var ctx = document.getElementById('meuGrafico').getContext('2d');
var myChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Label 1', 'Label 2', 'Label 3'],
        datasets: [{
            data: [30, 50, 20],
            backgroundColor: ['#c06c84', '#6c5b7b', '#f8b195']
        }]
    }
});
```

---

## 🔍 17. MELHORAR SEO E META TAGS

### Localização: `includes/header.php`

Adicione antes de `</head>`:
```html
<meta name="description" content="Sistema de orçamentos para ateliê">
<meta name="keywords" content="ateliê, costura, orçamentos">
<meta name="author" content="Seu Nome">
<link rel="icon" href="assets/img/favicon.png">
```

---

## 🎯 DICAS FINAIS

### Antes de Modificar:
1. ✅ Faça backup do arquivo original
2. ✅ Teste em ambiente de desenvolvimento
3. ✅ Documente suas alterações
4. ✅ Use versionamento (Git)

### Ferramentas Úteis:
- **VS Code**: Editor recomendado
- **XAMPP**: Ambiente local
- **phpMyAdmin**: Gestão de banco
- **Chrome DevTools**: Debug frontend

### Recursos de Aprendizado:
- PHP: https://www.php.net/
- AdminLTE: https://adminlte.io/docs/
- Bootstrap: https://getbootstrap.com/docs/
- Chart.js: https://www.chartjs.org/docs/

---

**Boa personalização! 🎨✨**

Para dúvidas, consulte a documentação oficial das bibliotecas ou revise os arquivos de exemplo do sistema.
