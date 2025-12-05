# ✅ Checklist de Deploy - Hostinger

Use este checklist para garantir que tudo está pronto para produção.

## 📝 Antes do Upload

- [ ] Testei o sistema localmente
- [ ] Criei backup do banco de dados local
- [ ] Revisei arquivo `config/config.hostinger.php`
- [ ] Verifiquei que `.htaccess` está configurado
- [ ] Comprimi arquivos em ZIP (opcional)

## 🗄️ Banco de Dados

- [ ] Criei banco MySQL na Hostinger
- [ ] Anotei credenciais (host, database, user, password)
- [ ] Importei arquivo `database/database.sql` via phpMyAdmin
- [ ] Verifiquei que todas as tabelas foram criadas
- [ ] Testei conexão via phpMyAdmin

## 📤 Upload de Arquivos

- [ ] Conectei via FTP ou usei Gerenciador de Arquivos
- [ ] Fiz upload de TODOS os arquivos para `public_html`
- [ ] Verifiquei que pasta `fpdf` foi enviada completamente
- [ ] Criei pastas `/uploads`, `/uploads/logo`, `/temp`
- [ ] Ajustei permissões (755 para pastas, 644 para arquivos)

## ⚙️ Configuração

- [ ] Renomeei `config/config.hostinger.php` para `config/config.php`
- [ ] Editei `config/config.php` com dados do banco
- [ ] Atualizei `SITE_URL` com domínio real
- [ ] Configurei `display_errors = 0` para produção
- [ ] Salvei arquivo `config/config.php`

## 🔐 Segurança

- [ ] Ativei certificado SSL (HTTPS)
- [ ] Descomentei regras de redirecionamento HTTPS no `.htaccess`
- [ ] Verifiquei proteção de arquivos sensíveis (.sql, .md)
- [ ] Configurei permissões corretas (755/644)
- [ ] Alterei senha padrão do admin após primeiro login

## 🧪 Testes

- [ ] Acessei `https://seudominio.com/login.php`
- [ ] Fiz login com `admin@atelie.com` / `admin123`
- [ ] Verifiquei dashboard (gráficos, estatísticas)
- [ ] Testei cadastro de cliente
- [ ] Testei cadastro de serviço
- [ ] Criei orçamento de teste
- [ ] Gerei PDF do orçamento
- [ ] Baixei PDF e verifiquei formatação
- [ ] Testei em dispositivo móvel
- [ ] Verifiquei caracteres especiais (ç, ã, ú, etc.)

## 📊 Configurações Finais

- [ ] Atualizei dados do ateliê em "Configurações"
- [ ] Adicionei logo do ateliê (se tiver)
- [ ] Cadastrei serviços reais
- [ ] Excluí dados de teste
- [ ] Alterei senha do admin para senha forte
- [ ] Configurei backup automático na Hostinger
- [ ] Documentei credenciais em local seguro

## 🎯 Pós-Deploy

- [ ] Enviei link para cliente/usuário
- [ ] Forneci credenciais de acesso
- [ ] Expliquei funcionalidades básicas
- [ ] Configurei email profissional (opcional)
- [ ] Agendei backup semanal dos dados
- [ ] Documentei possíveis personalizações futuras

## 📱 Extras (Opcional)

- [ ] Adicionei ícone do site (favicon.ico)
- [ ] Configurei Google Analytics
- [ ] Criei página 404 personalizada
- [ ] Configurei SMTP para envio de emails
- [ ] Ativei cache de servidor
- [ ] Otimizei imagens com compressão

---

## 🆘 Em caso de erro:

1. ✅ Ative debug temporário em `config.php`
2. ✅ Verifique logs de erro no hPanel
3. ✅ Confirme dados do banco de dados
4. ✅ Teste conexão via phpMyAdmin
5. ✅ Verifique permissões de pastas
6. ✅ Entre em contato com suporte da Hostinger

**Status:** ⚪ Não iniciado | 🔵 Em andamento | ✅ Concluído
