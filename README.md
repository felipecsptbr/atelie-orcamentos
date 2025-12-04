# 🧵 Sistema de Orçamentos para Ateliê de Costura

Sistema completo e profissional para gerenciamento de orçamentos, clientes e serviços de ateliês de costura. Desenvolvido com PHP puro, MySQL e AdminLTE para uma interface moderna e responsiva.

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![PHP](https://img.shields.io/badge/PHP-7.4+-purple.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

## ✨ Características

- 📱 **100% Responsivo** - Interface otimizada para desktop, tablet e celular
- 🎨 **Design Elegante** - Baseado no AdminLTE com paleta de cores suaves
- 📊 **Dashboard Completo** - Estatísticas, gráficos e visão geral do negócio
- 👥 **Gestão de Clientes** - Cadastro completo com histórico de orçamentos
- ✂️ **Catálogo de Serviços** - Organize por categorias com preços base
- 💰 **Orçamentos Profissionais** - Múltiplos itens, descontos e status
- 📄 **Geração de PDF** - PDFs elegantes e prontos para impressão
- 📈 **Relatórios Detalhados** - Análise por período com exportação CSV
- ⚙️ **Configurações Completas** - Personalize dados do ateliê e padrões
- 💾 **Backup de Dados** - Sistema de backup integrado

## 🎯 Funcionalidades Principais

### 1. Gestão de Clientes
- Cadastro completo (nome, telefone, WhatsApp, email, endereço)
- Histórico de orçamentos por cliente
- Busca e filtros avançados
- Estatísticas de valor gasto

### 2. Catálogo de Serviços
- Categorias: Ajustes, Confecções, Consertos, Reformas, Outros
- Preço base e tempo estimado
- Descrição detalhada de cada serviço
- Gestão completa (CRUD)

### 3. Sistema de Orçamentos
- Seleção de cliente (com cadastro rápido)
- Adição de múltiplos serviços
- Personalização de quantidade e preços
- Descontos (percentual ou valor fixo)
- Status: Pendente, Aprovado, Em Execução, Concluído, Cancelado
- Campos para observações, prazo e forma de pagamento
- Edição e duplicação de orçamentos

### 4. Geração de PDF
- Layout profissional e clean
- Logo do ateliê (personalizável)
- Dados completos do cliente e orçamento
- Tabela detalhada de serviços
- Cálculos automáticos (subtotal, desconto, total)
- Informações de validade e prazo
- Rodapé personalizável

### 5. Dashboard Inteligente
- Total de orçamentos do mês
- Valor total em vendas
- Taxa de aprovação
- Orçamentos por status
- Gráfico de evolução (6 meses)
- Orçamentos recentes

### 6. Relatórios e Análises
- Filtros por período
- Gráficos de status
- Estatísticas detalhadas
- Exportação para CSV/Excel
- Listagem completa com filtros

## 📋 Requisitos do Sistema

- **Servidor Web**: Apache 2.4+ (XAMPP, WAMP, LAMP)
- **PHP**: 7.4 ou superior
- **MySQL**: 5.7 ou superior / MariaDB 10.3+
- **Extensões PHP**: 
  - PDO
  - PDO_MySQL
  - GD (para manipulação de imagens)
  - mbstring
  - fileinfo

## 🚀 Instalação

### Passo 1: Preparar o Ambiente

1. **Instale o XAMPP** (se ainda não tiver)
   - Download: https://www.apachefriends.org/
   - Instale e inicie os serviços Apache e MySQL

### Passo 2: Configurar o Banco de Dados

1. **Acesse o phpMyAdmin**
   - Abra o navegador: `http://localhost/phpmyadmin`

2. **Importe o banco de dados**
   - Clique em "Novo" para criar um banco
   - Nome: `atelie_orcamentos`
   - Codificação: `utf8mb4_unicode_ci`
   - Vá na aba "Importar"
   - Selecione o arquivo: `database/database.sql`
   - Clique em "Executar"

### Passo 3: Configurar o Sistema

1. **Edite as configurações de conexão** (se necessário)
   - Arquivo: `config/config.php`
   - Ajuste as credenciais do banco de dados:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'atelie_orcamentos');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

2. **Crie as pastas necessárias** (se não existirem)
   ```
   uploads/logo/
   backups/
   temp/
   ```

### Passo 4: Acessar o Sistema

1. **Abra o navegador**
   - URL: `http://localhost/atelie-orcamentos`

2. **Faça login com as credenciais padrão**
   - **Email**: admin@atelie.com
   - **Senha**: admin123

⚠️ **IMPORTANTE**: Altere a senha padrão após o primeiro acesso!

## 📂 Estrutura de Pastas

