# ✅ CHECKLIST DE INSTALAÇÃO E CONFIGURAÇÃO

## 📦 Arquivos do Sistema

Verifique se todos os arquivos foram criados corretamente:

### Estrutura Principal
- [x] `README.md` - Documentação completa
- [x] `INSTALACAO.md` - Guia rápido de instalação
- [x] `index.html` - Redirecionamento automático
- [x] `.htaccess` - Configurações de segurança

### Configuração
- [x] `config/config.php` - Configurações gerais
- [x] `config/database.php` - Conexão com banco de dados

### Banco de Dados
- [x] `database/database.sql` - Estrutura completa do BD

### Autenticação
- [x] `login.php` - Página de login
- [x] `logout.php` - Logout
- [x] `includes/auth.php` - Verificação de sessão

### Layout
- [x] `includes/header.php` - Cabeçalho AdminLTE
- [x] `includes/sidebar.php` - Menu lateral
- [x] `includes/footer.php` - Rodapé e scripts
- [x] `assets/css/custom.css` - Estilos customizados

### Páginas Principais
- [x] `index.php` - Dashboard com estatísticas
- [x] `clientes.php` - CRUD de clientes
- [x] `cliente_historico.php` - Histórico (AJAX)
- [x] `cliente_rapido.php` - Cadastro rápido (AJAX)
- [x] `servicos.php` - CRUD de serviços
- [x] `orcamentos.php` - Listagem de orçamentos
- [x] `orcamento_novo.php` - Criar/Editar orçamento
- [x] `orcamento_visualizar.php` - Visualizar orçamento
- [x] `orcamento_pdf.php` - Geração de PDF
- [x] `configuracoes.php` - Configurações do sistema
- [x] `relatorios.php` - Relatórios e análises

### Biblioteca PDF
- [x] `fpdf/` - Biblioteca FPDF instalada

### Pastas
- [x] `uploads/logo/` - Para logos do ateliê
- [x] `backups/` - Para backups do sistema
- [x] `temp/` - Arquivos temporários

---

## 🔧 Passo a Passo de Instalação

### 1. Preparar Ambiente
- [ ] XAMPP instalado
- [ ] Apache iniciado
- [ ] MySQL iniciado

