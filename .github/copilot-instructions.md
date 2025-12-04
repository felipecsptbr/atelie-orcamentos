# Sistema de Orçamentos para Ateliê de Costura - Laravel

## Visão Geral
Este é um sistema de orçamentos para ateliê de costura desenvolvido com Laravel PHP. Permite criar orçamentos com múltiplos serviços, gerar PDFs e gerenciar clientes.

## Funcionalidades
- Criação de orçamentos com múltiplos serviços
- Biblioteca de serviços pré-definidos (conserto de calça, ajustes, etc.)
- Geração de PDF dos orçamentos
- Interface moderna e responsiva
- Gerenciamento de clientes

## Stack Tecnológica
- Laravel 10+ com PHP 8.1+
- Bootstrap 5 para estilização
- DomPDF para geração de PDFs
- MySQL/SQLite para banco de dados
- Blade Templates para views

## Estrutura do Projeto
- `/app/Models` - Models Eloquent (Cliente, Servico, Orcamento)
- `/app/Http/Controllers` - Controllers da aplicação
- `/resources/views` - Views Blade
- `/database/migrations` - Migrations do banco de dados
- `/public` - Arquivos públicos (CSS, JS, imagens)