```
atelie-orcamentos/
├── config/
│   ├── config.php          # Configurações gerais
│   └── database.php        # Classe de conexão com BD
├── database/
│   └── database.sql        # Estrutura do banco de dados
├── fpdf/
│   └── fpdf.php           # Biblioteca FPDF
├── includes/
│   ├── auth.php           # Verificação de autenticação
│   ├── header.php         # Cabeçalho da página
│   ├── footer.php         # Rodapé da página
│   └── sidebar.php        # Menu lateral
├── uploads/
│   └── logo/              # Logos do ateliê
├── backups/               # Backups do sistema
├── temp/                  # Arquivos temporários
├── index.php              # Dashboard
├── login.php              # Página de login
├── logout.php             # Logout
├── clientes.php           # Gestão de clientes
├── cliente_rapido.php     # Cadastro rápido (AJAX)
├── cliente_historico.php  # Histórico do cliente (AJAX)
├── servicos.php           # Gestão de serviços
├── orcamentos.php         # Listagem de orçamentos
├── orcamento_novo.php     # Criar/Editar orçamento
├── orcamento_visualizar.php  # Visualizar orçamento
├── orcamento_pdf.php      # Gerar PDF
├── configuracoes.php      # Configurações do sistema
├── relatorios.php         # Relatórios e análises
└── README.md              # Este arquivo
```

## 🎨 Personalização

### Alterar Cores do Sistema

Edite o arquivo `includes/header.php` na seção `<style>`:

```css
:root {
    --primary-color: #c06c84;      /* Cor principal */
    --secondary-color: #6c5b7b;    /* Cor secundária */
    --accent-color: #f8b195;       /* Cor de destaque */
}
```

### Configurar Dados do Ateliê

1. Acesse: **Configurações** no menu lateral
2. Preencha:
   - Nome do Ateliê
   - Endereço, telefone, email
   - Faça upload do logo
   - Configure padrões de orçamento

### Personalizar PDF

Edite o arquivo `orcamento_pdf.php` para ajustar:
- Layout e cores
- Fonte e tamanhos
- Posicionamento de elementos
- Mensagens personalizadas

## 📱 Responsividade

O sistema foi desenvolvido com foco em usabilidade mobile:

- ✅ Botões grandes e acessíveis
- ✅ Formulários otimizados para toque
- ✅ Tabelas responsivas com scroll horizontal
- ✅ Menu lateral adaptável (hambúrguer)
- ✅ Modais otimizados para telas pequenas

## 🔒 Segurança

- ✅ Senhas criptografadas com `password_hash()`
- ✅ Proteção contra SQL Injection (PDO Prepared Statements)
- ✅ Validação de sessão com timeout
- ✅ Sanitização de inputs
- ✅ Verificação de autenticação em todas as páginas
- ✅ Upload seguro de arquivos com validação

## 🐛 Solução de Problemas

### Erro de Conexão com Banco de Dados

- Verifique se o MySQL está rodando
- Confirme as credenciais em `config/config.php`
- Certifique-se que o banco foi criado corretamente

### PDF não é Gerado

- Verifique se a pasta `fpdf/` existe
- Confira permissões de escrita na pasta `temp/`
- Ative os erros do PHP para ver mensagens detalhadas

### Upload de Logo não Funciona

- Verifique permissões da pasta `uploads/logo/`
- Comando (Linux): `chmod 755 uploads/logo/`
- Confirme o tamanho máximo de upload no `php.ini`

### Erro ao Fazer Backup

- Certifique-se que o `mysqldump` está disponível
- Verifique permissões da pasta `backups/`
- Use um backup manual via phpMyAdmin se necessário

## 📊 Dados de Exemplo

O sistema vem com dados pré-cadastrados:

### Usuário Padrão
- Email: admin@atelie.com
- Senha: admin123

### Serviços de Exemplo
- Ajuste de Bainha - R$ 20,00
- Conserto de Zíper - R$ 15,00
- Costura de Vestido Simples - R$ 150,00
- Ajuste de Cintura - R$ 25,00
- Barra de Calça - R$ 18,00
- Colocação de Elástico - R$ 12,00
- Reforma de Blazer - R$ 80,00

## 🔄 Atualizações Futuras

Possíveis melhorias para versões futuras:

- [ ] Sistema de notificações por WhatsApp
- [ ] Agenda de atendimentos
- [ ] Controle de estoque de materiais
- [ ] Sistema de comissões
- [ ] App mobile (PWA)
- [ ] Integração com meios de pagamento
- [ ] Múltiplos usuários com permissões

## 📝 Licença

Este projeto está sob a licença MIT. Veja o arquivo LICENSE para mais detalhes.

## 👨‍💻 Suporte

Para dúvidas ou problemas:
- Documente o erro com prints
- Verifique os logs de erro do PHP
- Consulte a documentação do AdminLTE
- Revise as configurações do servidor

## 🎉 Créditos

- **AdminLTE**: https://adminlte.io/
- **Bootstrap**: https://getbootstrap.com/
- **Font Awesome**: https://fontawesome.com/
- **Chart.js**: https://www.chartjs.org/
- **FPDF**: http://www.fpdf.org/
- **DataTables**: https://datatables.net/
- **Select2**: https://select2.org/

---

**Desenvolvido com ❤️ para facilitar a gestão de ateliês de costura**

📧 Para sugestões de melhorias, entre em contato!
