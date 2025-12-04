# 🚀 DEPLOY EM HOSPEDAGEM GRATUITA

## Guia completo para hospedar seu sistema online

---

## 🎯 **Opção 1: Railway (Recomendado)**

### ✅ **Vantagens:**
- Deploy automático via GitHub
- $5 de crédito grátis por mês (suficiente para uso moderado)
- MySQL nativo
- SSL automático
- Fácil configuração

### 📋 **Passo a Passo:**

#### 1️⃣ **Preparar o Sistema**

Crie arquivo `railway.json` na raiz:
```json
{
  "build": {
    "builder": "NIXPACKS"
  },
  "deploy": {
    "startCommand": "php -S 0.0.0.0:$PORT -t .",
    "restartPolicyType": "ON_FAILURE",
    "restartPolicyMaxRetries": 10
  }
}
```

Crie arquivo `nixpacks.toml` na raiz:
```toml
[phases.setup]
nixPkgs = ['php82', 'php82Extensions.mysqli', 'php82Extensions.pdo', 'php82Extensions.pdo_mysql']

[phases.install]
cmds = ['echo "PHP instalado"']

[start]
cmd = 'php -S 0.0.0.0:$PORT -t .'
```

Atualize `config/config.php` para usar variáveis de ambiente:
```php
<?php
// Configurações do Banco de Dados
define('DB_HOST', getenv('MYSQLHOST') ?: 'localhost');
define('DB_NAME', getenv('MYSQLDATABASE') ?: 'atelie_orcamentos');
define('DB_USER', getenv('MYSQLUSER') ?: 'root');
define('DB_PASS', getenv('MYSQLPASSWORD') ?: '');
define('DB_PORT', getenv('MYSQLPORT') ?: '3306');
define('DB_CHARSET', 'utf8mb4');

// URL do site (ajuste após deploy)
define('SITE_URL', getenv('RAILWAY_STATIC_URL') ?: 'http://localhost');
```

#### 2️⃣ **Criar Repositório no GitHub**

1. Crie conta em https://github.com
2. Crie novo repositório: "atelie-orcamentos"
3. No terminal do VS Code:

```bash
cd C:\xampp\htdocs\atelie-orcamentos
git init
git add .
git commit -m "Sistema de Orçamentos - Deploy"
git branch -M main
git remote add origin https://github.com/SEU_USUARIO/atelie-orcamentos.git
git push -u origin main
```

#### 3️⃣ **Deploy no Railway**

1. Acesse https://railway.app
2. Faça login com GitHub
3. Clique em **"New Project"**
4. Selecione **"Deploy from GitHub repo"**
5. Escolha o repositório **"atelie-orcamentos"**
6. Aguarde o deploy

#### 4️⃣ **Adicionar Banco de Dados MySQL**

1. No projeto, clique em **"+ New"**
2. Selecione **"Database" > "Add MySQL"**
3. Aguarde provisionamento
4. As variáveis serão automaticamente injetadas

#### 5️⃣ **Importar Banco de Dados**

1. No painel MySQL, clique em **"Connect"**
2. Copie as credenciais
3. Use um cliente MySQL (MySQL Workbench, DBeaver, ou phpMyAdmin local)
4. Conecte remotamente e importe `database/database.sql`

**Ou use o terminal Railway:**
```bash
railway connect mysql
mysql -u root -p
use railway;
source /caminho/database.sql;
```

#### 6️⃣ **Configurar Domínio**

1. No serviço web, vá em **"Settings"**
2. Clique em **"Generate Domain"**
3. Acesse a URL gerada (ex: `atelie-orcamentos-production.up.railway.app`)

---

## 🎯 **Opção 2: Render**

### ✅ **Vantagens:**
- 750 horas grátis por mês
- PostgreSQL grátis
- SSL automático
- Deploy via GitHub

### 📋 **Passo a Passo:**

#### 1️⃣ **Preparar o Sistema**

⚠️ **IMPORTANTE:** Render usa PostgreSQL por padrão. Você precisará:

**Opção A - Usar PostgreSQL (Recomendado para Render):**

1. Atualize `config/database.php` para suportar PostgreSQL:

