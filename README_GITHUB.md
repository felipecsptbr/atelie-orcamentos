# Sistema de Orçamentos para Ateliê de Costura

Sistema completo para gestão de orçamentos desenvolvido em PHP puro, ideal para ateliês de costura e pequenos negócios.

## 🚀 Funcionalidades

- ✅ **Gestão de Clientes**: Cadastro automático de clientes
- ✅ **Criação de Orçamentos**: Interface intuitiva para criar orçamentos
- ✅ **Biblioteca de Serviços**: 12 serviços pré-cadastrados
- ✅ **Geração de PDF**: Orçamentos profissionais em PDF
- ✅ **Dashboard**: Estatísticas e visão geral
- ✅ **Responsivo**: Funciona perfeitamente em celulares e tablets

## 📋 Requisitos

- PHP 8.0 ou superior
- MySQL 5.7 ou superior / MariaDB 10.3+
- Apache (XAMPP recomendado para Windows)
- Extensões PHP: PDO, pdo_mysql, mbstring

## 🔧 Instalação

### Windows (XAMPP)

1. Clone ou baixe o repositório:
```bash
cd C:\xampp\htdocs
git clone https://github.com/SEU-USUARIO/atelie-orcamentos.git
cd atelie-orcamentos
```

2. Copie o arquivo de configuração:
```bash
copy config.example.php config.php
```

3. Edite o `config.php` com suas credenciais do MySQL

4. Inicie o Apache e MySQL no XAMPP

5. Acesse o instalador:
```
http://localhost/atelie-orcamentos/instalar.php
```

6. Clique em "Instalar Banco de Dados" e pronto!

### Linux

```bash
# Clone o repositório
git clone https://github.com/SEU-USUARIO/atelie-orcamentos.git /var/www/html/atelie-orcamentos
cd /var/www/html/atelie-orcamentos

# Configure
cp config.example.php config.php
nano config.php

# Ajuste permissões
chmod -R 755 .
chown -R www-data:www-data .

# Acesse http://seu-dominio/atelie-orcamentos/instalar.php
```

## 📱 Uso

### Criar um Orçamento

1. Acesse **Novo Orçamento**
2. Preencha os dados do cliente
3. Selecione os serviços desejados
4. Ajuste quantidades e valores
5. Adicione observações (opcional)
6. Clique em **Criar Orçamento**

### Gerar PDF

1. Na lista de orçamentos, clique no botão vermelho (PDF)
2. Ou acesse os detalhes e clique em **Gerar PDF**
3. O PDF será gerado automaticamente

## 🎨 Serviços Pré-configurados

| Serviço | Preço Base |
|---------|------------|
| Conserto de calça (barra) | R$ 15,00 |
| Conserto de calça (cintura) | R$ 25,00 |
| Conserto de camisa | R$ 20,00 |
| Troca de zíper | R$ 30,00 |
| Ajuste de vestido | R$ 40,00 |
| Barra de saia | R$ 15,00 |
| Conserto de rasgo | R$ 25,00 |
| Colocação de elástico | R$ 20,00 |
| Ajuste de manga | R$ 18,00 |
| Costura de botões | R$ 5,00 |
| Customização | R$ 50,00 |
| Outros serviços | Personalizável |

## 🗂️ Estrutura do Projeto

```
atelie-orcamentos/
├── config.php              # Configurações do banco de dados
├── config.example.php      # Exemplo de configuração
├── index.php               # Dashboard principal
├── novo-orcamento.php      # Formulário de novo orçamento
├── ver-orcamento.php       # Detalhes do orçamento
├── gerar-pdf.php           # Gerador de PDF
├── instalar.php            # Instalador automático
├── layout.php              # Template HTML
├── database.sql            # Schema do banco de dados
├── dompdf/                 # Biblioteca para geração de PDF
├── .gitignore              # Arquivos ignorados pelo Git
└── README.md               # Este arquivo
```

## 🛠️ Tecnologias

- **Backend**: PHP 8.2
- **Banco de Dados**: MySQL/MariaDB
- **Frontend**: Bootstrap 5.3
- **PDF**: DomPDF 2.0
- **Ícones**: Bootstrap Icons

## 🔒 Segurança

- Prepared Statements (prevenção de SQL Injection)
- Validação de dados no servidor
- Escape de HTML (prevenção de XSS)
- Configurações sensíveis em arquivo separado (.gitignore)

## 🚀 Deploy

### Hospedagem Compartilhada

1. Faça upload dos arquivos via FTP
2. Crie o banco de dados no cPanel
3. Configure o `config.php` com as credenciais
4. Acesse `seu-dominio.com/instalar.php`

### VPS/Cloud

```bash
# Atualize o sistema
sudo apt update && sudo apt upgrade -y

# Instale Apache, PHP e MySQL
sudo apt install apache2 php mysql-server php-mysql php-mbstring -y

# Clone o repositório
cd /var/www/html
sudo git clone https://github.com/SEU-USUARIO/atelie-orcamentos.git

# Configure permissões
sudo chmod -R 755 atelie-orcamentos
sudo chown -R www-data:www-data atelie-orcamentos

# Configure o banco de dados
sudo mysql
CREATE DATABASE atelie_orcamentos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'atelie'@'localhost' IDENTIFIED BY 'sua-senha-segura';
GRANT ALL PRIVILEGES ON atelie_orcamentos.* TO 'atelie'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Configure o config.php e acesse o instalador
```

## 🤝 Contribuindo

Contribuições são bem-vindas!

1. Fork o projeto
2. Crie uma branch (`git checkout -b feature/nova-funcionalidade`)
3. Commit suas mudanças (`git commit -m 'Adiciona nova funcionalidade'`)
4. Push para a branch (`git push origin feature/nova-funcionalidade`)
5. Abra um Pull Request

## 📝 Licença

Este projeto é de código aberto e está disponível sob a licença MIT.

## 📞 Suporte

Para reportar bugs ou sugerir melhorias, abra uma issue no GitHub.

---

**⭐ Se este projeto foi útil para você, considere dar uma estrela no GitHub!**
