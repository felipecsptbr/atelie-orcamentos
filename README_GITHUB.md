# 🧵 Sistema de Orçamentos para Ateliê de Costura

Sistema completo de geração de orçamentos desenvolvido em PHP puro, MySQL e AdminLTE.

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?logo=php)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-4-7952B3?logo=bootstrap&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green)

## ✨ Funcionalidades

- 🔐 **Autenticação** - Sistema de login seguro
- 👥 **Gestão de Clientes** - Cadastro completo com histórico
- ✂️ **Gestão de Serviços** - Categorias, preços e tempos estimados
- 💰 **Orçamentos** - Criação, edição, duplicação e controle de status
- 📄 **Geração de PDF** - Orçamentos profissionais personalizáveis
- 📊 **Dashboard** - Estatísticas e gráficos em tempo real
- 📈 **Relatórios** - Análises por período com exportação CSV
- ⚙️ **Configurações** - Personalização completa do sistema
- 📱 **Responsivo** - 100% mobile-friendly
- 💾 **Backup** - Geração de backups do banco de dados

## 🚀 Deploy Rápido

### Hospedagem Gratuita:

[![Deploy no Railway](https://railway.app/button.svg)](https://railway.app)

**Outras opções:**
- [InfinityFree](https://infinityfree.net) - Hospedagem PHP + MySQL gratuita
- [Render](https://render.com) - Deploy automático via Git

Veja [DEPLOY.md](DEPLOY.md) para instruções completas.

## 💻 Instalação Local (XAMPP)

### Pré-requisitos:
- XAMPP (Apache + MySQL + PHP 7.4+)
- Navegador moderno

### Passos:

1. **Clone ou baixe o projeto:**
```bash
cd C:\xampp\htdocs
git clone https://github.com/felipecsptbr/atelie-orcamentos.git
```

2. **Crie o banco de dados:**
- Acesse http://localhost/phpmyadmin
- Crie banco: `atelie_orcamentos`
- Importe: `database/database.sql`

3. **Acesse o sistema:**
- URL: http://localhost/atelie-orcamentos
- Email: `admin@atelie.com`
- Senha: `admin123`

📖 **Guia completo:** [INSTALACAO.md](INSTALACAO.md)

## 🎨 Personalização

### Alterar Cores:
Edite `includes/header.php`:
```css
:root {
    --primary-color: #c06c84;   /* Rosa */
    --secondary-color: #6c5b7b; /* Roxo */
    --accent-color: #f8b195;    /* Pêssego */
}
```

### Configurar Ateliê:
1. Login no sistema
2. Menu: **Configurações**
3. Preencha dados do ateliê
4. Faça upload do logo

## 📂 Estrutura do Projeto

```
atelie-orcamentos/
├── config/              # Configurações e conexão
├── database/            # Scripts SQL
├── includes/            # Header, sidebar, footer
├── assets/              # CSS customizado
├── uploads/             # Logo e arquivos
├── backups/             # Backups automáticos
├── fpdf/                # Biblioteca PDF
├── index.php            # Dashboard
├── login.php            # Autenticação
├── clientes.php         # Gestão de clientes
├── servicos.php         # Gestão de serviços
├── orcamentos.php       # Listagem de orçamentos
├── orcamento_novo.php   # Criar/Editar orçamento
├── orcamento_pdf.php    # Gerar PDF
├── configuracoes.php    # Configurações
└── relatorios.php       # Relatórios
```

## 🛠️ Tecnologias Utilizadas

- **Backend:** PHP 8.x (puro, sem frameworks)
- **Banco de Dados:** MySQL 5.7+
- **Frontend:** AdminLTE 3.2, Bootstrap 4, jQuery
- **PDF:** FPDF
- **Gráficos:** Chart.js
- **Tabelas:** DataTables
- **Ícones:** Font Awesome 5

## 📸 Screenshots

### Dashboard
![Dashboard](https://via.placeholder.com/800x400/c06c84/fff?text=Dashboard)

### Orçamentos
![Orçamentos](https://via.placeholder.com/800x400/6c5b7b/fff?text=Orçamentos)

### PDF Gerado
![PDF](https://via.placeholder.com/800x400/f8b195/fff?text=PDF)

## 🤝 Contribuindo

Contribuições são bem-vindas! Sinta-se à vontade para:
1. Fazer fork do projeto
2. Criar uma branch (`git checkout -b feature/nova-funcionalidade`)
3. Commit suas mudanças (`git commit -m 'Adiciona nova funcionalidade'`)
4. Push para a branch (`git push origin feature/nova-funcionalidade`)
5. Abrir um Pull Request

## 📝 Licença

Este projeto está sob a licença MIT. Veja [LICENSE](LICENSE) para mais detalhes.

## 💬 Suporte

- 📖 Documentação completa: [README.md](README.md)
- 🚀 Guia de instalação: [INSTALACAO.md](INSTALACAO.md)
- 🌐 Guia de deploy: [DEPLOY.md](DEPLOY.md)
- 🎨 Guia de customização: [CUSTOMIZACAO.md](CUSTOMIZACAO.md)

## ⭐ Agradecimentos

- [AdminLTE](https://adminlte.io/) - Template administrativo
- [FPDF](http://www.fpdf.org/) - Geração de PDFs
- [Chart.js](https://www.chartjs.org/) - Gráficos interativos
- [DataTables](https://datatables.net/) - Tabelas avançadas

---

**Desenvolvido com ❤️ para ateliês de costura**

🧵 ✂️ 📐 👗
