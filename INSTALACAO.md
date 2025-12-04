# 🚀 INSTALAÇÃO RÁPIDA - Sistema de Orçamentos para Ateliê

## ⚡ Instalação em 5 Minutos

### 1️⃣ Criar Banco de Dados

1. Abra o navegador: **http://localhost/phpmyadmin**
2. Clique em **"Novo"** (lado esquerdo)
3. Nome do banco: **`atelie_orcamentos`**
4. Codificação: **`utf8mb4_unicode_ci`**
5. Clique em **"Criar"**

### 2️⃣ Importar Estrutura

1. Clique no banco **`atelie_orcamentos`** criado
2. Vá na aba **"Importar"**
3. Clique em **"Escolher arquivo"**
4. Selecione: **`database/database.sql`**
5. Clique em **"Executar"** (no final da página)
6. Aguarde a mensagem de sucesso ✅

### 3️⃣ Acessar o Sistema

1. Abra: **http://localhost/atelie-orcamentos**
2. Login:
   - **Email:** admin@atelie.com
   - **Senha:** admin123

### 4️⃣ Primeiros Passos

1. **Altere sua senha**
   - Menu: Configurações

2. **Configure seu ateliê**
   - Menu: Configurações
   - Preencha nome, telefone, endereço
   - Faça upload do logo

3. **Cadastre seus serviços**
   - Menu: Serviços
   - Clique em "Novo Serviço"

4. **Cadastre clientes**
   - Menu: Clientes
   - Clique em "Novo Cliente"

5. **Crie seu primeiro orçamento**
   - Menu: Novo Orçamento
   - Selecione cliente
   - Adicione serviços
   - Salve e gere o PDF

## 🎯 Credenciais Padrão

| Campo | Valor |
|-------|-------|
| Email | admin@atelie.com |
| Senha | admin123 |

⚠️ **IMPORTANTE**: Troque a senha após o primeiro acesso!

## 📋 Verificações

### ✅ Checklist de Instalação

- [ ] XAMPP instalado e rodando (Apache + MySQL)
- [ ] Banco de dados criado
- [ ] Estrutura SQL importada
- [ ] Pastas criadas (uploads, backups, temp)
- [ ] Sistema acessível no navegador
- [ ] Login funcionando

### ⚙️ Se algo não funcionar:

1. **Erro de conexão ao banco:**
   - Verifique se MySQL está rodando no XAMPP
   - Confirme nome do banco em `config/config.php`

2. **Página em branco:**
   - Habilite erros no PHP
   - Edite `config/config.php`:
   ```php
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```

3. **Erro ao fazer upload:**
   - Verifique permissões da pasta `uploads/logo/`

4. **PDF não gera:**
   - Confirme que a pasta `fpdf/` existe
   - Verifique se o arquivo `fpdf/fpdf.php` está presente

## 🎨 Personalização Rápida

### Mudar Nome do Ateliê
1. Menu: **Configurações**
2. Campo: **"Nome do Ateliê"**
3. Salvar

### Upload de Logo
1. Menu: **Configurações**
2. Campo: **"Logo do Ateliê"**
3. Escolher arquivo (PNG, JPG, GIF)
4. Salvar

### Alterar Cores do Sistema
Edite: `includes/header.php`

```css
:root {
    --primary-color: #c06c84;      /* Rosa */
    --secondary-color: #6c5b7b;    /* Roxo */
    --accent-color: #f8b195;       /* Pêssego */
}
```

## 📱 Testar Responsividade

1. Abra o sistema no navegador
2. Pressione **F12** (DevTools)
3. Clique no ícone de **celular** 📱
4. Teste em diferentes tamanhos

## 🎉 Pronto!

Seu sistema está funcionando!

### Próximos Passos:
1. ✂️ Cadastre seus serviços principais
2. 👥 Adicione seus clientes
3. 💰 Crie seu primeiro orçamento
4. 📄 Gere um PDF de teste
5. 📊 Explore o dashboard e relatórios

---

## 🆘 Precisa de Ajuda?

### Recursos:
- 📖 Leia o **README.md** completo
- 🔍 Verifique logs de erro do PHP
- 💬 Revise as configurações em `config/config.php`

### Comandos Úteis:

**Verificar se Apache está rodando:**
- Abra: http://localhost

**Verificar se MySQL está rodando:**
- Abra: http://localhost/phpmyadmin

**Reiniciar serviços do XAMPP:**
- Abra o Painel de Controle do XAMPP
- Clique em "Stop" e depois "Start" em Apache e MySQL

---

**Boa sorte com seu ateliê! 🧵✨**
