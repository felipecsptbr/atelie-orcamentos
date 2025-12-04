# ⚠️ SOLUÇÃO DO ERRO DE LOGIN

## Problema Identificado

O erro ocorre porque **as tabelas do banco de dados ainda não foram criadas**.

---

## ✅ SOLUÇÃO RÁPIDA (2 minutos)

### Opção 1: Instalador Automático (RECOMENDADO)

1. **Acesse:** http://localhost/atelie-orcamentos/instalar.php
2. Clique no botão **"Iniciar Instalação"**
3. Aguarde a conclusão
4. Clique em **"Acessar o Sistema"**
5. Faça login com:
   - **Email:** admin@atelie.com
   - **Senha:** admin123

---

### Opção 2: Instalação Manual via phpMyAdmin

1. **Abra o phpMyAdmin:**
   - Acesse: http://localhost/phpmyadmin

2. **Crie o banco de dados:**
   - Clique em **"Novo"** (lado esquerdo)
   - Nome: `atelie_orcamentos`
   - Codificação: `utf8mb4_unicode_ci`
   - Clique em **"Criar"**

3. **Importe o SQL:**
   - Clique no banco `atelie_orcamentos` criado
   - Clique na aba **"Importar"**
   - Clique em **"Escolher arquivo"**
   - Selecione: `C:\xampp\htdocs\atelie-orcamentos\database\database.sql`
   - Role até o final e clique em **"Executar"**
   - Aguarde a mensagem: ✅ "Importação finalizada com sucesso"

4. **Acesse o sistema:**
   - Acesse: http://localhost/atelie-orcamentos/login.php
   - Login: **admin@atelie.com**
   - Senha: **admin123**

---

## 🔍 O Que Foi Corrigido

1. ✅ Corrigido método de conexão no `login.php`
2. ✅ Criado instalador automático (`instalar.php`)
3. ✅ Redirecionamento inicial agora vai para o instalador

---

## 📋 Checklist Pós-Instalação

Após fazer login, siga estes passos:

- [ ] Vá em **Configurações** e altere sua senha
- [ ] Configure os dados do seu ateliê
- [ ] Faça upload do logo
- [ ] Cadastre seus serviços principais
- [ ] Adicione alguns clientes
- [ ] Crie um orçamento de teste
- [ ] Gere um PDF para verificar o layout

---

## ❓ Ainda com Problemas?

### Erro: "SQLSTATE[HY000] [1049] Unknown database"
**Solução:** O banco `atelie_orcamentos` não foi criado. Crie-o no phpMyAdmin.

### Erro: "SQLSTATE[42S02]: Base table or view not found"
**Solução:** As tabelas não foram criadas. Execute o instalador ou importe o SQL.

### Erro: "Access denied for user 'root'@'localhost'"
**Solução:** Verifique se o MySQL está rodando no XAMPP.

### Página em branco
**Solução:** Habilite erros no `config/config.php`:
```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

---

## 🎯 Links Úteis

- **Instalador:** http://localhost/atelie-orcamentos/instalar.php
- **Login:** http://localhost/atelie-orcamentos/login.php
- **phpMyAdmin:** http://localhost/phpmyadmin
- **XAMPP Control:** C:\xampp\xampp-control.exe

---

## 📞 Verificações Básicas

Certifique-se que:

1. ✅ XAMPP está rodando (Apache + MySQL com luz verde)
2. ✅ Você consegue acessar http://localhost
3. ✅ Você consegue acessar http://localhost/phpmyadmin
4. ✅ O banco `atelie_orcamentos` existe no phpMyAdmin
5. ✅ As tabelas foram criadas (usuarios, clientes, servicos, etc)

---

**Pronto! Seu sistema estará funcionando! 🎉**
