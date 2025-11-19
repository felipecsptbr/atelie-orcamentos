# 📦 Como Hospedar no GitHub - Passo a Passo

## 1️⃣ Instalar o Git

### Windows:
1. Baixe: https://git-scm.com/download/win
2. Execute o instalador
3. Use as configurações padrão
4. Reinicie o PowerShell após a instalação

## 2️⃣ Configurar o Git (Execute no PowerShell)

```powershell
# Configure seu nome
git config --global user.name "Seu Nome"

# Configure seu email
git config --global user.email "seu-email@exemplo.com"

# Verifique a configuração
git config --list
```

## 3️⃣ Criar Repositório no GitHub

1. Acesse: https://github.com
2. Faça login ou crie uma conta
3. Clique no botão verde "New" ou "+" → "New repository"
4. Preencha:
   - **Repository name**: `atelie-orcamentos`
   - **Description**: `Sistema de Orçamentos para Ateliê de Costura`
   - **Visibility**: Public ou Private (sua escolha)
   - ❌ **NÃO** marque "Add a README file"
5. Clique em "Create repository"

## 4️⃣ Preparar o Projeto (Execute no PowerShell)

```powershell
# Vá para a pasta do projeto
cd C:\Users\filipe.cavalcante\Desktop\ORCAMENTOS

# Inicialize o Git
git init

# Adicione todos os arquivos
git add .

# Faça o primeiro commit
git commit -m "Primeiro commit: Sistema de Orçamentos completo"

# Renomeie a branch para main
git branch -M main
```

## 5️⃣ Conectar ao GitHub

```powershell
# Substitua SEU-USUARIO pelo seu usuário do GitHub
git remote add origin https://github.com/SEU-USUARIO/atelie-orcamentos.git

# Verifique se foi adicionado
git remote -v
```

## 6️⃣ Enviar para o GitHub

```powershell
# Envie os arquivos
git push -u origin main
```

### ⚠️ Se pedir autenticação:
- **Username**: Seu usuário do GitHub
- **Password**: Use um **Personal Access Token** (não sua senha)

### Como criar um Personal Access Token:
1. GitHub → Clique na sua foto → Settings
2. Developer settings (no final da página)
3. Personal access tokens → Tokens (classic)
4. Generate new token (classic)
5. Marque: `repo` (todos os sub-itens)
6. Clique em "Generate token"
7. **COPIE O TOKEN** (você não verá novamente!)
8. Use esse token como senha no `git push`

## 7️⃣ Verificar

Acesse: `https://github.com/SEU-USUARIO/atelie-orcamentos`

Você deve ver todos os arquivos do projeto!

## 🔄 Atualizações Futuras

Quando fizer alterações no código:

```powershell
# Adicione as mudanças
git add .

# Faça um commit descritivo
git commit -m "Descrição das mudanças"

# Envie para o GitHub
git push
```

## 📝 Comandos Úteis

```powershell
# Ver status dos arquivos
git status

# Ver histórico de commits
git log --oneline

# Ver arquivos ignorados pelo Git
cat .gitignore

# Clonar seu repositório em outro computador
git clone https://github.com/SEU-USUARIO/atelie-orcamentos.git
```

## ✅ Checklist Final

- [ ] Git instalado
- [ ] Configuração global feita
- [ ] Repositório criado no GitHub
- [ ] Git inicializado localmente
- [ ] Arquivos commitados
- [ ] Remote adicionado
- [ ] Push realizado com sucesso
- [ ] Arquivos visíveis no GitHub

## 🎉 Pronto!

Seu projeto está hospedado no GitHub e pode ser:
- Compartilhado com outras pessoas
- Clonado em outros computadores
- Versionado e rastreado
- Usado em seu portfólio

## 🔐 Segurança

**IMPORTANTE**: O arquivo `config.php` está no `.gitignore` e NÃO será enviado ao GitHub, protegendo suas credenciais do banco de dados!

## 💡 Dica

Adicione o link do GitHub ao seu README:

```markdown
## 🔗 Links

- [Repositório no GitHub](https://github.com/SEU-USUARIO/atelie-orcamentos)
- [Demonstração Online](seu-site.com) (se tiver)
```

---

**Precisando de ajuda? Abra uma issue no GitHub ou me pergunte!** 😊