### 2. Criar Banco de Dados
- [ ] Acessar phpMyAdmin (http://localhost/phpmyadmin)
- [ ] Criar banco: `atelie_orcamentos`
- [ ] Codificação: `utf8mb4_unicode_ci`
- [ ] Importar: `database/database.sql`
- [ ] Verificar 6 tabelas criadas

**Tabelas esperadas:**
1. usuarios
2. clientes
3. servicos
4. orcamentos
5. itens_orcamento
6. configuracoes

### 3. Verificar Configurações
- [ ] Abrir `config/config.php`
- [ ] Verificar credenciais do banco
- [ ] Confirmar URL do sistema

### 4. Primeiro Acesso
- [ ] Acessar: http://localhost/atelie-orcamentos
- [ ] Login: admin@atelie.com
- [ ] Senha: admin123
- [ ] Login bem-sucedido ✅

### 5. Configuração Inicial
- [ ] Ir em: Configurações
- [ ] Alterar senha do admin
- [ ] Preencher dados do ateliê:
  - [ ] Nome do ateliê
  - [ ] Endereço
  - [ ] Telefone
  - [ ] WhatsApp
  - [ ] Email
- [ ] Upload do logo (opcional)
- [ ] Configurar padrões de orçamento

### 6. Cadastros Iniciais
- [ ] Cadastrar 5-10 serviços principais
- [ ] Categorizar serviços corretamente
- [ ] Definir preços base realistas
- [ ] Adicionar tempo estimado

### 7. Teste Completo

#### Cliente
- [ ] Criar cliente teste
- [ ] Preencher todos os campos
- [ ] Verificar se salvou

#### Orçamento
- [ ] Criar novo orçamento
- [ ] Selecionar cliente
- [ ] Adicionar 3+ serviços
- [ ] Testar desconto (%)
- [ ] Testar desconto (R$)
- [ ] Salvar orçamento

#### PDF
- [ ] Gerar PDF do orçamento
- [ ] Verificar formatação
- [ ] Verificar dados do ateliê
- [ ] Verificar logo (se enviado)
- [ ] Verificar cálculos

#### Dashboard
- [ ] Verificar estatísticas
- [ ] Verificar gráfico
- [ ] Verificar orçamentos recentes

#### Relatórios
- [ ] Gerar relatório do mês
- [ ] Testar filtro por período
- [ ] Exportar CSV
- [ ] Verificar dados exportados

### 8. Testes Mobile
- [ ] Abrir no celular ou DevTools (F12)
- [ ] Testar menu hambúrguer
- [ ] Criar orçamento no mobile
- [ ] Verificar formulários
- [ ] Testar scroll em tabelas
- [ ] Verificar botões grandes

### 9. Segurança
- [ ] Alterar senha padrão ⚠️
- [ ] Testar logout
- [ ] Tentar acessar sem login
- [ ] Verificar timeout de sessão

### 10. Backup
- [ ] Ir em Configurações
- [ ] Clicar em "Fazer Backup"
- [ ] Verificar download do arquivo SQL

---

## 🎯 Funcionalidades Testadas

### Dashboard ✅
- [ ] Exibe total de orçamentos do mês
- [ ] Exibe valor total
- [ ] Exibe taxa de aprovação
- [ ] Exibe orçamentos pendentes
- [ ] Gráfico funciona
- [ ] Lista orçamentos recentes

### Clientes ✅
- [ ] Criar cliente
- [ ] Editar cliente
- [ ] Excluir cliente (soft delete)
- [ ] Ver histórico de orçamentos
- [ ] Buscar clientes
- [ ] Cadastro rápido funciona

### Serviços ✅
- [ ] Criar serviço
- [ ] Editar serviço
- [ ] Excluir serviço
- [ ] Categorias funcionam
- [ ] Preços formatados corretamente

### Orçamentos ✅
- [ ] Criar novo orçamento
- [ ] Editar orçamento existente
- [ ] Duplicar orçamento
- [ ] Adicionar múltiplos itens
- [ ] Remover itens
- [ ] Calcular subtotal
- [ ] Aplicar desconto %
- [ ] Aplicar desconto R$
- [ ] Calcular total
- [ ] Mudar status
- [ ] Visualizar orçamento
- [ ] Gerar PDF
- [ ] Filtrar por status
- [ ] Buscar por número/cliente

### PDF ✅
- [ ] Logo aparece (se configurado)
- [ ] Dados do ateliê corretos
- [ ] Dados do cliente corretos
- [ ] Tabela de serviços completa
- [ ] Cálculos corretos
- [ ] Formatação profissional
- [ ] Imprime bem

### Configurações ✅
- [ ] Salvar dados do ateliê
- [ ] Upload de logo funciona
- [ ] Padrões aplicados nos orçamentos
- [ ] Backup gera arquivo
- [ ] Estatísticas exibidas

### Relatórios ✅
- [ ] Filtro por período funciona
- [ ] Estatísticas corretas
- [ ] Gráfico de status funciona
- [ ] Exportar CSV funciona
- [ ] CSV abre no Excel

---

## 🐛 Problemas Comuns e Soluções

### ❌ Erro: "Access denied for user"
**Solução:**
1. Verifique `config/config.php`
2. Confirme usuário: `root`
3. Senha: (vazio no XAMPP padrão)

### ❌ Página em branco
**Solução:**
1. Habilite erros no `config/config.php`
2. Verifique logs do Apache
3. Confirme que PHP está instalado

### ❌ PDF não gera
**Solução:**
1. Verifique se `fpdf/fpdf.php` existe
2. Confirme permissões da pasta `temp/`
3. Verifique logs de erro do PHP

### ❌ Upload não funciona
**Solução:**
1. Permissões da pasta `uploads/logo/`
2. Verifique `php.ini`: `upload_max_filesize`
3. Confirme formatos permitidos

### ❌ Sessão expira muito rápido
**Solução:**
1. Edite `config/config.php`
2. Aumente `SESSION_LIFETIME`
3. Valor em segundos (ex: 7200 = 2 horas)

---

## 📊 Dados de Teste Incluídos

### Usuário Padrão
- Email: admin@atelie.com
- Senha: admin123

### Serviços Cadastrados (7)
1. Ajuste de Bainha - R$ 20,00
2. Conserto de Zíper - R$ 15,00
3. Costura de Vestido Simples - R$ 150,00
4. Ajuste de Cintura - R$ 25,00
5. Barra de Calça - R$ 18,00
6. Colocação de Elástico - R$ 12,00
7. Reforma de Blazer - R$ 80,00

---

## 🎉 Sistema Pronto Para Uso!

Se todos os itens acima foram verificados, seu sistema está **100% funcional**!

### Próximos Passos:
1. ✂️ **Personalize** os serviços para seu ateliê
2. 👥 **Cadastre** seus clientes reais
3. 💰 **Crie** orçamentos reais
4. 📊 **Acompanhe** suas vendas no dashboard
5. 🎨 **Customize** cores e logo

### Dicas de Uso:
- Faça backup regularmente (Menu: Configurações)
- Mantenha os serviços atualizados
- Use as categorias para organizar melhor
- Aproveite o histórico dos clientes
- Acompanhe os relatórios mensalmente

---

**Sucesso com seu ateliê! 🧵✨**

Se precisar de ajuda, consulte o `README.md` completo.