```php
<?php
require_once __DIR__ . '/config.php';

class Database {
    private static $instance = null;
    private $conn;
    
    private function __construct() {
        try {
            $db_type = DB_TYPE ?? 'mysql'; // 'mysql' ou 'pgsql'
            
            if ($db_type === 'pgsql') {
                $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;
            } else {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            }
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch(PDOException $e) {
            die("Erro de conexão: " . $e->getMessage());
        }
    }
    
    // ... resto do código igual
}
```

2. Atualize `config/config.php`:

```php
<?php
// Detectar ambiente
$is_render = getenv('RENDER') !== false;

if ($is_render) {
    // Configurações para Render (PostgreSQL)
    define('DB_TYPE', 'pgsql');
    define('DB_HOST', getenv('PGHOST'));
    define('DB_NAME', getenv('PGDATABASE'));
    define('DB_USER', getenv('PGUSER'));
    define('DB_PASS', getenv('PGPASSWORD'));
    define('DB_PORT', getenv('PGPORT') ?: '5432');
    define('SITE_URL', getenv('RENDER_EXTERNAL_URL'));
} else {
    // Configurações locais (MySQL)
    define('DB_TYPE', 'mysql');
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'atelie_orcamentos');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_PORT', '3306');
    define('SITE_URL', 'http://localhost/atelie-orcamentos');
}

define('DB_CHARSET', 'utf8mb4');
// ... resto do código
```

3. Converta SQL para PostgreSQL (principais diferenças):
   - `AUTO_INCREMENT` → `SERIAL`
   - `TINYINT(1)` → `BOOLEAN`
   - `DATETIME` → `TIMESTAMP`
   - `ENGINE=InnoDB` → remover

**Opção B - Usar MySQL no Render (Externo):**
Use um banco MySQL externo gratuito:
- **db4free.net** - MySQL 8.0 grátis
- **FreeMySQLHosting.net**

#### 2️⃣ **Deploy no Render**

1. Faça push no GitHub (como no Railway)
2. Acesse https://render.com
3. Clique em **"New +"** > **"Web Service"**
4. Conecte com GitHub
5. Selecione o repositório
6. Configure:
   - **Name:** atelie-orcamentos
   - **Environment:** PHP
   - **Build Command:** (deixe vazio)
   - **Start Command:** `php -S 0.0.0.0:$PORT`
7. Adicione variáveis de ambiente
8. Clique em **"Create Web Service"**

#### 3️⃣ **Adicionar PostgreSQL**

1. No dashboard, clique em **"New +"** > **"PostgreSQL"**
2. Configure e crie
3. Copie a **Internal Database URL**
4. No Web Service, adicione como variável de ambiente

---

## 🆓 **Opção 3: InfinityFree (Mais Simples)**

Se você quer algo **100% gratuito sem limitações** e mais tradicional:

### ✅ **Vantagens:**
- PHP + MySQL nativos
- 5GB de espaço
- Painel cPanel
- phpMyAdmin incluído
- Sem necessidade de Git

### 📋 **Passo a Passo:**

1. **Cadastro:** https://infinityfree.net
2. **Criar conta de hospedagem**
3. **Criar banco MySQL** no painel
4. **Upload via FTP ou File Manager**
5. **Importar SQL via phpMyAdmin**
6. **Acessar:** `seuatelie.infinityfreeapp.com`

---

## 📊 **Comparação:**

| Recurso | Railway | Render | InfinityFree |
|---------|---------|--------|--------------|
| **Preço** | $5 crédito/mês | 750h grátis | 100% grátis |
| **MySQL** | ✅ Nativo | ❌ Só PostgreSQL | ✅ Nativo |
| **PHP** | ✅ | ✅ | ✅ |
| **Deploy** | Git automático | Git automático | FTP manual |
| **SSL** | ✅ Auto | ✅ Auto | ✅ Auto |
| **Domínio** | Subdomínio grátis | Subdomínio grátis | Subdomínio grátis |
| **Facilidade** | ⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ |

---

## 🎯 **Recomendação:**

### **Para Iniciantes:** 
👉 **InfinityFree** - Mais fácil, familiar (cPanel), sem configuração complexa

### **Para Desenvolvedores:**
👉 **Railway** - Deploy automático, MySQL nativo, melhor para desenvolvimento

### **Se Conhece PostgreSQL:**
👉 **Render** - Ótimo plano gratuito, mas precisa adaptar banco

---

## 📝 **Arquivos Necessários para Railway/Render:**

Vou criar os arquivos de configuração para você!
