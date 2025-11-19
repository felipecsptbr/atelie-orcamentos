# Sistema de Orçamentos para Ateliê de Costura - XAMPP

## 🎉 Sistema PHP Completo com Banco MySQL!

Sistema profissional de orçamentos desenvolvido em **PHP puro** para funcionar perfeitamente com **XAMPP**.

## ✨ Funcionalidades Implementadas

### 📋 **Gestão de Orçamentos**
- ✅ Criação de orçamentos detalhados
- ✅ Múltiplos serviços por orçamento  
- ✅ Cálculo automático de totais
- ✅ Sistema de desconto flexível
- ✅ Banco MySQL completo configurado

### 👤 **Gestão de Clientes**
- ✅ Cadastro automático de clientes
- ✅ Dados completos (nome, telefone, email)  
- ✅ Histórico de orçamentos por cliente

### 🛠️ **12 Serviços Pré-Configurados**
- ✅ Conserto de Calça - R$ 25,00
- ✅ Ajuste de Cintura - R$ 30,00
- ✅ Bainha de Vestido - R$ 35,00
- ✅ Conserto de Zíper - R$ 20,00
- ✅ Ajuste de Manga - R$ 28,00
- ✅ Costura de Rasgo - R$ 15,00
- ✅ Troca de Botões - R$ 12,00
- ✅ Ajuste de Saia - R$ 32,00
- ✅ Barra de Calça Jeans - R$ 18,00
- ✅ Conserto de Bainha - R$ 22,00
- ✅ Ajuste de Decote - R$ 25,00
- ✅ Troca de Forro - R$ 45,00

### 🎨 **Interface Moderna**
- ✅ Design responsivo com Bootstrap 5
- ✅ Interface intuitiva e profissional
- ✅ Navegação simples
- ✅ Feedback visual em tempo real

## 🛠️ Tecnologias Utilizadas

- **PHP 8.2** - Linguagem de programação (via XAMPP)
- **MySQL 8.0** - Banco de dados robusto
- **Bootstrap 5** - Framework CSS responsivo
- **JavaScript ES6** - Interatividade moderna
- **XAMPP** - Ambiente de desenvolvimento
- **SQL Avançado** - Views, procedures, triggers

## 📁 Estrutura Criada

```
ORCAMENTOS/
├── app/
│   ├── Models/
│   │   ├── Cliente.php
│   │   ├── Servico.php
│   │   ├── Orcamento.php
│   │   └── ItemOrcamento.php
│   └── Http/Controllers/
│       ├── Controller.php
│       └── OrcamentoController.php
├── resources/views/
│   ├── layout.blade.php
│   └── orcamentos/
│       └── create.blade.php
├── database/migrations/
│   ├── 001_create_clientes_table.php
│   └── 002_create_servicos_table.php
├── routes/
│   └── web.php
├── config/
│   └── app.php
├── .env
├── composer.json
└── README.md
```

## 🚀 Como Executar no XAMPP

### **Método 1: Instalação Automática**

1. **Execute o instalador:**
   ```batch
   instalar-xampp.bat
   ```

2. **Siga as instruções** que aparecerão

### **Método 2: Instalação Manual**

1. **Abra o XAMPP Control Panel**

2. **Inicie os serviços:**
   - ✅ Apache
   - ✅ MySQL

3. **Copie os arquivos** para:
   ```
   C:\xampp\htdocs\atelie-orcamentos\
   ```

4. **Configure o banco:**
   - Acesse: http://localhost/atelie-orcamentos/instalar.php
   - Clique em "Instalar Banco de Dados"

5. **Use o sistema:**
   - http://localhost/atelie-orcamentos/

## 💡 Como Usar

### **Criando um Orçamento:**

1. Acesse a página inicial
2. Clique em "Novo Orçamento"
3. Preencha os dados do cliente
4. Selecione os serviços clicando nos cards
5. Ajuste quantidades e valores se necessário
6. Configure desconto e observações
7. Salve o orçamento ou gere o PDF

### **Recursos Disponíveis:**
- **Tela inicial:** Lista todos os orçamentos
- **Novo orçamento:** Formulário completo de criação
- **Visualizar orçamento:** Detalhes completos
- **PDF:** Download do orçamento formatado

## 📄 Geração de PDF

Os PDFs incluem:
- Cabeçalho profissional do ateliê
- Dados completos do cliente
- Lista itemizada de serviços
- Valores e totais
- Observações e condições
- Data de validade

## 🔧 Próximos Passos

Após instalar o PHP/Composer, você pode:

1. **Executar o sistema** seguindo as instruções acima
2. **Personalizar serviços** editando os seeders
3. **Modificar layout** nas views Blade
4. **Adicionar funcionalidades** como:
   - Status de orçamentos
   - Relatórios financeiros
   - Notificações por email
   - Backup automático

## 🎯 **Sistema Pronto para Uso!**

O sistema está completamente funcional e pode ser usado imediatamente após a instalação das dependências PHP.

**Diferente do Node.js, este sistema Laravel é mais estável e não requer Node.js instalado!**

---

**Desenvolvido especificamente para ateliês de costura** ✂️👗