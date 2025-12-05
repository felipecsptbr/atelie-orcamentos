# 🚀 Guia de Instalação - Hostinger

Este guia vai te ajudar a instalar o Sistema de Orçamentos na Hostinger passo a passo.

## 📋 Pré-requisitos

- Conta ativa na Hostinger
- Domínio configurado (ou subdomínio)
- Acesso ao hPanel (painel de controle da Hostinger)
- Cliente FTP (FileZilla recomendado)

## 🎯 Passo 1: Criar Banco de Dados MySQL

1. **Acesse o hPanel da Hostinger**
2. **Vá em "Bancos de Dados" → "Gerenciamento"**
3. **Clique em "Novo Banco de Dados"**
4. **Preencha:**
   - Nome do banco: `atelie_orcamentos` (ou outro nome)
   - Nome de usuário: será gerado automaticamente (ex: `u123456789_user`)
   - Senha: crie uma senha forte
5. **Anote esses dados:**
   ```
   Host: localhost
   Nome do banco: u123456789_atelie (exemplo)
   Usuário: u123456789_user (exemplo)
   Senha: [sua senha]
   Porta: 3306
   ```

## 📤 Passo 2: Upload dos Arquivos via FTP

### Opção A: Usando FileZilla (Recomendado)

1. **Baixe e instale o FileZilla:** https://filezilla-project.org/
2. **Configure a conexão FTP:**
   - Host: ftp.seudominio.com (ou IP fornecido pela Hostinger)
   - Usuário: seu usuário FTP
   - Senha: sua senha FTP
   - Porta: 21
3. **Conecte-se ao servidor**
4. **Navegue até a pasta `public_html`**
5. **Faça upload de TODOS os arquivos do sistema**
   - Origem: `C:\xampp\htdocs\atelie-orcamentos\`
   - Destino: `/public_html/` (ou `/public_html/atelie/` se quiser em subpasta)

### Opção B: Usando o Gerenciador de Arquivos do hPanel

1. **Acesse hPanel → "Arquivos" → "Gerenciador de Arquivos"**
2. **Vá para a pasta `public_html`**
3. **Clique em "Upload"**
4. **Comprima todos os arquivos em um ZIP localmente**
5. **Faça upload do ZIP**
6. **Extraia o arquivo ZIP no servidor**

## 🗄️ Passo 3: Importar Banco de Dados

1. **Acesse hPanel → "Bancos de Dados" → "phpMyAdmin"**
2. **Selecione seu banco de dados** (ex: `u123456789_atelie`)
3. **Clique na aba "Importar"**
4. **Clique em "Escolher arquivo"**
5. **Selecione o arquivo:** `database/database.sql`
6. **Clique em "Executar"**
7. **Aguarde a confirmação** (deve aparecer mensagem de sucesso)

## ⚙️ Passo 4: Configurar Arquivo config.php

### Método 1: Substituir arquivo completo

1. **Renomeie o arquivo atual:**
   - `config/config.php` → `config/config.php.bak`
2. **Renomeie o arquivo da Hostinger:**
   - `config/config.hostinger.php` → `config/config.php`
3. **Edite `config/config.php` com os dados do banco:**
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'u123456789_atelie'); // SEU BANCO
   define('DB_USER', 'u123456789_user');   // SEU USUÁRIO
   define('DB_PASS', 'SuaSenhaAqui');      // SUA SENHA
   define('SITE_URL', 'https://seudominio.com'); // SEU DOMÍNIO
   ```

### Método 2: Editar via Gerenciador de Arquivos

1. **hPanel → Gerenciador de Arquivos**
2. **Navegue até:** `public_html/config/config.php`
3. **Clique com botão direito → "Editar"**
4. **Localize as linhas com `DB_HOST`, `DB_NAME`, etc.**
5. **Atualize com seus dados**
6. **Salve o arquivo**

## 🔐 Passo 5: Configurar Permissões

No Gerenciador de Arquivos ou via FTP, ajuste as permissões:

```
/uploads/        → 755
/uploads/logo/   → 755
/temp/           → 755
/config/         → 755
config.php       → 644
```

## 🌐 Passo 6: Configurar SSL (HTTPS)

1. **Acesse hPanel → "Segurança" → "SSL"**
2. **Ative o certificado SSL gratuito** (Let's Encrypt)
3. **Aguarde 10-15 minutos** para ativação
4. **Edite `.htaccess`** e descomente as linhas de redirecionamento HTTPS:
   ```apache
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```

## ✅ Passo 7: Testar a Instalação

1. **Acesse:** `https://seudominio.com/login.php`
2. **Faça login com:**
   - Email: `admin@atelie.com`
   - Senha: `admin123`
3. **Após o login bem-sucedido:**
   - Vá em **"Configurações"**
   - Atualize **nome do ateliê**, **telefone**, **endereço**
   - **ALTERE A SENHA DO ADMINISTRADOR** imediatamente!

## 🔧 Solução de Problemas

### Erro "500 Internal Server Error"

- Verifique permissões dos arquivos
- Ative debug temporário em `config.php`:
  ```php
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
  ```
- Verifique logs de erro no hPanel

### Erro "Database connection failed"

- Confirme os dados do banco em `config/config.php`
- Teste a conexão via phpMyAdmin
- Verifique se o SQL foi importado corretamente

### Página em branco

- Ative display_errors em `config.php`
- Verifique se todas as extensões PHP estão ativas
- Verifique logs de erro do servidor

### Caracteres estranhos (ü, ç, ã)

- Confirme que o charset está em `utf8mb4` no config.php
- Verifique se o banco foi importado com charset correto
- Execute no phpMyAdmin:
  ```sql
  ALTER DATABASE nome_do_banco CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
  ```

## 📱 Recursos da Hostinger

- **phpMyAdmin:** Gerenciar banco de dados
- **Gerenciador de Arquivos:** Editar arquivos online
- **Suporte 24/7:** Chat ao vivo em português
- **Backup Automático:** Ative backups semanais
- **Email Profissional:** Configure emails com seu domínio

## 🎓 Próximos Passos

1. ✅ Alterar senha do administrador
2. ✅ Configurar dados do ateliê
3. ✅ Cadastrar serviços
4. ✅ Cadastrar clientes
5. ✅ Criar primeiro orçamento
6. ✅ Configurar email profissional (opcional)
7. ✅ Ativar backups automáticos

## 📞 Suporte

Se encontrar problemas:

1. Verifique a documentação completa em `INSTALACAO.md`
2. Acesse o suporte da Hostinger (chat 24/7)
3. Verifique os logs de erro no hPanel

---

**Sistema desenvolvido para Ateliês de Costura**  
**Versão:** 1.0  
**Licença:** MIT
